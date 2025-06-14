<?php
session_start();
if (empty($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
require_once __DIR__ . '/../config/db.php';
$offer_id = $_GET['offer_id'] ?? '';
$stmt = $pdo->prepare('SELECT * FROM offers WHERE offer_id = ?');
$stmt->execute([$offer_id]);
$offer = $stmt->fetch(PDO::FETCH_ASSOC);
function render_main() {
    global $offer;
    if (!$offer) {
        echo '<div class="text-center text-red-500">Offer not found.</div>';
        return;
    }
    if (isset($_GET['success']) && $_GET['success'] == '1') {
        echo '<div class="mb-6 p-4 bg-green-100 border border-green-300 text-green-800 rounded text-center font-semibold">Your application was submitted successfully!</div>';
    }
?>
<div class="flex-1 flex flex-col px-8 py-8 bg-gray-50 min-h-screen">
    <div class="max-w-2xl mx-auto">
        <a href="offers.php" class="flex items-center mb-6 text-gray-600 hover:text-purple-600">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
            Apply to Offer
        </a>
        <!-- Offer Summary Card -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <div class="font-bold text-xl mb-2"><?php echo htmlspecialchars($offer['offer_title']); ?></div>
            <div class="flex flex-wrap gap-2 mb-4">
                <?php foreach (explode(',', $offer['tags']) as $tag): ?>
                    <span class="px-4 py-1 rounded-full bg-blue-50 text-blue-500 border border-blue-200 text-sm"><?php echo htmlspecialchars(trim($tag)); ?></span>
                <?php endforeach; ?>
            </div>
            <div class="flex flex-wrap gap-8">
                <div class="flex items-center gap-2 text-gray-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3" /></svg>
                    <div>
                        <div class="text-xs">Deadline</div>
                        <div class="font-semibold"><?php echo htmlspecialchars($offer['offer_end']); ?></div>
                    </div>
                </div>
                <div class="flex items-center gap-2 text-gray-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3" /></svg>
                    <div>
                        <div class="text-xs">Budget</div>
                        <div class="font-semibold text-purple-600"><?php echo htmlspecialchars($offer['offer_budget']); ?> MAD</div>
                    </div>
                </div>
            </div>
        </div>
        <!-- File Upload Section -->
        <form action="../controllers/submit-design.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="offer_id" value="<?php echo htmlspecialchars($offer['offer_id']); ?>">
            <div class="bg-white rounded-lg shadow p-6 mb-6">
                <h2 class="text-lg font-bold mb-4">Select Relevant Work</h2>
                <div class="border-2 border-dashed border-gray-300 rounded-lg p-8 text-center bg-gray-50">
                    <input type="file" name="design_file" id="design_file" class="hidden">
                    <label for="design_file" class="flex flex-col items-center cursor-pointer">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-gray-400 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4a1 1 0 011-1h8a1 1 0 011 1v12M7 16l-4 4m0 0l4-4m-4 4h18" /></svg>
                        <span class="text-gray-500">Drag and drop files here or click to browse</span>
                        <button id="add-files-btn" type="button" class="mt-3 bg-white border px-6 py-2 rounded shadow text-gray-700 font-semibold">+ Add Files</button>
                        <span id="selected-file-name" class="block mt-2 text-sm text-green-600"></span>
                    </label>
                    <script>
                        // Show file name when selected
                        const fileInput = document.getElementById('design_file');
                        const fileNameSpan = document.getElementById('selected-file-name');
                        document.getElementById('add-files-btn').addEventListener('click', function(e) {
                            e.preventDefault();
                            fileInput.click();
                        });
                        fileInput.addEventListener('change', function() {
                            if (fileInput.files.length > 0) {
                                fileNameSpan.textContent = 'Selected: ' + fileInput.files[0].name;
                            } else {
                                fileNameSpan.textContent = '';
                            }
                        });
                    </script>
                </div>
            </div>
            <button type="submit" class="w-full bg-purple-600 hover:bg-purple-700 text-white font-bold py-3 rounded-lg text-lg">Submit Application</button>
        </form>
    </div>
</div>
<?php
}
include 'base.php';
