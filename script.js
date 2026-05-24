document.addEventListener("DOMContentLoaded", function() {
    
    // ===================================================
    // 1. Scroll Reveal Animation
    // ===================================================
    const revealElements = document.querySelectorAll('.card-item, .comment-item-bubble, section h2');
    const revealObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('reveal');
            }
        });
    }, { threshold: 0.1 });
    revealElements.forEach(el => { revealObserver.observe(el); });

    // ===================================================
    // 2. AJAX Comment Functions
    // ===================================================
    window.loadComments = function(itemId) {
        const container = document.getElementById(`comments-container-${itemId}`);
        if (!container) return;

        fetch(`get_comments.php?item_id=${itemId}`)
            .then(res => res.json())
            .then(data => {
                if (data.error) {
                    container.innerHTML = `<p style="color:#ef4444; font-size:0.8rem;">Error: ${data.error}</p>`;
                    return;
                }
                if (!Array.isArray(data) || data.length === 0) {
                    container.innerHTML = '<p style="color:rgba(0,0,0,0.2); font-size:0.9rem;">Belum ada komentar. Jadilah yang pertama!</p>';
                    return;
                }
                container.innerHTML = data.map(c => `
                    <div class="comment-box" style="padding:25px; border-radius:25px; background:#ffffff; border:1px solid #e2e8f0; margin-bottom:20px; box-shadow:0 10px 20px rgba(0,0,0,0.02);">
                        <div class="comment-header" style="display:flex; justify-content:space-between; align-items:center; margin-bottom:15px;">
                            <div style="display:flex; align-items:center; gap:12px;">
                                <div style="width:35px; height:35px; background:var(--accent); border-radius:50%; display:flex; align-items:center; justify-content:center; color:white; font-size:0.8rem; font-weight:800;">${c.username.charAt(0).toUpperCase()}</div>
                                <strong style="color:#0f172a; font-size:0.95rem;">${c.username}</strong>
                            </div>
                            <span style="font-size:0.75rem; color:#94a3b8;">${c.created_at}</span>
                        </div>
                        <p style="color:#475569; font-size:1rem; line-height:1.7; margin-bottom:20px;">${c.comment_text}</p>
                        <div style="display:flex; align-items:center; gap:15px;">
                            <button onclick="likeComment(${c.id}, ${itemId})" style="background:none; border:none; color:${window.isLoggedIn ? 'var(--accent)' : '#94a3b8'}; cursor:pointer; font-weight:800; font-size:0.85rem; display:flex; align-items:center; gap:8px;">
                                <i class="fas fa-thumbs-up"></i> Bermanfaat (${c.likes})
                            </button>
                        </div>
                    </div>
                `).join('');
            });
    };

    window.likeComment = function(commentId, itemId) {
        if (!window.isLoggedIn) {
            alert('Silakan login terlebih dahulu untuk memberikan Like.');
            window.location.href = 'login.php';
            return;
        }

        const formData = new URLSearchParams();
        formData.append('comment_id', commentId);

        fetch('like_comment.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: formData.toString()
        })
        .then(res => res.json())
        .then(data => {
            if (data.status === 'liked' || data.status === 'unliked') {
                loadComments(itemId);
            } else {
                alert(data.message || 'Gagal memberikan Like. Silakan coba lagi.');
            }
        })
        .catch(err => {
            console.error('Fetch error:', err);
            alert('Koneksi bermasalah saat mengirim Like.');
        });
    };

    window.submitComment = function(itemId) {
        const textarea = document.getElementById(`comment-text-${itemId}`);
        const text = textarea.value;
        if (!text.trim()) {
            alert('Silakan tulis komentar terlebih dahulu');
            return;
        }

        const formData = new URLSearchParams();
        formData.append('item_id', itemId);
        formData.append('text', text);

        fetch('add_comment.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: formData.toString()
        })
        .then(res => res.json())
        .then(data => {
            if (data.status === 'success') {
                textarea.value = '';
                loadComments(itemId);
            } else {
                alert(data.message || 'Gagal mengirim komentar. Silakan coba lagi.');
            }
        })
        .catch(err => {
            console.error('Error:', err);
            alert('Terjadi kesalahan koneksi.');
        });
    };
});
