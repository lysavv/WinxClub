<?php
include 'db_config.php';
session_start();

if (!isset($_SESSION['role']) || ($_SESSION['role'] != 'owner' && $_SESSION['role'] != 'admin')) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];

if (!isset($_GET['id'])) {
    header("Location: owner_dashboard.php");
    exit();
}

$item_id = (int)$_GET['id'];

// Security: Owners can only edit their own items. Admins can edit anything.
if ($role == 'admin') {
    $sql = "SELECT * FROM items WHERE id = $item_id";
} else {
    $sql = "SELECT * FROM items WHERE id = $item_id AND owner_id = $user_id";
}

$res = mysqli_query($conn, $sql);
$item = mysqli_fetch_assoc($res);

if (!$item) {
    echo "Akses ditolak atau data tidak ditemukan.";
    exit();
}

// Handle Update
if (isset($_POST['update_item'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $desc = mysqli_real_escape_string($conn, $_POST['desc']);
    $price = mysqli_real_escape_string($conn, $_POST['price']);
    $hours = mysqli_real_escape_string($conn, $_POST['hours']);
    $location = mysqli_real_escape_string($conn, $_POST['location']);
    $sosmed = mysqli_real_escape_string($conn, $_POST['sosmed']);
    $maps = mysqli_real_escape_string($conn, $_POST['maps']);
    $whatsapp = mysqli_real_escape_string($conn, $_POST['whatsapp']);
    
    $image_path = $item['image_url']; // Default to old image
    
    // Handle New File Upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $target_dir = "uploads/";
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        $file_ext = pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION);
        $new_filename = "article_" . time() . "_" . uniqid() . "." . $file_ext;
        $target_file = $target_dir . $new_filename;

        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            // Delete old file if it exists and is local
            if (!empty($item['image_url']) && file_exists($item['image_url'])) {
                unlink($item['image_url']);
            }
            $image_path = $target_file;
        }
    }
    
    // New fields
    $menu = mysqli_real_escape_string($conn, $_POST['menu'] ?? '');
    $room_types = mysqli_real_escape_string($conn, $_POST['room_types'] ?? '');
    $facilities = mysqli_real_escape_string($conn, $_POST['facilities'] ?? '');
    $status = mysqli_real_escape_string($conn, $_POST['status'] ?? 'Buka');

    $update_sql = "UPDATE items SET 
        name='$name', description='$desc', price='$price', hours='$hours', 
        location='$location', sosmed='$sosmed', maps_url='$maps', 
        image_url='$image_path', whatsapp='$whatsapp',
        menu='$menu', room_types='$room_types', facilities='$facilities', status='$status'
        WHERE id = $item_id";
    
    if (mysqli_query($conn, $update_sql)) {
        header("Location: dashboard.php?msg=updated");
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Bisnis - Jejak Negeri</title>
    <link rel="stylesheet" href="style.css?v=1.4">
    <style>
        .form-container { background: white; padding: 50px; border-radius: 40px; box-shadow: 0 20px 50px rgba(0,0,0,0.03); max-width: 900px; margin: 0 auto; }
        .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        .input-wrap { margin-bottom: 25px; }
        .input-wrap label { display: block; font-size: 0.75rem; font-weight: 700; color: #94a3b8; margin-bottom: 8px; text-transform: uppercase; }
        .dash-input { width: 100%; padding: 15px; border-radius: 15px; border: 1.5px solid #f1f5f9; background: #f8fafc; font-family: inherit; }
    </style>
</head>
<body style="background: #f8fafc;">
    <?php include 'sidebar.php'; ?>
    <main class="content-area-with-sidebar" style="padding: 60px;">
        <div class="form-container">
            <h1 style="font-family: 'Playfair Display'; margin-bottom: 10px;">Edit Informasi Bisnis<span>.</span></h1>
            <p style="color: #64748b; margin-bottom: 40px;">Pastikan data yang kamu masukkan akurat untuk menarik pengunjung.</p>
            
            <form method="POST" enctype="multipart/form-data">
                <div class="form-grid">
                    <div class="input-wrap">
                        <label>Nama Tempat</label>
                        <input type="text" name="name" class="dash-input" value="<?= $item['name'] ?>" required>
                    </div>
                    <div class="input-wrap">
                        <label>Harga / Tiket</label>
                        <input type="text" name="price" class="dash-input" value="<?= $item['price'] ?>">
                    </div>
                </div>
                <div class="input-wrap">
                    <label>Deskripsi</label>
                    <textarea name="desc" class="dash-input" style="height: 150px; resize: none;"><?= $item['description'] ?></textarea>
                </div>
                <div class="form-grid">
                    <div class="input-wrap">
                        <label>Jam Buka</label>
                        <input type="text" name="hours" class="dash-input" value="<?= $item['hours'] ?>">
                    </div>
                    <div class="input-wrap">
                        <label>WhatsApp</label>
                        <input type="text" name="whatsapp" class="dash-input" value="<?= $item['whatsapp'] ?>">
                    </div>
                    <div class="input-wrap">
                        <label>Instagram / Sosmed</label>
                        <input type="text" name="sosmed" class="dash-input" value="<?= $item['sosmed'] ?>">
                    </div>
                    <div class="input-wrap">
                        <label>Lokasi</label>
                        <input type="text" name="location" class="dash-input" value="<?= $item['location'] ?>">
                    </div>
                </div>

                <!-- Category Specific Fields -->
                <?php if($item['category'] == 'kuliner'): ?>
                <div class="input-wrap">
                    <label>Menu (Daftar Makanan & Harga)</label>
                    <textarea name="menu" class="dash-input" style="height: 100px;"><?= $item['menu'] ?></textarea>
                </div>
                <?php endif; ?>

                <?php if($item['category'] == 'penginapan'): ?>
                <div class="form-grid">
                    <div class="input-wrap">
                        <label>Tipe Kamar</label>
                        <textarea name="room_types" class="dash-input" style="height: 100px;"><?= $item['room_types'] ?></textarea>
                    </div>
                    <div class="input-wrap">
                        <label>Fasilitas</label>
                        <textarea name="facilities" class="dash-input" style="height: 100px;"><?= $item['facilities'] ?></textarea>
                    </div>
                </div>
                <?php endif; ?>

                <?php if($item['category'] == 'wisata'): ?>
                <div class="input-wrap">
                    <label>Status Operasional</label>
                    <select name="status" class="dash-input">
                        <option value="Buka" <?= $item['status'] == 'Buka' ? 'selected' : '' ?>>Buka Normal</option>
                        <option value="Tutup Sementara" <?= $item['status'] == 'Tutup Sementara' ? 'selected' : '' ?>>Tutup Sementara</option>
                        <option value="Dalam Perbaikan" <?= $item['status'] == 'Dalam Perbaikan' ? 'selected' : '' ?>>Dalam Perbaikan</option>
                        <option value="Libur" <?= $item['status'] == 'Libur' ? 'selected' : '' ?>>Sedang Libur</option>
                    </select>
                </div>
                <?php endif; ?>

                <div class="input-wrap">
                    <label>Foto Thumbnail (Biarkan kosong jika tidak ingin ganti)</label>
                    <input type="file" name="image" class="dash-input" accept="image/*">
                    <?php if(!empty($item['image_url'])): ?>
                        <p style="font-size: 0.7rem; color: #94a3b8; margin-top: 5px;">File saat ini: <?= $item['image_url'] ?></p>
                    <?php endif; ?>
                </div>
                <div class="input-wrap">
                    <label>Google Maps URL</label>
                    <input type="text" name="maps" class="dash-input" value="<?= $item['maps_url'] ?>">
                </div>
                <button type="submit" name="update_item" class="btn-premium" style="width: 100%; border: none; background: var(--primary); color: white;">Simpan Perubahan</button>
            </form>
        </div>
    </main>
</body>
</html>
