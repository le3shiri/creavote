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
                var newIcon = document.createElement('div');
                newIcon.className = 'bg-blue-600 rounded-full p-3 shadow-lg flex items-center justify-center w-12 h-12 text-white text-xl font-bold select-none';
                newIcon.title = 'Your vote: ' + data.rating;
                newIcon.textContent = data.rating;
                if (voteButtonArea) {
                    voteButtonArea.innerHTML = '';
                    voteButtonArea.appendChild(newIcon);
                }
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
