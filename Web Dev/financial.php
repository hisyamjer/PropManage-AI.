<?php
session_start();
$host = "127.0.0.1";
$user = "root";
$pass = "Hisyam.2005";
$dbname = "users";

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("❌ Connection failed: " . $conn->connect_error);
}

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'owner') {
    header("Location: login.php");
    exit();
}

$ownerId = $_SESSION['user_id'];
$userName = $_SESSION['user_name'] ?? 'Owner';
$logoutUrl = 'login.php?action=logout';

$sql = "SELECT p.address, p.price, u.name AS tenant_name, 
               pay.status AS payment_status, 
               pay.paymentDate, 
               pay.method
        FROM property p 
        JOIN agreements a ON p.idproperty = a.idproperty
        JOIN tenants t ON a.tenant_id = t.id         
        JOIN user u ON t.user_id = u.id              
        LEFT JOIN payment pay ON t.id = pay.tenant_id 
        WHERE p.owner_id = ?
        ORDER BY pay.paymentDate DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $ownerId);
$stmt->execute();
$result = $stmt->get_result();

$totalCollected = 0;
$totalPending = 0;
$financeData = [];

while($row = $result->fetch_assoc()){
    if(strtolower($row['payment_status'] ?? '') === 'paid') {
        $totalCollected += $row['price'];
    } else {
        $totalPending += $row['price'];
    }
    $financeData[] = $row;
}

// Calculate collection percentage for the progress bar
$totalBilled = $totalCollected + $totalPending;
$collectionRate = $totalBilled > 0 ? ($totalCollected / $totalBilled) * 100 : 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Financial Overview - PropManage</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f8fafc; }
        .glass { background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(10px); }
        .nav-link { position: relative; transition: all 0.3s; }
        .nav-link::after { content: ''; position: absolute; bottom: -2px; left: 0; width: 0; height: 2px; background: white; transition: width 0.3s; }
        .nav-link:hover::after { width: 100%; }
    </style>
</head>

<body class="pb-12">

<nav class="bg-[#004AAD] text-white px-6 py-3 sticky top-0 z-50 shadow-lg">
    <div class="max-w-7xl mx-auto flex justify-between items-center">
        <div class="flex items-center space-x-8">
            <span class="text-2xl font-bold tracking-tighter">PropManage</span>
            <div class="hidden md:flex space-x-6">
                <a href="proowner.php" class="nav-link text-sm font-medium opacity-80 hover:opacity-100">Dashboard</a>
                <a href="ownerProfile.php" class="text-sm font-bold border-b-2 border-white pb-1">My Profile</a>
                <a href="financial.php" class="nav-link text-sm font-medium border-b-2 border-white">Financial</a>
                <a href="ownerAgreement.php" class="nav-link text-sm font-medium opacity-80 hover:opacity-100">Agreements</a>
                <a href="addPro.php" class="nav-link text-sm font-medium opacity-80 hover:opacity-100">Add Property</a>
            </div>
        </div>
        <div class="flex items-center space-x-4">
            <div class="text-right hidden sm:block">
                <p class="text-xs opacity-70">Welcome back,</p>
                <p class="text-sm font-bold"><?= htmlspecialchars($userName) ?></p>
            </div>
            <a href="<?= $logoutUrl ?>" class="p-2 hover:bg-red-500 rounded-lg transition" title="Logout">
                <i data-lucide="log-out" class="w-5 h-5"></i>
            </a>
        </div>
    </div>
</nav>

<main class="max-w-6xl mx-auto px-4 mt-8">
    <div class="flex flex-col md:flex-row md:items-center justify-between mb-8 gap-4">
        <div>
            <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">Financial Insights</h1>
            <p class="text-slate-500">Track your revenue and pending collections.</p>
        </div>
        <button onclick="window.print()" class="flex items-center gap-2 bg-white border border-slate-200 px-4 py-2 rounded-xl text-sm font-semibold text-slate-700 hover:bg-slate-50 transition shadow-sm">
            <i data-lucide="download" class="w-4 h-4"></i> Export Report
        </button>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white p-6 rounded-3xl shadow-sm border border-slate-100 relative overflow-hidden group">
            <div class="absolute right-[-10px] top-[-10px] opacity-5 group-hover:scale-110 transition duration-500">
                <i data-lucide="circle-check" class="w-24 h-24 text-green-600"></i>
            </div>
            <p class="text-sm font-medium text-slate-400 mb-1">Total Collected</p>
            <h3 class="text-3xl font-black text-slate-900">RM <?= number_format($totalCollected, 2) ?></h3>
            <div class="mt-4 flex items-center text-xs font-bold text-green-600">
                <i data-lucide="trending-up" class="w-3 h-3 mr-1"></i> +12% from last month
            </div>
        </div>

        <div class="bg-white p-6 rounded-3xl shadow-sm border border-slate-100 relative overflow-hidden group">
            <div class="absolute right-[-10px] top-[-10px] opacity-5 group-hover:scale-110 transition duration-500">
                <i data-lucide="clock" class="w-24 h-24 text-orange-600"></i>
            </div>
            <p class="text-sm font-medium text-slate-400 mb-1">Outstanding</p>
            <h3 class="text-3xl font-black text-slate-900">RM <?= number_format($totalPending, 2) ?></h3>
            <div class="mt-4 flex items-center text-xs font-bold text-orange-500">
                <i data-lucide="alert-circle" class="w-3 h-3 mr-1"></i> Requires attention
            </div>
        </div>

        <div class="bg-white p-6 rounded-3xl shadow-sm border border-slate-100">
            <p class="text-sm font-medium text-slate-400 mb-1">Collection Rate</p>
            <h3 class="text-3xl font-black text-slate-900"><?= number_format($collectionRate, 1) ?>%</h3>
            <div class="mt-4 w-full bg-slate-100 rounded-full h-2">
                <div class="bg-blue-600 h-2 rounded-full transition-all duration-1000" style="width: <?= $collectionRate ?>%"></div>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-[2rem] shadow-xl border border-slate-100 overflow-hidden">
        <div class="p-6 border-b border-slate-50 flex justify-between items-center bg-slate-50/50">
            <h2 class="font-bold text-slate-800 flex items-center gap-2">
                <i data-lucide="list" class="w-5 h-5 text-blue-600"></i> Payment History
            </h2>
            <div class="flex gap-2">
                <span class="bg-green-100 text-green-700 text-[10px] px-2 py-1 rounded-md font-bold uppercase">Paid: <?= count(array_filter($financeData, fn($f) => strtolower($f['payment_status'] ?? '') === 'paid')) ?></span>
                <span class="bg-red-100 text-red-700 text-[10px] px-2 py-1 rounded-md font-bold uppercase">Pending: <?= count(array_filter($financeData, fn($f) => strtolower($f['payment_status'] ?? '') !== 'paid')) ?></span>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-slate-50/50 text-slate-400 uppercase text-[11px] font-bold">
                    <tr>
                        <th class="px-6 py-4">Property Identity</th>
                        <th class="px-6 py-4 text-center">Payment Info</th>
                        <th class="px-6 py-4 text-right">Amount</th>
                        <th class="px-6 py-4 text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    <?php if(empty($financeData)): ?>
                    <tr>
                        <td colspan="4" class="p-12 text-center text-slate-400">
                            <i data-lucide="folder-open" class="w-12 h-12 mx-auto mb-4 opacity-20"></i>
                            <p>No financial records found.</p>
                        </td>
                    </tr>
                    <?php endif; ?>

                    <?php foreach($financeData as $row): ?>
                    <tr class="hover:bg-slate-50/80 transition group">
                        <td class="px-6 py-5">
                            <p class="font-bold text-slate-800 group-hover:text-blue-700 transition"><?= htmlspecialchars($row['address']) ?></p>
                            <p class="text-xs text-slate-400 mt-1 flex items-center gap-1">
                                <i data-lucide="user" class="w-3 h-3"></i> Tenant: <?= htmlspecialchars($row['tenant_name']) ?>
                            </p>
                        </td>
                        <td class="px-6 py-5 text-center">
                            <p class="text-slate-700 font-medium"><?= htmlspecialchars($row['method'] ?? 'Pending') ?></p>
                            <p class="text-[11px] text-slate-400 mt-1"><?= $row['paymentDate'] ? date('M d, Y', strtotime($row['paymentDate'])) : 'Awaiting payment' ?></p>
                        </td>
                        <td class="px-6 py-5 text-right font-extrabold text-slate-900">
                            RM <?= number_format($row['price'], 2) ?>
                        </td>
                        <td class="px-6 py-5 text-center">
                            <?php if(strtolower($row['payment_status'] ?? '') === 'paid'): ?>
                                <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-[10px] font-extrabold uppercase bg-green-100 text-green-700">
                                    <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span> Paid
                                </span>
                            <?php else: ?>
                                <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-[10px] font-extrabold uppercase bg-red-100 text-red-700">
                                    <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span> Pending
                                </span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</main>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        lucide.createIcons();
    });
</script>
</body>
</html>