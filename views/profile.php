<?php
require_once __DIR__ . '/../config/db.php';
session_start();
if (empty($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
// Determine which profile to show
$view_user_id = isset($_GET['user_id']) ? $_GET['user_id'] : $_SESSION['user_id'];
$is_own_profile = ($view_user_id == $_SESSION['user_id']);
// Fetch user info
$stmt = $pdo->prepare('SELECT * FROM users WHERE user_id = ?');
$stmt->execute([$view_user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
// Fetch user's designs
$stmt = $pdo->prepare('SELECT * FROM designs WHERE designer_id = ? ORDER BY submitted_at DESC');
$stmt->execute([$view_user_id]);
$designs = $stmt->fetchAll(PDO::FETCH_ASSOC);
// Fetch user's saved designs only if own profile
$saved_designs = [];
if ($is_own_profile) {
    $stmt = $pdo->prepare('SELECT d.* FROM saves s JOIN designs d ON s.design_id = d.design_id WHERE s.user_id = ? ORDER BY s.saved_at DESC');
    $stmt->execute([$view_user_id]);
    $saved_designs = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
function render_main() {
    global $user, $designs, $saved_designs, $is_own_profile;
?>
<div class="flex-1 flex flex-col px-8 py-8 bg-gray-50 min-h-screen">
    <div class="max-w-3xl mx-auto">
        <!-- User Card -->
        <div class="flex items-center bg-white rounded-xl shadow p-6 mb-8">
            <img src="<?php echo htmlspecialchars($user['profile_picture'] ?? 'https://randomuser.me/api/portraits/lego/1.jpg'); ?>" class="w-20 h-20 rounded-full border-4 border-white shadow mr-6" alt="User">
            <div class="flex-1">
                <div class="text-2xl font-bold"><?php echo htmlspecialchars($user['username'] ?? ($user['firstname'].'_'.$user['lastname'])); ?></div>
                <div class="text-gray-500"><?php echo ucfirst($user['role'] ?? ''); ?></div>
            </div>
            <?php if ($is_own_profile): ?>
            <div class="flex gap-3">
                <a href="profile-settings.php" class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-2 rounded font-semibold">Settings</a>
                <a href="profile-edit.php" class="bg-blue-100 hover:bg-blue-200 text-blue-600 px-6 py-2 rounded font-semibold">Edit profile</a>
            </div>
            <?php endif; ?>
        </div>
        <!-- Tabs -->
        <div class="bg-white rounded-xl shadow p-6">
            <div class="flex border-b mb-6">
                <button id="tab-designs" class="flex-1 text-lg font-semibold pb-2 border-b-2 border-blue-500 text-blue-600 focus:outline-none">Designs</button>
                <button id="tab-saved" class="flex-1 text-lg font-semibold pb-2 border-b-2 border-transparent text-gray-400 focus:outline-none">Save</button>
            </div>
            <!-- Designs Grid -->
            <div id="designs-grid" class="grid grid-cols-3 gap-6">
                <?php if ($designs): ?>
                    <?php foreach ($designs as $design): ?>
                        <div class="aspect-square bg-gray-200 rounded flex items-center justify-center overflow-hidden">
                            <?php if (!empty($design['file_url'])): ?>
                                <img src="<?php echo htmlspecialchars($design['file_url']); ?>" alt="Design" class="object-cover w-full h-full">
                            <?php else: ?>
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a4 4 0 004 4h6a4 4 0 004-4V7" /></svg>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-span-3 text-center text-gray-400 py-8">No designs yet.</div>
                <?php endif; ?>
            </div>
            <!-- Saved Grid (hidden by default) -->
            <div id="saved-grid" class="grid grid-cols-3 gap-6 hidden">
                <?php if ($saved_designs): ?>
                    <?php foreach ($saved_designs as $design): ?>
                        <div class="aspect-square bg-gray-200 rounded flex flex-col items-center justify-center overflow-hidden relative">
                            <?php if (!empty($design['file_url'])): ?>
                                <?php if (preg_match('/\.(mp4|webm|mov)$/i', $design['file_url'])): ?>
                                    <video src="<?php echo htmlspecialchars($design['file_url']); ?>" autoplay loop muted playsinline controls class="object-cover w-full h-full rounded bg-black" style="aspect-ratio:1/1;"></video>
                                    <?php if (!empty($design['description'])): ?>
                                        <div class="absolute bottom-0 left-0 right-0 bg-black/60 text-white text-xs px-2 py-1 line-clamp-2"> <?php echo htmlspecialchars($design['description']); ?> </div>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <img src="<?php echo htmlspecialchars($design['file_url']); ?>" alt="Design" class="object-cover w-full h-full">
                                <?php endif; ?>
                            <?php else: ?>
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a4 4 0 004 4h6a4 4 0 004-4V7" /></svg>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-span-3 text-center text-gray-400 py-8">No saved designs yet.</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<script>
// Simple tab switching
const tabDesigns = document.getElementById('tab-designs');
const tabSaved = document.getElementById('tab-saved');
const designsGrid = document.getElementById('designs-grid');
const savedGrid = document.getElementById('saved-grid');

tabDesigns.addEventListener('click', () => {
    tabDesigns.classList.add('border-blue-500', 'text-blue-600');
    tabDesigns.classList.remove('border-transparent', 'text-gray-400');
    tabSaved.classList.remove('border-blue-500', 'text-blue-600');
    tabSaved.classList.add('border-transparent', 'text-gray-400');
    designsGrid.classList.remove('hidden');
    savedGrid.classList.add('hidden');
});
tabSaved.addEventListener('click', () => {
    tabSaved.classList.add('border-blue-500', 'text-blue-600');
    tabSaved.classList.remove('border-transparent', 'text-gray-400');
    tabDesigns.classList.remove('border-blue-500', 'text-blue-600');
    tabDesigns.classList.add('border-transparent', 'text-gray-400');
    designsGrid.classList.add('hidden');
    savedGrid.classList.remove('hidden');
});
</script>
<?php
}
include 'base.php';
