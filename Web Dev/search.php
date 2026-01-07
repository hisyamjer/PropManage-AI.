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

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'tenant') {
    header("Location: login.php");
    exit();
}

$userName = $_SESSION['user_name'] ?? 'Tenant User';
$logoutUrl = 'login.php?action=logout';

$searchTerm = $_GET['search_term'] ?? '';
$location = $_GET['location'] ?? 'malaysia'; 
$propertyType = $_GET['property_type'] ?? '';
$maxPrice = $_GET['max_price'] ?? '';

// --- 1. SQL: Ensure property_image is selected ---
$sql = "SELECT p.idproperty, p.type, p.address, p.price, p.state, p.poscode, p.property_image, o.phone_number 
        FROM property p JOIN owners o ON p.owner_id=o.user_id";
$whereClauses = [];
$bindTypes = '';
$bindParams = [];

if (!empty($searchTerm)) {
    $whereClauses[] = "(address LIKE ? OR state LIKE ? OR poscode LIKE ?)";
    $searchWildcard = "%" . $searchTerm . "%";
    $bindTypes .= 'sss';
    $bindParams[] = $searchWildcard;
    $bindParams[] = $searchWildcard;
    $bindParams[] = $searchWildcard;
}

if ($location !== '' && $location !== 'malaysia') {
    $whereClauses[] = "state = ?";
    $bindTypes .= 's';
    $bindParams[] = $location;
}

if ($propertyType !== '') {
    $whereClauses[] = "type = ?";
    $bindTypes .= 's';
    $bindParams[] = $propertyType;
}

if ($maxPrice !== '') {
    $priceValue = (int)$maxPrice;
    if ($priceValue === 3000) { $whereClauses[] = "price >= ?"; } 
    elseif ($priceValue > 0) { $whereClauses[] = "price <= ?"; }
    
    if ($priceValue > 0) {
        $bindTypes .= 'i';
        $bindParams[] = $priceValue;
    }
}

if (count($whereClauses) > 0) { $sql .= " WHERE " . implode(" AND ", $whereClauses); }
$sql .= " ORDER BY price DESC";

$stmt = $conn->prepare($sql);
if (count($bindParams) > 0) { $stmt->bind_param($bindTypes, ...$bindParams); }
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Property Search - PropManage</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&display=swap" rel="stylesheet">
    <script type="module" src="https://unpkg.com/lucide@latest"></script>
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f8fafc; }
        .navbar-container { background-color: #004AAD; height: 60px; }
        .ma { color: white; text-decoration: none; padding: 0 15px; font-weight: 600; transition: 0.2s; }
        .ma:hover { color: #cbd5e1; }
    </style>
</head>
<body class="pb-12">

<div class="navbar-container rounded-2xl flex items-center px-6 shadow-lg mb-10">
    <nav class="flex items-center w-full">
        <span class="text-2xl mr-6">🏠</span>
        <div class="flex-grow flex space-x-2">
           <a href="tenant.php" class="ma">Dashboard</a>
            <a href="payment.php" class="ma">Payment</a>
            <a href="agreement.php" class="ma">Lease Agreement</a>
            <a href="search.php" class="ma">Available Property</a>
            <a href="tenant_profile.php" class="ma">My Profile</a>
        </div>
        <a href="<?php echo $logoutUrl; ?>" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-xl text-sm font-bold flex items-center gap-2">
            <i data-lucide="log-out" class="w-4 h-4"></i> Logout
        </a>
    </nav>
</div>

<div class="max-w-6xl mx-auto">
    <h1 class="text-4xl font-black text-slate-800 mb-8">Find Your <span class="text-blue-600">Perfect Home</span></h1>

    <div class="bg-white p-4 rounded-3xl shadow-xl border border-slate-100 mb-10">
        <form action="" method="GET" class="flex flex-col md:flex-row gap-4">
            <div class="flex-grow flex items-center bg-slate-50 rounded-2xl px-4 py-2 border border-slate-200">
                <i data-lucide="search" class="text-slate-400 mr-2"></i>
                <input type="text" name="search_term" value="<?php echo htmlspecialchars($searchTerm); ?>" placeholder="Search area or state..." class="bg-transparent w-full outline-none text-slate-700">
            </div>
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold px-8 py-3 rounded-2xl transition shadow-lg shadow-blue-200">
                Search Now
            </button>
        </form>
    </div>

    <div class="grid grid-cols-1 gap-6">
        <?php if ($result->num_rows > 0): ?>
            <?php while($row = $result->fetch_assoc()): ?>
                <div class="bg-white rounded-3xl p-4 shadow-md border border-slate-100 flex flex-col md:flex-row gap-6 hover:shadow-xl transition-shadow duration-300">
                    
                    <div class="w-full md:w-64 h-48 flex-shrink-0">
                        <?php if (!empty($row['property_image'])): ?>
                            <img src="<?php echo htmlspecialchars($row['property_image']); ?>" class="w-full h-full object-cover rounded-2xl shadow-inner border border-slate-100">
                        <?php else: ?>
                            <div class="w-full h-full bg-slate-100 rounded-2xl flex flex-col items-center justify-center text-slate-400 border-2 border-dashed border-slate-200">
                                <i data-lucide="image" class="w-10 h-10 mb-2"></i>
                                <span class="text-xs font-bold uppercase tracking-widest">No Image Available</span>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="flex-grow flex flex-col justify-between py-2">
                        <div>
                            <div class="flex justify-between items-start mb-2">
                                <span class="px-3 py-1 bg-blue-50 text-blue-600 text-xs font-extrabold rounded-lg uppercase">
                                    <?php echo htmlspecialchars($row['type']); ?>
                                </span>
                                <span class="text-2xl font-black text-blue-600">
                                    RM <?php echo number_format($row['price'], 0); ?><span class="text-sm text-slate-400 font-normal">/mo</span>
                                </span>
                            </div>
                            <h3 class="text-xl font-bold text-slate-800 mb-1">Property #<?php echo $row['idproperty']; ?></h3>
                            <p class="text-slate-500 flex items-center gap-1 mb-4">
                                <i data-lucide="map-pin" class="w-4 h-4 text-red-400"></i>
                                <?php echo htmlspecialchars($row['address']); ?>, <?php echo htmlspecialchars($row['poscode']); ?>, <?php echo htmlspecialchars($row['state']); ?>
                            </p>
                        </div>

                        <div class="flex items-center justify-between border-t border-slate-50 pt-4">
                            <div class="flex gap-4 text-slate-400 text-sm">
                                <span class="flex items-center gap-1"><i data-lucide="bed" class="w-4 h-4"></i> Available</span>
                                <span class="flex items-center gap-1"><i data-lucide="shield-check" class="w-4 h-4"></i> Verified</span>
                                <span class="flex items-center gap-1"><i data-lucide="phone" class="w-4 h-4"></i>Contact: <?php echo htmlspecialchars($row['phone_number']);?></span>
                            </div>
                            <a href="rent_details.php?property_id=<?php echo $row['idproperty']; ?>" class="bg-slate-900 hover:bg-blue-600 text-white px-6 py-2 rounded-xl font-bold text-sm transition transform hover:scale-105">
                                View Details
                            </a>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="text-center py-20 bg-white rounded-3xl border-2 border-dashed border-slate-200">
                <i data-lucide="search-x" class="w-16 h-16 mx-auto text-slate-300 mb-4"></i>
                <p class="text-slate-500 font-medium">No properties found matching your criteria.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        if (typeof lucide !== 'undefined') lucide.createIcons();
    });
</script>
</body>
</html>