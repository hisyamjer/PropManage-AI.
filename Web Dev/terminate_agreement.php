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

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $propertyId = (int)$_GET['id'];
    $ownerId = $_SESSION['user_id'];

    // 2. Update the status to 'Terminated'
    // We check owner_id to ensure the owner actually owns this contract
    $stmt = $conn->prepare("UPDATE agreements SET status = 'Terminated' WHERE idproperty = ? AND owner_id = ?");
    $stmt->bind_param("ii", $propertyId, $ownerId);

    if ($stmt->execute()) {
        header("Location: ownerAgreement.php?msg=terminated");
    } else {
        echo "Error: " . $conn->error;
    }
    $stmt->close();
}

$conn->close();
header("Location: ownerAgreement.php");
exit();