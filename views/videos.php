<?php
session_start();
if (empty($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
function render_main() {
?>
<div class="flex-1 flex flex-row items-start justify-center py-10 px-4 gap-8">
    <!-- Video/Card Area -->
    <div class="flex-1 flex items-center justify-center">
        <div class="bg-gray-100 rounded-xl shadow-lg flex flex-col relative w-[400px] h-[500px]">
            <div class="flex-1 flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-20 w-20 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A2 2 0 0021 6.382V17.618a2 2 0 01-1.447 1.894L15 17.618M15 10v7.618m0-7.618L9 13.618m6-3.618v7.618m0-7.618L3 17.618m6-3.618v7.618" /></svg>
            </div>
            <div class="absolute left-4 bottom-4 flex items-center">
                <img src="https://randomuser.me/api/portraits/women/68.jpg" class="w-10 h-10 rounded-full border-2 border-white" alt="User">
                <span class="ml-2 text-sm font-semibold text-gray-700">Hafsa_Elmalki</span>
            </div>
            <div class="absolute right-4 bottom-4 flex flex-col items-center gap-3">
                <button class="bg-blue-100 text-blue-500 p-2 rounded-full shadow"><svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M21 12c0 4.418-4.03 8-9 8s-9-3.582-9-8c0-1.657 1.343-3 3-3 .656 0 1.283.213 1.789.576A7.975 7.975 0 0112 4a7.975 7.975 0 016.211 5.576c.506-.363 1.133-.576 1.789-.576 1.657 0 3 1.343 3 3z" /></svg></button>
                <button class="bg-gray-100 text-gray-500 p-2 rounded-full shadow"><svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v10a2 2 0 01-2 2H5a2 2 0 01-2-2V10a2 2 0 012-2h2" /></svg></button>
                <button class="bg-gray-100 text-gray-500 p-2 rounded-full shadow"><svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg></button>
            </div>
        </div>
    </div>
    <!-- Comments Section -->
    <div class="w-[350px] bg-blue-50 rounded-xl shadow-lg p-6">
        <div class="font-bold text-lg mb-4">Comments</div>
        <input type="text" placeholder="Write a comment..." class="w-full px-4 py-2 mb-4 border rounded focus:outline-none focus:ring-2 focus:ring-blue-400">
        <div class="space-y-4">
            <?php for($i=0; $i<4; $i++): ?>
            <div class="bg-white rounded-lg p-3 shadow flex flex-col">
                <div class="flex items-center mb-1">
                    <span class="w-6 h-6 rounded-full bg-blue-100 flex items-center justify-center text-blue-500 font-bold mr-2">O</span>
                    <span class="font-semibold text-sm">User 1</span>
                </div>
                <div class="text-gray-700 text-sm mb-2">The typography choice is perfect for the brand identity. Great attention to detail.</div>
                <div class="flex items-center gap-4 text-xs text-gray-400">
                    <span class="flex items-center gap-1"><svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10h4.553A2 2 0 0021 6.382V17.618a2 2 0 01-1.447 1.894L14 17.618" /></svg>4</span>
                    <span class="flex items-center gap-1"><svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2 2m-2-2v6" /></svg>2</span>
                    <a href="#" class="text-blue-400 hover:underline">Reply</a>
                </div>
            </div>
            <?php endfor; ?>
        </div>
    </div>
</div>
<?php
}
include 'base.php';
