<?php
include 'db_config.php';
session_start();

if (!isset($_GET['id'])) {
    header("Location: beranda.php");
    exit();
}

$id = mysqli_real_escape_string($conn, $_GET['id']);
$sql = "SELECT * FROM items WHERE id = '$id'";
$result = mysqli_query($conn, $sql);
$item = mysqli_fetch_assoc($result);

if (!$item) {
    header("Location: beranda.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $item['name'] ?> - Jejak Negeri Atas Awan</title>
    <link rel="stylesheet" href="style.css?v=2.0"> 

    <style>
        .detail-hero {
            height: 60vh;
            background: linear-gradient(rgba(0,0,0,0.3), rgba(15,23,42,1)), url('<?= $item['image_url'] ?>') center/cover;
            display: flex;
            align-items: flex-end;
            padding: 80px;
        }
        .detail-container {
            max-width: 1200px;
            margin: -100px auto 0;
            padding: 0 40px 100px;
            position: relative;
            z-index: 10;
        }
        .detail-card {
            background: white;
            border-radius: 40px;
            padding: 60px;
            box-shadow: 0 30px 60px rgba(0,0,0,0.1);
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 60px;
        }
        .info-panel {
            background: #f8fafc;
            padding: 40px;
            border-radius: 30px;
            height: fit-content;
        }
        .btn-action {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            padding: 18px;
            border-radius: 15px;
            text-decoration: none;
            font-weight: 700;
            margin-bottom: 15px;
            transition: 0.3s;
        }
    </style>
</head>
<body style="background: #0f172a;">
    <?php include 'sidebar.php'; ?>

    <main class="content-area-with-sidebar">
        <div class="detail-hero">
            <div class="hero-text" style="padding: 0;">
                <p style="color: var(--accent-blue); font-weight: 700; text-transform: uppercase; letter-spacing: 3px;"><?= ucfirst($item['category']) ?></p>
                <h1 style="font-size: 5rem; color: white;"><?= $item['name'] ?></h1>
            </div>
        </div>

        <div class="detail-container">
            <div class="detail-card">
                <div class="main-content">
                    <!-- Announcement Section (if any) -->
                    <?php if(!empty($item['announcement'])): ?>
                        <div style="background: #fff7ed; padding: 25px; border-radius: 20px; margin-bottom: 40px; border: 1px solid #ffedd5; display: flex; gap: 20px; align-items: flex-start;">
                            <div style="width: 45px; height: 45px; background: #f59e0b; border-radius: 12px; display: flex; align-items: center; justify-content: center; color: white; flex-shrink: 0;">
                                <i class="fas fa-bullhorn"></i>
                            </div>
                            <div>
                                <h5 style="margin: 0 0 5px; color: #9a3412; font-size: 0.9rem; text-transform: uppercase; letter-spacing: 1px;">Pengumuman Penting</h5>
                                <p style="margin: 0; color: #7c2d12; font-weight: 600; line-height: 1.5;"><?= nl2br(htmlspecialchars($item['announcement'])) ?></p>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Deskripsi Section -->
                    <div style="margin-bottom: 40px;">
                        <h4 style="font-family: 'Playfair Display'; font-size: 2rem; margin-bottom: 20px;">Tentang <?= $item['name'] ?></h4>
                        <p style="font-size: 1.1rem; line-height: 1.8; color: #475569; text-align: justify;">
                            <?= nl2br(htmlspecialchars($item['description'])) ?>
                        </p>
                    </div>

                    <!-- Alamat Section -->
                    <div style="background: #f8fafc; padding: 30px; border-radius: 25px; margin-bottom: 40px; border: 1px solid #e2e8f0;">
                        <h5 style="font-size: 0.8rem; color: #94a3b8; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 10px;">Alamat & Lokasi</h5>
                        <p style="font-weight: 600; color: #1e293b; font-size: 1.05rem;">
                            <i class="fas fa-map-marker-alt" style="color: #3b82f6; margin-right: 10px;"></i>
                            <?= htmlspecialchars($item['location']) ?>
                        </p>
                    </div>

                    <!-- Dynamic Price List (from business_items table) -->
                    <div style="background: #ffffff; padding: 40px; border-radius: 35px; margin-bottom: 40px; border: 1px solid #f1f5f9; box-shadow: 0 15px 40px rgba(0,0,0,0.02);">
                        <h4 style="font-family: 'Playfair Display'; font-size: 2rem; margin-bottom: 30px;">Daftar Menu & Harga</h4>
                        <div style="display: grid; gap: 20px;">
                            <?php 
                            $price_sql = "SELECT * FROM business_items WHERE parent_item_id = " . $item['id'] . " ORDER BY created_at ASC";
                            $price_res = mysqli_query($conn, $price_sql);
                            if (mysqli_num_rows($price_res) > 0):
                                while($p = mysqli_fetch_assoc($price_res)):
                            ?>
                                <div style="display: flex; align-items: center; gap: 20px; padding: 15px 0; border-bottom: 1.5px dashed #f1f5f9;">
                                    <?php if(!empty($p['image_url'])): ?>
                                        <img src="<?= htmlspecialchars($p['image_url']) ?>" style="width: 80px; height: 80px; object-fit: cover; border-radius: 15px; flex-shrink: 0; box-shadow: 0 5px 15px rgba(0,0,0,0.05);">
                                    <?php endif; ?>
                                    <div style="flex-grow: 1;">
                                        <div style="display: flex; justify-content: space-between; align-items: center;">
                                            <span style="font-weight: 700; color: #1e293b; font-size: 1.1rem;"><?= htmlspecialchars($p['name']) ?></span>
                                            <span style="font-weight: 800; color: var(--accent); font-size: 1.1rem;"><?= htmlspecialchars($p['price']) ?></span>
                                        </div>
                                    </div>
                                </div>
                            <?php 
                                endwhile;
                            else:
                            ?>
                                <p style="color: #94a3b8; font-style: italic;">Daftar harga belum tersedia.</p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Operational Status for Wisata (Existing) -->
                    <?php if($item['category'] == 'wisata' && $item['status'] != 'Buka'): ?>
                        <div style="background: #fff1f2; padding: 25px; border-radius: 20px; margin-bottom: 40px; border: 1px solid #fecdd3;">
                            <p style="color: #be123c; font-weight: 600; margin: 0;">
                                <i class="fas fa-exclamation-circle"></i> Saat ini sedang tutup atau tidak beroperasi (<?= htmlspecialchars($item['status']) ?>).
                            </p>
                        </div>
                    <?php endif; ?>

                    <!-- Penginapan: Rooms & Facilities -->
                    <?php if($item['category'] == 'penginapan'): ?>
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px; margin-bottom: 40px;">
                            <?php if(!empty($item['room_types'])): ?>
                            <div style="background: #f0f9ff; padding: 30px; border-radius: 25px; border: 1px solid #e0f2fe;">
                                <h4 style="font-family: 'Playfair Display'; color: #0369a1; margin-bottom: 15px;">Tipe Kamar</h4>
                                <p style="font-size: 0.95rem; color: #0c4a6e;"><?= nl2br($item['room_types']) ?></p>
                            </div>
                            <?php endif; ?>
                            
                            <?php if(!empty($item['facilities'])): ?>
                            <div style="background: #f0fdf4; padding: 30px; border-radius: 25px; border: 1px solid #dcfce7;">
                                <h4 style="font-family: 'Playfair Display'; color: #166534; margin-bottom: 15px;">Fasilitas</h4>
                                <p style="font-size: 0.95rem; color: #064e3b;"><?= nl2br($item['facilities']) ?></p>
                            </div>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                    <div class="comment-section" style="border-top: 1px solid #e2e8f0; padding-top: 40px; margin-top: 40px;">
                        <h3 style="font-family: 'Playfair Display'; font-size: 2rem; margin-bottom: 30px;">Komentar Pengunjung</h3>
                        
                        <div id="comments-container-<?= $item['id'] ?>">
                            <!-- Comments will be loaded here via JS -->
                        </div>

                        <div class="add-comment-form" style="margin-top: 40px;">
                            <?php if(isset($_SESSION['user_id'])): ?>
                                <h4 style="margin-bottom: 15px;">Bagikan Pengalamanmu</h4>
                                <textarea id="comment-text-<?= $item['id'] ?>" placeholder="Tulis sesuatu yang menarik tentang tempat ini..." style="width:100%; height: 150px; background:#f8fafc; border:2px solid #e2e8f0; padding:20px; border-radius:20px; outline:none; resize:none; font-family: inherit;"></textarea>
                                <button onclick="submitComment(<?= $item['id'] ?>)" class="btn-small-center" style="margin-top:15px; width: 100%; border:none; padding: 18px;">Kirim Komentar</button>
                            <?php else: ?>
                                <div style="background:#f0f9ff; padding:25px; border-radius:20px; text-align:center; border: 1px dashed var(--accent-blue);">
                                    <p style="color:#0369a1; font-weight: 600;">Ingin ikut berkomentar? <a href="login.php" style="color:var(--accent-blue); text-decoration:underline;">Masuk ke akunmu</a> dulu ya!</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="side-info">
                    <div class="info-panel">
                        <h4 style="font-family: 'Playfair Display'; font-size: 1.5rem; margin-bottom: 25px;">Detail Informasi</h4>
                        
                        <div style="margin-bottom: 20px;">
                            <span style="display:block; font-size: 0.7rem; color: #94a3b8; text-transform: uppercase; letter-spacing: 1px;">Biaya / Harga</span>
                            <span style="font-weight: 700; color: #0f172a;"><?= $item['price'] ?></span>
                        </div>
                        <div style="margin-bottom: 20px;">
                            <span style="display:block; font-size: 0.7rem; color: #94a3b8; text-transform: uppercase; letter-spacing: 1px;">Jam Operasional</span>
                            <span style="font-weight: 700; color: #0f172a;"><?= $item['hours'] ?></span>
                        </div>
                        <div style="margin-bottom: 20px;">
                            <span style="display:block; font-size: 0.7rem; color: #94a3b8; text-transform: uppercase; letter-spacing: 1px;">Lokasi</span>
                            <span style="font-weight: 700; color: #0f172a;"><?= $item['location'] ?></span>
                        </div>
                        <div style="margin-bottom: 35px;">
                            <span style="display:block; font-size: 0.7rem; color: #94a3b8; text-transform: uppercase; letter-spacing: 1px;">Media Sosial</span>
                            <span style="font-weight: 700; color: #0f172a;"><?= $item['sosmed'] ?></span>
                        </div>

                        <a href="<?= $item['maps_url'] ?>" target="_blank" class="btn-action" style="background: var(--accent-blue); color: white;">
                            <i class="fas fa-directions"></i> Buka Google Maps
                        </a>
                        
                        <?php if(!empty($item['whatsapp'])): ?>
                        <a href="https://wa.me/<?= $item['whatsapp'] ?>" target="_blank" class="btn-action" style="background: #25D366; color: white;">
                            <i class="fab fa-whatsapp"></i> Hubungi Pengelola
                        </a>
                        <?php endif; ?>
                    </div>

                    <div style="margin-top: 30px; text-align: center;">
                        <a href="javascript:history.back()" style="color: #94a3b8; text-decoration: none; font-size: 0.9rem; font-weight: 600;">
                            <i class="fas fa-arrow-left"></i> Kembali ke Jelajah
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script src="script.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            loadComments(<?= $item['id'] ?>);
        });
    </script>
</body>
</html>
