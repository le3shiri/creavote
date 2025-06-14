<?php
session_start();
if (empty($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
function render_main() {
?>
<div class="flex-1 flex flex-col px-8 py-8 bg-gray-50 min-h-screen">
    <div class="max-w-2xl mx-auto">
        <a href="create-offer.php" class="flex items-center mb-6 text-gray-600 hover:text-purple-600">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
            Payment Confirmation
        </a>
        <form action="../controllers/payment/confirm-payment.php" method="POST" class="bg-white rounded-lg shadow p-8 mb-8">
            <h2 class="text-xl font-bold mb-6">Payment Method</h2>
            <div class="mb-5">
                <label class="flex items-center mb-3">
                    <input type="radio" name="payment_method" value="existing" class="mr-3">
                    <span class="flex items-center gap-2">
                        <img src="https://upload.wikimedia.org/wikipedia/commons/4/41/Visa_Logo.png" alt="Visa" class="w-8 h-8 object-contain">
                        <span class="font-semibold">Visa **** 4242</span>
                        <span class="text-xs text-gray-500 ml-2">Expires 12/24</span>
                    </span>
                </label>
                <label class="flex items-center">
                    <input type="radio" name="payment_method" value="new" class="mr-3" checked>
                    <span class="font-semibold">Add new card</span>
                </label>
            </div>
            <div class="space-y-4">
                <div>
                    <label class="block text-gray-700 mb-1">Cardholder Name</label>
                    <input type="text" name="cardholder_name" class="w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-purple-500">
                </div>
                <div>
                    <label class="block text-gray-700 mb-1">Card Number</label>
                    <input type="text" name="card_number" maxlength="19" class="w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-purple-500">
                </div>
                <div class="flex gap-4">
                    <div class="flex-1">
                        <label class="block text-gray-700 mb-1">Expiry Date</label>
                        <input type="text" name="expiry_date" placeholder="MM/YY" maxlength="5" class="w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-purple-500">
                    </div>
                    <div class="flex-1">
                        <label class="block text-gray-700 mb-1">CVC</label>
                        <input type="text" name="cvc" maxlength="4" class="w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-purple-500">
                    </div>
                </div>
                <div>
                    <label class="block text-gray-700 mb-1">Country</label>
                    <input type="text" name="country" class="w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-purple-500">
                </div>
                <div>
                    <label class="block text-gray-700 mb-1">ZIP Code</label>
                    <input type="text" name="zip_code" class="w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-purple-500">
                </div>
            </div>
        </form>
        <button type="submit" form="" class="w-full bg-purple-600 hover:bg-purple-700 text-white font-bold py-3 rounded-lg text-lg mt-2">Pay & Publish Offer</button>
    </div>
</div>
<?php
}
include 'base.php';
