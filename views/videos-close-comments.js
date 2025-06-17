// Ensure the close button always closes the comment sidebar

document.addEventListener('DOMContentLoaded', function() {
    var closeBtn = document.getElementById('close-comments');
    var sidebar = document.getElementById('comments-sidebar');
    if (closeBtn && sidebar) {
        closeBtn.addEventListener('click', function() {
            sidebar.classList.add('hidden');
        });
    }
});
