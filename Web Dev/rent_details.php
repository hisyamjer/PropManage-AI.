<?php
// --- Database Configuration ---
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

// Check if the user is logged in AND if they are a tenant
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'tenant') {
    header("Location: login.php");
    exit();
}

$userName = $_SESSION['user_name'] ?? 'Tenant User';
$logoutUrl = 'login.php?action=logout';

// --- 1. Retrieve Property ID ---
// Get the ID from the URL (e.g., rent_details.php?property_id=123)
$propertyId = $_GET['property_id'] ?? null;

if (empty($propertyId)) {
    die("⚠️ Error: Property ID is missing.");
}

// --- 2. Fetch Property Details Safely ---
$sql = "SELECT * FROM property WHERE idproperty = ?";
$stmt = $conn->prepare($sql);

if ($stmt === false) {
    die("❌ SQL Prepare failed: " . htmlspecialchars($conn->error));
}

// Bind parameter (i for integer, assuming idproperty is an integer)
$stmt->bind_param("i", $propertyId);
$stmt->execute();
$result = $stmt->get_result();
$property = $result->fetch_assoc(); // Fetch the single property row

if (!$property) {
    die("⚠️ Property not found or invalid ID.");
}

// Close statement and connection (connection is closed at the end of the file)
$stmt->close();
$conn->close();

// --- Formatting Data for Display ---
$propertyType = ucfirst(htmlspecialchars($property['type']));
$address = htmlspecialchars($property['address']);
$price = number_format($property['price'], 0);
$state = htmlspecialchars($property['state']);
// Assuming other fields exist, like description, size, rooms, etc.
$description = htmlspecialchars($property['description'] ?? "No detailed description available for this property.");
$bedrooms = htmlspecialchars($property['bedrooms'] ?? "N/A");
$bathrooms = htmlspecialchars($property['bathrooms'] ?? "N/A");

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $propertyType; ?> Details - PropManage</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&display=swap" rel="stylesheet">
    <script type="module" src="https://unpkg.com/lucide@latest"></script>
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f4f4f7; }
        .details-container { max-width: 900px; margin: 0 auto; }
    </style>
</head>
<body class="p-4 sm:p-8">


    <div class="details-container pt-12">
        
        <div class="flex justify-between items-start mb-8">
            <h1 class="text-4xl font-extrabold text-gray-900">
                <?php echo $propertyType; ?> Details
            </h1>
            <a href="<?php echo $logoutUrl; ?>" 
               class="flex items-center space-x-2 bg-red-600 text-white text-sm font-semibold px-4 py-2 rounded-xl shadow-md transition hover:bg-red-700">
                <i data-lucide="log-out" class="w-4 h-4"></i>
                <span>Logout</span>
            </a>
        </div>
        
        <a href="search.php" class="inline-flex items-center text-blue-600 hover:text-blue-700 font-semibold mb-6">
            <i data-lucide="chevron-left" class="w-5 h-5 mr-1"></i>
            Back to Search Results
        </a>

        <div class="bg-white rounded-3xl shadow-xl overflow-hidden p-6 md:p-10">

            <div class="flex flex-col lg:flex-row gap-8">
                
                <div class="lg:w-2/3">
                    <h2 class="text-3xl font-bold text-blue-700 mb-2">
                        <?php echo $address; ?>
                    </h2>
                    <p class="text-xl font-medium text-gray-500 mb-6">
                        <i data-lucide="map-pin" class="w-5 h-5 inline mr-1 text-gray-400"></i>
                        <?php echo $state; ?>
                    </p>

                    <div class="p-4 bg-green-50 rounded-xl mb-6 border border-green-200">
                        <p class="text-lg font-semibold text-green-700">Monthly Rent</p>
                        <p class="text-4xl font-extrabold text-green-600">
                            RM <?php echo $price; ?> 
                            <span class="text-xl font-normal text-gray-500">/ month</span>
                        </p>
                    </div>

                    
                    <h3 class="text-xl font-bold text-gray-800 mb-3 border-b pb-2">Key Features</h3>
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-4 text-gray-700">
                        <p class="flex items-center"><i data-lucide="home" class="w-5 h-5 mr-2 text-blue-500"></i> Type: **<?php echo $propertyType; ?>**</p>
                        <p class="flex items-center"><i data-lucide="bed" class="w-5 h-5 mr-2 text-blue-500"></i> Bedrooms: **<?php echo $bedrooms; ?>**</p>
                        <p class="flex items-center"><i data-lucide="bath" class="w-5 h-5 mr-2 text-blue-500"></i> Bathrooms: **<?php echo $bathrooms; ?>**</p>
                        <p class="flex items-center"><i data-lucide="calendar" class="w-5 h-5 mr-2 text-blue-500"></i> Property ID: **<?php echo htmlspecialchars($propertyId); ?>**</p>
                    </div>
                </div>

                <div class="lg:w-1/3 bg-gray-50 p-6 rounded-2xl border border-gray-200 sticky top-4 self-start">
                    <h3 class="text-2xl font-bold text-gray-800 mb-4">Ready to Rent?</h3>
                    <p class="text-gray-600 mb-6">
                        Click the button below to submit your application and secure this property.
                    </p>

                    <form action="rent_action.php" method="POST">
                        <input type="hidden" name="property_id" value="<?php echo htmlspecialchars($propertyId); ?>">
                        <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($_SESSION['user_id']); ?>">
                        

                        <div>
                            <label for="start_date" class="block text-sm font-semibold text-gray-700 mb-1">
                            Preferred Start Date
                            </label>
                            <input type="date" 
                             id="start_date" 
                            name="start_date" 
                            required
                            min="<?php echo date('Y-m-d'); ?>" 
                            class="w-full p-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-transparent outline-none transition">
                        </div>

                        <div>
                        <label for="duration" class="block text-sm font-bold text-gray-700 mb-1">
                         Lease Duration
                        </label>
                            <select name="duration" 
                                id="duration" 
                                required 
                                class="w-full p-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none transition bg-white">
                            <option value="" disabled selected>Select duration</option>
                            <option value="6">6 Months</option>
                            <option value="12">12 Months (1 Year)</option>
                            <option value="18">18 Months</option>
                            <option value="24">24 Months (2 Years)</option>
                            </select>
                        </div>
                        <br>
                        <button type="submit"
                            class="w-full bg-green-600 hover:bg-green-700 text-white text-lg font-bold py-3 px-4 rounded-xl transition duration-200 uppercase tracking-wider shadow-lg">
                            Apply to Rent Now
                        </button>
                    </form>

                    <p class="text-xs text-center text-gray-400 mt-4">
                        Submitting an application does not guarantee acceptance.
                    </p>
                </div>

            </div>
        </div>

    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            if (typeof lucide !== 'undefined' && lucide.createIcons) {
                lucide.createIcons();
            }
        });
    </script>
</body>
</html>