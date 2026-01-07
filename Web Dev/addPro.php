<?php
$host = "127.0.0.1";
$user = "root";
$pass = "Hisyam.2005";
$dbname = "users";

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("❌ Connection failed: " . $conn->connect_error);
}
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'owner') {
    header("Location: login.php");
    exit();
}

$ownerId = $_SESSION['user_id']; // This matches your proowner.php dashboard
$userName = $_SESSION['user_name'] ?? 'Owner';
$logoutUrl = 'login.php?action=logout';

$message = '';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $type = $_POST['type'] ?? '';
    $address = trim($_POST['address'] ?? '');
    $price = filter_input(INPUT_POST, 'price', FILTER_VALIDATE_FLOAT); 
    $state = trim($_POST['state'] ?? '');
    $poscode = trim($_POST['poscode'] ?? ''); 

    // Logic Fix: Check for required fields without needing icNumber
    if (!empty($type) && !empty($address) && $price && !empty($_FILES['property_image']['name'])) {
        
        $targetDir = "uploads/";
        if (!is_dir($targetDir)) mkdir($targetDir, 0777, true);
        $fileExt = strtolower(pathinfo($_FILES["property_image"]["name"], PATHINFO_EXTENSION));
        $newFileName = "prop_" . uniqid() . "." . $fileExt;
        $targetFilePath = $targetDir . $newFileName;

        if (in_array($fileExt, ['jpg', 'jpeg', 'png', 'webp'])) {
            if (move_uploaded_file($_FILES["property_image"]["tmp_name"], $targetFilePath)) {
                
                // INSERT using the Session ownerId and setting is_deleted to 0
                $stmtProp = $conn->prepare("INSERT INTO property (owner_id, address, price, type, state, poscode, property_image, is_deleted) VALUES (?, ?, ?, ?, ?, ?, ?, 0)");
                $stmtProp->bind_param("issssss", $ownerId, $address, $price, $type, $state, $poscode, $targetFilePath);
                
                if ($stmtProp->execute()) {
                    $message = "✅ Property added successfully!";
                } else { 
                    $message = "❌ Database Error: " . $conn->error; 
                }
            } else { $message = "❌ Upload failed."; }
        } else { $message = "❌ Invalid format."; }
    } else { $message = "⚠️ Please fill all fields and select an image."; }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Property - PropManage</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f8fafc; }
        .nav-link { position: relative; transition: all 0.3s; }
        .nav-link::after { content: ''; position: absolute; bottom: -2px; left: 0; width: 0; height: 2px; background: white; transition: width 0.3s; opacity: 0; }
        .nav-link:hover::after { width: 100%; opacity: 1; }
    </style>
</head>
<body class="pb-12">

<nav class="bg-[#004AAD] text-white px-6 py-3 sticky top-0 z-50 shadow-md">
    <div class="max-w-7xl mx-auto flex justify-between items-center">
        <div class="flex items-center space-x-10">
            <span class="text-2xl font-bold tracking-tighter">PropManage</span>
            <div class="hidden md:flex space-x-6">
                <a href="proowner.php" class="nav-link text-sm font-semibold opacity-90">Dashboard</a>
                <a href="ownerProfile.php" class="nav-link text-sm font-semibold opacity-90">My Profile</a>
                <a href="financial.php" class="nav-link text-sm font-semibold opacity-90">Financial</a>
                <a href="ownerAgreement.php" class="nav-link text-sm font-semibold opacity-90">Agreements</a>
                <a href="addPro.php" class="text-sm font-bold border-b-2 border-white pb-1">Add Property</a>
            </div>
        </div>
        <div class="flex items-center space-x-4">
            <div class="hidden sm:block text-right mr-2">
                <p class="text-[10px] uppercase font-bold opacity-60">Listing Manager</p>
                <p class="text-sm font-bold"><?= htmlspecialchars($userName) ?></p>
            </div>
            <a href="<?= $logoutUrl ?>" class="bg-white/10 p-2 rounded-xl hover:bg-red-500 transition duration-300">
                <i data-lucide="log-out" class="w-5 h-5"></i>
            </a>
        </div>
    </div>
</nav>

<div class="max-w-2xl mx-auto mt-12 px-4">
    <div class="bg-white rounded-[2.5rem] shadow-2xl overflow-hidden border border-slate-100">
        <div class="bg-slate-50 p-8 border-b border-slate-100">
            <h1 class="text-3xl font-black text-slate-800">List New <span class="text-blue-600">Property</span></h1>
            <p class="text-slate-500 font-medium mt-1">Fill in the details below to reach thousands of tenants.</p>
        </div>

        <div class="p-8">
            <?php if ($message): ?>
                <div class="mb-8 p-4 rounded-2xl text-center font-bold animate-pulse <?= (strpos($message, '✅') !== false) ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'; ?>">
                    <?= $message; ?>
                </div>
            <?php endif; ?>

            <form action="addPro.php" method="POST" enctype="multipart/form-data" class="space-y-6" id="propertyForm">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-2">Property Category</label>
                        <select name="type" class="w-full p-4 bg-slate-50 border border-slate-200 rounded-2xl focus:border-blue-500 outline-none transition" required>
                            <option value="apartment">Apartment / Condo</option>
                            <option value="house">Landed House</option>
                            <option value="room">Single Room</option>
                        </select>
                    </div>
                    <div>
                         <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-2">Price (RM/mo)</label>
                         <input type="number" name="price" step="0.01" placeholder="1200.00" class="w-full p-4 bg-slate-50 border border-slate-200 rounded-2xl outline-none focus:border-blue-500 font-bold text-blue-600" required>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-2">Full Property Address</label>
                    <textarea name="address" rows="3" placeholder="Enter street, unit number, and building name..." class="w-full p-4 bg-slate-50 border border-slate-200 rounded-2xl outline-none focus:border-blue-500" required></textarea>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-2">Postcode</label>
                        <input type="text" name="poscode" placeholder="54000" class="w-full p-4 bg-slate-50 border border-slate-200 rounded-2xl outline-none focus:border-blue-500" required>
                    </div>
                    <div>
                        <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-2">State</label>
                        <input type="text" name="state" placeholder="Kuala Lumpur" class="w-full p-4 bg-slate-50 border border-slate-200 rounded-2xl outline-none focus:border-blue-500" required>
                    </div>
                </div>

                <div class="relative group">
                    <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-2">Property Media</label>
                    <div id="drop-area" class="p-8 bg-blue-50 rounded-[2rem] border-2 border-dashed border-blue-200 hover:border-blue-400 transition-all flex flex-col items-center justify-center text-center cursor-pointer">
                        <div id="preview-container" class="hidden mb-4">
                            <img id="image-preview" src="#" alt="Preview" class="w-32 h-32 object-cover rounded-2xl shadow-lg border-4 border-white">
                        </div>
                        <div id="upload-prompt">
                            <i data-lucide="upload-cloud" class="w-10 h-10 text-blue-500 mx-auto mb-2"></i>
                            <p class="text-sm font-bold text-blue-700">Click to upload photo</p>
                        </div>
                        <input type="file" name="property_image" id="file-input" accept="image/*" class="hidden" required>
                    </div>
                </div>

                <button type="submit" class="w-full bg-slate-900 text-white font-black py-5 rounded-[2rem] shadow-xl hover:bg-blue-700 transition-all duration-300 transform hover:scale-[1.02] flex items-center justify-center gap-3">
                    <i data-lucide="plus-circle" class="w-5 h-5"></i>
                    Publish Listing
                </button>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        lucide.createIcons();
        const fileInput = document.getElementById('file-input');
        const dropArea = document.getElementById('drop-area');
        const preview = document.getElementById('image-preview');
        const previewContainer = document.getElementById('preview-container');
        const uploadPrompt = document.getElementById('upload-prompt');

        dropArea.addEventListener('click', () => fileInput.click());

        fileInput.addEventListener('change', function() {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    previewContainer.classList.remove('hidden');
                    uploadPrompt.querySelector('p').innerText = file.name;
                    dropArea.classList.add('bg-blue-100', 'border-solid', 'border-blue-400');
                }
                reader.readAsDataURL(file);
            }
        });
    });
</script>
</body>
</html>