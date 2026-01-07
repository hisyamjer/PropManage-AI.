<?php
// =========================================
// User Login and Role-Based Redirection (MySQLi)
// =========================================

$host = "127.0.0.1";
$user = "root";
$pass = "Hisyam.2005";
$dbname = "users";

// --- Configuration ---
// These must match the redirection targets you desire
$TENANT_PAGE = 'tenant.php'; 
$OWNER_PAGE = 'proowner.php';

// Connect to MySQL
$conn = new mysqli($host, $user, $pass, $dbname);

// Check connection
if ($conn->connect_error) {
    die("❌ Database connection failed: " . htmlspecialchars($conn->connect_error));
}

session_start();



// --- LOGOUT HANDLING (KEPT FOR MANUAL LOGOUT) ---
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    session_unset(); // 1. Clears all session variables (user_id, role, name, etc.)
    session_destroy(); // 2. Destroys the entire session and cookie
    // Redirect to the login page to show the form clearly
    header("Location: login.php");
    exit();
}
// --- END LOGOUT HANDLING ---


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
        $email = trim($_POST['email']);
        $password = $_POST['password'];

        // Validation
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Invalid email address format.";
        if (empty($password)) $errors[] = "Password is required.";

        if (empty($errors)) {
            // 1. Fetch user data (id, password, name) from the 'user' table (consistent with your provided SQL)
            $stmtUser = $conn->prepare("SELECT id, password, name FROM user WHERE email=?");
            $stmtUser->bind_param("s", $email);
            $stmtUser->execute();
            $stmtUser->store_result();
            
            if ($stmtUser->num_rows === 1) {
                $stmtUser->bind_result($user_id, $hashed_password, $name);
                $stmtUser->fetch();

                // 2. Verify password
                if (password_verify($password, $hashed_password)) {
                    $role = 'unknown';

                    // 3. Determine Role: Check the 'tenants' table
                    $stmtTenant = $conn->prepare("SELECT user_id FROM tenants WHERE user_id = ?");
                    $stmtTenant->bind_param("i", $user_id);
                    $stmtTenant->execute();
                    if ($stmtTenant->get_result()->num_rows > 0) {
                        $role = 'tenant';
                    }
                    $stmtTenant->close();

                    // 4. Determine Role: Check the 'owners' table (only if not a tenant)
                    if ($role === 'unknown') {
                        $stmtOwner = $conn->prepare("SELECT user_id FROM owners WHERE user_id = ?");
                        $stmtOwner->bind_param("i", $user_id);
                        $stmtOwner->execute();
                        if ($stmtOwner->get_result()->num_rows > 0) {
                            $role = 'owner';
                        }
                        $stmtOwner->close();
                    }

                    // 5. Success: Set Session, Regenerate ID, and Redirect
                    if ($role !== 'unknown') {
                        // The user's information is still saved to the session so the dashboard pages 
                        // can know who is logged in, but the login.php page itself no longer checks for it 
                        // and redirects automatically.
                        $_SESSION['user_id'] = $user_id;
                        $_SESSION['user_name'] = $name;
                        $_SESSION['role'] = $role; // Set the crucial role session variable
                        session_regenerate_id(true); 

                        // Redirect based on role
                        if ($role === 'tenant') {
                            header("Location: " . $TENANT_PAGE); 
                        } else { // 'owner'
                            header("Location: " . $OWNER_PAGE);
                        }
                        exit; // Stop execution after redirection

                    } else {
                        // User exists but is not in the tenants or owners table (a data anomaly)
                        $errors[] = "User profile role is missing. Please contact support.";
                    }

                } else {
                    $errors[] = "Invalid email or password.";
                }
            } else {
                $errors[] = "Invalid email or password."; // CORRECTED: Removed the '->' operator here
            }
            $stmtUser->close();
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
<title>User Login</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
<style>
/* Same CSS as your registration page for a consistent look */
* { box-sizing: border-box; }
body {
    font-family: Poppins, sans-serif;
    background: linear-gradient(135deg, #1e40af, #3b82f6); 
    color: #fff;
    margin: 0;
    padding: 20px;
    min-height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center; /* Center the form vertically */
    overflow-y: auto;
}
form {
    background: rgba(255,255,255,0.1);
    padding: 20px;
    border-radius: 15px;
    width: 100%;
    max-width: 400px; /* Slightly narrower form for login */
    backdrop-filter: blur(10px);
    box-shadow: 0 10px 30px rgba(0,0,0,0.4);
    margin: auto;
}
h2 { text-align: center; margin-bottom: 20px; font-size: 1.5em; }
label { font-size: 14px; margin-top: 10px; display: block; }
input, select {
    width: 100%; padding: 10px; border: none; border-radius: 8px;
    margin-top: 6px; background: rgba(255,255,255,0.2); color: #fff; font-size: 16px;
}
input::placeholder { color: #ddd; }
button {
    margin-top: 20px; width: 100%; padding: 12px; border: none; border-radius: 8px;
    background: #22d3ee; color: #0f172a; font-weight: 600; cursor: pointer; font-size: 16px;
    transition: background 0.3s ease;
}
button:hover { background: #0ea5e9; }
.msg { padding: 10px; border-radius: 8px; margin-bottom: 10px; font-size: 14px; }
.error { background: rgba(239,68,68,0.2); color: #fecaca; }
.success { background: rgba(34,197,94,0.2); color: #bbf7d0; }
.link { text-align: center; margin-top: 15px; }
.link a { color: #22d3ee; text-decoration: none; transition: color 0.3s ease; }
.link a:hover { text-decoration: underline; color: #0ea5e9; }
/* CSS for the logout button */
.logout-btn {
    text-align: center;
    margin-top: 10px;
}
.logout-btn a {
    background: #f87171;
    color: white;
    padding: 8px 15px;
    border-radius: 8px;
    font-size: 14px;
    text-decoration: none;
    display: inline-block;
    transition: background 0.3s ease;
}
@media (max-width: 600px) {
    body { padding: 10px; }
    form { padding: 15px; }
}
</style>
</head>
<body>
<form method="POST">
    <h2>Login to Your Account</h2>

    <?php if ($errors): ?>
        <div class="msg error">🛑 <?= implode("<br>", array_map('e', $errors)) ?></div>
    <?php endif; ?>

    <!-- Display user info and instructions when logged in -->
    <?php if (isset($_SESSION['user_id'])): ?>
        <div class="msg success">
            ✅ You are currently logged in as **<?= e($_SESSION['user_name'] ?? 'User') ?> (<?= e($_SESSION['role'] ?? 'Unknown') ?>)**. 
            <br>Click <a href="<?= e($_SESSION['role'] === 'tenant' ? $TENANT_PAGE : $OWNER_PAGE) ?>" style="color:#0ea5e9; text-decoration:underline;">here</a> to go to your dashboard, or log out below to use the form.
        </div>
    <?php endif; ?>

    <input type="hidden" name="csrf" value="<?= e($csrf) ?>">

    <label>Email</label>
    <!-- Disable inputs if already logged in (user must manually log out to switch accounts) -->
    <input type="email" name="email" placeholder="Enter your email" required value="<?= e($_POST['email'] ?? '') ?>" <?= isset($_SESSION['user_id']) ? 'disabled' : '' ?>>

    <label>Password</label>
    <input type="password" name="password" placeholder="Enter password" required <?= isset($_SESSION['user_id']) ? 'disabled' : '' ?>>

    <button type="submit" <?= isset($_SESSION['user_id']) ? 'disabled' : '' ?>>Log In</button>

    <?php if (isset($_SESSION['user_id'])): ?>
    <!-- Show Logout button when logged in -->
    <div class="logout-btn">
        <a href="login.php?action=logout">Log Out</a>
    </div>
    <?php else: ?>
    <div class="link">
        <a href="register.php">Don't have an account? Register here</a>
    </div>
    <?php endif; ?>
</form>
</body>
</html>