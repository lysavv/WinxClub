<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Beranda - Jejak Negeri Atas Awan</title>
    <link rel="stylesheet" href="style.css?v=2.0"> 
</head>
<body>
    
    <!-- Background Foto Penuh -->
    <div class="site-bg"></div>

    <!-- Sidebar -->
    <?php include 'sidebar.php'; ?>

    <!-- Content Area Sebelah Kanan -->
    <main class="content-area-with-sidebar">
        
        <!-- Hero Header -->
        <header class="hero-header">
            <div class="hero-text" style="position: relative; z-index: 20;">
                <p style="text-transform: uppercase; letter-spacing: 6px; font-weight: 800; margin-bottom: 20px; color: #ffffff; opacity: 0.9;">Exploring the majestic</p>
                <h1 style="color: white; font-size: 5rem; margin-bottom: 40px; line-height: 0.95;">Welcome to<br>Dieng Plateau.</h1>
                <a href="wisata.php" class="btn-premium">Jelajahi Sekarang <i class="fas fa-arrow-right" style="margin-left:10px;"></i></a>
            </div>
        </header>

        <!-- White Section -->
        <section class="white-content-section">
            
            <!-- Category Summary -->
            <div style="text-align: center; margin-bottom: 80px;">
                <h2 style="font-size: 3.5rem; margin-bottom: 20px;">Eksplorasi Pilihan</h2>
                <p style="color: #64748b; max-width: 600px; margin: 0 auto;">Temukan keajaiban alam, cita rasa kuliner yang menghangatkan, dan warisan budaya luhur di dataran tinggi Dieng.</p>
            </div>

            <div class="grid-container" style="margin-bottom: 100px;">
                <a href="wisata.php" class="card-item" style="text-decoration: none; padding: 50px 40px;">
                    <div class="cat-icon"><i class="fas fa-mountain"></i></div>
                    <h3>Wisata Alam</h3>
                    <p>Jelajahi telaga warna-warni dan kawah vulkanik yang menakjubkan di puncak Jawa.</p>
                    <span class="button-link">Lihat Wisata</span>
                </a>
                <a href="kuliner.php" class="card-item" style="text-decoration: none; padding: 50px 40px;">
                    <div class="cat-icon"><i class="fas fa-utensils"></i></div>
                    <h3>Kuliner Khas</h3>
                    <p>Nikmati Mie Ongklok dan segarnya Carica langsung dari dapur masyarakat lokal.</p>
                    <span class="button-link">Lihat Kuliner</span>
                </a>
                <a href="artikel.php" class="card-item" style="text-decoration: none; padding: 50px 40px;">
                    <div class="cat-icon"><i class="fas fa-camera-retro"></i></div>
                    <h3>Budaya & Event</h3>
                    <p>Saksikan ritual Ruwatan Rambut Gimbal yang penuh magis dan sejarah luhur.</p>
                    <span class="button-link">Lihat Budaya</span>
                </a>
            </div>

            <!-- Suara Wisatawan (Dynamic) -->
            <div class="testimonial-section">
                <h3 style="font-family: 'Playfair Display'; font-size: 3.5rem; text-align: center; margin-bottom: 60px; letter-spacing: -2px;">Suara Wisatawan</h3>
                <div id="top-comments-display" class="grid-container" style="margin-bottom: 0;">
                    <!-- Will be populated by JS with most liked comments -->
                    <p style="text-align: center; color: #94a3b8; width: 100%; grid-column: 1 / -1;">Menjelajahi cerita terbaik...</p>
                </div>
            </div>

            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    fetch('get_top_comments.php')
                        .then(res => res.json())
                        .then(data => {
                            const container = document.getElementById('top-comments-display');
                            if (!container) return;
                            if (data.length === 0) {
                                container.innerHTML = '<p style="text-align:center; color:#94a3b8; width:100%;">Belum ada cerita perjalanan. Ayo mulai bercerita!</p>';
                                return;
                            }
                            container.innerHTML = data.map(c => `
                                <div class="card-item" style="background: var(--bg-light); border: none; padding: 40px;">
                                    <div style="color: var(--accent); margin-bottom: 20px;"><i class="fas fa-quote-left fa-2x"></i></div>
                                    <p style="font-style: italic; color: #475569; font-size: 1.1rem; min-height: 100px;">"${c.comment_text}"</p>
                                    <div style="display: flex; align-items: center; justify-content: space-between; margin-top: 30px; border-top: 1px solid rgba(0,0,0,0.05); padding-top: 20px;">
                                        <div style="display: flex; align-items: center; gap: 12px;">
                                            <div style="width: 35px; height: 35px; background: var(--accent); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: 800; font-size: 0.8rem;">
                                                ${c.username.charAt(0).toUpperCase()}
                                            </div>
                                            <strong style="color: var(--primary); font-size: 0.9rem;">${c.username}</strong>
                                        </div>
                                        <button onclick="likeCommentHome(${c.id})" style="background:none; border:none; cursor:pointer; color: var(--accent); font-size: 0.85rem; font-weight: 800; display:flex; align-items:center; gap:5px;">
                                            <i class="fas fa-heart"></i> <span id="like-count-home-${c.id}">${c.likes}</span>
                                        </button>
                                    </div>
                                    <p style="font-size: 0.7rem; color: #94a3b8; margin-top: 10px; margin-bottom:0;">Tentang: <strong>${c.item_name}</strong></p>
                                </div>
                            `).join('');
                        });
                });

                function likeCommentHome(commentId) {
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
                            // Reload testimonials to show new order/counts
                            location.reload(); 
                        } else {
                            alert(data.message || 'Gagal memberikan Like');
                        }
                    });
                }
            </script>

            <!-- Map Section -->
            <div style="margin-bottom: 50px;">
                <h3 style="font-family: 'Playfair Display'; font-size: 2.5rem; margin-bottom: 30px;">Lokasi Strategis</h3>
                <div style="width: 100%; height: 450px; border-radius: 30px; overflow: hidden; box-shadow: 0 20px 40px rgba(0,0,0,0.1);">
                    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d31644.062534568853!2d109.89437146524317!3d-7.205973950484379!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e700ce3291885f7%3A0x5027a76e3569760!2sDieng%2C%20Kejajar%2C%20Wonosobo%20Regency%2C%20Central%20Java!5e0!3m2!1sen!2sid!4v1716000000000!5m2!1sen!2sid" width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                </div>
            </div>

        </section>

        <footer style="padding: 60px; background: white; text-align: center; border-top: 1px solid #f0f0f0;">
            <p>&copy; 2026 JEJAK NEGERI. Seluruh Hak Cipta Dilindungi.</p>
        </footer>
    </main>

    <script src="script.js"></script> 
</body>
</html>
