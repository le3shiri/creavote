<?php
session_start();
if (empty($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
function render_main() {
?>
<div class="fixed inset-0 bg-black bg-opacity-30 flex items-center justify-center z-50">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-xl p-8 relative">
        <!-- Close Button -->
        <button onclick="window.location.href='profile-settings.php'" class="absolute top-5 right-5 text-gray-400 hover:text-gray-700 text-2xl font-bold">&times;</button>
        <h2 class="text-2xl font-bold mb-6">Add Your Card</h2>
        <form action="../controllers/payment/add-card.php" method="POST">
            <div class="mb-4">
                <label class="block text-gray-700 mb-1">Cardholder Name</label>
                <input type="text" name="cardholder" required class="w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-purple-500">
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 mb-1">Card Number</label>
                <input type="text" name="card_number" required maxlength="19" class="w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-purple-500" placeholder="1234 5678 9012 3456">
            </div>
            <div class="flex gap-4 mb-4">
                <div class="flex-1">
                    <label class="block text-gray-700 mb-1">Expiry Date</label>
                    <input type="text" name="expiry" required maxlength="5" class="w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-purple-500" placeholder="MM/YY">
                </div>
                <div class="flex-1">
                    <label class="block text-gray-700 mb-1">CVC</label>
                    <input type="text" name="cvc" required maxlength="4" class="w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-purple-500" placeholder="123">
                </div>
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 mb-1">Country</label>
                <input type="text" name="country" required class="w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-purple-500">
            </div>
            <div class="mb-6">
                <label class="block text-gray-700 mb-1">ZIP Code</label>
                <input type="text" name="zip" required class="w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-purple-500">
            </div>
            <button type="submit" class="w-full bg-blue-500 hover:bg-blue-600 text-white px-6 py-3 rounded font-semibold">Add Card</button>
        </form>
    </div>
</div>
<?php
}
include 'base.php';
