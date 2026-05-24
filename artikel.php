<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jurnal Informasi - Jejak Negeri Atas Awan</title>
    <link rel="stylesheet" href="style.css?v=2.0"> 
</head>
<body>
    <div class="site-bg"></div>
    <?php include 'sidebar.php'; ?>

    <main class="content-area-with-sidebar">
        <header class="hero-header">
            <div class="hero-text">
                <p style="text-transform: uppercase; letter-spacing: 4px; font-weight: 600; margin-bottom: 10px; opacity: 0.8;">Wawasan & Cerita</p>
                <h1>Jurnal Dieng.</h1>
            </div>
        </header>

        <section class="white-content-section">
            <h2 style="font-size: 3rem; margin-bottom: 40px;">Informasi Terbaru</h2>
            
            <div class="grid-container">
                <?php
                include 'db_config.php';
                $sql = "SELECT * FROM articles ORDER BY created_at DESC";
                $res = mysqli_query($conn, $sql);
                
                if (mysqli_num_rows($res) > 0):
                    while($row = mysqli_fetch_assoc($res)):
                ?>
                <div class="card-item">
                    <?php if($row['image_url']): ?>
                        <img src="<?= $row['image_url'] ?>" alt="<?= $row['title'] ?>">
                    <?php endif; ?>
                    <div class="card-content">
                        <p style="font-size: 0.75rem; color: var(--accent); font-weight: 800; text-transform: uppercase; margin-bottom: 10px;">
                            <?= date('d M Y', strtotime($row['created_at'])) ?>
                        </p>
                        <h3><?= $row['title'] ?></h3>
                        <p><?= substr($row['content'], 0, 150) ?>...</p>
                        <a href="article_detail.php?id=<?= $row['id'] ?>" class="button-link">Baca Selengkapnya</a>
                    </div>
                </div>
                <?php 
                    endwhile;
                else:
                ?>
                <p style="grid-column: 1 / -1; text-align: center; color: #94a3b8; padding: 100px 0;">Belum ada informasi atau jurnal yang diterbitkan.</p>
                <?php endif; ?>
            </div>
        </section>

        <footer style="padding: 60px; background: white; text-align: center; border-top: 1px solid #f0f0f0;">
            <p>&copy; 2026 JEJAK NEGERI. Seluruh Hak Cipta Dilindungi.</p>
        </footer>
    </main>
    <script src="script.js"></script> 
</body>
</html>