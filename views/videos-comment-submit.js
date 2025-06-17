// Handle comment submission for video sidebar

document.addEventListener('DOMContentLoaded', function() {
    const sidebar = document.getElementById('comments-sidebar');
    if (!sidebar) return;
    const commentsSection = sidebar.querySelector('.comments-section');
    const commentInput = sidebar.querySelector('.comment-input');
    const postBtn = sidebar.querySelector('.submit-comment');
    let currentDesignId = null;

    // When sidebar is opened, update currentDesignId
    document.querySelectorAll('.comment-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const card = btn.closest('[data-design-id]');
            currentDesignId = card ? card.getAttribute('data-design-id') : null;
        });
    });

    postBtn.addEventListener('click', function() {
        if (!currentDesignId) return;
        const comment = commentInput.value.trim();
        if (!comment) {
            commentInput.focus();
            return;
        }
        postBtn.disabled = true;
        postBtn.textContent = 'Posting...';
        fetch('/creavote/controllers/comment.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'design_id=' + encodeURIComponent(currentDesignId) + '&comment=' + encodeURIComponent(comment)
        })
        .then(res => res.json())
        .then(data => {
            postBtn.disabled = false;
            postBtn.textContent = 'Post';
            if (data.success) {
                commentInput.value = '';
                // Reload comments
                fetch('/creavote/controllers/get-comments.php?design_id=' + encodeURIComponent(currentDesignId))
                    .then(res => res.text())
                    .then(html => {
                        commentsSection.innerHTML = html;
                    });
            } else {
                alert(data.message || 'Failed to post comment.');
            }
        })
        .catch(() => {
            postBtn.disabled = false;
            postBtn.textContent = 'Post';
            alert('Failed to post comment.');
        });
    });
});
