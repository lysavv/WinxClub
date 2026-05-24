<?php
include 'db_config.php';
session_start();

if (!isset($_GET['id'])) {
    header("Location: artikel.php");
    exit();
}

$id = mysqli_real_escape_string($conn, $_GET['id']);
$sql = "SELECT a.*, u.username as author FROM articles a LEFT JOIN users u ON a.author_id = u.id WHERE a.id = '$id'";
$result = mysqli_query($conn, $sql);
$article = mysqli_fetch_assoc($result);

if (!$article) {
    header("Location: artikel.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $article['title'] ?> - Jejak Negeri</title>
    <link rel="stylesheet" href="style.css?v=2.0"> 
    <style>
        .article-hero {
            height: 50vh;
            background: linear-gradient(rgba(0,0,0,0.3), rgba(15,23,42,1)), url('<?= $article['image_url'] ?>') center/cover;
            display: flex;
            align-items: flex-end;
            padding: 80px;
        }
        .article-container {
            max-width: 900px;
            margin: -80px auto 0;
            padding: 0 20px 100px;
            position: relative;
            z-index: 10;
        }
        .article-card {
            background: white;
            border-radius: 40px;
            padding: 60px;
            box-shadow: 0 30px 60px rgba(0,0,0,0.1);
        }
        .article-content {
            font-size: 1.2rem;
            line-height: 1.8;
            color: #475569;
            text-align: justify;
        }
        .article-meta {
            display: flex;
            align-items: center;
            gap: 20px;
            margin-bottom: 40px;
            padding-bottom: 20px;
            border-bottom: 1px solid #f1f5f9;
            color: #94a3b8;
            font-size: 0.9rem;
        }
    </style>
</head>
<body style="background: #0f172a;">
    <?php include 'sidebar.php'; ?>

    <main class="content-area-with-sidebar">
        <div class="article-hero">
            <h1 style="font-size: 3.5rem; color: white; line-height: 1.2;"><?= $article['title'] ?></h1>
        </div>

        <div class="article-container">
            <div class="article-card">
                <div class="article-meta">
                    <span><i class="fas fa-user-edit" style="margin-right: 8px;"></i> Oleh: <?= $article['author'] ?? 'Admin' ?></span>
                    <span><i class="fas fa-calendar-alt" style="margin-right: 8px;"></i> <?= date('d M Y', strtotime($article['created_at'])) ?></span>
                </div>
                
                <div class="article-content">
                    <?= nl2br(htmlspecialchars($article['content'])) ?>
                </div>

                <div style="margin-top: 60px; text-align: center; border-top: 1px solid #f1f5f9; padding-top: 40px;">
                    <a href="artikel.php" style="color: var(--accent); text-decoration: none; font-weight: 700;">
                        <i class="fas fa-arrow-left"></i> Kembali ke Daftar Jurnal
                    </a>
                </div>
            </div>
        </div>
    </main>
    <script src="script.js"></script>
</body>
</html>