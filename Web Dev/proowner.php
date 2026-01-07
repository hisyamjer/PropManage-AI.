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

$ownerId = $_SESSION['user_id'];
// Updated to match your login session variable name
$userName = $_SESSION['user_name'] ?? 'Owner User'; 
$logoutUrl = 'login.php?action=logout';

// Fetch only properties belonging to THIS owner + tenant count from agreements
$sql = "SELECT p.idproperty, p.type, p.address, p.price, p.state, 
               (SELECT COUNT(*) FROM agreements a WHERE a.idproperty = p.idproperty) AS tenant_count
        FROM property p 
        WHERE p.owner_id = ? AND is_deleted = 0";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $ownerId);
$stmt->execute();
$result = $stmt->get_result();

$totalTenants = 0;
$totalValue = 0;
$propertyRows = []; // Create an array to store the data

// 1. FETCH ALL DATA INTO AN ARRAY FIRST
while($row = $result->fetch_assoc()){
    $totalTenants += $row['tenant_count'];
    if($row['tenant_count'] > 0) {
        $totalValue += $row['price'];
    }
    $propertyRows[] = $row; // Store each row so we can use it in the table later
}
$totalProperties = count($propertyRows);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Owner Dashboard - PropManage</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&display=swap" rel="stylesheet">
    <script type="module" src="https://unpkg.com/lucide@latest"></script>
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

<div class="max-w-6xl mx-auto mt-12">
    <header class="flex justify-between items-end mb-8">
        <div>
            <h1 class="text-3xl font-extrabold text-blue-600">Owner <span class="text-gray-900">Dashboard</span></h1>
            <p class="text-gray-500">Welcome back, <?php echo htmlspecialchars($userName); ?></p>
        </div>
        <div class="flex gap-3">
        <a href="generate_report.php" class="bg-slate-800 text-white px-6 py-3 rounded-2xl font-bold shadow-lg hover:bg-black transition flex items-center gap-2">
            <i data-lucide="file-text" class="w-4 h-4"></i> Download PDF Report
        </a>
        <a href="addPro.php" class="bg-blue-600 text-white px-6 py-3 rounded-2xl font-bold shadow-lg hover:bg-blue-700 transition">
            + Add New Property
        </a>
    </header>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
        <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100 flex items-center space-x-4">
            <div class="p-4 bg-blue-50 rounded-2xl"><i data-lucide="building-2" class="w-6 h-6 text-blue-600"></i></div>
            <div>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Total Properties</p>
                <h3 class="text-2xl font-black text-gray-900"><?php echo $totalProperties; ?></h3>
            </div>
        </div>
        <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100 flex items-center space-x-4">
            <div class="p-4 bg-green-50 rounded-2xl"><i data-lucide="users" class="w-6 h-6 text-green-600"></i></div>
            <div>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Active Tenants</p>
                <h3 class="text-2xl font-black text-gray-900"><?php echo $totalTenants; ?></h3>
            </div>
        </div>
        <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100 flex items-center space-x-4">
            <div class="p-4 bg-orange-50 rounded-2xl"><i data-lucide="wallet" class="w-6 h-6 text-orange-600"></i></div>
            <div>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Monthly Revenue</p>
                <h3 class="text-2xl font-black text-gray-900">RM <?php echo number_format($totalValue, 0); ?></h3>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-3xl shadow-xl overflow-hidden border border-gray-100">
        <div class="p-6 border-b border-gray-100 flex justify-between items-center">
            <h2 class="text-xl font-bold text-gray-800">Your Managed Listings</h2>
            <span class="bg-blue-100 text-blue-700 text-xs font-bold px-3 py-1 rounded-full">
                <?php echo $totalProperties; ?> Total
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-700">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="p-4">ID</th>
                        <th class="p-4">Type</th>
                        <th class="p-4">Address</th>
                        <th class="p-4 hidden sm:table-cell">State</th>
                        <th class="p-4 text-right">Price (RM)</th>
                        <th class="p-4 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($totalProperties > 0): ?>
                        <?php foreach($propertyRows as $row): ?>
                            <tr class="border-b hover:bg-blue-50/50 transition">
                                <td class="p-4 font-semibold text-gray-500">#<?php echo $row["idproperty"]; ?></td>
                                <td class="p-4"><span class="px-2 py-1 rounded-lg bg-gray-100 text-xs font-bold uppercase"><?php echo ucfirst($row["type"]); ?></span></td>
                                <td class="p-4 font-medium text-gray-900"><?php echo htmlspecialchars($row["address"]); ?></td>
                                <td class="p-4 hidden sm:table-cell text-gray-600"><?php echo htmlspecialchars($row["state"]); ?></td>
                                <td class="p-4 text-right font-extrabold text-blue-600">RM <?php echo number_format($row["price"], 0); ?></td>
                                <td class="p-4 text-center space-x-2">
                                    <a href="#?id=<?php echo $row['idproperty']; ?>" class="text-blue-600 hover:text-blue-800 font-bold px-2">Edit</a>
                                    <a href="delete_property.php?id=<?php echo $row['idproperty']; ?>" class="text-red-500 hover:text-red-700 font-bold px-2" onclick="return confirm('Delete this listing?')">Remove</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="p-16 text-center text-gray-400">
                                <i data-lucide="building" class="w-12 h-12 mx-auto mb-4 opacity-20"></i>
                                <p>You haven't added any properties yet.</p>
                                <a href="addPro.php" class="text-blue-600 font-bold hover:underline">Click here to start.</a>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        if (typeof lucide !== 'undefined') lucide.createIcons();
    });
</script>
</body>
</html>