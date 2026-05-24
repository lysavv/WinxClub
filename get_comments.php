<?php
include 'db_config.php';
header('Content-Type: application/json');

if (!isset($_GET['item_id'])) {
    echo json_encode(['error' => 'Missing item_id']);
    exit();
}

$item_id = mysqli_real_escape_string($conn, $_GET['item_id']);
$sql = "SELECT comments.*, users.username FROM comments 
        JOIN users ON comments.user_id = users.id 
        WHERE item_id = '$item_id' 
        ORDER BY likes DESC, created_at DESC";

$result = mysqli_query($conn, $sql);

if (!$result) {
    echo json_encode(['error' => mysqli_error($conn)]);
    exit();
}

$comments = [];
while ($row = mysqli_fetch_assoc($result)) {
    $comments[] = $row;
}

echo json_encode($comments);
?>
