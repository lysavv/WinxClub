<?php
include 'db_config.php';
session_start();

if (!isset($_SESSION['role']) || ($_SESSION['role'] != 'owner' && $_SESSION['role'] != 'admin')) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// 1. Get the business linked to this owner
$biz_sql = "SELECT * FROM items WHERE owner_id = '$user_id' LIMIT 1";
$biz_res = mysqli_query($conn, $biz_sql);
$biz = mysqli_fetch_assoc($biz_res);

// If owner has no linked business yet
if (!$biz) {
    echo "Akun Anda belum dikaitkan dengan tempat wisata/kuliner apapun. Silakan hubungi Admin.";
    exit();
}

$biz_id = $biz['id'];

// 2. Handle Add New Sub-Item (Menu/Price)
if (isset($_POST['add_sub_item'])) {
    $name = mysqli_real_escape_string($conn, $_POST['sub_name']);
    $price = mysqli_real_escape_string($conn, $_POST['sub_price']);
    $image_path = "";

    // Handle File Upload
    if (isset($_FILES['sub_image']) && $_FILES['sub_image']['error'] == 0) {
        $target_dir = "uploads/";
        $file_ext = pathinfo($_FILES["sub_image"]["name"], PATHINFO_EXTENSION);
        $new_filename = "sub_" . time() . "_" . uniqid() . "." . $file_ext;
        $target_file = $target_dir . $new_filename;

        if (move_uploaded_file($_FILES["sub_image"]["tmp_name"], $target_file)) {
            $image_path = $target_file;
        }
    }
    
    $sql = "INSERT INTO business_items (parent_item_id, name, price, image_url) VALUES ($biz_id, '$name', '$price', '$image_path')";
    mysqli_query($conn, $sql);
    header("Location: owner_dashboard.php?msg=added");
    exit();
}

// 3. Handle Delete Sub-Item
if (isset($_GET['delete_sub'])) {
    $del_id = (int)$_GET['delete_sub'];

    // Delete image file if exists
    $file_check = mysqli_query($conn, "SELECT image_url FROM business_items WHERE id = $del_id");
    if ($row = mysqli_fetch_assoc($file_check)) {
        if (!empty($row['image_url']) && file_exists($row['image_url'])) {
            unlink($row['image_url']);
        }
    }

    mysqli_query($conn, "DELETE FROM business_items WHERE id = $del_id AND parent_item_id = $biz_id");
    header("Location: owner_dashboard.php?msg=deleted");
    exit();
}

// 4. Handle Update Business Info
if (isset($_POST['update_biz_info'])) {
    $desc = mysqli_real_escape_string($conn, $_POST['description']);
    $location = mysqli_real_escape_string($conn, $_POST['location']);
    $hours = mysqli_real_escape_string($conn, $_POST['hours']);
    $wa = mysqli_real_escape_string($conn, $_POST['whatsapp']);
    
    // Status & Announcement (Optional fields)
    $status = mysqli_real_escape_string($conn, $_POST['status'] ?? 'Buka');
    $announcement = mysqli_real_escape_string($conn, $_POST['announcement'] ?? '');
    
    $sql = "UPDATE items SET 
            description = '$desc', 
            location = '$location', 
            hours = '$hours', 
            whatsapp = '$wa',
            status = '$status',
            announcement = '$announcement'
            WHERE id = $biz_id";
            
    mysqli_query($conn, $sql);
    header("Location: owner_dashboard.php?msg=updated");
    exit();
}

// 5. Dynamic Labels based on Category
$label_title = "Daftar Harga & Menu";
$label_item = "Nama Item";
$label_price = "Harga";
$label_stats = "Layanan/Menu";

if ($biz['category'] == 'penginapan') {
    $label_title = "Daftar Kamar & Fasilitas";
    $label_item = "Tipe Kamar / Layanan";
    $label_price = "Biaya per Malam";
    $label_stats = "Tipe Kamar";
} elseif ($biz['category'] == 'wisata') {
    $label_title = "Daftar Tiket & Wahana";
    $label_item = "Jenis Tiket / Wahana";
    $label_price = "Harga Tiket";
    $label_stats = "Tiket/Wahana";
}

// 6. Stats
$comment_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM comments WHERE item_id = $biz_id"))['count'];
$price_item_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM business_items WHERE parent_item_id = $biz_id"))['count'];

// 7. Fetch all Sub-Items for this business
$sub_items_res = mysqli_query($conn, "SELECT * FROM business_items WHERE parent_item_id = $biz_id ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ringkasan Bisnis - <?= $biz['name'] ?></title>
    <link rel="stylesheet" href="style.css?v=1.6">
    <style>
        .page-container { padding: 40px 60px; }
        .biz-info-header { margin-bottom: 40px; }
        
        .stats-row { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-bottom: 40px; }
        .stat-card { 
            background: white; padding: 25px; border-radius: 20px; 
            box-shadow: 0 10px 30px rgba(0,0,0,0.02); border: 1px solid rgba(0,0,0,0.03);
            text-align: center;
        }
        .stat-card h4 { color: #94a3b8; font-size: 0.65rem; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 8px; }
        .stat-card p { font-size: 1.8rem; font-weight: 800; color: var(--primary); }

        .dashboard-grid { display: grid; grid-template-columns: 1.5fr 1fr; gap: 30px; }
        
        .card-panel { 
            background: white; padding: 35px; border-radius: 30px; 
            box-shadow: 0 15px 40px rgba(0,0,0,0.02); border: 1px solid rgba(0,0,0,0.03);
            margin-bottom: 30px;
        }
        .panel-title { font-family: 'Playfair Display'; font-size: 1.5rem; margin-bottom: 25px; display: flex; align-items: center; gap: 12px; }
        
        .form-row { display: grid; gap: 20px; }
        .input-wrap { display: flex; flex-direction: column; gap: 8px; }
        .input-wrap label { font-size: 0.7rem; font-weight: 800; color: #94a3b8; text-transform: uppercase; letter-spacing: 1px; }
        .minimal-input { 
            width: 100%; padding: 14px 18px; border-radius: 12px; border: 1.5px solid #f1f5f9; 
            background: #f8fafc; font-family: inherit; font-weight: 600; color: var(--primary); transition: 0.3s;
        }
        .minimal-input:focus { border-color: var(--accent); outline: none; background: white; }
        
        .btn-action { 
            background: var(--primary); color: white; border: none; padding: 14px 25px; 
            border-radius: 12px; font-weight: 800; cursor: pointer; transition: 0.3s;
            width: 100%; margin-top: 10px;
        }
        .btn-action:hover { background: var(--accent); }

        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th { background: #f8fafc; padding: 15px; text-align: left; font-size: 0.7rem; text-transform: uppercase; color: #94a3b8; border-radius: 10px 0 0 10px; }
        th:last-child { border-radius: 0 10px 10px 0; }
        td { padding: 15px; border-bottom: 1px solid #f1f5f9; font-size: 0.9rem; color: var(--primary); }
    </style>
</head>
<body style="background: #f8fafc;">
    <?php include 'sidebar.php'; ?>

    <main class="content-area-with-sidebar">
        <div class="page-container">
            <div class="biz-info-header">
                <p style="color: var(--accent); font-weight: 800; text-transform: uppercase; letter-spacing: 2px; font-size: 0.7rem;">Ringkasan Bisnis</p>
                <h1 style="font-family: 'Playfair Display'; font-size: 3rem; margin-bottom: 10px;"><?= $biz['name'] ?><span>.</span></h1>
                <p style="color: #64748b;">Kelola informasi dan layanan bisnis Anda secara real-time.</p>
            </div>

            <!-- Stats Overview -->
            <div class="stats-row">
                <div class="stat-card">
                    <h4>Status Bisnis</h4>
                    <p style="color: #059669; font-size: 1.4rem;"><?= $biz['status'] ?? 'Buka' ?></p>
                </div>
                <div class="stat-card">
                    <h4><?= $label_stats ?></h4>
                    <p><?= $price_item_count ?></p>
                </div>
                <div class="stat-card">
                    <h4>Komentar</h4>
                    <p><?= $comment_count ?></p>
                </div>
            </div>

            <div class="dashboard-grid">
                <!-- Left Column: Manage Prices -->
                <div class="left-col">
                    <div class="card-panel" id="manage">
                        <h3 class="panel-title"><i class="fas fa-tags" style="color: var(--accent);"></i> <?= $label_title ?></h3>
                        
                        <form action="owner_dashboard.php" method="POST" enctype="multipart/form-data" style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 30px;">
                            <div class="input-wrap">
                                <label><?= $label_item ?></label>
                                <input type="text" name="sub_name" class="minimal-input" placeholder="Isi di sini..." required>
                            </div>
                            <div class="input-wrap">
                                <label><?= $label_price ?></label>
                                <input type="text" name="sub_price" class="minimal-input" placeholder="Rp ..." required>
                            </div>
                            <div class="input-wrap" style="grid-column: span 2;">
                                <label>Upload Foto (Real Pict)</label>
                                <input type="file" name="sub_image" class="minimal-input" style="padding: 10px;" accept="image/*">
                            </div>
                            <button type="submit" name="add_sub_item" class="btn-action" style="grid-column: span 2; margin-top:0;">
                                <i class="fas fa-plus"></i> Tambah Item
                            </button>
                        </form>

                        <table>
                            <thead>
                                <tr>
                                    <th>Preview</th>
                                    <th><?= $label_item ?></th>
                                    <th><?= $label_price ?></th>
                                    <th style="text-align: right;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while($row = mysqli_fetch_assoc($sub_items_res)): ?>
                                <tr>
                                    <td>
                                        <?php if($row['image_url']): ?>
                                            <img src="<?= $row['image_url'] ?>" style="width: 60px; height: 60px; object-fit: cover; border-radius: 10px;">
                                        <?php else: ?>
                                            <div style="width: 60px; height: 60px; background: #f1f5f9; border-radius: 10px; display: flex; align-items: center; justify-content: center; color: #94a3b8;"><i class="fas fa-image"></i></div>
                                        <?php endif; ?>
                                    </td>
                                    <td style="font-weight: 700;"><?= htmlspecialchars($row['name']) ?></td>
                                    <td style="font-weight: 800; color: #166534;"><?= htmlspecialchars($row['price']) ?></td>
                                    <td style="text-align: right;">
                                        <a href="owner_dashboard.php?delete_sub=<?= $row['id'] ?>" onclick="return confirm('Hapus?')" style="color: #ef4444; text-decoration: none; font-weight: 700;"><i class="fas fa-trash"></i></a>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Right Column: Business Info -->
                <div class="right-col">
                    <div class="card-panel" id="bullhorn">
                        <h3 class="panel-title"><i class="fas fa-bullhorn" style="color: #f59e0b;"></i> Status & Pengumungan</h3>
                        <form action="owner_dashboard.php" method="POST" class="form-row">
                            <div class="input-wrap">
                                <label>Status Operasional</label>
                                <select name="status" class="minimal-input">
                                    <option value="Buka" <?= $biz['status'] == 'Buka' ? 'selected' : '' ?>>Buka Normal</option>
                                    <option value="Tutup Sementara" <?= $biz['status'] == 'Tutup Sementara' ? 'selected' : '' ?>>Tutup Sementara</option>
                                    <option value="Dalam Perbaikan" <?= $biz['status'] == 'Dalam Perbaikan' ? 'selected' : '' ?>>Dalam Perbaikan</option>
                                    <option value="Libur" <?= $biz['status'] == 'Libur' ? 'selected' : '' ?>>Sedang Libur</option>
                                </select>
                            </div>
                            <div class="input-wrap">
                                <label>Pengumungan Khusus</label>
                                <textarea name="announcement" class="minimal-input" style="height: 80px; resize: none;" placeholder="Contoh: Diskon 20% khusus hari ini! atau Sedang ada renovasi di area parkir."><?= htmlspecialchars($biz['announcement'] ?? '') ?></textarea>
                            </div>
                            
                            <!-- Hidden fields to preserve other info -->
                            <input type="hidden" name="description" value="<?= htmlspecialchars($biz['description']) ?>">
                            <input type="hidden" name="location" value="<?= htmlspecialchars($biz['location']) ?>">
                            <input type="hidden" name="hours" value="<?= htmlspecialchars($biz['hours']) ?>">
                            <input type="hidden" name="whatsapp" value="<?= htmlspecialchars($biz['whatsapp']) ?>">
                            
                            <button type="submit" name="update_biz_info" class="btn-action" style="background: #f59e0b;">Update Pengumungan</button>
                        </form>
                    </div>

                    <div class="card-panel">
                        <h3 class="panel-title"><i class="fas fa-edit" style="color: var(--accent);"></i> Edit Info Bisnis</h3>
                        <form action="owner_dashboard.php" method="POST" class="form-row">
                            <div class="input-wrap">
                                <label>Deskripsi Singkat</label>
                                <textarea name="description" class="minimal-input" style="height: 100px; resize: none;"><?= htmlspecialchars($biz['description']) ?></textarea>
                            </div>
                            <div class="input-wrap">
                                <label>Alamat Lengkap</label>
                                <input type="text" name="location" class="minimal-input" value="<?= htmlspecialchars($biz['location']) ?>">
                            </div>
                            <div class="input-wrap">
                                <label>Jam Operasional</label>
                                <input type="text" name="hours" class="minimal-input" value="<?= htmlspecialchars($biz['hours']) ?>">
                            </div>
                            <div class="input-wrap">
                                <label>WhatsApp (628xxx)</label>
                                <input type="text" name="whatsapp" class="minimal-input" value="<?= htmlspecialchars($biz['whatsapp']) ?>">
                            </div>
                            <button type="submit" name="update_biz_info" class="btn-action">Simpan Perubahan</button>
                        </form>
                    </div>

                    <a href="beranda.php" target="_blank" class="card-panel" style="display: block; text-decoration: none; text-align: center; background: var(--nature); color: white; padding: 20px;">
                        <i class="fas fa-external-link-alt"></i> Lihat Tampilan Live
                    </a>
                </div>
            </div>
        </div>
    </main>
</body>
</html>
