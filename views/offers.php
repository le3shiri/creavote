<?php
session_start();
if (empty($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
require_once __DIR__ . '/../config/db.php';
function render_main() {
    global $pdo;
    // Fetch offers from DB
    $stmt = $pdo->query('SELECT o.*, u.firstname, u.lastname FROM offers o LEFT JOIN users u ON o.user_id = u.user_id ORDER BY o.offer_start DESC');
    $offers = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<div class="flex-1 flex flex-col px-8 py-8">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-800 mb-1">Offers</h1>
            <p class="text-gray-500">Find freelance project opportunities</p>
        </div>
        <a href="create-offer.php" class="bg-purple-600 hover:bg-purple-700 text-white font-semibold px-5 py-2 rounded-lg shadow">+ Create Offer</a>
    </div>
    <div class="mb-8">
        <form class="flex items-center w-full max-w-xl">
            <input type="text" placeholder="Search offers" class="flex-1 px-4 py-2 border rounded-l-lg focus:outline-none focus:ring-2 focus:ring-purple-500 bg-gray-50">
            <button type="submit" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-r-lg font-semibold">Search</button>
        </form>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-6">
        <?php if ($offers): ?>
            <?php foreach ($offers as $offer): ?>
                <div class="bg-white rounded-lg shadow p-5 flex flex-col md:flex-row items-center md:items-start">
                    <div class="flex-1">
                        <a href="offer-details.php?id=<?php echo $offer['offer_id']; ?>" class="font-bold text-lg mb-1 text-blue-600 hover:underline"><?php echo htmlspecialchars($offer['offer_title']); ?></a>
                        <div class="text-gray-600 mb-2 text-sm"><?php echo htmlspecialchars($offer['description']); ?></div>
                        <div class="text-xs text-gray-400 mb-2">By <?php echo htmlspecialchars($offer['firstname'] . ' ' . $offer['lastname']); ?> | Starts <?php echo htmlspecialchars($offer['offer_start']); ?></div>
                        <div class="flex flex-wrap gap-2 mb-4">
                            <?php foreach (explode(',', $offer['tags']) as $tag): ?>
                                <span class="bg-gray-100 text-gray-700 px-2 py-1 rounded text-xs"><?php echo htmlspecialchars(trim($tag)); ?></span>
                            <?php endforeach; ?>
                        </div>
                        <?php
                        // Check if user has applied or saved
                        $applied = false;
                        $saved = false;
                        if (!empty($_SESSION['user_id'])) {
                            // Applied?
                            $stmt = $pdo->prepare('SELECT 1 FROM applications WHERE user_id = ? AND offer_id = ?');
                            $stmt->execute([$_SESSION['user_id'], $offer['offer_id']]);
                            $applied = $stmt->fetchColumn();
                            // Saved?
                            $stmt = $pdo->prepare('SELECT 1 FROM saves WHERE user_id = ? AND design_id = ?');
                            $stmt->execute([$_SESSION['user_id'], $offer['offer_id']]); // NOTE: This assumes offer_id == design_id for save, but you may want to adjust this logic if saves are for designs only
                            $saved = $stmt->fetchColumn();
                        }
                        ?>
                        <div class="flex gap-3">
    <a href="offer-details.php?id=<?php echo $offer['offer_id']; ?>" class="bg-blue-100 hover:bg-blue-200 text-blue-700 px-4 py-2 rounded font-semibold text-sm">Details</a>
                            <?php if ($applied): ?>
                                <button class="apply-btn bg-gray-300 text-gray-500 px-6 py-2 rounded font-semibold cursor-not-allowed" disabled data-offer="<?php echo $offer['offer_id']; ?>">Applied</button>
                            <?php else: ?>
                                <button type="button" class="apply-btn bg-purple-600 hover:bg-purple-700 text-white px-6 py-2 rounded font-semibold" data-offer="<?php echo $offer['offer_id']; ?>">Apply</button>
                            <?php endif; ?>
                            <button class="bg-gray-100 hover:bg-purple-50 text-gray-700 px-6 py-2 rounded font-semibold">Details</button>

                        </div>
                    </div>
                    <div class="w-28 h-28 bg-gray-100 flex items-center justify-center ml-0 md:ml-6 mt-6 md:mt-0 rounded">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a4 4 0 004 4h6a4 4 0 004-4V7" /></svg>
                    </div>
                    <div class="ml-0 md:ml-6 mt-4 md:mt-0 text-right">
                        <div class="text-purple-600 font-bold text-lg"><?php echo htmlspecialchars($offer['offer_budget']); ?> MAD</div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-span-2 text-center text-gray-400 py-12 text-lg">No offers found.</div>
        <?php endif; ?>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Apply button AJAX
    document.querySelectorAll('.apply-btn').forEach(function(btn) {
        if (btn.disabled) return;
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            var offerId = btn.getAttribute('data-offer');
            btn.disabled = true;
            btn.textContent = 'Applying...';
            fetch('/creavote/controllers/apply.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'offer_id=' + encodeURIComponent(offerId)
            })
            .then(r => r.json())
            .then(res => {
                if (res.success) {
                    window.location.href = 'apply-offer.php?offer_id=' + encodeURIComponent(offerId);
                } else {
                    btn.disabled = false;
                    btn.textContent = 'Apply';
                    alert(res.message || 'Could not apply.');
                }
            })
            .catch(() => { btn.disabled = false; btn.textContent = 'Apply'; alert('Could not apply.'); });
        });
    });

});
</script>
<?php
}
include 'base.php';
