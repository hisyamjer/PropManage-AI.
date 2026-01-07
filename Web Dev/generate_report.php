<?php
session_start();

// Database Connection
$host = "127.0.0.1";
$user = "root";
$pass = "Hisyam.2005";
$dbname = "users";
$conn = new mysqli($host, $user, $pass, $dbname);

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'owner') {
    die("Unauthorized access.");
}

$ownerId = $_SESSION['user_id'];
$userName = $_SESSION['user_name'] ?? 'Owner';

// Fetch Data
$sql = "SELECT p.address, p.price, p.idproperty, u.name AS tenant_name, 
               pay.status AS payment_status, pay.paymentDate
        FROM property p 
        JOIN agreements a ON p.idproperty = a.idproperty
        JOIN tenants t ON a.tenant_id = t.id         
        JOIN user u ON t.user_id = u.id              
        LEFT JOIN payment pay ON t.id = pay.tenant_id 
        WHERE p.owner_id = ?
        ORDER BY pay.paymentDate DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $ownerId);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Property Report - <?= htmlspecialchars($userName) ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            .no-print { display: none; }
            body { background: white; }
            @page { margin: 2cm; }
        }
    </style>
</head>
<body class="bg-gray-100 p-10">

    <div class="max-w-4xl mx-auto bg-white p-8 shadow-lg rounded-lg" id="reportContent">
        <div class="flex justify-between items-center border-b-2 border-blue-600 pb-4 mb-6">
            <div>
                <h1 class="text-2xl font-bold text-blue-800">PropManage Report</h1>
                <p class="text-gray-500 text-sm">Owner: <?= htmlspecialchars($userName) ?></p>
            </div>
            <div class="text-right">
                <p class="text-sm font-bold"><?= date('d F Y') ?></p>
                <button onclick="window.print()" class="no-print bg-blue-600 text-white px-4 py-2 rounded text-sm mt-2 hover:bg-blue-700">
                    Save as PDF
                </button>
            </div>
        </div>

        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50">
                    <th class="p-3 border-b text-xs uppercase text-gray-400">Property</th>
                    <th class="p-3 border-b text-xs uppercase text-gray-400">Tenant</th>
                    <th class="p-3 border-b text-xs uppercase text-gray-400">Status</th>
                    <th class="p-3 border-b text-xs uppercase text-gray-400 text-right">Amount</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $total = 0;
                while($row = $result->fetch_assoc()): 
                    if(strtolower($row['payment_status'] ?? '') === 'paid') $total += $row['price'];
                ?>
                <tr>
                    <td class="p-3 border-b text-sm"><?= htmlspecialchars($row['address']) ?></td>
                    <td class="p-3 border-b text-sm"><?= htmlspecialchars($row['tenant_name']) ?></td>
                    <td class="p-3 border-b text-sm font-bold <?= strtolower($row['payment_status'] ?? '') === 'paid' ? 'text-green-600' : 'text-red-600' ?>">
                        <?= ucfirst($row['payment_status'] ?? 'Pending') ?>
                    </td>
                    <td class="p-3 border-b text-sm text-right">RM <?= number_format($row['price'], 2) ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
            <tfoot>
                <tr class="bg-blue-50">
                    <td colspan="3" class="p-3 font-bold text-right">Total Collected:</td>
                    <td class="p-3 font-bold text-right text-blue-700">RM <?= number_format($total, 2) ?></td>
                </tr>
            </tfoot>
        </table>

        <table class="w-full text-left border-collapse">
            <thead>
               <tr class="bg-gray-50">
                <th colspan="2" class="p-3 border-b text-xs uppercase text-gray-400 text-center">
                    List of Property did not rent
                </th>
                </tr>

                <tr class="bg-gray-50">
                    <th class="p-3 border-b text-xs uppercase text-gray-400 text-left">Address</th>
                    <th class="p-3 border-b text-xs uppercase text-gray-400 text-left">State</th>
                </tr>
                
            </thead>
            <tbody>
                <?php 
                $result->data_seek(0);
                while($row = $result->fetch_assoc()): 
   
                        if (empty($row['tenant_name'])): 
                    ?>
                        <tr>
                            <td class="p-3 border-b text-sm">
                                <?= htmlspecialchars($row['address']) ?>
                            </td>
                            <td class="p-3 border-b text-sm text-center">
                                <?= htmlspecialchars($row['state']) ?>
                            </td>
                        </tr>
                    <?php 
                        endif; 
                    endwhile; 
                    ?>
        </table>
        
        <p class="mt-10 text-center text-xs text-gray-400">Computer generated report via PropManage Rental System.</p>
    </div>

    <script>
        // Automatically trigger print dialog
        window.onload = function() {
            // Optional: window.print();
        };
    </script>
</body>
</html>