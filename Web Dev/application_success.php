<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$propertyId = $_GET['property'] ?? 'N/A';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Application Submitted - PropManage</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script type="module" src="https://unpkg.com/lucide@latest"></script>
</head>
<body class="bg-gray-50 flex items-center justify-center min-h-screen p-4">

    <div class="max-w-md w-full bg-white rounded-3xl shadow-2xl p-8 text-center border border-gray-100">
        <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6">
            <i data-lucide="check-circle" class="w-12 h-12 text-green-600"></i>
        </div>

        <h1 class="text-3xl font-extrabold text-gray-900 mb-2">Application Sent!</h1>
        <p class="text-gray-500 mb-8">
            Your request for Property ID <span class="font-bold text-blue-600">#<?php echo htmlspecialchars($propertyId); ?></span> has been submitted successfully.
        </p>

        <div class="bg-blue-50 rounded-2xl p-4 mb-8 text-left border border-blue-100">
            <h3 class="text-blue-800 font-bold mb-2 flex items-center">
                <i data-lucide="info" class="w-4 h-4 mr-2"></i> What happens next?
            </h3>
            <ul class="text-sm text-blue-700 space-y-2">
                <li>• The landlord will review your request.</li>
                <li>• You will receive an update on your dashboard.</li>
                <li>• Ensure your contact details are up to date.</li>
            </ul>
        </div>

        <div class="space-y-3">
            <a href="search.php" 
               class="block w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-4 rounded-xl transition duration-200">
                Go to My Dashboard
            </a>
            <a href="search.php" 
               class="block w-full bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold py-3 px-4 rounded-xl transition duration-200">
                Browse More Properties
            </a>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            if (typeof lucide !== 'undefined') { lucide.createIcons(); }
        });
    </script>
</body>
</html>