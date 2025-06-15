<?php
session_start();
if (empty($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
require_once '../config/db.php';
// Fetch all designs (applications) with offer and designer info, random order
$stmt = $pdo->query('SELECT d.*, o.offer_title, o.offer_id, u.firstname, u.lastname, u.profile_picture FROM designs d JOIN offers o ON d.offer_id = o.offer_id JOIN users u ON d.designer_id = u.user_id ORDER BY RAND() LIMIT 30');
$designs = $stmt->fetchAll(PDO::FETCH_ASSOC);
function render_main() {
    global $designs, $pdo;
?>
<div class="flex-1 flex flex-col items-center justify-start py-10 px-4">
    <div class="w-full max-w-2xl">
        <a href="add_video.php" class="inline-block mb-4 bg-blue-600 hover:bg-blue-700 text-white font-semibold px-5 py-2 rounded-lg shadow transition">+ Upload a Video</a>
        <h1 class="text-3xl font-bold mb-6 text-purple-700">Vote on Applications</h1>
        <?php
// Fetch user's saved designs for quick lookup
$saved_ids = [];
if (!empty($_SESSION['user_id'])) {
    $stmt = $pdo->prepare('SELECT design_id FROM saves WHERE user_id = ?');
    $stmt->execute([$_SESSION['user_id']]);
    $saved_ids = array_column($stmt->fetchAll(PDO::FETCH_ASSOC), 'design_id');
}
?>
<?php foreach ($designs as $design): ?>
        <div class="bg-white rounded-lg shadow mb-8">
    <div class="relative w-full aspect-square bg-gray-200 flex items-center justify-center overflow-hidden">

        <?php
        $file_url = htmlspecialchars($design['file_url']);
        $is_img = preg_match('/\.(jpg|jpeg|png|gif|webp)$/i', $file_url);
        if ($is_img): ?>
            <img src="<?php echo (strpos($file_url, '/uploads') === 0 ? '/creavote' . $file_url : $file_url); ?>" alt="Design" class="object-contain w-full h-full" onerror="this.onerror=null;this.src='/creavote/assets/image-placeholder.png';" />
        <?php else: ?>
            <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a4 4 0 004 4h6a4 4 0 004-4V7" /></svg>
        <?php endif; ?>
        <?php if (isset($_SESSION['user_id'])): ?>
            <?php
            // Check if user has already voted on this design
            $user_vote = null;
            if (!empty($_SESSION['user_id'])) {
                $stmt_vote = $pdo->prepare('SELECT rating FROM votes WHERE voter_id = ? AND design_id = ?');
                $stmt_vote->execute([$_SESSION['user_id'], $design['design_id']]);
                $user_vote = $stmt_vote->fetchColumn();
            }
            ?>
            <div class="absolute bottom-4 right-4 z-20">
                <?php if ($user_vote): ?>
                    <div class="bg-blue-600 rounded-full p-3 shadow-lg flex items-center justify-center w-12 h-12 text-white text-xl font-bold select-none" title="Your vote: <?php echo $user_vote; ?>">
                        <?php echo $user_vote; ?>
                    </div>
                <?php else: ?>
                    <div class="group">
                        <button type="button" class="bg-blue-500 hover:bg-blue-600 rounded-full p-3 shadow-lg focus:outline-none flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3" /></svg>
                        </button>
                        <div class="hidden group-hover:flex absolute bottom-12 right-0 bg-white rounded-full shadow-lg px-3 py-2 gap-2 border border-gray-200 animate-fade-in" style="min-width: 270px;">
                            <?php for ($i = 1; $i <= 10; $i++): ?>
                                <form class="vote-form inline" data-design-id="<?php echo htmlspecialchars($design['design_id']); ?>" data-rating="<?php echo $i; ?>">
                                    <input type="hidden" name="design_id" value="<?php echo htmlspecialchars($design['design_id']); ?>">
                                    <input type="hidden" name="rating" value="<?php echo $i; ?>">
                                    <button type="submit" class="w-8 h-8 flex items-center justify-center rounded-full bg-blue-100 hover:bg-blue-400 text-blue-700 hover:text-white font-bold text-sm transition-all duration-150 shadow-md mx-0.5" title="Vote <?php echo $i; ?>">
                                        <?php echo $i; ?>
                                    </button>
                                </form>
                            <?php endfor; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
            <style>
                .group:hover .group-hover\:flex { display: flex !important; }
                .animate-fade-in { animation: fadeIn 0.2s; }
                @keyframes fadeIn { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
            </style>
        <?php endif; ?>
    </div>
    <div class="flex items-center justify-between p-4">
        <div class="flex items-center">
            <a href="/creavote/views/profile.php?user_id=<?php echo urlencode($design['designer_id']); ?>" target="_blank" class="flex items-center group">
                <img src="<?php echo htmlspecialchars($design['profile_picture'] ?? '/assets/default-profile.png'); ?>" class="w-8 h-8 rounded-full object-cover mr-2 group-hover:ring-2 group-hover:ring-blue-400 transition" alt="Designer">
                <span class="font-semibold text-gray-800 mr-2 group-hover:underline"><?php echo htmlspecialchars($design['firstname'] . '_' . $design['lastname']); ?></span>
            </a>
        </div>
        <button class="p-2 text-gray-400 hover:text-gray-700"><svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><circle cx="12" cy="12" r="2" /><circle cx="19" cy="12" r="2" /><circle cx="5" cy="12" r="2" /></svg></button>
    </div>
    <div class="flex items-center gap-4 px-4 pb-4">
        <button type="button" class="flex items-center text-gray-500 hover:text-purple-600" onclick="toggleCommentDropdown('<?php echo htmlspecialchars($design['design_id']); ?>')"><svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v10a2 2 0 01-2 2H5a2 2 0 01-2-2V10a2 2 0 012-2h2" /></svg>Comment</button>
<div class="comment-input-wrapper mt-3 mb-2 hidden w-full px-0" id="comment-input-<?php echo htmlspecialchars($design['design_id']); ?>">
    <form class="comment-form flex items-center bg-gray-50 border border-gray-200 px-0 py-2 shadow-sm w-full mb-2 rounded-none" style="margin-left:0;margin-right:0;" data-design-id="<?php echo htmlspecialchars($design['design_id']); ?>">
        <textarea name="comment" rows="1" class="flex-1 resize-none bg-transparent border-0 focus:ring-0 text-sm placeholder-gray-400 p-2 rounded-none" placeholder="Write a comment..." style="min-height:36px;max-height:80px;overflow:auto;"></textarea>
        <button type="submit" class="ml-2 flex items-center justify-center bg-purple-600 hover:bg-purple-700 text-white w-8 h-8 rounded-full shadow transition-colors duration-150" title="Send Comment">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
        </button>
    </form>
    <div class="comments-list space-y-2 w-full overflow-y-auto border border-gray-200 bg-gray-50 p-2 rounded-none" style="max-height:260px; min-width:0; width:100%;margin-left:0;margin-right:0;">
        <?php
        // Fetch comments for this design
        $stmt = $pdo->prepare('SELECT c.*, u.username, u.profile_picture FROM comments c JOIN users u ON c.user_id = u.user_id WHERE c.design_id = ? ORDER BY c.created_at ASC');
        $stmt->execute([$design['design_id']]);
        $comments = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $commentCount = count($comments);
        foreach ($comments as $i => $comment): ?>
            <div class="flex items-start gap-2 comment-item-<?php echo $design['design_id']; ?>">
                <img src="<?php echo htmlspecialchars($comment['profile_picture'] ?? '/creavote/assets/avatar-placeholder.png'); ?>" class="w-8 h-8 rounded-full object-cover bg-gray-200" alt="avatar">
                <div class="bg-gray-100 rounded-xl px-3 py-2 flex-1">
                    <div class="flex items-center gap-2 mb-0.5">
                        <span class="font-semibold text-sm text-gray-800"><?php echo htmlspecialchars($comment['username']); ?></span>
                        <span class="text-xs text-gray-400"><?php echo date('M j, H:i', strtotime($comment['created_at'])); ?></span>
                    </div>
                    <div class="text-sm text-gray-700"><?php echo nl2br(htmlspecialchars($comment['comment'])); ?></div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
        <?php
$is_saved = in_array($design['design_id'], $saved_ids);
?>
<button onclick="toggleSaveDesign('<?php echo htmlspecialchars($design['design_id']); ?>', this)"
    class="flex items-center px-4 py-2 rounded ml-2 <?php echo $is_saved ? 'bg-green-100 text-green-600' : 'bg-blue-100 text-blue-600'; ?>"
    data-saved="<?php echo $is_saved ? '1' : '0'; ?>">
    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
    <span><?php echo $is_saved ? 'Saved' : 'Save'; ?></span>
</button>
    </div>
    <span class="block mt-2 text-xs text-purple-700 px-4 pb-4">Average rating: <b data-design-rating="<?php echo htmlspecialchars($design['design_id']); ?>"><?php echo number_format($design['rating'], 1); ?></b> (<span data-design-votes="<?php echo htmlspecialchars($design['design_id']); ?>"><?php echo (int)$design['votes_count']; ?></span> votes)</span>
</div>
<?php endforeach; ?>
</div>
</div>
<?php } ?>
<script>
// --- AJAX Voting ---
document.addEventListener('DOMContentLoaded', function() {
    // Use event delegation for vote forms (in case of dynamic rendering)
    document.body.addEventListener('submit', function(e) {
        if (e.target.classList.contains('vote-form')) {
            e.preventDefault();
            var form = e.target;
            var designId = form.querySelector('input[name="design_id"]').value;
            var rating = form.querySelector('input[name="rating"]').value;
            var voteButtonArea = form.closest('.absolute.bottom-4.right-4.z-20');
            fetch('/creavote/controllers/vote-ajax.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'design_id=' + encodeURIComponent(designId) + '&rating=' + encodeURIComponent(rating)
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    // Replace vote icon with number
                    var newIcon = document.createElement('div');
                    newIcon.className = 'bg-blue-600 rounded-full p-3 shadow-lg flex items-center justify-center w-12 h-12 text-white text-xl font-bold select-none';
                    newIcon.title = 'Your vote: ' + data.rating;
                    newIcon.textContent = data.rating;
                    if (voteButtonArea) {
                        voteButtonArea.innerHTML = '';
                        voteButtonArea.appendChild(newIcon);
                    }
                    // Update rating and votes count
                    var ratingSpan = document.querySelector('[data-design-rating="' + designId + '"]');
                    var votesSpan = document.querySelector('[data-design-votes="' + designId + '"]');
                    if (ratingSpan) ratingSpan.textContent = data.avg_rating;
                    if (votesSpan) votesSpan.textContent = data.votes_count;
                } else {
                    alert(data.error || 'Voting failed');
                }
            })
            .catch(() => alert('Voting failed'));
        }
    });
});

function loadMoreComments(designId) {
    var items = document.querySelectorAll('.comment-item-' + designId);
    items.forEach(function(item) { item.classList.remove('hidden'); });
    document.querySelector('.load-more-btn[data-design-id="' + designId + '"]').classList.add('hidden');
    document.querySelector('.show-less-btn[data-design-id="' + designId + '"]').classList.remove('hidden');
}
function showLessComments(designId) {
    var items = document.querySelectorAll('.comment-item-' + designId);
    items.forEach(function(item, idx) { if(idx >= 3) item.classList.add('hidden'); });
    document.querySelector('.load-more-btn[data-design-id="' + designId + '"]').classList.remove('hidden');
    document.querySelector('.show-less-btn[data-design-id="' + designId + '"]').classList.add('hidden');
}
function toggleCommentDropdown(designId) {
    var el = document.getElementById('comment-input-' + designId);
    if (el) {
        el.classList.toggle('hidden');
    }
}
function toggleSaveDesign(designId, btn) {
    btn.disabled = true;
    const isSaved = btn.getAttribute('data-saved') === '1';
    const action = isSaved ? 'unsave' : 'save';
    fetch('../controllers/save.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'design_id=' + encodeURIComponent(designId) + '&action=' + action
    })
    .then(r => r.json())
    .then(res => {
        if (res.success) {
            if (res.saved) {
                btn.setAttribute('data-saved', '1');
                btn.querySelector('span').textContent = 'Saved';
                btn.classList.remove('bg-blue-100', 'text-blue-600');
                btn.classList.add('bg-green-100', 'text-green-600');
            } else {
                btn.setAttribute('data-saved', '0');
                btn.querySelector('span').textContent = 'Save';
                btn.classList.remove('bg-green-100', 'text-green-600');
                btn.classList.add('bg-blue-100', 'text-blue-600');
            }
        }
        btn.disabled = false;
    })
    .catch(() => { btn.disabled = false; });
}
document.addEventListener('DOMContentLoaded', function() {
    // AJAX comment submit
    document.querySelectorAll('.comment-form').forEach(function(form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const designId = this.getAttribute('data-design-id');
            const textarea = this.querySelector('textarea[name="comment"]');
            const comment = textarea.value.trim();
            if (!comment) {
                textarea.classList.add('border-red-400');
                textarea.placeholder = 'Comment cannot be empty!';
                return;
            } else {
                textarea.classList.remove('border-red-400');
                textarea.placeholder = 'Write a comment...';
            }
            const formData = new FormData();
            formData.append('design_id', designId);
            formData.append('comment', comment);
            fetch('/creavote/controllers/comment.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.success && data.comment) {
                    // Build comment HTML
                    const c = data.comment;
                    const commentDiv = document.createElement('div');
                    commentDiv.className = 'flex items-start gap-2';
                    commentDiv.innerHTML = `
                        <img src="${c.profile_picture || '/creavote/assets/avatar-placeholder.png'}" class="w-8 h-8 rounded-full object-cover bg-gray-200" alt="avatar">
                        <div class="bg-gray-100 rounded-xl px-3 py-2 flex-1">
                            <div class="flex items-center gap-2 mb-0.5">
                                <span class="font-semibold text-sm text-gray-800">${c.username}</span>
                                <span class="text-xs text-gray-400">${c.created_at}</span>
                            </div>
                            <div class="text-sm text-gray-700">${c.comment.replace(/\n/g,'<br>')}</div>
                        </div>
                    `;
                    const list = form.parentElement.querySelector('.comments-list');
                    if (list) list.appendChild(commentDiv);
                    textarea.value = '';
                } else {
                    textarea.classList.add('border-red-400');
                    textarea.placeholder = data.message || 'Failed to submit comment.';
                }
            })
            .catch(() => {
                textarea.classList.add('border-red-400');
                textarea.placeholder = 'Failed to submit comment.';
            });
        });
    });
});
</script>
<?php 
include 'base.php';

