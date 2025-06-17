// AJAX save/unsave functionality for video save buttons

document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.save-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const designId = btn.getAttribute('data-design-id');
            let isSaved = btn.getAttribute('data-saved') === '1';
            btn.disabled = true;
            fetch('/creavote/controllers/save.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'design_id=' + encodeURIComponent(designId) + '&action=' + (isSaved ? 'unsave' : 'save')
            })
            .then(res => res.json())
            .then(data => {
                btn.disabled = false;
                if (data.success) {
                    isSaved = data.saved;
                    btn.setAttribute('data-saved', isSaved ? '1' : '0');
                    btn.style.background = isSaved ? '#55A9FF' : '#55A9FF22';
                    btn.style.color = isSaved ? '#fff' : '#55A9FF';
                    btn.querySelector('svg').setAttribute('stroke', isSaved ? '#fff' : '#55A9FF');
                    btn.querySelector('.save-label').textContent = isSaved ? 'Saved' : 'Save';
                } else {
                    alert(data.message || 'Failed to save.');
                }
            })
            .catch(() => {
                btn.disabled = false;
                alert('Failed to save.');
            });
        });
    });
});
