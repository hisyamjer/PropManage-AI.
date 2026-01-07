<?php
session_start();
$host = "127.0.0.1";
$user = "root";
$pass = "Hisyam.2005";
$dbname = "users";

// 1. Security Check
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'owner') {
    header("Location: login.php");
    exit();
}

$conn = new mysqli($host, $user, $pass, $dbname);

// 2. Validate ID (Notice the underscore in $_GET)
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $propertyId = (int)$_GET['id'];
    $ownerId = $_SESSION['user_id'];

    // 3. Perform Hard Delete
    // Using both idproperty and owner_id for security
    $stmt = $conn->prepare("DELETE FROM agreements WHERE idproperty = ? AND owner_id = ?");
    $stmt->bind_param("ii", $propertyId, $ownerId);

    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            header("Location: ownerAgreement.php?msg=deleted");
            exit();
        } else {
            echo "No record found or you don't have permission.";
        }
    } else {
        echo "Error: " . $conn->error;
    }
    $stmt->close();
} else {
    echo "Invalid ID provided.";
}

$conn->close();