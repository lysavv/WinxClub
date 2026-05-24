<?php
include 'db_config.php';
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit();
}

$user_id = $_SESSION['user_id'];
$item_id = $_POST['item_id'];
$text = mysqli_real_escape_string($conn, $_POST['text']);

$sql = "INSERT INTO comments (item_id, user_id, comment_text) VALUES ('$item_id', '$user_id', '$text')";

if (mysqli_query($conn, $sql)) {
    echo json_encode(['status' => 'success']);
} else {
    echo json_encode(['status' => 'error', 'message' => mysqli_error($conn)]);
}
?>
