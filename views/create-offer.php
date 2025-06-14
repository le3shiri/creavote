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
        <a href="offers.php" class="flex items-center mb-6 text-gray-600 hover:text-purple-600">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
            Back to Offers
        </a>
        <form action="../controllers/offer/create-offer.php" method="POST" enctype="multipart/form-data">
            <!-- Basic Information -->
            <div class="bg-white rounded-lg shadow p-6 mb-6">
                <h2 class="text-xl font-bold mb-4">Basic Information</h2>
                <div class="mb-4">
                    <label class="block text-gray-700 mb-1">Offer Title</label>
                    <input type="text" name="offer_title" required class="w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-purple-500">
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 mb-1">Category</label>
                    <input type="text" name="category" required class="w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-purple-500">
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 mb-1">Description</label>
                    <textarea name="description" rows="3" required class="w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-purple-500"></textarea>
                </div>
                <div class="mb-2 flex items-center">
                    <div class="flex-1">
                        <label class="block text-gray-700 mb-1">Tags (optional)</label>
                        <input type="text" name="tags" class="w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-purple-500">
                    </div>
                    <button type="button" class="ml-2 bg-blue-400 hover:bg-blue-500 text-white px-5 py-2 rounded">Add</button>
                </div>
            </div>
            <!-- Requirements -->
            <div class="bg-white rounded-lg shadow p-6 mb-6">
                <h2 class="text-xl font-bold mb-4">Requirements</h2>
                <div class="mb-4 flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-400 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3" /></svg>
                    <input type="date" name="offer_end" class="flex-1 px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-purple-500" placeholder="Deadline (optional)">
                </div>
                <div class="mb-4 flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-400 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3" /></svg>
                    <input type="number" name="offer_budget" required min="1" step="0.01" class="flex-1 px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-purple-500" placeholder="Budget">
                </div>
            </div>
            <!-- File Upload -->
            <div class="bg-white rounded-lg shadow p-6 mb-6">
                <h2 class="text-xl font-bold mb-4">Description</h2>
                <div class="border-2 border-dashed border-gray-300 rounded-lg p-8 text-center bg-gray-50">
                    <input type="file" name="offer_files[]" id="offer_files" class="hidden" multiple>
                    <label for="offer_files" class="flex flex-col items-center cursor-pointer">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-gray-400 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4a1 1 0 011-1h8a1 1 0 011 1v12M7 16l-4 4m0 0l4-4m-4 4h18" /></svg>
                        <span class="text-gray-500">Drag and drop files here or click to browse</span>
                        <button type="button" class="mt-3 bg-white border px-6 py-2 rounded shadow text-gray-700 font-semibold">+ Add Files</button>
                    </label>
                </div>
            </div>
            <button type="submit" class="w-full bg-purple-600 hover:bg-purple-700 text-white font-bold py-3 rounded-lg text-lg">Publish Offer</button>
        </form>
    </div>
</div>
<?php
}
include 'base.php';
