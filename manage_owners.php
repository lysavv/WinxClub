<?php
include 'db_config.php';
session_start();

// Only Admins can see this page (Government-like oversight)
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: dashboard.php");
    exit();
}

$error_msg = "";
$success_msg = "";

// 1. Handle Add New Owner
if (isset($_POST['add_owner'])) {
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $item_id = (int)$_POST['item_id'];

    // Check duplicate
    $check = mysqli_query($conn, "SELECT id FROM users WHERE username='$username' OR email='$email'");
    if (mysqli_num_rows($check) > 0) {
        $error_msg = "Username atau Email sudah terdaftar!";
    } else {
        $sql = "INSERT INTO users (nama_lengkap, username, email, password, role) VALUES ('$nama', '$username', '$email', '$password', 'owner')";
        if (mysqli_query($conn, $sql)) {
            $new_owner_id = mysqli_insert_id($conn);
            
            // Link the selected item to this new owner
            if ($item_id > 0) {
                mysqli_query($conn, "UPDATE items SET owner_id = $new_owner_id WHERE id = $item_id");
                $success_msg = "Akun Owner berhasil dibuat dan dikaitkan dengan tempat!";
            } else {
                $success_msg = "Akun Owner berhasil dibuat (Tanpa kaitan tempat)!";
            }
        } else {
            $error_msg = "Gagal membuat akun: " . mysqli_error($conn);
        }
    }
}

// 2. Handle Delete Owner
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    mysqli_query($conn, "DELETE FROM users WHERE id = $id AND role = 'owner'");
    header("Location: manage_owners.php?msg=deleted");
    exit();
}

// Fetch all Owners
$sql = "SELECT users.*, 
        (SELECT COUNT(*) FROM items WHERE owner_id = users.id) as total_items 
        FROM users WHERE role = 'owner' ORDER BY created_at DESC";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Akun Owner - Jejak Negeri</title>
    <link rel="stylesheet" href="style.css?v=1.4">
    <style>
        .form-container { 
            background: white; padding: 40px; border-radius: 35px; margin-bottom: 50px;
            box-shadow: 0 20px 50px rgba(0,0,0,0.02); border: 1px solid rgba(0,0,0,0.03);
        }
        .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        .input-wrap { margin-bottom: 20px; }
        .input-wrap label { display: block; font-size: 0.7rem; font-weight: 700; color: #94a3b8; margin-bottom: 8px; text-transform: uppercase; }
        .dash-input { 
            width: 100%; padding: 14px 18px; border-radius: 12px; border: 1.5px solid #f1f5f9; 
            background: #f8fafc; font-family: inherit; transition: 0.3s;
        }
        .dash-input:focus { border-color: var(--accent); outline: none; background: white; }

        .table-container { background: white; border-radius: 30px; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.02); }
        table { width: 100%; border-collapse: collapse; }
        th { background: #f8fafc; padding: 20px; text-align: left; font-size: 0.75rem; text-transform: uppercase; color: #94a3b8; }
        td { padding: 20px; border-bottom: 1px solid #f1f5f9; font-size: 0.9rem; }
        
        .alert { padding: 15px 20px; border-radius: 15px; margin-bottom: 30px; font-weight: 700; font-size: 0.9rem; }
        .alert-error { background: #fef2f2; color: #ef4444; }
        .alert-success { background: #f0fdf4; color: #10b981; }
    </style>
</head>
<body style="background: #f8fafc;">
    <?php include 'sidebar.php'; ?>

    <main class="content-area-with-sidebar" style="padding: 60px 80px;">
        <div class="dash-header" style="margin-bottom: 50px;">
            <h1 style="font-family: 'Playfair Display'; font-size: 3rem; margin-bottom: 10px;">Manajemen Owner<span>.</span></h1>
            <p style="color: #64748b;">Daftarkan dan pantau akun pemilik bisnis (Wisata, Kuliner, Akomodasi).</p>
        </div>

        <?php if($error_msg): ?> <div class="alert alert-error"><?= $error_msg ?></div> <?php endif; ?>
        <?php if($success_msg): ?> <div class="alert alert-success"><?= $success_msg ?></div> <?php endif; ?>

        <!-- Form Tambah Owner Baru -->
        <div class="form-container">
            <h3 style="font-family: 'Playfair Display'; font-size: 1.5rem; margin-bottom: 25px;">Registrasi Akun Owner Baru</h3>
            <form action="manage_owners.php" method="POST">
                <div class="form-grid">
                    <div class="input-wrap">
                        <label>Nama Lengkap</label>
                        <input type="text" name="nama" class="dash-input" placeholder="Nama Pemilik" required>
                    </div>
                    <div class="input-wrap">
                        <label>Email Bisnis</label>
                        <input type="email" name="email" class="dash-input" placeholder="owner@gmail.com" required>
                    </div>
                    <div class="input-wrap">
                        <label>Username</label>
                        <input type="text" name="username" class="dash-input" placeholder="username_bisnis" required>
                    </div>
                    <div class="input-wrap">
                        <label>Password Akun</label>
                        <input type="password" name="password" class="dash-input" placeholder="••••••••" required>
                    </div>
                    <div class="input-wrap" style="grid-column: span 2;">
                        <label>Pilih Tempat yang Dikelola (Wisata/Kuliner/Akomodasi)</label>
                        <select name="item_id" class="dash-input" required>
                            <option value="0">-- Pilih Tempat --</option>
                            <?php 
                            $items_res = mysqli_query($conn, "SELECT id, name, category FROM items ORDER BY category ASC, name ASC");
                            while($i = mysqli_fetch_assoc($items_res)): 
                            ?>
                                <option value="<?= $i['id'] ?>">[<?= ucfirst($i['category']) ?>] <?= $i['name'] ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                </div>
                <button type="submit" name="add_owner" class="btn-premium" style="width: 100%; border: none; background: var(--primary); color: white; margin-top: 10px;">
                    Buat Akun Owner & Berikan Akses Kelola
                </button>
            </form>
        </div>

        <!-- Tabel Daftar Owner -->
        <h3 style="font-family: 'Playfair Display'; font-size: 1.5rem; margin-bottom: 20px;">Daftar Pengelola Aktif</h3>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Nama Lengkap</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Jumlah Konten</th>
                        <th style="text-align: right;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td style="font-weight: 700; color: var(--primary);"><?= $row['nama_lengkap'] ?></td>
                        <td><?= $row['username'] ?></td>
                        <td style="color: #64748b;"><?= $row['email'] ?></td>
                        <td><span style="background: #e0f2fe; color: #0369a1; padding: 4px 12px; border-radius: 20px; font-weight: 700; font-size: 0.75rem;"><?= $row['total_items'] ?> Tempat</span></td>
                        <td style="text-align: right;">
                            <a href="manage_owners.php?delete=<?= $row['id'] ?>" onclick="return confirm('Hapus akun owner ini? Semua datanya akan ikut terhapus!')" style="color: #ef4444; text-decoration: none; font-weight: 700;">Hapus Akun</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                    <?php if(mysqli_num_rows($result) == 0): ?>
                    <tr>
                        <td colspan="5" style="text-align: center; padding: 40px; color: #94a3b8;">Belum ada owner yang terdaftar.</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>
</body>
</html>
