<?php
include 'db_config.php';

echo "<h2>Checking Database Structure...</h2>";

// Check comments table
$res = mysqli_query($conn, "SHOW COLUMNS FROM comments");
echo "<h3>Table 'comments':</h3><ul>";
$has_likes = false;
while($row = mysqli_fetch_assoc($res)) {
    echo "<li>" . $row['Field'] . " (" . $row['Type'] . ")</li>";
    if ($row['Field'] == 'likes') $has_likes = true;
}
echo "</ul>";

if (!$has_likes) {
    mysqli_query($conn, "ALTER TABLE comments ADD COLUMN likes INT DEFAULT 0 AFTER comment_text");
    echo "<p style='color:green;'>Added 'likes' column to 'comments' table.</p>";
}

// Check comment_likes table
$res = mysqli_query($conn, "SHOW TABLES LIKE 'comment_likes'");
if (mysqli_num_rows($res) == 0) {
    $sql = "CREATE TABLE comment_likes (
        id INT AUTO_INCREMENT PRIMARY KEY,
        comment_id INT NOT NULL,
        user_id INT NOT NULL,
        UNIQUE KEY unique_like (comment_id, user_id),
        FOREIGN KEY (comment_id) REFERENCES comments(id) ON DELETE CASCADE,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    )";
    if (mysqli_query($conn, $sql)) {
        echo "<p style='color:green;'>Created 'comment_likes' table.</p>";
    } else {
        echo "<p style='color:red;'>Error creating 'comment_likes': " . mysqli_error($conn) . "</p>";
    }
} else {
    echo "<p>'comment_likes' table already exists.</p>";
}

// Check item_id data type in comments
$res = mysqli_query($conn, "SHOW COLUMNS FROM comments LIKE 'item_id'");
$col = mysqli_fetch_assoc($res);
echo "<p>item_id type: " . $col['Type'] . "</p>";

echo "<h3>Current Comments:</h3>";
$res = mysqli_query($conn, "SELECT * FROM comments");
if (mysqli_num_rows($res) > 0) {
    while($row = mysqli_fetch_assoc($res)) {
        echo "ID: " . $row['id'] . " | Item: " . $row['item_id'] . " | Text: " . $row['comment_text'] . " | Likes: " . $row['likes'] . "<br>";
    }
} else {
    echo "No comments found.";
}

echo "<br><br><a href='beranda.php'>Back to Home</a>";
?>
