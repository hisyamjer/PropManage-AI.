<?php
$host = "127.0.0.1";
$user = "root";
$pass = "Hisyam.2005";
$dbname = "users";

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("❌ Database connection failed: " . htmlspecialchars($conn->connect_error));
}
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'owner') {
    header("Location: login.php");
    exit();
}

$owner_id = $_SESSION['user_id'];

// --- UPDATED SQL: Joining tenants and user table to get name and phone ---
$sql = "SELECT a.startDate, a.endDate, a.rentAmount, a.status, a.agreementDate, a.idproperty, 
               p.address, 
               u.name AS tenant_name, t.phone_number AS tenant_phone
        FROM agreements a 
        JOIN property p ON a.idproperty = p.idproperty 
        JOIN tenants t ON a.tenant_id = t.id
        JOIN user u ON t.user_id = u.id
        WHERE a.owner_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $owner_id);
$stmt->execute();
$result = $stmt->get_result();
$stmt->close();

$userName = $_SESSION['name'] ?? 'Owner User';
$logoutUrl = 'login.php?action=logout';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lease Agreements - PropManage</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f8fafc; }
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
                <a href="ownerProfile.php" class="nav-link text-sm font-medium opacity-80 hover:opacity-100">My Profile</a>
                <a href="financial.php" class="nav-link text-sm font-medium opacity-80 hover:opacity-100">Financial</a>
                <a href="ownerAgreement.php" class="nav-link text-sm font-medium border-b-2 border-white">Agreements</a>
                <a href="addPro.php" class="nav-link text-sm font-medium opacity-80 hover:opacity-100">Add Property</a>
            </div>
        </div>
        <div class="flex items-center space-x-4">
            <div class="text-right hidden sm:block">
                <p class="text-xs opacity-70">Logged in as</p>
                <p class="text-sm font-bold"><?= htmlspecialchars($userName) ?></p>
            </div>
            <a href="<?= $logoutUrl ?>" class="p-2 hover:bg-red-500 rounded-lg transition">
                <i data-lucide="log-out" class="w-5 h-5"></i>
            </a>
        </div>
    </div>
</nav>

<main class="max-w-7xl mx-auto px-4 mt-10">
    <div class="mb-8">
        <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight flex items-center gap-3">
            <i data-lucide="file-text" class="text-blue-600"></i> Lease Agreements
        </h1>
        <p class="text-slate-500 mt-1">Detailed view of your tenants and active contracts.</p>
    </div>

    <div class="bg-white rounded-[2rem] shadow-xl border border-slate-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-slate-50 border-b border-slate-100">
                    <tr>
                        <th class="px-6 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-wider">Property</th>
                        <th class="px-6 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-wider">Tenant Details</th>
                        <th class="px-6 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-wider text-center">Duration</th>
                        <th class="px-6 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-wider text-center">Status</th>
                        <th class="px-6 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-wider text-right">Rent</th>
                        <th class="px-6 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-wider text-center">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    <?php if ($result->num_rows > 0): ?>
                        <?php while($row = $result->fetch_assoc()): 
                            $formatted_start = date('M d, Y', strtotime($row["startDate"]));
                            $formatted_end = date('M d, Y', strtotime($row["endDate"]));
                            
                            $isActive = strtolower($row["status"]) == 'active';
                            $status_class = $isActive ? 'bg-green-100 text-green-700' : 'bg-amber-100 text-amber-700';
                            $dot_class = $isActive ? 'bg-green-500' : 'bg-amber-500';
                        ?>
                        <tr class="hover:bg-slate-50/80 transition group">
                            <td class="px-6 py-5">
                                <div class="flex flex-col">
                                    <span class="font-bold text-slate-800"><?= htmlspecialchars($row['address']) ?></span>
                                    <span class="text-[10px] text-slate-400">ID: #<?= htmlspecialchars($row['idproperty']) ?></span>
                                </div>
                            </td>

                            <td class="px-6 py-5">
                                <div class="flex flex-col">
                                    <span class="font-bold text-blue-600 flex items-center gap-1">
                                        <i data-lucide="user" class="w-3 h-3"></i> <?= htmlspecialchars($row['tenant_name']) ?>
                                    </span>
                                    <a href="tel:<?= $row['tenant_phone'] ?>" class="text-[11px] text-slate-500 hover:text-blue-500 flex items-center gap-1 mt-1">
                                        <i data-lucide="phone" class="w-3 h-3"></i> <?= htmlspecialchars($row['tenant_phone']) ?>
                                    </a>
                                </div>
                            </td>

                            <td class="px-6 py-5 text-center">
                                <div class="inline-flex flex-col items-center">
                                    <span class="text-xs font-semibold text-slate-600"><?= $formatted_start ?></span>
                                    <i data-lucide="arrow-down" class="w-3 h-3 text-slate-300 my-0.5"></i>
                                    <span class="text-xs font-semibold text-red-500"><?= $formatted_end ?></span>
                                </div>
                            </td>

                            <td class="px-6 py-5 text-center">
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-extrabold uppercase <?= $status_class ?>">
                                    <span class="w-1.5 h-1.5 rounded-full <?= $dot_class ?>"></span>
                                    <?= htmlspecialchars($row["status"]) ?>
                                </span>
                            </td>

                            <td class="px-6 py-5 text-right font-extrabold text-lg text-slate-900">
                                RM <?= number_format($row["rentAmount"], 0) ?>
                            </td>

                            <td class="px-6 py-5 text-center">
                            <?php if (strtolower($row["status"]) !== 'terminated'): ?>
                                <a href="terminate_agreement.php?id=<?= $row['idproperty'] ?>" 
                                onclick="return confirm('Are you sure you want to terminate this agreement?')"
                                class="bg-red-50 text-red-600 hover:bg-red-600 hover:text-white px-3 py-1.5 rounded-lg text-xs font-bold transition duration-200">
                                    Terminate
                                </a>
                                    <?php else: ?>
                                        <span class="text-slate-400 text-xs italic">No Actions</span>
                                    <?php endif; ?>
                            </td>
                            <td class="px-6 py-5 text-center">
                                <a href="delete_agreement.php?id=<?= $row['idproperty'] ?>" 
                                onclick="return confirm('WARNING: This will permanently delete this contract from history. Proceed?')"
                                class="bg-red-50 text-red-600 hover:bg-red-600 hover:text-white px-3 py-1.5 rounded-lg text-xs font-bold transition duration-200">
                                    Delete Record
                                </a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="p-16 text-center text-slate-400">
                                <i data-lucide="users" class="w-12 h-12 mx-auto mb-4 opacity-20"></i>
                                <p class="font-medium">No tenants or agreements found.</p>
                            </td>
                        </tr>
                    <?php endif; ?>
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