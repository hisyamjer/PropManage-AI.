<?php
// =========================================
// User Registration (MySQLi) - Linked Users Table
// =========================================

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
if (empty($_SESSION['csrf'])) {
    $_SESSION['csrf'] = bin2hex(random_bytes(16));
}
$csrf = $_SESSION['csrf'];

$errors = [];
$success = false;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (!isset($_POST['csrf']) || $_POST['csrf'] !== $_SESSION['csrf']) {
        $errors[] = "Invalid form submission.";
    } else {
        $name = trim($_POST['name']);
        $email = trim($_POST['email']);
        $password = $_POST['password'];
        $confirm = $_POST['password_confirm'];
        $ic_number = trim($_POST['ic_number']);
        $phone_number = trim($_POST['phone_number']);
        $gender = $_POST['gender'] ?? '';
        $role = $_POST['role'] ?? '';

        // Validation
        if (strlen($name) < 2) $errors[] = "Name must be at least 2 characters.";
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Invalid email address.";
        if (strlen($password) < 6) $errors[] = "Password must be at least 6 characters.";
        if ($password !== $confirm) $errors[] = "Passwords do not match.";
        if (empty($ic_number) || !preg_match('/^[a-zA-Z0-9]+$/', $ic_number)) $errors[] = "IC number is required and must be alphanumeric.";
        if (empty($phone_number) || !preg_match('/^[0-9]+$/', $phone_number)) $errors[] = "Phone number is required and must be numeric.";
        if (!in_array($gender, ['male', 'female', 'other'])) $errors[] = "Please select a valid gender.";
        if (!in_array($role, ['tenant', 'owner'])) $errors[] = "Please select a role (Tenant or Owner).";

        if (empty($errors)) {
            // Check email uniqueness
            $checkUser = $conn->prepare("SELECT id FROM user WHERE email=?"); // NOTE: Changed 'user' to 'users' here for consistency
            $checkUser->bind_param("s", $email);
            $checkUser->execute();
            $checkUser->store_result();

            if ($checkUser->num_rows > 0) {
                $errors[] = "Email already exists.";
            } else {
                $hash = password_hash($password, PASSWORD_DEFAULT);

                // Insert into `user`
                $stmtUser = $conn->prepare("INSERT INTO user (name, email, password) VALUES (?, ?, ?)"); // NOTE: Changed 'user' to 'users' here for consistency
                $stmtUser->bind_param("sss", $name, $email, $hash);
                $stmtUser->execute();
                $user_id = $stmtUser->insert_id;
                $stmtUser->close();

                // Insert into tenant/owner table
                if ($role === 'tenant') {
                    $stmt = $conn->prepare("INSERT INTO tenants (user_id, ic_number, phone_number, gender) VALUES (?, ?, ?, ?)");
                } else {
                    $stmt = $conn->prepare("INSERT INTO owners (user_id, ic_number, phone_number, gender) VALUES (?, ?, ?, ?)");
                }
                $stmt->bind_param("isss", $user_id, $ic_number, $phone_number, $gender);
                $stmt->execute();
                $stmt->close();

                $success = true;
            }
            $checkUser->close();
        }
    }
}
$conn->close();

function e($str) {
    return htmlspecialchars($str ?? '', ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>User Registration</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;800&display=swap" rel="stylesheet">
<style>
/* Reset and Base Styles */
* { 
    box-sizing: border-box; 
    transition: background 0.3s, box-shadow 0.3s, color 0.3s;
}
body {
    font-family: 'Poppins', sans-serif;
    background: linear-gradient(135deg, #1e40af, #3b82f6); 
    color: #fff;
    margin: 0;
    padding: 20px;
    min-height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center; /* Center form vertically */
    overflow-y: auto;
}

/* Form Container Styling */
form {
    background: rgba(255, 255, 255, 0.1); /* Slightly transparent white */
    padding: 30px; /* Increased padding */
    border-radius: 20px; /* More rounded corners */
    width: 100%;
    max-width: 450px; /* Slightly narrower for focus */
    backdrop-filter: blur(15px); /* Stronger blur effect */
    box-shadow: 0 15px 40px rgba(0, 0, 0, 0.5); /* Stronger shadow */
    border: 1px solid rgba(255, 255, 255, 0.2); /* Subtle white border */
}

h2 { 
    text-align: center; 
    margin-bottom: 30px; 
    font-size: 1.8em; 
    font-weight: 800; /* Extra bold */
}

/* Input Group Styling */
label { 
    font-size: 15px; 
    margin-top: 15px; 
    display: block; 
    font-weight: 600;
}

input:not([type="radio"]), select {
    width: 100%; 
    padding: 12px; 
    border: none; 
    border-radius: 10px; 
    margin-top: 8px; 
    background: rgba(255, 255, 255, 0.15); /* Slightly darker input background */
    color: #fff; 
    font-size: 16px;
    box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.1);
}
input:not([type="radio"]):focus {
    outline: none;
    background: rgba(255, 255, 255, 0.25);
    box-shadow: 0 0 0 2px #3b82f6; /* Blue focus ring */
}
input::placeholder { 
    color: #bbb; 
}

/* Radio/Checkbox Group Styling */
.radio-group { 
    display: flex; 
    gap: 20px; 
    margin-top: 10px; 
    flex-wrap: wrap; 
    padding: 10px 0;
}
.radio-group label {
    display: flex;
    align-items: center;
    cursor: pointer;
    margin-top: 0;
    font-weight: 400;
}
.radio-group input[type="radio"] {
    margin-right: 8px;
    /* Custom styling for radio buttons */
    appearance: none;
    -webkit-appearance: none;
    width: 16px;
    height: 16px;
    border: 2px solid #fff;
    border-radius: 50%;
    background: transparent;
    position: relative;
    top: 0;
}
.radio-group input[type="radio"]:checked:before {
    content: '';
    display: block;
    width: 8px;
    height: 8px;
    background: #22d3ee;
    border-radius: 50%;
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
}

/* Button Styling */
button {
    margin-top: 30px; 
    width: 100%; 
    padding: 15px; /* Increased padding */
    border: none; 
    border-radius: 10px;
    background: #22d3ee; /* Cyan color */
    color: #0f172a; 
    font-weight: 800; /* Extra bold */
    cursor: pointer; 
    font-size: 1.1em;
    box-shadow: 0 5px 15px rgba(34, 211, 238, 0.4); /* Cyan shadow */
}
button:hover { 
    background: #0ea5e9; /* Blue color on hover */
    box-shadow: 0 5px 15px rgba(14, 165, 233, 0.6); /* Blue shadow on hover */
}

/* Message Styling */
.msg { 
    padding: 15px; 
    border-radius: 10px; 
    margin-bottom: 20px; 
    font-size: 15px;
    font-weight: 600;
}
.error { 
    background: rgba(239, 68, 68, 0.9); /* Solid background for errors */
    color: #fff;
    border: 1px solid #fecaca;
}
.success { 
    background: rgba(34, 197, 94, 0.9); /* Solid background for success */
    color: #fff;
    border: 1px solid #bbf7d0;
}

/* Link Styling */
.link { 
    text-align: center; 
    margin-top: 25px; 
}
.link a { 
    color: #22d3ee; 
    text-decoration: none; 
    font-weight: 600;
}
.link a:hover { 
    text-decoration: underline; 
    color: #0ea5e9; 
}

/* Responsive Adjustments */
@media (max-width: 600px) {
    body { padding: 10px; align-items: flex-start; } /* Allow scrolling on small screens */
    form { padding: 20px; max-width: 100%; }
}
</style>
</head>
<body>
<form method="POST">
    <h2>Register Account</h2>

    <?php if ($errors): ?>
        <div class="msg error">
            <p>🛑 Registration Failed:</p>
            <?= implode("<br>", array_map('e', $errors)) ?>
        </div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div class="msg success">✅ Registration successful! Please proceed to login.</div>
    <?php endif; ?>

    <input type="hidden" name="csrf" value="<?= e($csrf) ?>">

    <label>Full Name</label>
    <input type="text" name="name" placeholder="E.g., Ahmad Bin Kassim" required value="<?= e($_POST['name'] ?? '') ?>">

    <label>Email</label>
    <input type="email" name="email" placeholder="E.g., user@example.com" required value="<?= e($_POST['email'] ?? '') ?>">

    <label>Password</label>
    <input type="password" name="password" placeholder="Min 6 characters" required>

    <label>Confirm Password</label>
    <input type="password" name="password_confirm" placeholder="Re-enter password" required>

    <label>IC Number</label>
    <input type="text" name="ic_number" placeholder="E.g., 901231145678" required value="<?= e($_POST['ic_number'] ?? '') ?>">

    <label>Phone Number</label>
    <input type="text" name="phone_number" placeholder="E.g., 0123456789" required value="<?= e($_POST['phone_number'] ?? '') ?>">

    <label>Gender</label>
    <div class="radio-group">
        <label><input type="radio" name="gender" value="male" <?= ($_POST['gender'] ?? '') === 'male' ? 'checked' : '' ?> required> Male</label>
        <label><input type="radio" name="gender" value="female" <?= ($_POST['gender'] ?? '') === 'female' ? 'checked' : '' ?>> Female</label>
        <label><input type="radio" name="gender" value="other" <?= ($_POST['gender'] ?? '') === 'other' ? 'checked' : '' ?>> Other</label>
    </div>

    <label>Register As</label>
    <div class="radio-group">
        <label><input type="radio" name="role" value="tenant" <?= ($_POST['role'] ?? '') === 'tenant' ? 'checked' : '' ?> required> Tenant</label>
        <label><input type="radio" name="role" value="owner" <?= ($_POST['role'] ?? '') === 'owner' ? 'checked' : '' ?>> Owner</label>
    </div>

    <button type="submit">Register</button>

    <div class="link">
        <a href="login.php">Already have an account? Login here</a>
    </div>
</form>
</body>
</html>