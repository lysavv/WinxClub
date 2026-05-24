<?php
include 'db_config.php';
session_start();

// Protection: Only Admin can manage articles
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// 1. Handle Add Article
if (isset($_POST['add_article'])) {
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $content = mysqli_real_escape_string($conn, $_POST['content']);
    $image_path = "";

    // Handle File Upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $target_dir = "uploads/";
        $file_ext = pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION);
        $new_filename = "article_" . time() . "_" . uniqid() . "." . $file_ext;
        $target_file = $target_dir . $new_filename;

        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            $image_path = $target_file;
        }
    }
    
    $sql = "INSERT INTO articles (title, content, image_url, author_id) VALUES ('$title', '$content', '$image_path', '$user_id')";
    mysqli_query($conn, $sql);
    header("Location: manage_articles.php?msg=added");
    exit();
}

// 2. Handle Delete Article
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    
    // Delete physical file if exists
    $file_check = mysqli_query($conn, "SELECT image_url FROM articles WHERE id = $id");
    if ($row = mysqli_fetch_assoc($file_check)) {
        if (!empty($row['image_url']) && file_exists($row['image_url'])) {
            unlink($row['image_url']);
        }
    }

    mysqli_query($conn, "DELETE FROM articles WHERE id = $id");
    header("Location: manage_articles.php?msg=deleted");
    exit();
}

$art_res = mysqli_query($conn, "SELECT * FROM articles ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Jurnal - Admin Panel</title>
    <link rel="stylesheet" href="style.css?v=1.5">
    <style>
        .form-container { 
            background: white; padding: 45px; border-radius: 35px; margin-bottom: 60px;
            box-shadow: 0 20px 50px rgba(0,0,0,0.03);
        }
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
    </style>
</head>
<body style="background: #f8fafc;">
    <?php include 'sidebar.php'; ?>

    <main class="content-area-with-sidebar" style="padding: 60px 80px;">
        <div style="margin-bottom: 50px;">
            <h1 style="font-family: 'Playfair Display'; font-size: 3rem; margin-bottom: 10px;">Manajemen Jurnal<span>.</span></h1>
            <p style="color: #64748b;">Tulis dan publikasikan informasi terbaru tentang Dieng untuk pengunjung.</p>
        </div>

        <div class="form-container">
            <h3 style="font-family: 'Playfair Display'; font-size: 1.8rem; margin-bottom: 30px;">Tambah Jurnal Baru</h3>
            <form action="manage_articles.php" method="POST" enctype="multipart/form-data">
                <div class="input-wrap">
                    <label>Judul Artikel</label>
                    <input type="text" name="title" class="dash-input" placeholder="Contoh: Keajaiban Blue Fire Dieng" required>
                </div>
                <div class="input-wrap">
                    <label>Isi Konten</label>
                    <textarea name="content" class="dash-input" style="height: 300px; resize: none;" placeholder="Tulis wawasan lengkap di sini..." required></textarea>
                </div>
                <div class="input-wrap">
                    <label>Upload Foto Thumbnail</label>
                    <input type="file" name="image" class="dash-input" style="padding: 10px;" accept="image/*" required>
                </div>
                <button type="submit" name="add_article" class="btn-premium" style="width: 100%; border: none; background: var(--accent); color: white; margin-top: 10px;">
                    Publikasikan Jurnal
                </button>
            </form>
        </div>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Thumbnail</th>
                        <th>Judul</th>
                        <th>Tanggal Terbit</th>
                        <th style="text-align: right;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($art = mysqli_fetch_assoc($art_res)): ?>
                    <tr>
                        <td>
                            <?php if($art['image_url']): ?>
                                <img src="<?= $art['image_url'] ?>" style="width: 80px; height: 50px; object-fit: cover; border-radius: 8px;">
                            <?php else: ?>
                                <div style="width: 80px; height: 50px; background: #f1f5f9; border-radius: 8px; display: flex; align-items: center; justify-content: center; color: #94a3b8;"><i class="fas fa-image"></i></div>
                            <?php endif; ?>
                        </td>
                        <td style="font-weight: 700; color: var(--primary);"><?= $art['title'] ?></td>
                        <td style="color: #64748b;"><?= date('d M Y', strtotime($art['created_at'])) ?></td>
                        <td style="text-align: right;">
                            <a href="article_detail.php?id=<?= $art['id'] ?>" target="_blank" style="color: var(--accent); text-decoration: none; font-weight: 700; margin-right: 20px;"><i class="fas fa-eye"></i> Lihat</a>
                            <a href="manage_articles.php?delete=<?= $art['id'] ?>" onclick="return confirm('Hapus jurnal ini?')" style="color: #ef4444; text-decoration: none; font-weight: 700;"><i class="fas fa-trash"></i> Hapus</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                    <?php if(mysqli_num_rows($art_res) == 0): ?>
                    <tr>
                        <td colspan="3" style="text-align: center; padding: 40px; color: #94a3b8;">Belum ada artikel yang diterbitkan.</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>
</body>
</html>