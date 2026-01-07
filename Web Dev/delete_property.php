<?php
session_start();
$host = "127.0.0.1";
$user = "root";
$pass = "Hisyam.2005";
$dbname = "users";

// 1. Security Check: Only logged-in owners can delete
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'owner') {
    header("Location: login.php");
    exit();
}

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// 2. Validate the Property ID
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $propertyId = (int)$_GET['id'];
    $ownerId = $_SESSION['user_id'];

    // 3. Permission Check: Ensure this owner actually owns the property
    // This prevents Owner A from deleting Owner B's properties via URL manipulation
    $checkStmt = $conn->prepare("SELECT idproperty FROM property WHERE idproperty = ? AND owner_id = ?");
    $checkStmt->bind_param("ii", $propertyId, $ownerId);
    $checkStmt->execute();
    $checkStmt->store_result();

    if ($checkStmt->num_rows > 0) {
        $delStmt = $conn->prepare("UPDATE property SET is_deleted = 1 WHERE idproperty = ?");
        $delStmt->bind_param("i", $propertyId);

        if ($delStmt->execute()) {
            $delStmt->close();
            $conn->close();
            header("Location: proowner.php?msg=archived"); // Redirect on success
            exit(); 
        } else {
            echo "Error updating record: " . $conn->error;
        }
    } else {
        echo "Error: You do not have permission to delete this property.";
    }
    $checkStmt->close();
}

$conn->close();
header("Location: proowner.php");
exit();
?>