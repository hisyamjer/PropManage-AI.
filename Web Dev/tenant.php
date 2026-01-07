<?php
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

// --- SECURITY AND ACCESS CONTROL ---
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'tenant') {
    header("Location: login.php");
    exit();
}

$sessionUserId = $_SESSION['user_id'];
$userName = $_SESSION['user_name'] ?? 'Tenant User';
$logoutUrl = 'login.php?action=logout';

// 1. Fetch Tenant ID (Matching your specific DB schema)
$tLookup = $conn->prepare("SELECT id FROM tenants WHERE user_id = ?");
$tLookup->bind_param("i", $sessionUserId);
$tLookup->execute();
$tenantId = $tLookup->get_result()->fetch_assoc()['id'] ?? null;

// 2. Fetch Active Lease & Property Info
$dashboardData = null;
$progressPercent = 0;

if ($tenantId) {
    $sql = "SELECT p.address, p.price, p.type, a.startDate, a.endDate, a.status, a.idproperty,
               (SELECT pay.status FROM payment pay 
                WHERE pay.tenant_id = a.tenant_id 
                ORDER BY pay.paymentDate DESC LIMIT 1) as last_payment
        FROM agreements a
        JOIN property p ON a.idproperty = p.idproperty
        WHERE a.tenant_id = ? AND a.status = 'Pending'
        LIMIT 1";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $tenantId);
    $stmt->execute();
    $dashboardData = $stmt->get_result()->fetch_assoc();

    // 3. Calculate Lease Progress Percentage
    if ($dashboardData) {
        $start = strtotime($dashboardData['startDate']);
        $end = strtotime($dashboardData['endDate']);
        $now = time();
        
        if ($now > $start && $now < $end) {
            $total = $end - $start;
            $passed = $now - $start;
            $progressPercent = round(($passed / $total) * 100);
        } elseif ($now >= $end) {
            $progressPercent = 100;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - PropManage</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&display=swap" rel="stylesheet">
    <script type="module" src="https://unpkg.com/lucide@latest"></script>
    <script src="https://js.puter.com/v2/"></script>

    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f4f4f7; }
        .main { list-style: none; display: flex; margin: 0; padding: 0; }
        .navbar-container { background-color: #004AAD; display: flex; align-items: center; justify-content: space-between; padding: 0 20px; height: 50px; }
        .ma { color: white; text-decoration: none; padding: 20px 15px; display: block; font-weight: bold; }
    </style>
</head>
<body class="pb-12">

    <div class="navbar-container rounded-xl shadow-lg mb-8">
        <nav class="main">
            <p class="ma">🏠</p>
            <a href="tenant.php" class="ma">Dashboard</a>
            <a href="payment.php" class="ma">Payment</a>
            <a href="agreement.php" class="ma">Lease Agreement</a>
            <a href="search.php" class="ma">Available Property</a>
            <a href="tenant_profile.php" class="ma">My Profile</a>
        </nav>
        
        <a href="<?php echo $logoutUrl; ?>" 
           class="logout-link flex items-center space-x-2 bg-red-600 text-white text-sm font-semibold px-4 py-2 rounded-xl shadow-md transition duration-150 hover:bg-red-700">
            <i data-lucide="log-out" class="w-4 h-4"></i>
            <span>Logout</span>
        </a>
    </div>

    <div class="max-w-7xl mx-auto">
        <header class="mb-8">
            <h1 class="text-3xl font-extrabold text-gray-800 ml-4 ">Welcome, <?= htmlspecialchars($userName) ?>!</h1>
            <p class="text-gray-500 font-medium ml-2">Here is a summary of your current rental status.</p>
        </header>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <div class="lg:col-span-2 space-y-6">
                <?php if ($dashboardData): ?>
                    <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100">
                        <div class="flex justify-between items-center mb-6">
                            <h2 class="text-xl font-bold text-blue-700">Active Lease</h2>
                            <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider">
                                <?= $dashboardData['status'] ?>
                            </span>
                        </div>
                        
                        <div class="mb-6">
                            <h3 class="text-2xl font-black text-gray-900"><?= $dashboardData['address'] ?></h3>
                            <p class="text-gray-500 font-medium"><?= ucfirst($dashboardData['type']) ?> • ID: #<?= $dashboardData['idproperty'] ?></p>
                        </div>

                        <div class="grid grid-cols-2 gap-4 mb-8">
                            <div class="bg-gray-50 p-4 rounded-2xl border border-gray-100">
                                <p class="text-gray-400 text-xs font-bold uppercase">Monthly Rent</p>
                                <p class="text-xl font-black text-gray-900">RM <?= number_format($dashboardData['price'], 0) ?></p>
                            </div>
                            <div class="bg-gray-50 p-4 rounded-2xl border border-gray-100">
                                <p class="text-gray-400 text-xs font-bold uppercase">Lease Ends</p>
                                <p class="text-xl font-black text-gray-900"><?= date('M d, Y', strtotime($dashboardData['endDate'])) ?></p>
                            </div>
                        </div>

                        <div>
                            <div class="flex justify-between items-center mb-2 text-sm font-bold">
                                <span class="text-gray-600">Lease Completion</span>
                                <span class="text-blue-600"><?= $progressPercent ?>%</span>
                            </div>
                            <div class="w-full bg-gray-100 rounded-full h-4 overflow-hidden">
                                <div class="bg-blue-600 h-full rounded-full transition-all duration-1000 ease-out" style="width: <?= $progressPercent ?>%"></div>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="bg-white p-12 rounded-3xl text-center border-2 border-dashed border-gray-300">
                        <div class="bg-gray-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i data-lucide="home" class="text-gray-400"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-800">No active lease found</h3>
                        <p class="text-gray-500 mb-6">You are not currently renting any property.</p>
                        <a href="search.php" class="inline-flex items-center px-6 py-3 bg-blue-600 text-white font-bold rounded-xl hover:bg-blue-700 transition">
                            Browse Available Homes
                        </a>
                    </div>
                <?php endif; ?>
            </div>

            <div class="space-y-6">
                <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100">
                    <h3 class="font-bold text-gray-800 mb-4">Quick Actions</h3>
                    <div class="space-y-3">
                        <a href="payment.php" class="flex items-center p-3 bg-blue-50 text-blue-700 rounded-2xl font-bold hover:bg-blue-100 transition group">
                            <i data-lucide="credit-card" class="w-5 h-5 mr-3"></i>
                            Make Payment
                        </a>
                        <a href="agreement.php" class="flex items-center p-3 bg-purple-50 text-purple-700 rounded-2xl font-bold hover:bg-purple-100 transition group">
                            <i data-lucide="file-text" class="w-5 h-5 mr-3"></i>
                            View Contracts
                        </a>
                        <button class="w-full flex items-center p-3 bg-red-50 text-red-700 rounded-2xl font-bold hover:bg-red-100 transition group">
                            <i data-lucide="alert-circle" class="w-5 h-5 mr-3"></i>
                            Report Issue
                        </button>
                    </div>
                </div>

                <div class="bg-blue-900 p-6 rounded-3xl shadow-xl text-white relative overflow-hidden">
                    <i data-lucide="help-circle" class="absolute -right-4 -bottom-4 w-24 h-24 text-white/10"></i>
                    <button onclick="toggleChat()" class="text-sm font-bold underline hover:text-blue-200">Chat with AI Support</button>
                    <p class="text-sm text-blue-100 opacity-80 mb-4">Our support team is ready to assist you with any property issues.</p>
                    <a href="#" class="text-sm font-bold underline hover:text-blue-200">Contact Support</a>
                </div>
            </div>

        </div>
    </div>

    <script>
        // Initialize Lucide icons
        document.addEventListener('DOMContentLoaded', () => {
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }
        });
        function toggleChat() {
    const chat = document.getElementById('ai-chat');
    chat.classList.toggle('hidden');
}

async function sendMessage() {
    const input = document.getElementById('chat-input');
    const content = document.getElementById('chat-content');
    const message = input.value.trim();
    if (!message) return;

    // Show User Message
    content.innerHTML += `<div class="bg-gray-200 text-gray-800 p-3 rounded-2xl rounded-tr-none ml-8 text-right">${message}</div>`;
    input.value = '';
    content.scrollTop = content.scrollHeight;

    const typingId = 'typing-' + Date.now();
    content.innerHTML += `<div id="${typingId}" class="text-gray-400 italic text-xs px-2">Analysis...</div>`;

    try {
        // --- ADDING CONTEXT HERE ---
        // We take the PHP variables and put them inside the AI's prompt
        const rentAmount = "<?= number_format($dashboardData['price'], 2) ?>";
        const rentStatus = "<?= $dashboardData['last_payment'] ?? 'No payment record' ?>";
        const dueDate = "<?= date('M d, Y', strtotime($dashboardData['endDate'])) ?>";

        const systemPrompt = `You are a rental assistant for PropManage. 
            User Info:
            - Name: ${'<?= $userName ?>'}
            - Monthly Rent: RM ${rentAmount}
            - Payment Status: ${rentStatus}
            - Lease End Date: ${dueDate}
            
            Always be polite. If the user asks about rent or payments, use the info provided above.
            User's Question: ${message}`;

        const response = await puter.ai.chat(systemPrompt);

        document.getElementById(typingId).remove();
        const aiText = response.toString();
        content.innerHTML += `<div class="bg-blue-100 text-blue-800 p-3 rounded-2xl rounded-tl-none mr-8 shadow-sm">${aiText}</div>`;

    } catch (error) {
        console.error("Error:", error);
        if (document.getElementById(typingId)) document.getElementById(typingId).remove();
        content.innerHTML += `<div class="text-red-500 text-xs italic p-2">Error: AI could not access database info.</div>`;
    }
    content.scrollTop = content.scrollHeight;
}

    </script>
    <div id="ai-chat" class="hidden fixed bottom-6 right-6 w-80 md:w-96 bg-white rounded-3xl shadow-2xl border border-gray-200 flex flex-col overflow-hidden z-50">
    <div class="bg-blue-600 p-4 text-white flex justify-between items-center">
        <h4 class="font-bold">PropManage AI Support</h4>
        <button onclick="toggleChat()" class="text-white hover:text-gray-200">✕</button>
    </div>
    <div id="chat-content" class="h-80 overflow-y-auto p-4 space-y-4 text-sm bg-gray-50">
        <div class="bg-blue-100 text-blue-800 p-3 rounded-2xl rounded-tl-none mr-8">
            Hello <?= htmlspecialchars($userName) ?>! How can I help you today?
        </div>
    </div>
    <div class="p-4 border-t bg-white flex space-x-2">
        <input id="chat-input" type="text" placeholder="Type a message..." class="flex-1 border rounded-xl px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
        <button onclick="sendMessage()" class="bg-blue-600 text-white p-2 rounded-xl">
            <i data-lucide="send" class="w-5 h-5"></i>
        </button>
    </div>
</div>
</body>
</html>