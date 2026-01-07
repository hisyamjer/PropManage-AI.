<?php
session_start();
$host = "127.0.0.1";
$user = "root";
$pass = "Hisyam.2005";
$dbname = "users";

// 1. Security Check: Ensure only owners can access
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'tenant') {
    header("Location: login.php");
    exit();
}

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$userId = $_SESSION['user_id'];
$successMsg = "";
$errorMsg = "";

// --- 2. HANDLE FORM SUBMISSION ---
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $newName = trim($_POST['name']);
    $newEmail = trim($_POST['email']);
    $newIC = trim($_POST['ic_number']);
    $newPhone = trim($_POST['phone_number']);
    $newGender = $_POST['gender'];

    // Start transaction to ensure both tables update or neither does
    $conn->begin_transaction();

    try {
        // Update the 'user' table
        $stmt1 = $conn->prepare("UPDATE user SET name = ?, email = ? WHERE id = ?");
        $stmt1->bind_param("ssi", $newName, $newEmail, $userId);
        $stmt1->execute();

        // Update the 'owners' table
        $stmt2 = $conn->prepare("UPDATE tenants SET ic_number = ?, phone_number = ?, gender = ? WHERE user_id = ?");
        $stmt2->bind_param("sssi", $newIC, $newPhone, $newGender, $userId);
        $stmt2->execute();

        $conn->commit();
        $_SESSION['user_name'] = $newName; // Sync the session name for the dashboard
        $successMsg = "Profile updated successfully!";
    } catch (Exception $e) {
        $conn->rollback();
        $errorMsg = "Error updating profile: " . $e->getMessage();
    }
}

// --- 3. FETCH CURRENT INFORMATION (JOINED TABLES) ---
$sql = "SELECT u.name, u.email, t.ic_number, t.phone_number, t.gender 
        FROM user u 
        JOIN tenants t ON u.id = t.user_id 
        WHERE u.id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userId);
$stmt->execute();
$data = $stmt->get_result()->fetch_assoc();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Profile - PropManage</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&display=swap" rel="stylesheet">
</head>
<body class="bg-[#f4f4f7] font-['Inter'] p-4 sm:p-8">

    <div class="max-w-2xl mx-auto">
        <a href="tenant.php" class="text-blue-600 font-bold hover:underline mb-6 inline-block">← Back to Dashboard</a>
        
        <div class="bg-white rounded-3xl shadow-xl p-8 border border-gray-100">
            <h1 class="text-2xl font-extrabold text-gray-900 mb-2">Personal Information</h1>
            <p class="text-gray-500 mb-8">Update your profile details below.</p>

            <?php if ($successMsg): ?>
                <div class="bg-green-100 text-green-700 p-4 rounded-2xl mb-6 border border-green-200">✅ <?php echo $successMsg; ?></div>
            <?php endif; ?>
            
            <?php if ($errorMsg): ?>
                <div class="bg-red-100 text-red-700 p-4 rounded-2xl mb-6 border border-red-200">🛑 <?php echo $errorMsg; ?></div>
            <?php endif; ?>

            <form method="POST" class="space-y-5">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Full Name</label>
                        <input type="text" name="name" value="<?php echo htmlspecialchars($data['name']); ?>" required
                               class="w-full p-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Email Address</label>
                        <input type="email" name="email" value="<?php echo htmlspecialchars($data['email']); ?>" required
                               class="w-full p-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">IC Number</label>
                        <input type="text" name="ic_number" value="<?php echo htmlspecialchars($data['ic_number']); ?>" required
                               class="w-full p-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Phone Number</label>
                        <input type="text" name="phone_number" value="<?php echo htmlspecialchars($data['phone_number']); ?>" required
                               class="w-full p-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Gender</label>
                    <select name="gender" class="w-full p-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none">
                        <option value="male" <?php echo $data['gender'] == 'male' ? 'selected' : ''; ?>>Male</option>
                        <option value="female" <?php echo $data['gender'] == 'female' ? 'selected' : ''; ?>>Female</option>
                        <option value="other" <?php echo $data['gender'] == 'other' ? 'selected' : ''; ?>>Other</option>
                    </select>
                </div>

                <button type="submit" class="w-full bg-[#004AAD] text-white font-bold py-4 rounded-2xl hover:bg-blue-800 transition shadow-lg mt-4">
                    Save Changes
                </button>
            </form>
        </div>
    </div>

</body>
</html>