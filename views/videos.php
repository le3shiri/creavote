<?php
session_start();
if (empty($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
function render_main() {
?>
<div class="flex-1 flex flex-row items-start justify-center py-10 px-4 gap-8">
    <!-- Video Feed Area -->
    <div class="flex-1 flex flex-col items-center" id="videos-feed">
        <!-- Video posts will be rendered here -->
    </div>
    <!-- Comments Sidebar (dynamic) -->
    <div id="comments-sidebar" class="w-[350px] bg-blue-50 rounded-xl shadow-lg p-6 fixed right-8 top-24 z-30 hidden flex-col">
        <div class="font-bold text-lg mb-4">Comments</div>
        <div class="comments-section"></div>
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
                <div class="absolute top-3 right-3 flex items-center bg-black/60 rounded-full px-2 py-1 z-10 shadow">
                    <img src="${v.profile_picture || '/creavote/assets/default-profile.png'}" class="w-8 h-8 rounded-full border-2 border-white" alt="Designer">
                    <span class="ml-2 text-sm font-semibold text-white">${v.firstname}_${v.lastname}</span>
                </div>
            </div>
            <div class="px-4 py-2 w-full max-w-xs">
                <div class="text-gray-700 mb-1 line-clamp-2">${v.description || ''}</div>
                <!-- Voting system -->
                <div class="flex items-center justify-end gap-2 mb-2">
                  <span class="text-yellow-500 flex items-center"><svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.97a1 1 0 00.95.69h4.175c.969 0 1.371 1.24.588 1.81l-3.38 2.455a1 1 0 00-.364 1.118l1.287 3.97c.3.921-.755 1.688-1.54 1.118l-3.38-2.455a1 1 0 00-1.176 0l-3.38 2.455c-.784.57-1.838-.197-1.54-1.118l1.287-3.97a1 1 0 00-.364-1.118L2.05 9.397c-.783-.57-.38-1.81.588-1.81h4.175a1 1 0 00.95-.69l1.286-3.97z"/></svg>${v.rating ? v.rating.toFixed(1) : '0.0'}</span>
                  <button class="like-btn flex items-center gap-1 p-2 rounded-full hover:bg-blue-100 text-blue-500 border border-blue-200 ml-2" title="Vote">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 9l-1-1m0 0a3 3 0 00-4.243 0l-1 1a3 3 0 000 4.243l6 6a3 3 0 004.243 0l1-1a3 3 0 000-4.243l-6-6z" /></svg>
                    <span class="like-count">${v.votes_count || 0}</span>
                  </button>
                </div>
                <div class="flex items-center justify-center gap-6 mt-2">
                    <button class="comment-btn flex items-center gap-1 p-2 rounded-full hover:bg-blue-100" title="Comment">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v8a2 2 0 01-2 2H7a2 2 0 01-2-2V10a2 2 0 012-2h2m2-4h4a2 2 0 012 2v2a2 2 0 01-2 2h-4a2 2 0 01-2-2V6a2 2 0 012-2z" /></svg>
                        <span>Comment</span>
                    </button>
                    <button class="save-btn flex items-center gap-1 p-2 rounded-full" title="Save" data-saved="${v.is_saved ? '1' : '0'}" style="background:${v.is_saved ? '#d1fae5' : '#eff6ff'};color:${v.is_saved ? '#059669' : '#2563eb'}">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5v14l7-7 7 7V5a2 2 0 00-2-2H7a2 2 0 00-2 2z" /></svg>
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

        // Like button handler
        document.querySelectorAll('.like-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const card = btn.closest('[data-design-id]');
                const designId = card.getAttribute('data-design-id');
                fetch('/creavote/controllers/vote-ajax.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: `design_id=${encodeURIComponent(designId)}&rating=10`
                })
                .then(res => res.json())
                .then(res => {
                    if(res.success) {
                        btn.querySelector('.like-count').textContent = res.votes_count;
                    } else {
                        alert(res.error || 'Could not like this design.');
                    }
                });
            });
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
<?php
}
include 'base.php';
