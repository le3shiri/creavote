<?php
session_start();
if (empty($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
function render_main() {
    global $pdo;
?>
<div class="flex-1 flex flex-row items-start justify-center py-10 px-4 gap-8">
    <!-- Video Feed Area -->
    <div class="flex-1 flex flex-col items-center" id="videos-feed">
    <?php
    require_once '../config/db.php';
    $user_id = $_SESSION['user_id'];
    // Fetch all video designs
    $stmt = $pdo->prepare("SELECT d.*, u.firstname, u.lastname, u.profile_picture FROM designs d JOIN users u ON d.designer_id = u.user_id WHERE d.file_url LIKE '%.mp4' OR d.file_url LIKE '%.webm' OR d.file_url LIKE '%.mov' ORDER BY d.submitted_at DESC");
    $stmt->execute();
    $videos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    // Fetch user's votes
    $votes = [];
    $stmt_vote = $pdo->prepare('SELECT design_id, rating FROM votes WHERE voter_id = ?');
    $stmt_vote->execute([$user_id]);
    foreach ($stmt_vote->fetchAll(PDO::FETCH_ASSOC) as $v) {
        $votes[$v['design_id']] = $v['rating'];
    }
    ?>
    <?php foreach ($videos as $video): ?>
        <div class="bg-white rounded-lg shadow mb-8 w-full max-w-xs mx-auto flex flex-col items-center" data-design-id="<?php echo htmlspecialchars($video['design_id']); ?>">
            <div class="relative w-full" style="aspect-ratio:9/16; max-width:360px; max-height:640px;">
                <?php if (preg_match('/\.(mp4|webm|mov)$/i', $video['file_url'])): ?>
                    <video src="<?php echo htmlspecialchars($video['file_url']); ?>" controls autoplay loop muted playsinline class="object-cover w-full h-full rounded-xl bg-black" style="aspect-ratio:9/16; max-width:360px; max-height:640px;"></video>
                <?php else: ?>
                    <img src="<?php echo htmlspecialchars($video['file_url']); ?>" class="object-cover w-full h-full rounded-xl bg-black" style="aspect-ratio:9/16; max-width:360px; max-height:640px;" alt="Design Image" />
                <?php endif; ?>
                <div class="absolute top-3 right-3 flex items-center bg-black/60 rounded-full px-2 py-1 z-10 shadow border-2" style="border-color:#55A9FF; box-shadow:0 0 0 4px #55A9FF22;">
                    <img src="<?php echo $video['profile_picture'] ?: '/creavote/assets/default-profile.png'; ?>" class="w-8 h-8 rounded-full border-2 border-white" alt="Designer">
                    <span class="ml-2 text-sm font-semibold text-white"><?php echo htmlspecialchars($video['firstname'] . '_' . $video['lastname']); ?></span>
                </div>
                <!-- VOTING GROUP INSIDE RELATIVE VIDEO CONTAINER -->
                <?php
// Check if user has already voted on this video
$user_vote = null;
if (!empty($_SESSION['user_id'])) {
    $stmt_vote = $pdo->prepare('SELECT rating FROM votes WHERE voter_id = ? AND design_id = ?');
    $stmt_vote->execute([$_SESSION['user_id'], $video['design_id']]);
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
                            <img src="../assets/logo.png" alt="" srcset="" width="20px">
                        </button>
                        <div class="hidden group-hover:flex absolute bottom-12 right-0 bg-white rounded-full shadow-lg px-3 py-2 gap-2 border border-gray-200 animate-fade-in" style="min-width: 270px;">
                            <?php for ($i = 1; $i <= 10; $i++): ?>
                                <form class="vote-form inline" data-design-id="<?php echo htmlspecialchars($video['design_id']); ?>" data-rating="<?php echo $i; ?>">
                                    <input type="hidden" name="design_id" value="<?php echo htmlspecialchars($video['design_id']); ?>">
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
            </div>
            <div class="px-4 py-2 w-full max-w-xs">
                <div class="text-gray-700 mb-1 line-clamp-2"><?php echo htmlspecialchars($video['description'] ?? ''); ?></div>
                <!-- Voting system -->
                <div class="flex items-center justify-end gap-2 mb-2">
                    <span class="text-yellow-500 flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.97a1 1 0 00.95.69h4.175c.969 0 1.371 1.24.588 1.81l-3.38 2.455a1 1 0 00-.364 1.118l1.287 3.97c.3.921-.755 1.688-1.54 1.118l-3.38-2.455a1 1 0 00-1.176 0l-3.38 2.455c-.784.57-1.838-.197-1.54-1.118l1.287-3.97a1 1 0 00-.364-1.118L2.05 9.397c-.783-.57-.38-1.81.588-1.81h4.175a1 1 0 00.95-.69l1.286-3.97z"/></svg>
                        <span data-design-rating="<?php echo $video['design_id']; ?>"><?php echo isset($video['rating']) ? number_format($video['rating'], 1) : '0.0'; ?></span>
                    </span>
                    <span class="ml-2 text-blue-500"><span data-design-votes="<?php echo $video['design_id']; ?>"><?php echo $video['votes_count'] ?? 0; ?></span> votes</span>
                </div>
                <div class="flex items-center justify-center gap-6 mt-2">
                    <button class="comment-btn flex items-center gap-1 p-2 rounded-full transition-all" style="background:#55A9FF22;color:#55A9FF;" title="Comment">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="#55A9FF"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v8a2 2 0 01-2 2H7a2 2 0 01-2-2V10a2 2 0 012-2h2m2-4h4a2 2 0 012 2v2a2 2 0 01-2 2h-4a2 2 0 01-2-2V6a2 2 0 012-2z"/></svg>
                        <span>Comment</span>
                    </button>
                    <?php
                        // Fetch user's saved design ids for PHP rendering (move to top if needed)
                        if (!isset($saved_ids_php)) {
                            $stmt_saved = $pdo->prepare('SELECT design_id FROM saves WHERE user_id = ?');
                            $stmt_saved->execute([$user_id]);
                            $saved_ids_php = array_column($stmt_saved->fetchAll(PDO::FETCH_ASSOC), 'design_id');
                        }
                        $is_saved = in_array($video['design_id'], $saved_ids_php);
                    ?>
                    <button class="save-btn flex items-center gap-1 p-2 rounded-full transition-all" title="Save" data-design-id="<?php echo $video['design_id']; ?>" data-saved="<?php echo $is_saved ? '1' : '0'; ?>" style="background:<?php echo $is_saved ? '#55A9FF' : '#55A9FF22'; ?>;color:<?php echo $is_saved ? '#fff' : '#55A9FF'; ?>;border:1.5px solid #55A9FF;">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="<?php echo $is_saved ? '#fff' : '#55A9FF'; ?>"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5v14l7-7 7 7V5a2 2 0 00-2-2H7a2 2 0 00-2 2z" /></svg>
                        <span class="save-label"><?php echo $is_saved ? 'Saved' : 'Save'; ?></span>
                    </button>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

    <!-- Comments Sidebar (dynamic) -->
    <div id="comments-sidebar" class="w-[350px] h-[80vh] bg-blue-50 rounded-xl shadow-lg p-6 fixed right-8 top-24 z-30 hidden flex flex-col">
        <div class="font-bold text-lg mb-4">Comments</div>
        <div class="comments-section flex-1 overflow-y-auto max-h-[55vh] mb-2"></div>
        <div class="add-comment mt-2">
            <input type="text" class="comment-input border px-2 py-1 rounded w-3/4" placeholder="Add a comment...">
            <button class="submit-comment bg-blue-500 text-white px-3 py-1 rounded ml-2">Post</button>
        </div>
        <button id="close-comments" class="absolute top-2 right-2 text-gray-400 hover:text-red-500">&times;</button>
    </div>
</div>
<script>
fetch('/creavote/controllers/videos.php')
    .then(res => res.json())
    .then(data => {
        const feed = document.getElementById('videos-feed');
        if (!data.success || !data.videos.length) {
            feed.innerHTML = '<div class="text-gray-400 text-xl">No designs found.</div>';
            return;
        }
        feed.innerHTML = data.videos.map(v => `
        <div class="bg-white rounded-lg shadow mb-8 w-full max-w-xs mx-auto flex flex-col items-center" data-design-id="${v.design_id}">
            <div class="relative w-full" style="aspect-ratio:9/16; max-width:360px; max-height:640px;">
                ${/\.(mp4|webm|mov)$/i.test(v.file_url) ? `<video src="${v.file_url}" controls autoplay loop muted playsinline class="object-cover w-full h-full rounded-xl bg-black" style="aspect-ratio:9/16; max-width:360px; max-height:640px;"></video>` : `<img src="${v.file_url}" class="object-cover w-full h-full rounded-xl bg-black" style="aspect-ratio:9/16; max-width:360px; max-height:640px;" alt="Design Image" />`}
                <div class="absolute top-3 right-3 flex items-center bg-black/60 rounded-full px-2 py-1 z-10 shadow border-2" style="border-color:#55A9FF; box-shadow:0 0 0 4px #55A9FF22;">
                    <img src="${v.profile_picture || '/creavote/assets/default-profile.png'}" class="w-8 h-8 rounded-full border-2 border-white" alt="Designer">
                    <span class="ml-2 text-sm font-semibold text-white">${v.firstname}_${v.lastname}</span>
                </div>
            </div>
            <div class="px-4 py-2 w-full max-w-xs">
                <div class="text-gray-700 mb-1 line-clamp-2">${v.description || ''}</div>
                <!-- Voting system -->
                <div class="flex items-center justify-end gap-2 mb-2">
                  <span class="text-yellow-500 flex items-center"><svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.97a1 1 0 00.95.69h4.175c.969 0 1.371 1.24.588 1.81l-3.38 2.455a1 1 0 00-.364 1.118l1.287 3.97c.3.921-.755 1.688-1.54 1.118l-3.38-2.455a1 1 0 00-1.176 0l-3.38 2.455c-.784.57-1.838-.197-1.54-1.118l1.287-3.97a1 1 0 00-.364-1.118L2.05 9.397c-.783-.57-.38-1.81.588-1.81h4.175a1 1 0 00.95-.69l1.286-3.97z"/></svg>${v.rating ? v.rating.toFixed(1) : '0.0'}</span>
                  <button class="like-btn flex items-center gap-1 p-2 rounded-full border ml-2 transition-all" style="background:#55A9FF22;border-color:#55A9FF;color:#55A9FF;" title="Vote">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="#55A9FF"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 9l-1-1m0 0a3 3 0 00-4.243 0l-1 1a3 3 0 000 4.243l6 6a3 3 0 004.243 0l1-1a3 3 0 000-4.243l-6-6z" /></svg>
                    <span class="like-count">${v.votes_count || 0}</span>
                  </button>
                </div>
                <div class="flex items-center justify-center gap-6 mt-2">
                    <button class="comment-btn flex items-center gap-1 p-2 rounded-full transition-all" style="background:#55A9FF22;color:#55A9FF;" title="Comment">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="#55A9FF"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v8a2 2 0 01-2 2H7a2 2 0 01-2-2V10a2 2 0 012-2h2m2-4h4a2 2 0 012 2v2a2 2 0 01-2 2h-4a2 2 0 01-2-2V6a2 2 0 012-2z" /></svg>
                        <span>Comment</span>
                    </button>
                    <button class="save-btn flex items-center gap-1 p-2 rounded-full transition-all" title="Save" data-saved="${v.is_saved ? '1' : '0'}" style="background:${v.is_saved ? '#55A9FF' : '#55A9FF22'};color:${v.is_saved ? '#fff' : '#55A9FF'};border:1.5px solid #55A9FF;">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="${v.is_saved ? '#fff' : '#55A9FF'}"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5v14l7-7 7 7V5a2 2 0 00-2-2H7a2 2 0 00-2 2z" /></svg>
                        <span class="save-label">${v.is_saved ? 'Saved' : 'Save'}</span>
                    </button>
                </div>
                <div class="comments-section mt-4 hidden"></div>
                <div class="add-comment mt-2 hidden">
                    <input type="text" class="comment-input border px-2 py-1 rounded w-3/4" placeholder="Add a comment...">
                    <button class="submit-comment bg-blue-500 text-white px-3 py-1 rounded ml-2">Post</button>
                </div>
            </div>
        </div>
        `).join('');

        // --- AJAX Voting for videos (same as feed) ---
document.addEventListener('DOMContentLoaded', function() {
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


        // Save button handler
        document.querySelectorAll('.save-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const card = btn.closest('[data-design-id]');
                const designId = card.getAttribute('data-design-id');
                let isSaved = btn.getAttribute('data-saved') === '1';
                const action = isSaved ? 'unsave' : 'save';
                fetch('/creavote/controllers/save.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: `design_id=${encodeURIComponent(designId)}&action=${action}`
                })
                .then(res => res.json())
                .then(res => {
                    if(res.success) {
                        isSaved = res.saved;
                        btn.setAttribute('data-saved', isSaved ? '1' : '0');
                        btn.querySelector('.save-label').textContent = isSaved ? 'Saved' : 'Save';
                        btn.style.background = isSaved ? '#d1fae5' : '#eff6ff';
                        btn.style.color = isSaved ? '#059669' : '#2563eb';
                    } else {
                        alert(res.message || 'Could not save this design.');
                    }
                });
            });
        });

        // Comment sidebar logic
        const sidebar = document.getElementById('comments-sidebar');
        const commentsSection = sidebar.querySelector('.comments-section');
        const commentInput = sidebar.querySelector('.comment-input');
        const submitComment = sidebar.querySelector('.submit-comment');
        let currentDesignId = null;

        document.querySelectorAll('.comment-btn').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.stopPropagation();
                const card = btn.closest('[data-design-id]');
                const designId = card.getAttribute('data-design-id');
                if (currentDesignId === designId && !sidebar.classList.contains('hidden')) {
                    sidebar.classList.add('hidden');
                    return;
                }
                currentDesignId = designId;
                sidebar.classList.remove('hidden');
                commentsSection.innerHTML = '<div class="text-gray-400 text-center py-8">Loading comments...</div>';
                fetch(`/creavote/controllers/get-comments.php?design_id=${encodeURIComponent(designId)}`)
                    .then(res => res.text())
                    .then(html => {
                        commentsSection.innerHTML = html;
                    });
                commentInput.value = '';
            });
        });

        // Submit comment in sidebar
        submitComment.addEventListener('click', function() {
            if (!currentDesignId) return;
            const comment = commentInput.value.trim();
            if (!comment) return;
            fetch('/creavote/controllers/comment.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `design_id=${encodeURIComponent(currentDesignId)}&comment=${encodeURIComponent(comment)}`
            })
            .then(res => res.json())
            .then(res => {
                if(res.success) {
                    // Prepend new comment
                    const newComment = document.createElement('div');
                    newComment.className = 'bg-gray-50 rounded-lg p-4 mb-3 shadow';
                    newComment.innerHTML = `<div class=\"flex items-center mb-2\"><span class=\"w-6 h-6 rounded-full bg-blue-100 flex items-center justify-center text-blue-500 font-bold mr-2\">${res.comment.username[0]}</span><span class=\"font-semibold text-sm\">${res.comment.username}</span></div><div class=\"text-gray-700 mb-2\">${res.comment.comment}</div><div class=\"flex items-center gap-4 text-gray-500 text-sm\"><span>${res.comment.created_at}</span></div>`;
                    commentsSection.prepend(newComment);
                    commentInput.value = '';
                } else {
                    alert(res.message || 'Could not add comment.');
                }
            });
        });

        // Close sidebar
        document.getElementById('close-comments').addEventListener('click', function() {
            sidebar.classList.add('hidden');
            currentDesignId = null;
        });

        // Hide sidebar when clicking outside
        document.addEventListener('click', function(e) {
            if (!sidebar.contains(e.target) && !e.target.classList.contains('comment-btn')) {
                sidebar.classList.add('hidden');
                currentDesignId = null;
            }
        });
    });
</script>
<script src="/creavote/views/videos-vote-ajax.js"></script>
<script src="/creavote/views/videos-comment-btn.js"></script>
<script src="/creavote/views/videos-close-comments.js"></script>
<script src="/creavote/views/videos-comment-submit.js"></script>
<script src="/creavote/views/videos-save-ajax.js"></script>
<?php
}
include 'base.php';
