<?php
include 'db_config.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE username='$username'";
    $result = mysqli_query($conn, $sql);
    $user = mysqli_fetch_assoc($result);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];
        
        // Redirect based on role
        if ($user['role'] == 'admin') {
            header("Location: dashboard.php");
        } elseif ($user['role'] == 'owner') {
            header("Location: owner_dashboard.php");
        } else {
            header("Location: beranda.php");
        }
    } else {
        header("Location: login.php?status=failed");
    }
}
?>
