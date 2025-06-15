<?php
session_start();
require_once __DIR__ . '/../config/db.php';
$offer_id = isset($_GET['id']) ? $_GET['id'] : null;
$offer = null;
$applies = [];
if ($offer_id) {
    $stmt = $pdo->prepare('SELECT o.*, u.firstname, u.lastname FROM offers o LEFT JOIN users u ON o.user_id = u.user_id WHERE o.offer_id = ?');
    $stmt->execute([$offer_id]);
    $offer = $stmt->fetch(PDO::FETCH_ASSOC);
    // Fetch applications for this offer (for the "Applies" section)
    $stmt = $pdo->prepare('SELECT * FROM designs WHERE offer_id = ?');
    $stmt->execute([$offer_id]);
    $applies = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
function render_main() {
    global $offer, $applies;
    if (!$offer) {
        echo '<div class="max-w-xl mx-auto mt-10 p-6 bg-white rounded shadow text-center text-red-600">Offer not found.</div>';
        return;
    }
?>
<div class="max-w-2xl mx-auto py-8">
    <a href="offers.php" class="flex items-center mb-6 text-gray-600 hover:text-purple-600">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
        Back to Offers
    </a>
    <div class="bg-white rounded-xl shadow p-6 mb-6">
        <h1 class="text-2xl font-bold mb-4 flex items-center gap-2"><?php echo htmlspecialchars($offer['offer_title']); ?></h1>
        <div class="flex flex-wrap gap-2 mb-6">
            <?php foreach (explode(',', $offer['tags']) as $tag): if (trim($tag)): ?>
                <span class="px-4 py-1 border border-blue-300 text-blue-600 rounded-full text-sm bg-blue-50"><?php echo htmlspecialchars(trim($tag)); ?></span>
            <?php endif; endforeach; ?>
        </div>
        <div class="flex flex-wrap gap-8 mb-6">
            <div class="flex flex-col items-center">
                <span class="text-xs text-gray-400">Deadline</span>
                <span class="font-semibold text-lg text-gray-700"><?php echo $offer['offer_end'] ? date('M d, Y', strtotime($offer['offer_end'])) : '—'; ?></span>
            </div>
            <div class="flex flex-col items-center">
                <span class="text-xs text-gray-400">Estimated Time</span>
                <span class="font-semibold text-lg text-gray-700">4 Days</span>
            </div>
            <div class="flex flex-col items-center">
                <span class="text-xs text-gray-400">Budget</span>
                <span class="font-semibold text-lg text-gray-700"><?php echo htmlspecialchars($offer['offer_budget']); ?> MAD</span>
            </div>
        </div>
        <a href="#apply" class="block w-full bg-blue-400 hover:bg-blue-500 text-white font-semibold py-3 rounded-lg text-center transition">Apply for this offre</a>
    </div>
    <div class="bg-white rounded-xl shadow p-6 mb-6">
        <h2 class="text-lg font-bold mb-2">Description</h2>
        <div class="text-gray-700 whitespace-pre-line"><?php echo nl2br(htmlspecialchars($offer['description'])); ?></div>
    </div>
    <div class="bg-white rounded-xl shadow p-6 mb-6">
        <h2 class="text-lg font-bold mb-4">Applys</h2>
        <div class="flex gap-4 overflow-x-auto">
            <?php foreach ($applies as $apply): ?>
                <div class="w-32 h-32 bg-gray-200 rounded-lg flex items-center justify-center">
                    <?php if (!empty($apply['file_url']) && preg_match('/\.(jpg|jpeg|png|gif|webp)$/i', $apply['file_url'])): ?>
                        <img src="<?php echo (strpos($apply['file_url'], '/uploads') === 0 ? '/creavote' . $apply['file_url'] : $apply['file_url']); ?>" alt="Design" class="object-contain w-full h-full rounded-lg" />
                    <?php else: ?>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><rect width="20" height="20" x="2" y="2" rx="4"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16l4-4 4 4" /></svg>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
<?php }
include 'base.php';
