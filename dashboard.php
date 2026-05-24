<?php
include 'db_config.php';
session_start();

// Protection
if (!isset($_SESSION['role']) || ($_SESSION['role'] != 'admin' && $_SESSION['role'] != 'owner')) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];

// 1. Handle Delete
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    if ($role == 'admin') {
        mysqli_query($conn, "DELETE FROM items WHERE id = $id");
    } else {
        mysqli_query($conn, "DELETE FROM items WHERE id = $id AND owner_id = $user_id");
    }
    header("Location: dashboard.php?msg=deleted");
}

// 2. Handle Add Item
if (isset($_POST['add_item'])) {
    $category = $_POST['category'];
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $desc = mysqli_real_escape_string($conn, $_POST['desc']);
    $price = mysqli_real_escape_string($conn, $_POST['price']);
    $hours = mysqli_real_escape_string($conn, $_POST['hours']);
    $location = mysqli_real_escape_string($conn, $_POST['location']);
    $sosmed = mysqli_real_escape_string($conn, $_POST['sosmed'] ?? '');
    $maps = mysqli_real_escape_string($conn, $_POST['maps']);
    $whatsapp = mysqli_real_escape_string($conn, $_POST['whatsapp']);
    
    $image_path = "";
    // Handle File Upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $target_dir = "uploads/";
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        $file_ext = pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION);
        $new_filename = "article_" . time() . "_" . uniqid() . "." . $file_ext;
        $target_file = $target_dir . $new_filename;

        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            $image_path = $target_file;
        }
    }

    // New fields
    $menu = mysqli_real_escape_string($conn, $_POST['menu'] ?? '');
    $room_types = mysqli_real_escape_string($conn, $_POST['room_types'] ?? '');
    $facilities = mysqli_real_escape_string($conn, $_POST['facilities'] ?? '');
    $status = mysqli_real_escape_string($conn, $_POST['status'] ?? 'Buka');

    $sql = "INSERT INTO items (category, name, description, price, hours, location, sosmed, maps_url, image_url, whatsapp, menu, room_types, facilities, status, owner_id) 
            VALUES ('$category', '$name', '$desc', '$price', '$hours', '$location', '$sosmed', '$maps', '$image_path', '$whatsapp', '$menu', '$room_types', '$facilities', '$status', '$user_id')";
    mysqli_query($conn, $sql);
    header("Location: dashboard.php?msg=added");
}

// 3. Stats
$total_items = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM items" . ($role == 'owner' ? " WHERE owner_id=$user_id" : "")))['count'];
$total_users = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM users"))['count'];
$total_comments = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM comments"))['count'];

// 4. Fetch Items
if ($role == 'admin') {
    $sql = "SELECT items.*, users.username as owner_name FROM items LEFT JOIN users ON items.owner_id = users.id ORDER BY created_at DESC";
} else {
    $sql = "SELECT * FROM items WHERE owner_id = '$user_id' ORDER BY created_at DESC";
}
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Management Suite - Jejak Negeri</title>
    <link rel="stylesheet" href="style.css?v=1.4">
    <style>
        .dash-header { margin-bottom: 50px; }
        .stats-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 25px; margin-bottom: 60px; }
        .stat-card { 
            background: white; padding: 30px; border-radius: 25px; 
            box-shadow: 0 10px 30px rgba(0,0,0,0.02); border: 1px solid rgba(0,0,0,0.03);
        }
        .stat-card h4 { color: #94a3b8; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 10px; }
        .stat-card p { font-size: 2.2rem; font-weight: 800; color: var(--primary); }

        .form-container { 
            background: white; padding: 45px; border-radius: 35px; margin-bottom: 60px;
            box-shadow: 0 20px 50px rgba(0,0,0,0.03);
        }
        .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        .input-wrap { margin-bottom: 20px; }
        .input-wrap label { display: block; font-size: 0.75rem; font-weight: 700; color: #94a3b8; margin-bottom: 8px; text-transform: uppercase; }
        .dash-input { 
            width: 100%; padding: 14px 18px; border-radius: 12px; border: 1.5px solid #f1f5f9; 
            background: #f8fafc; font-family: inherit; transition: 0.3s;
        }
        .dash-input:focus { border-color: var(--accent); outline: none; background: white; }

        .table-container { background: white; border-radius: 30px; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.02); }
        table { width: 100%; border-collapse: collapse; }
        th { background: #f8fafc; padding: 20px; text-align: left; font-size: 0.75rem; text-transform: uppercase; color: #94a3b8; }
        td { padding: 20px; border-bottom: 1px solid #f1f5f9; font-size: 0.9rem; }
        .badge { padding: 4px 10px; border-radius: 6px; font-size: 0.7rem; font-weight: 700; }
        .badge-wisata { background: #e0f2fe; color: #0369a1; }
        .badge-kuliner { background: #fef3c7; color: #92400e; }
        .badge-penginapan { background: #dcfce7; color: #166534; }
    </style>
</head>
<body style="background: #f8fafc;">
    <?php include 'sidebar.php'; ?>

    <main class="content-area-with-sidebar" style="padding: 60px 80px;">
        <div class="dash-header">
            <h1 style="font-family: 'Playfair Display'; font-size: 3rem; margin-bottom: 10px;">Management Suite<span>.</span></h1>
            <p style="color: #64748b;">Selamat datang, <strong><?= $_SESSION['username'] ?></strong>. Kelola ekosistem pariwisata Dieng di sini.</p>
        </div>

        <!-- Stats Overview -->
        <div class="stats-grid">
            <div class="stat-card">
                <h4>Total Konten</h4>
                <p><?= $total_items ?></p>
            </div>
            <div class="stat-card">
                <h4>Pengguna Terdaftar</h4>
                <p><?= $total_users ?></p>
            </div>
            <div class="stat-card">
                <h4>Komentar Masuk</h4>
                <p><?= $total_comments ?></p>
            </div>
        </div>

        <!-- Add New Item Form -->
        <div class="form-container">
            <h3 style="font-family: 'Playfair Display'; font-size: 1.8rem; margin-bottom: 30px;">Tambah Konten Baru</h3>
            <form action="dashboard.php" method="POST" enctype="multipart/form-data">
                <div class="form-grid">
                    <div class="input-wrap">
                        <label>Kategori</label>
                        <select name="category" class="dash-input" required>
                            <option value="wisata">Wisata Alam</option>
                            <option value="kuliner">Kuliner Khas</option>
                            <option value="penginapan">Akomodasi</option>
                        </select>
                    </div>
                    <div class="input-wrap">
                        <label>Nama Tempat</label>
                        <input type="text" name="name" class="dash-input" placeholder="Contoh: Kawah Sikidang" required>
                    </div>
                </div>

                <div class="input-wrap">
                    <label>Deskripsi Lengkap</label>
                    <textarea name="desc" class="dash-input" style="height: 120px; resize: none;" placeholder="Ceritakan keindahan tempat ini..." required></textarea>
                </div>

                <div class="form-grid">
                    <div class="input-wrap">
                        <label>Harga / Tiket</label>
                        <input type="text" name="price" class="dash-input" placeholder="Rp 15.000">
                    </div>
                    <div class="input-wrap">
                        <label>Jam Operasional</label>
                        <input type="text" name="hours" class="dash-input" placeholder="08:00 - 17.00">
                    </div>
                    <div class="input-wrap">
                        <label>Lokasi (Alamat)</label>
                        <input type="text" name="location" class="dash-input" placeholder="Dieng Kulon, Banjarnegara">
                    </div>
                    <div class="input-wrap">
                        <label>WhatsApp (Format: 628xxx)</label>
                        <input type="text" name="whatsapp" class="dash-input" placeholder="628123456789">
                    </div>
                    <div class="input-wrap">
                        <label>Upload Foto Thumbnail</label>
                        <input type="file" name="image" class="dash-input" accept="image/*" required>
                    </div>
                    <div class="input-wrap">
                        <label>URL Google Maps</label>
                        <input type="text" name="maps" class="dash-input" placeholder="https://goo.gl/maps/...">
                    </div>
                </div>

                <!-- Category Specific Fields -->
                <div id="kuliner-fields" class="extra-fields" style="display:none;">
                    <div class="input-wrap">
                        <label>Menu (Daftar Makanan & Harga)</label>
                        <textarea name="menu" class="dash-input" style="height: 100px;" placeholder="Contoh: Mie Ongklok - 15k, Sate Sapi - 25k..."></textarea>
                    </div>
                </div>

                <div id="penginapan-fields" class="extra-fields" style="display:none;">
                    <div class="form-grid">
                        <div class="input-wrap">
                            <label>Tipe Kamar</label>
                            <textarea name="room_types" class="dash-input" style="height: 80px;" placeholder="Contoh: Deluxe Room, Family Suite..."></textarea>
                        </div>
                        <div class="input-wrap">
                            <label>Fasilitas</label>
                            <textarea name="facilities" class="dash-input" style="height: 80px;" placeholder="Contoh: Wi-Fi, Kolam Renang, Sarapan..."></textarea>
                        </div>
                    </div>
                </div>

                <div id="wisata-fields" class="extra-fields" style="display:none;">
                    <div class="input-wrap">
                        <label>Status Operasional Wisata</label>
                        <select name="status" class="dash-input">
                            <option value="Buka">Buka Normal</option>
                            <option value="Tutup Sementara">Tutup Sementara</option>
                            <option value="Dalam Perbaikan">Dalam Perbaikan (Maintenance)</option>
                            <option value="Libur">Sedang Libur</option>
                        </select>
                    </div>
                </div>

                <button type="submit" name="add_item" class="btn-premium" style="width: 100%; border: none; background: var(--primary); color: white; margin-top: 20px;">
                    Publikasikan Sekarang
                </button>
            </form>
        </div>

        <script>
            const categorySelect = document.querySelector('select[name="category"]');
            const kulinerFields = document.getElementById('kuliner-fields');
            const penginapanFields = document.getElementById('penginapan-fields');
            const wisataFields = document.getElementById('wisata-fields');

            categorySelect.addEventListener('change', function() {
                // Hide all first
                kulinerFields.style.display = 'none';
                penginapanFields.style.display = 'none';
                wisataFields.style.display = 'none';

                // Show based on selection
                if (this.value === 'kuliner') {
                    kulinerFields.style.display = 'block';
                } else if (this.value === 'penginapan') {
                    penginapanFields.style.display = 'block';
                } else if (this.value === 'wisata') {
                    wisataFields.style.display = 'block';
                }
            });
            // Trigger once on load
            categorySelect.dispatchEvent(new Event('change'));
        </script>

        <!-- Items Table -->
        <h3 style="font-family: 'Playfair Display'; font-size: 1.8rem; margin-bottom: 25px;">Daftar Konten Terbit</h3>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>Kategori</th>
                        <th>Lokasi</th>
                        <?php if($role == 'admin') echo "<th>Owner</th>"; ?>
                        <th style="text-align: right;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td style="font-weight: 700; color: var(--primary);"><?= $row['name'] ?></td>
                        <td><span class="badge badge-<?= $row['category'] ?>"><?= ucfirst($row['category']) ?></span></td>
                        <td style="color: #64748b;"><?= $row['location'] ?></td>
                        <?php if($role == 'admin'): ?>
                            <td style="font-weight: 600; color: var(--accent);"><?= $row['owner_name'] ?? 'System' ?></td>
                        <?php endif; ?>
                        <td style="text-align: right;">
                            <a href="edit_item.php?id=<?= $row['id'] ?>" style="color: var(--accent); text-decoration: none; font-weight: 700; margin-right: 15px;">Edit</a>
                            <a href="dashboard.php?delete=<?= $row['id'] ?>" onclick="return confirm('Yakin ingin menghapus?')" style="color: #ef4444; text-decoration: none; font-weight: 700;">Hapus</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                    <?php if(mysqli_num_rows($result) == 0): ?>
                    <tr>
                        <td colspan="5" style="text-align: center; padding: 40px; color: #94a3b8;">Belum ada konten yang Anda tambahkan.</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>
</body>
</html>