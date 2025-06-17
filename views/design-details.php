<?php
// Design Details Page
session_start();
require_once '../config/db.php';
if (empty($_GET['design_id'])) {
    die('No design specified.');
}
$design_id = $_GET['design_id'];
$stmt = $pdo->prepare('SELECT d.*, o.offer_title, o.offer_end, u.firstname, u.lastname, u.profile_picture FROM designs d JOIN offers o ON d.offer_id = o.offer_id JOIN users u ON d.designer_id = u.user_id WHERE d.design_id = ?');
$stmt->execute([$design_id]);
$design = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$design) {
    die('Design not found.');
}
$is_img = preg_match('/\.(jpg|jpeg|png|gif|webp)$/i', $design['file_url']);
$is_video = preg_match('/\.(mp4|webm|mov)$/i', $design['file_url']);
?>
<?php include 'base.php'; ?>
<div class="max-w-xl mx-auto py-10">
    <div class="w-full bg-white rounded-2xl shadow-xl p-0 overflow-hidden">
        <!-- Image Preview with FAB -->
            <div class="relative bg-gray-200 flex items-center justify-center" style="aspect-ratio:1.6/1;">
                <?php if ($is_img): ?>
                    <img src="<?php echo (strpos($design['file_url'], '/uploads') === 0 ? '/creavote' . $design['file_url'] : $design['file_url']); ?>"
                         alt="Design Image"
                         class="object-contain w-full max-h-[340px] mx-auto rounded-xl shadow"
                         style="background:#f3f4f6;" />
                <?php elseif ($is_video): ?>
                    <video src="<?php echo (strpos($design['file_url'], '/uploads') === 0 ? '/creavote' . $design['file_url'] : $design['file_url']); ?>"
                           controls
                           class="object-contain w-full max-h-[340px] mx-auto bg-black rounded-xl shadow"></video>
                <?php else: ?>
                    <div class="flex items-center justify-center w-full h-[200px] bg-gray-200">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a4 4 0 004 4h6a4 4 0 004-4V7" /></svg>
                    </div>
                <?php endif; ?>
                <!-- Floating Action Button -->
                <button class="absolute bottom-4 right-4 bg-blue-500 hover:bg-blue-600 text-white rounded-full p-3 shadow-lg focus:outline-none">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8s-9-3.582-9-8 4.03-8 9-8 9 3.582 9 8z" /></svg>
                </button>
            </div>
            <!-- Title & Subtitle -->
            <div class="px-8 pt-6">
                <div class="font-bold text-2xl text-gray-800 mb-1"><?php echo htmlspecialchars($design['title'] ?? $design['offer_title']); ?></div>
                <div class="text-gray-500 text-base mb-3"><?php echo htmlspecialchars($design['subtitle'] ?? $design['category'] ?? ''); ?></div>
            </div>
            <!-- Designer Card -->
            <div class="bg-blue-50 rounded-xl shadow p-5 mx-6 mb-4 flex gap-4 items-center">
                <img src="<?php echo htmlspecialchars($design['profile_picture'] ?? '/creavote/assets/default-profile.png'); ?>" class="w-14 h-14 rounded-full border-2 border-blue-300" alt="Designer">
                <div>
                    <div class="font-semibold text-lg text-blue-700 mb-1"><?php echo htmlspecialchars($design['firstname'] . ' ' . $design['lastname']); ?></div>
                    <div class="flex flex-wrap gap-2 mb-2">
                        <?php if (!empty($design['tags'])): foreach (explode(',', $design['tags']) as $tag): ?>
                            <span class="bg-blue-100 text-blue-700 px-2 py-1 rounded text-xs font-medium"><?php echo htmlspecialchars(trim($tag)); ?></span>
                        <?php endforeach; endif; ?>
                    </div>
                    <div class="text-gray-600 text-sm"><?php echo nl2br(htmlspecialchars($design['description'])); ?></div>
                </div>
            </div>
            <!-- Ratings -->
            <div class="flex items-center gap-3 px-8 mb-4">
                <div class="text-yellow-500 flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.97a1 1 0 00.95.69h4.175c.969 0 1.371 1.24.588 1.81l-3.38 2.455a1 1 0 00-.364 1.118l1.287 3.97c.3.921-.755 1.688-1.54 1.118l-3.38-2.455a1 1 0 00-1.176 0l-3.38 2.455c-.784.57-1.838-.197-1.54-1.118l1.287-3.97a1 1 0 00-.364-1.118L2.05 9.397c-.783-.57-.38-1.81.588-1.81h4.175a1 1 0 00.95-.69l1.286-3.97z"/></svg>
                    <span class="font-bold text-lg mr-1"><?php echo isset($design['rating']) ? number_format($design['rating'], 1) : '0.0'; ?></span>
                    <span class="text-xs">(<?php echo (int)($design['votes_count'] ?? 0); ?> votes)</span>
                </div>
            </div>
            <!-- Original Offer Section -->
            <div class="px-8 pb-8">
                <div class="font-bold text-lg mb-2 mt-4">Original Offer</div>
                <div class="bg-gray-50 rounded-xl p-5 flex flex-col md:flex-row gap-6 items-center md:items-start">
                    <div class="flex-1">
                        <div class="font-semibold text-gray-800 text-base mb-1"><?php echo htmlspecialchars($design['offer_title']); ?></div>
                        <div class="text-gray-500 text-sm mb-2"><?php echo htmlspecialchars($design['category'] ?? ''); ?></div>
                        <div class="text-gray-600 text-sm mb-3"><?php echo htmlspecialchars($design['description']); ?></div>
                        <div class="flex flex-wrap gap-2 mb-3">
                            <?php if (!empty($design['tags'])): foreach (explode(',', $design['tags']) as $tag): ?>
                                <span class="bg-gray-200 text-gray-700 px-2 py-1 rounded text-xs font-medium"><?php echo htmlspecialchars(trim($tag)); ?></span>
                            <?php endforeach; endif; ?>
                        </div>
                        <div class="flex items-center gap-4 mt-2">
                            <span class="text-blue-700 font-bold text-lg"><?php echo isset($design['amount']) ? htmlspecialchars($design['amount']) . ' MAD' : ''; ?></span>
                            <span class="text-xs text-gray-400">Ends: <?php echo htmlspecialchars($design['offer_end']); ?></span>
                        </div>
                    </div>
                    <div class="flex flex-col gap-2 items-center">
                        <div class="w-24 h-24 bg-gray-200 rounded flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a4 4 0 004 4h6a4 4 0 004-4V7" /></svg>
                        </div>
                        <a href="/creavote/views/offer-details.php?id=<?php echo urlencode($design['offer_id']); ?>" class="bg-blue-100 hover:bg-blue-200 text-blue-700 px-4 py-2 rounded-lg font-semibold text-sm transition-colors">Details</a>
                        <a href="#" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-semibold text-sm transition-colors">Apply</a>
                    </div>
                </div>
            </div>
            <div class="px-8 pb-8">
                <a href="/creavote/views/home.php" class="inline-block bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded shadow">Back to Feed</a>
            </div>
        </div>
    </div>
</div>
