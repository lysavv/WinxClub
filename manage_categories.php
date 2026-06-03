<?php
include 'db_config.php';
session_start();

// Protection: Only Admin
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}

// 1. Handle Tambah Kategori
if (isset($_POST['add_category'])) {
    $name = mysqli_real_escape_string($conn, $_POST['category_name']);
    
    if (!empty($name)) {
        // Validasi Anti-Duplikasi (Pengecekan apakah kategori sudah ada)
        $check = mysqli_query($conn, "SELECT * FROM categories WHERE LOWER(name) = LOWER('$name')");
        if (mysqli_num_rows($check) > 0) {
            header("Location: manage_categories.php?msg=duplicate");
            exit();
        }

        $sql = "INSERT INTO categories (name) VALUES ('$name')";
        mysqli_query($conn, $sql);
    }
    header("Location: manage_categories.php?msg=added");
    exit();
}

// 2. Handle Hapus Kategori
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];

    // Ambil nama kategori yang mau dihapus untuk keperluan proteksi fallback
    $cat_check = mysqli_query($conn, "SELECT name FROM categories WHERE id = $id");
    if ($cat_row = mysqli_fetch_assoc($cat_check)) {
        $category_name = $cat_row['name'];
        
        // Proteksi: Pindahkan artikel dengan kategori ini ke 'Umum' terlebih dahulu
        mysqli_query($conn, "UPDATE articles SET category = 'Umum' WHERE category = '$category_name'");
    }

    mysqli_query($conn, "DELETE FROM categories WHERE id = $id");
    header("Location: manage_categories.php?msg=deleted");
    exit();
}

// FIX: Menambahkan perintah COLLATE pada klausa ON untuk mengatasi masalah bentrokan Collation (utf8mb4_0900_ai_ci vs utf8mb4_general_ci)
$query = "SELECT c.id, c.name, COUNT(a.id) AS total_articles 
          FROM categories c 
          LEFT JOIN articles a ON c.name COLLATE utf8mb4_general_ci = a.category COLLATE utf8mb4_general_ci
          GROUP BY c.id ORDER BY c.name ASC";
$cat_res = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Kategori - Admin Panel</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="style.css?v=1.5">
    <style>
        /* Menggunakan font bawaan project premium */
        @import url('https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400..900;1,400..900&family=Plus+Jakarta+Sans:ital,wght@0,200..800;1,200..800&display=swap');
        
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: #f8fafc;
            margin: 0;
        }

        /* Layout Grid Responsif (Berdampingan pada layar desktop) */
        .dashboard-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 40px;
            margin-top: 20px;
        }
        @media (min-width: 1024px) {
            .dashboard-grid {
                grid-template-columns: 4fr 6fr;
            }
        }

        /* Form Card Container */
        .form-container { 
            background: white; 
            padding: 40px; 
            border-radius: 24px; 
            box-shadow: 0 15px 35px rgba(0,0,0,0.02);
            height: fit-content;
            border: 1px solid #f1f5f9;
        }
        .form-container h3 {
            font-family: 'Playfair Display', serif;
            font-size: 1.6rem;
            margin-top: 0;
            margin-bottom: 25px;
            color: #0f172a;
        }

        .input-wrap { margin-bottom: 24px; }
        .input-wrap label { 
            display: block; 
            font-size: 0.75rem; 
            font-weight: 700; 
            color: #94a3b8; 
            margin-bottom: 10px; 
            text-transform: uppercase; 
            letter-spacing: 0.5px;
        }
        
        .dash-input { 
            width: 100%; 
            padding: 14px 18px; 
            border-radius: 12px; 
            border: 1.5px solid #e2e8f0; 
            background: #f8fafc; 
            font-family: inherit; 
            font-size: 0.95rem;
            color: #334155;
            transition: all 0.3s ease;
            box-sizing: border-box;
        }
        .dash-input:focus { 
            border-color: var(--accent, #3b82f6); 
            outline: none; 
            background: white; 
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
        }

        /* Premium Button Style */
        .btn-submit {
            width: 100%;
            padding: 14px;
            border: none;
            background: var(--accent, #0f172a);
            color: white;
            font-weight: 700;
            font-size: 0.95rem;
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(15, 23, 42, 0.15);
        }
        .btn-submit:hover {
            opacity: 0.9;
            transform: translateY(-2px);
        }

        /* Table Card Container */
        .table-container { 
            background: white; 
            border-radius: 24px; 
            overflow: hidden; 
            box-shadow: 0 15px 35px rgba(0,0,0,0.02);
            border: 1px solid #f1f5f9;
        }
        
        table { width: 100%; border-collapse: collapse; }
        th { 
            background: #f8fafc; 
            padding: 18px 24px; 
            text-align: left; 
            font-size: 0.75rem; 
            text-transform: uppercase; 
            color: #94a3b8; 
            font-weight: 800;
            letter-spacing: 0.5px;
            border-bottom: 1px solid #f1f5f9;
        }
        td { 
            padding: 20px 24px; 
            border-bottom: 1px solid #f1f5f9; 
            font-size: 0.95rem; 
            color: #334155;
            vertical-align: middle;
        }
        tr:last-child td { border-bottom: none; }
        tr:hover td { background-color: #fafafa; }

        /* Badge Kategori */
        .badge-category {
            background: #f0fdf4; 
            color: #166534; 
            padding: 6px 14px; 
            border-radius: 20px; 
            font-size: 0.8rem; 
            font-weight: 700; 
            display: inline-block;
        }
        
        /* Badge Total Artikel */
        .badge-count {
            background: #f1f5f9;
            color: #475569;
            padding: 4px 10px;
            border-radius: 8px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        /* Action Link Button */
        .btn-delete {
            color: #ef4444; 
            text-decoration: none; 
            font-weight: 700;
            font-size: 0.85rem;
            padding: 8px 12px;
            border-radius: 8px;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .btn-delete:hover {
            background: #fef2f2;
        }

        /* Alert Notifications */
        .alert {
            padding: 14px 20px;
            border-radius: 12px;
            margin-bottom: 30px;
            font-size: 0.9rem;
            font-weight: 600;
        }
        .alert-success { background: #dcfce7; color: #166534; }
        .alert-danger { background: #fee2e2; color: #991b1b; }
    </style>
</head>
<body style="background: #f8fafc;">
    <?php include 'sidebar.php'; ?>

    <main class="content-area-with-sidebar" style="padding: 60px 80px;">
        <div style="margin-bottom: 40px;">
            <h1 style="font-family: 'Playfair Display', serif; font-size: 3rem; margin: 0 0 10px 0; font-weight: 700;">Manajemen Kategori<span style="color: #0f172a;">.</span></h1>
            <p style="color: #64748b; margin: 0;">Kelola kelompok kategori jurnal untuk mempermudah struktur navigasi pengunjung.</p>
        </div>

        <?php if (isset($_GET['msg'])): ?>
            <?php if ($_GET['msg'] == 'added'): ?>
                <div class="alert alert-success"><i class="fas fa-check-circle"></i> Kategori berhasil ditambahkan!</div>
            <?php elseif ($_GET['msg'] == 'deleted'): ?>
                <div class="alert alert-success"><i class="fas fa-info-circle"></i> Kategori berhasil dihapus. Artikel terkait dipindahkan ke 'Umum'.</div>
            <?php elseif ($_GET['msg'] == 'duplicate'): ?>
                <div class="alert alert-danger"><i class="fas fa-exclamation-triangle"></i> Gagal! Nama kategori tersebut sudah ada.</div>
            <?php endif; ?>
        <?php endif; ?>

        <div class="dashboard-grid">
            
            <div class="form-container">
                <h3>Tambah Kategori</h3>
                <form action="manage_categories.php" method="POST">
                    <div class="input-wrap">
                        <label>Nama Kategori Baru</label>
                        <input type="text" name="category_name" class="dash-input" placeholder="Misal: Event, Spot Foto, Penginapan" autocomplete="off" required>
                    </div>
                    <button type="submit" name="add_category" class="btn-submit">
                        <i class="fas fa-plus" style="margin-right: 5px;"></i> Simpan Kategori
                    </button>
                </form>
            </div>

            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Nama Kategori</th>
                            <th>Digunakan</th>
                            <th style="text-align: right;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($cat = mysqli_fetch_assoc($cat_res)): ?>
                        <tr>
                            <td>
                                <span class="badge-category"><?= htmlspecialchars($cat['name']) ?></span>
                            </td>
                            <td>
                                <span class="badge-count"><?= $cat['total_articles'] ?> Artikel</span>
                            </td>
                            <td style="text-align: right;">
                                <?php if (strtolower($cat['name']) != 'umum'): ?>
                                    <a href="manage_categories.php?delete=<?= $cat['id'] ?>" 
                                       onclick="return confirm('Hapus kategori ini? Artikel di dalamnya otomatis dipindahkan ke kategori \'Umum\'.')" 
                                       class="btn-delete">
                                        <i class="fas fa-trash-alt"></i> Hapus
                                    </a>
                                <?php else: ?>
                                    <span style="font-size: 0.8rem; color: #94a3b8; font-style: italic; padding-right: 12px;">Sistem (Kunci)</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                        <?php if(mysqli_num_rows($cat_res) == 0): ?>
                        <tr>
                            <td colspan="3" style="text-align: center; padding: 40px; color: #94a3b8;">Belum ada kategori yang terdaftar.</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

        </div>
    </main>
</body>
</html>