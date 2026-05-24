<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kontak - Jejak Negeri Atas Awan</title>
    <link rel="stylesheet" href="style.css?v=2.0"> 
</head>
<body>
    <!-- Background Foto Penuh -->
    <div class="site-bg"></div>

    <?php include 'sidebar.php'; ?>

    <main class="content-area-with-sidebar">
        <!-- Hero Header -->
        <header class="hero-header">
            <div class="hero-text">
                <p style="text-transform: uppercase; letter-spacing: 4px; font-weight: 600; margin-bottom: 10px; opacity: 0.8;">Mari Terhubung</p>
                <h1>Kontak Kami.</h1>
            </div>
        </header>

        <!-- White Section -->
        <section class="white-content-section">
            <h2>Hubungi Kami</h2>
            <div style="max-width: 600px; margin-top: 30px;">
                <form action="#" method="POST">
                    <div class="input-group">
                        <label style="color: var(--text-dark);">Nama Lengkap</label>
                        <input type="text" class="input-bar" style="border-bottom-color: rgba(0,0,0,0.1); color: var(--text-dark);" required>
                    </div>
                    <div class="input-group">
                        <label style="color: var(--text-dark);">Alamat Email</label>
                        <input type="email" class="input-bar" style="border-bottom-color: rgba(0,0,0,0.1); color: var(--text-dark);" required>
                    </div>
                    <div class="input-group">
                        <label style="color: var(--text-dark);">Pesan</label>
                        <textarea class="input-bar" style="border-bottom-color: rgba(0,0,0,0.1); color: var(--text-dark); width: 100%; min-height: 100px; background: transparent; border: none; border-bottom: 1px solid rgba(0,0,0,0.1); outline: none;" required></textarea>
                    </div>
                    <button type="submit" class="btn-maps" style="width: 100%; border: none; cursor: pointer; margin-top: 20px;">Kirim Pesan</button>
                </form>
            </div>
        </section>

        <footer style="padding: 60px; background: white; text-align: center; border-top: 1px solid #f0f0f0;">
            <p>&copy; 2026 JEJAK NEGERI. Seluruh Hak Cipta Dilindungi.</p>
        </footer>
    </main>

    <script src="script.js"></script> 
</body>
</html>