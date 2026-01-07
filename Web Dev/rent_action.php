<?php
session_start(); 

// --- Database Configuration ---
$host = "127.0.0.1";
$user = "root";
$pass = "Hisyam.2005";
$dbname = "users";

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("❌ Database connection failed: " . htmlspecialchars($conn->connect_error));
}

// --- 1. Security Check ---
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'tenant') {
    header("Location: login.php");
    exit();
}

// --- 2. Retrieve POST Data ---
$sessionUserId = $_SESSION['user_id']; // This is the ID from 'users' table
$propertyId = $_POST['property_id'] ?? null; 
$startDate = $_POST['start_date'] ?? null;
$durationMonths = $_POST['duration'] ?? null;

if (empty($propertyId) || empty($startDate) || empty($durationMonths)) {
    die("⚠️ Missing required data for rental application.");
}

// --- 3. NEW STEP: Fetch the actual Tenant ID ---
// We search the 'tenants' table for the row that belongs to the logged-in user
$tenantLookupSql = "SELECT id FROM tenants WHERE user_id = ?";
$tStmt = $conn->prepare($tenantLookupSql);
$tStmt->bind_param("i", $sessionUserId);
$tStmt->execute();
$tResult = $tStmt->get_result();
$tenantData = $tResult->fetch_assoc();

if (!$tenantData) {
    die("❌ Error: Your account is not properly registered as a tenant in the database.");
}

$actualTenantId = $tenantData['id']; // This is the ID the 'agreements' table is waiting for
$tStmt->close();

// --- 4. Logic: Fetch Property Price & Owner ---
$endDate = date('Y-m-d', strtotime("+$durationMonths months", strtotime($startDate)));

$propQuery = "SELECT price, owner_id FROM property WHERE idproperty = ?";
$pStmt = $conn->prepare($propQuery);
$pStmt->bind_param("i", $propertyId);
$pStmt->execute();
$pResult = $pStmt->get_result();
$propertyData = $pResult->fetch_assoc();

if (!$propertyData) {
    die("❌ Property not found.");
}

$rentAmount = $propertyData['price'];
$ownerId = $propertyData['owner_id']; 
$pStmt->close();

// --- 5. Process Application ---
$sql = "INSERT INTO agreements (idproperty, tenant_id, owner_id, startDate, endDate, rentAmount, status, agreementDate)
        VALUES (?, ?, ?, ?, ?, ?, 'Pending', NOW())";

$stmt = $conn->prepare($sql);

if ($stmt === false) {
    die("❌ SQL Prepare failed: " . htmlspecialchars($conn->error));
}

// We use $actualTenantId here instead of $sessionUserId
$stmt->bind_param("iiissd", $propertyId, $actualTenantId, $ownerId, $startDate, $endDate, $rentAmount);

if ($stmt->execute()) {
    $stmt->close();
    $conn->close();
    header("Location: application_success.php?property=" . urlencode($propertyId));
    exit();
} else {
    die("❌ Database Error: " . htmlspecialchars($stmt->error));
}
?>