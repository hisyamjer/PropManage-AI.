<?php
$host = "127.0.0.1";
$user = "root";
$pass = "Hisyam.2005";
$dbname = "users";

// Connect to MySQL
$conn = new mysqli($host, $user, $pass, $dbname);

// Check connection
if ($conn->connect_error) {
    die("❌ Database connection failed: " . htmlspecialchars($conn->connect_error));
}
session_start();

// --- SECURITY AND ACCESS CONTROL ---
// Check if the user is logged in AND if they are a tenant
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'tenant') {
    // If not logged in or not a tenant, redirect to the login page
    header("Location: login.php");
    exit();
}

// OPTIMIZED QUERY: Only fetch agreements relevant to the logged-in user!
// This is a CRITICAL security and performance measure.
$sessionUserId = $_SESSION['user_id'];

$tenantLookupSql = "SELECT id FROM tenants WHERE user_id = ?";
$tStmt = $conn->prepare($tenantLookupSql);
$tStmt->bind_param("i", $sessionUserId);
$tStmt->execute();
$tResult = $tStmt->get_result();
$tenantData = $tResult->fetch_assoc();

if (!$tenantData) {
    // If they aren't in the tenants table, they won't have agreements
    $result = null; 
} else {
    $actualTenantId = $tenantData['id'];

    // 2. Updated Query: Use 'tenant_id' instead of 'user_id'
    $sql = "SELECT startDate, endDate, rentAmount, status, agreementDate, idproperty 
            FROM agreements 
            WHERE tenant_id = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $actualTenantId);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
}
$tStmt->close();

$userName = $_SESSION['user_name'] ?? 'Tenant User';
$logoutUrl = 'login.php?action=logout';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lease Agreement - PropManage</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&display=swap" rel="stylesheet">
    <script type="module" src="https://unpkg.com/lucide@latest"></script>

    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f4f4f7; }
        .main { list-style: none; display: flex; margin: 0; padding: 0; }
        .navbar-container { background-color: #004AAD; display: flex; align-items: center; justify-content: space-between; padding: 0 20px; height: 50px; }
        .ma { color: white; text-decoration: none; padding: 20px 15px; display: block; font-weight: bold; }
        .table-wrapper { overflow-x: auto; border-radius: 0.5rem; }
    </style>
</head>
<body class="pb-12">

    <div class="navbar-container rounded-xl mb-8 shadow-lg">
        <nav class="main">
            <p class="ma">🏠</p>
            <a href="tenant.php" class="ma">Dashboard</a>
            <a href="payment.php" class="ma">Payment</a>
            <a href="agreement.php" class="ma">Lease Agreement</a>
            <a href="search.php" class="ma">Available Property</a>
            <a href="tenant_profile.php" class="ma">My Profile</a>
        </nav>
        
        <a href="<?php echo $logoutUrl; ?>" 
            class="logout-link flex items-center space-x-1 bg-red-600 text-white text-sm font-semibold px-4 py-2 rounded-xl shadow-md transition duration-150 hover:bg-red-700" >
            <i data-lucide="log-out" class="w-4 h-4"></i>
            <span>Logout</span>
        </a>
    </div>

    <h2 class="text-3xl font-extrabold text-gray-800 mb-6 mt-8">📜 Your Lease Agreements</h2>

    <div class="table-wrapper bg-white shadow-xl rounded-xl">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Start Date</th>
                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">End Date</th>
                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Agreement Date</th>
                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 hidden md:table-cell text-xs font-bold text-gray-500 uppercase tracking-wider">Property ID</th>
                    <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Rent Amount (RM)</th>
                </tr>
            </thead>
            
            <tbody class="bg-white divide-y divide-gray-200">
            <?php
            // The date variables are DEFINED AND USED INSIDE THE LOOP.
            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    
                    // FIX 1: CORRECTLY defining and using the date formatting variables inside the loop.
                    $formatted_start_date = date('M d, Y', strtotime($row["startDate"]));
                    $formatted_end_date = date('M d, Y', strtotime($row["endDate"]));
                    $aDate = date('M d, Y', strtotime($row["agreementDate"]));

                    // Status Logic and Tailwind Class assignment
                    $status_class = strtolower($row["status"]) == 'active' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800';

                    echo "<tr class='bg-white border-b hover:bg-blue-50/50 transition duration-150'>";
                    
                    // 1. Start Date
                    echo "<td class='p-4 font-semibold whitespace-nowrap'>" . htmlspecialchars($formatted_start_date) . "</td>";
                    
                    // 2. End Date
                    echo "<td class='p-4 whitespace-nowrap text-red-600 font-semibold'>" . htmlspecialchars($formatted_end_date) . "</td>";
                    
                    // 3. Agreement Date
                    echo "<td class='p-4 font-medium text-gray-900 whitespace-nowrap'>" . htmlspecialchars($aDate) . "</td>";
                    
                    // 4. Status
                    echo "<td class='p-4 hidden sm:table-cell'>";
                    echo "<span class='inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium " . $status_class . "'>";
                    echo htmlspecialchars(ucfirst($row["status"])) . "</span></td>";
                    
                    // 5. Property ID
                    echo "<td class='p-4 hidden md:table-cell text-gray-600'>" . htmlspecialchars($row["idproperty"]) . "</td>";
                    
                    // 6. Rent Amount (The last column)
                    echo "<td class='p-4 text-right font-extrabold text-lg text-green-600 whitespace-nowrap'>RM " . number_format($row["rentAmount"], 0) . "</td>";
                    
                    echo "</tr>";
                }
            }
            else {
                // FIX 3: Colspan is set to 6 because we have 6 visible columns now.
                echo "<tr><td colspan='6' class='p-8 text-center text-gray-500'>No active lease agreements found for your account.</td></tr>";
            }
            ?>
            </tbody>
        </table>
    </div>

</body>
</html>