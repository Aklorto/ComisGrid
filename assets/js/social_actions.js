function toggleLike(productId, btn){
    fetch('toggle_like.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'product_id=' + encodeURIComponent(productId)
    })
    .then(res => res.json())
    .then(data => {
        if(data.success){
            btn.classList.toggle('active', data.liked);
        } else {
            alert(data.message || 'Failed to like.');
        }
    });
}

function toggleBookmark(productId, btn){
    fetch('toggle_bookmark.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'product_id=' + encodeURIComponent(productId)
    })
    .then(res => res.json())
    .then(data => {
        if(data.success){
            btn.classList.toggle('active', data.bookmarked);
        } else {
            alert(data.message || 'Failed to bookmark.');
        }
    });
}
