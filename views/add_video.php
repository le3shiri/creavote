<?php
session_start();
if (empty($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
require_once '../config/db.php';

function render_main() {
    ?>
    <div class="flex flex-col items-center justify-center min-h-screen py-10 px-4 bg-gray-50">
        <div class="bg-white rounded-lg shadow-lg p-8 w-full max-w-md">
            <h1 class="text-2xl font-bold text-[#2c5282] mb-6 text-center">Upload a Video</h1>
            <?php if (!empty($_SESSION['upload_error'])): ?>
                <div class="bg-red-100 text-red-700 rounded p-2 mb-4 text-sm">
                    <?php echo $_SESSION['upload_error']; unset($_SESSION['upload_error']); ?>
                </div>
            <?php endif; ?>
            <form action="../controllers/add_video.php" method="POST" enctype="multipart/form-data" class="space-y-4">
                <div>
                    <label class="block text-gray-700 font-semibold mb-1">Offer <span class="text-red-500">*</span></label>
                    <select name="offer_id" required class="w-full border rounded-lg px-3 py-2 bg-white focus:ring-2 focus:ring-[#55A9FF] focus:border-[#55A9FF] transition-colors">
                        <option value="">Select an offer...</option>
                        <?php
                        // Fetch offers for dropdown
                        $stmt = $pdo->query('SELECT offer_id, offer_title FROM offers ORDER BY offer_start DESC');
                        $offers = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        foreach ($offers as $offer): ?>
                            <option value="<?php echo htmlspecialchars($offer['offer_id']); ?>"><?php echo htmlspecialchars($offer['offer_title']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-gray-700 font-semibold mb-1">Video File <span class="text-red-500">*</span></label>
                    <input type="file" name="video_file" accept="video/mp4,video/webm,video/mov" required class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-[#55A9FF] focus:border-[#55A9FF] transition-colors" />
                </div>
                <div>
                    <label class="block text-gray-700 font-semibold mb-1">Description</label>
                    <textarea name="description" rows="3" class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-[#55A9FF] focus:border-[#55A9FF] transition-colors" placeholder="Describe your video..."></textarea>
                </div>
                <button type="submit" class="w-full bg-[#55A9FF] hover:bg-[#3d94f5] text-white font-bold py-2.5 rounded-lg transition-colors shadow-md hover:shadow-lg">Upload Video</button>
            </form>
        </div>
    </div>
    <?php
}
include 'base.php';
