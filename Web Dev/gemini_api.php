<?php
header('Content-Type: application/json');

// --- DATABASE CONNECTION ---
$host = "127.0.0.1";
$user = "root";
$pass = "Hisyam.2005";
$dbname = "users";
$conn = new mysqli($host, $user, $pass, $dbname);

// --- GET INPUT DATA ---
$input = json_decode(file_get_contents('php://input'), true);
$userMessage = $input['message'] ?? '';
$userName = $input['userName'] ?? 'Tenant';
$userId = $input['userId'] ?? 0;

// --- FETCH DATA FOR AI CONTEXT ---
$balanceData = "No specific balance found.";
$tLookup = $conn->prepare("SELECT id FROM tenants WHERE user_id = ?");
$tLookup->bind_param("i", $userId);
$tLookup->execute();
$tenantId = $tLookup->get_result()->fetch_assoc()['id'] ?? null;

if ($tenantId) {
    $stmt = $conn->prepare("SELECT p.price, pay.status FROM agreements a JOIN property p ON a.idproperty = p.idproperty LEFT JOIN payment pay ON a.tenant_id = pay.tenant_id WHERE a.tenant_id = ? ORDER BY pay.paymentDate DESC LIMIT 1");
    $stmt->bind_param("i", $tenantId);
    $stmt->execute();
    $res = $stmt->get_result()->fetch_assoc();
    if ($res) {
        $balanceData = "Monthly Rent: RM" . $res['price'] . ". Current Status: " . $res['status'];
    }
}

// --- GEMINI API CALL (UPDATED 2026) ---
// 1. Updated URL for 2026 Stable Flash
// --- UPDATED GEMINI API CALL (JAN 2026) ---
$apiKey = "AIzaSyCtBr-tJZx3yJXmTRCqOYG0tAJELtxypFQ"; 

// 1. Use the exact model name from your list
// 1. Use the "Lite" model which has the best FREE quota in 2026
$modelName = "gemini-2.0-flash-lite"; 

// 2. Use the v1beta URL (most stable for free accounts)
$url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash-lite:generateContent?key=" . $apiKey;

// 3. Keep your payload the same
$payload = [
    "contents" => [
        ["parts" => [["text" => "Context: $balanceData. User: $userMessage"]]]
    ]
];

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Required for Laragon
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

$response = curl_exec($ch);
curl_close($ch);

echo $response;