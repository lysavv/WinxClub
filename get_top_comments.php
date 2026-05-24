<?php
include 'db_config.php';
header('Content-Type: application/json');

// Fetch top 3 most liked comments across all items
$sql = "SELECT comments.*, users.username, items.name as item_name FROM comments 
        JOIN users ON comments.user_id = users.id 
        JOIN items ON comments.item_id = items.id
        ORDER BY likes DESC, created_at DESC LIMIT 3";

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
