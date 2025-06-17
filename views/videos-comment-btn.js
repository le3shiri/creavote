// Attach comment button handlers for PHP-rendered video cards

document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.comment-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.stopPropagation();
            const card = btn.closest('[data-design-id]');
            const designId = card.getAttribute('data-design-id');
            const sidebar = document.getElementById('comments-sidebar');
            const commentsSection = sidebar.querySelector('.comments-section');
            const commentInput = sidebar.querySelector('.comment-input');
            let currentDesignId = designId;

            sidebar.classList.remove('hidden');
            commentsSection.innerHTML = '<div class="text-gray-400 text-center py-8">Loading comments...</div>';
            fetch('/creavote/controllers/get-comments.php?design_id=' + encodeURIComponent(designId))
                .then(res => res.text())
                .then(html => {
                    commentsSection.innerHTML = html;
                });
            commentInput.value = '';
        });
    });
});
