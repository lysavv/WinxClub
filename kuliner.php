<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kuliner - Jejak Negeri Atas Awan</title>
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
                <p style="text-transform: uppercase; letter-spacing: 4px; font-weight: 600; margin-bottom: 10px; opacity: 0.8;">Cita Rasa</p>
                <h1>Negeri Awan.</h1>
            </div>
        </header>

        <!-- White Section -->
        <section class="white-content-section">
            <h2>Kuliner Khas</h2>
            <div class="grid-container">
                <?php
                include 'db_config.php';
                $sql = "SELECT * FROM items WHERE category='kuliner'";
                $res = mysqli_query($conn, $sql);
                if (mysqli_num_rows($res) > 0):
                    while($row = mysqli_fetch_assoc($res)):
                ?>
                <div class="card-item" 
                     data-id="<?= $row['id'] ?>"
                     data-desc="<?= htmlspecialchars($row['description']) ?>"
                     data-price="<?= htmlspecialchars($row['price']) ?>"
                     data-hours="<?= htmlspecialchars($row['hours']) ?>"
                     data-location="<?= htmlspecialchars($row['location']) ?>"
                     data-sosmed="<?= htmlspecialchars($row['sosmed']) ?>"
                     data-maps="<?= htmlspecialchars($row['maps_url']) ?>"
                     data-wa="<?= htmlspecialchars($row['whatsapp'] ?? '') ?>">
                    <img src="<?= $row['image_url'] ?>" alt="<?= $row['name'] ?>">
                    <div class="card-content">
                        <h3><?= $row['name'] ?></h3>
                        <p><?= substr($row['description'], 0, 100) ?>...</p>
                        <a href="detail.php?id=<?= $row['id'] ?>" class="button-link">Lihat Selengkapnya</a>
                    </div>
                </div>
                <?php 
                    endwhile;
                else:
                ?>
                <p>Data belum tersedia.</p>
                <?php endif; ?>
            </div>
        </section>

        <footer style="padding: 60px; background: white; text-align: center; border-top: 1px solid #f0f0f0;">
            <p>&copy; 2026 JEJAK NEGERI. Seluruh Hak Cipta Dilindungi.</p>
        </footer>
    </main>

    <!-- Detail Modal -->
    <div id="detail-modal" class="modal">
        <div class="modal-content">
            <span class="modal-close">&times;</span>
            <div class="modal-content-inner"></div>
        </div>
    </div>

    <script src="script.js"></script> 
</body>
</html>