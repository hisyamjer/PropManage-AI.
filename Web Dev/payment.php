<?php
// 1. Database Configuration
$host = "127.0.0.1";
$user = "root";
$pass = "Hisyam.2005";
$dbname = "users";

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("❌ Database connection failed: " . htmlspecialchars($conn->connect_error));
}

// 2. Authentication and Session Management
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'tenant') {
    header("Location: login.php");
    exit();
}

$tenantUserId = $_SESSION['user_id'];
$userName = $_SESSION['user_name'] ?? 'Tenant User';
$logoutUrl = 'login.php?action=logout';

// 3. Optimized SQL Query
// We join property to get the address and tenants to link the session user_id to the agreement
$sql = "SELECT a.rentAmount AS price, p.address 
        FROM agreements a
        JOIN tenants t ON a.tenant_id = t.id
        JOIN property p ON a.idproperty = p.idproperty
        WHERE t.user_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $tenantUserId);
$stmt->execute();
$result = $stmt->get_result();

// Fetch the data once here to avoid "Undefined variable" errors in the HTML
$row = $result->fetch_assoc(); 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment - PropManage</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <script type="module" src="https://unpkg.com/lucide@latest"></script>

    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f8fafc; }
        .navbar-container { background-color: #004AAD; }
        .bank-card { transition: all 0.2s ease; border: 2px solid #f1f5f9; }
        .selected-bank { 
            border-color: #004AAD !important; 
            background-color: #f0f7ff !important;
            transform: scale(1.02);
        }
    </style>
</head>
<body class="bg-slate-50 pb-12">

<div class="navbar-container flex items-center justify-between px-6 py-2 text-white shadow-lg m-4 rounded-xl">
    <nav class="flex items-center space-x-6">
        <span class="text-xl">🏠</span>
        <a href="tenant.php" class="hover:text-blue-200 font-semibold transition">Dashboard</a>
        <a href="payment.php" class="border-b-2 border-white pb-1 font-semibold">Payment</a>
        <a href="agreement.php" class="hover:text-blue-200 font-semibold transition">Lease Agreement</a>
        <a href="search.php" class="hover:text-blue-200 font-semibold transition">Available Property</a>
        <a href="tenant_profile.php" class="hover:text-blue-200 font-semibold transition">My Profile</a>
    </nav>
    
    <a href="<?php echo $logoutUrl; ?>" class="flex items-center space-x-2 bg-red-600 px-4 py-2 rounded-lg hover:bg-red-700 transition shadow-md">
        <i data-lucide="log-out" class="w-4 h-4"></i>
        <span class="text-sm font-bold">Logout</span>
    </a>
</div>

<main class="max-w-4xl mx-auto mt-10 p-6">
    <?php if ($row): ?>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <div class="md:col-span-1">
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
                <h2 class="text-lg font-bold text-slate-800 mb-4 text-center">Payment Summary</h2>
                <div class="space-y-3 border-b pb-4 text-sm">
                    <div class="flex justify-between text-slate-500">
                        <span>Invoice #</span>
                        <span class="font-mono font-bold text-slate-700">INV-<?= rand(10000, 99999) ?></span>
                    </div>
                    <div class="flex justify-between text-slate-500">
                        <span>Property</span>
                        <span class="font-semibold text-right text-slate-700"><?= htmlspecialchars($row['address']) ?></span>
                    </div>
                </div>
                <div class="mt-4 text-center">
                    <span class="text-slate-500 text-sm">Total Amount Due</span>
                    <div class="text-3xl font-extrabold text-blue-700">
                        RM <?= number_format($row['price'], 2) ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="md:col-span-2">
            <div class="bg-white p-8 rounded-2xl shadow-sm border border-slate-200">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-bold text-slate-800">Select Your Bank</h2>
                    <img src="https://upload.wikimedia.org/wikipedia/commons/a/a2/FPX_logo.png" alt="FPX" class="h-6">
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-3 gap-4 mb-8">
                    <div class="bank-card rounded-xl p-4 flex flex-col items-center justify-center cursor-pointer" onclick="selectBank(this, 'Maybank')">
                        <div class="w-12 h-12 bg-yellow-400 rounded-full mb-2 flex items-center justify-center font-bold text-xs uppercase text-slate-800">MBB</div>
                        <span class="text-xs font-bold text-slate-700">Maybank2u</span>
                    </div>
                    <div class="bank-card rounded-xl p-4 flex flex-col items-center justify-center cursor-pointer" onclick="selectBank(this, 'CIMB')">
                        <div class="w-12 h-12 bg-red-600 rounded-full mb-2 flex items-center justify-center font-bold text-xs text-white uppercase">CIMB</div>
                        <span class="text-xs font-bold text-slate-700">CIMB Clicks</span>
                    </div>
                    <div class="bank-card rounded-xl p-4 flex flex-col items-center justify-center cursor-pointer" onclick="selectBank(this, 'Public Bank')">
                        <div class="w-12 h-12 bg-red-700 rounded-full mb-2 flex items-center justify-center font-bold text-xs text-white uppercase">PBB</div>
                        <span class="text-xs font-bold text-slate-700">Public Bank</span>
                    </div>
                    <div class="bank-card rounded-xl p-4 flex flex-col items-center justify-center cursor-pointer" onclick="selectBank(this, 'RHB')">
                        <div class="w-12 h-12 bg-blue-600 rounded-full mb-2 flex items-center justify-center font-bold text-xs text-white uppercase">RHB</div>
                        <span class="text-xs font-bold text-slate-700">RHB Now</span>
                    </div>
                    <div class="bank-card rounded-xl p-4 flex flex-col items-center justify-center cursor-pointer" onclick="selectBank(this, 'Hong Leong')">
                        <div class="w-12 h-12 bg-blue-800 rounded-full mb-2 flex items-center justify-center font-bold text-xs text-white uppercase">HLB</div>
                        <span class="text-xs font-bold text-slate-700">HLB Connect</span>
                    </div>
                    <div class="bank-card rounded-xl p-4 flex flex-col items-center justify-center cursor-pointer" onclick="selectBank(this, 'Bank Islam')">
                        <div class="w-12 h-12 bg-emerald-700 rounded-full mb-2 flex items-center justify-center font-bold text-xs text-white uppercase">BIMB</div>
                        <span class="text-xs font-bold text-slate-700">Bank Islam</span>
                    </div>
                </div>

                <button type="button" id="payButton" onclick="proceedToPay()" class="w-full bg-blue-700 text-white py-4 rounded-xl font-bold text-lg hover:bg-blue-800 transition shadow-lg flex items-center justify-center space-x-2">
                    <span>Proceed to Pay</span>
                    <i data-lucide="arrow-right" class="w-5 h-5"></i>
                </button>
                
                <p class="text-center text-[10px] text-slate-400 mt-4 uppercase tracking-widest">Secure Payment Powered by FPX</p>
            </div>
        </div>
    </div>
    <?php else: ?>
        <div class="bg-white p-12 rounded-3xl text-center shadow-md max-w-lg mx-auto">
            <div class="w-16 h-16 bg-orange-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i data-lucide="file-warning" class="w-8 h-8 text-orange-600"></i>
            </div>
            <h2 class="text-2xl font-bold text-slate-800">No Payment Due</h2>
            <p class="text-slate-500 mt-2">We couldn't find an active lease agreement for your account. Please contact your property manager.</p>
            <a href="tenant.php" class="inline-block mt-6 bg-blue-600 text-white px-6 py-2 rounded-lg font-bold hover:bg-blue-700 transition">Return to Dashboard</a>
        </div>
    <?php endif; ?>
</main>

<div id="loadingOverlay" class="fixed inset-0 bg-slate-900/80 backdrop-blur-sm z-[100] hidden flex flex-col items-center justify-center text-white">
    <div class="w-16 h-16 border-4 border-blue-400 border-t-transparent rounded-full animate-spin mb-4"></div>
    <h2 class="text-xl font-bold">Connecting to Bank...</h2>
    <p class="text-slate-300 text-sm mt-2 font-medium">Please do not refresh the page.</p>
</div>

<script>
    // Initialize Lucide Icons
    document.addEventListener('DOMContentLoaded', () => {
        lucide.createIcons();
    });

    let selectedBankName = "";

    function selectBank(element, bankName) {
        document.querySelectorAll('.bank-card').forEach(card => {
            card.classList.remove('selected-bank');
        });
        element.classList.add('selected-bank');
        selectedBankName = bankName;
    }

    function proceedToPay() {
        if (!selectedBankName) {
            alert("Please select a bank first!");
            return;
        }

        const overlay = document.getElementById('loadingOverlay');
        overlay.classList.remove('hidden');

        const bankUrls = {
            'Maybank': 'https://www.maybank2u.com.my/home/m2u/common/login.do',
            'CIMB': 'https://www.cimbclicks.com.my/',
            'Public Bank': 'https://www.pbebank.com/',
            'RHB': 'https://onlinebanking.rhbgroup.com/main',
            'Hong Leong': 'https://www.hlb.com.my/hlb-connect',
            'Bank Islam': 'https://www.bankislam.biz/'
        };

        setTimeout(() => {
            if (bankUrls[selectedBankName]) {
                window.location.href = bankUrls[selectedBankName];
            } else {
                alert("Error: URL not found for " + selectedBankName);
                overlay.classList.add('hidden');
            }
        }, 2000);
    }
</script>

</body>
</html>