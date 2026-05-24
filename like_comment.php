<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'db_config.php';
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Silakan login terlebih dahulu untuk memberikan Like.']);
    exit();
}

if (!isset($_POST['comment_id']) || empty($_POST['comment_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Komentar tidak ditemukan (Missing ID)']);
    exit();
}

$user_id = $_SESSION['user_id'];
$comment_id = (int)$_POST['comment_id'];

// 1. Ensure Table Structure is Perfect
mysqli_query($conn, "CREATE TABLE IF NOT EXISTS comment_likes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    comment_id INT NOT NULL,
    user_id INT NOT NULL,
    UNIQUE KEY unique_like (comment_id, user_id)
)");

// 2. Check current state
$check = mysqli_query($conn, "SELECT id FROM comment_likes WHERE comment_id = $comment_id AND user_id = $user_id");

if (mysqli_num_rows($check) > 0) {
    // UNLIKE
    $res1 = mysqli_query($conn, "DELETE FROM comment_likes WHERE comment_id = $comment_id AND user_id = $user_id");
    $res2 = mysqli_query($conn, "UPDATE comments SET likes = GREATEST(0, likes - 1) WHERE id = $comment_id");
    
    if($res1 && $res2) {
        echo json_encode(['status' => 'unliked', 'message' => 'Like dihapus']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Gagal update database (Unlike)']);
    }
} else {
    // LIKE
    $res1 = mysqli_query($conn, "INSERT INTO comment_likes (comment_id, user_id) VALUES ($comment_id, $user_id)");
    $res2 = mysqli_query($conn, "UPDATE comments SET likes = likes + 1 WHERE id = $comment_id");
    
    if($res1 && $res2) {
        echo json_encode(['status' => 'liked', 'message' => 'Berhasil disukai']);
    } else {
        // Handle potential error like missing column
        $error = mysqli_error($conn);
        if (strpos($error, "Unknown column 'likes'") !== false) {
            mysqli_query($conn, "ALTER TABLE comments ADD COLUMN likes INT DEFAULT 0");
            // Retry once
            mysqli_query($conn, "INSERT INTO comment_likes (comment_id, user_id) VALUES ($comment_id, $user_id)");
            mysqli_query($conn, "UPDATE comments SET likes = likes + 1 WHERE id = $comment_id");
            echo json_encode(['status' => 'liked', 'message' => 'Kolom diperbaiki & Like berhasil']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Gagal: ' . $error]);
        }
    }
}
?>
