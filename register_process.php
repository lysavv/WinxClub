<?php
include 'db_config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $role = 'user'; // Hardcoded to user for public registration
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // 1. Cek apakah username sudah ada
    $checkUsername = mysqli_query($conn, "SELECT id FROM users WHERE username='$username'");
    if (mysqli_num_rows($checkUsername) > 0) {
        header("Location: register.php?error=username_taken");
        exit();
    }

    // 2. Cek apakah email sudah ada
    $checkEmail = mysqli_query($conn, "SELECT id FROM users WHERE email='$email'");
    if (mysqli_num_rows($checkEmail) > 0) {
        header("Location: register.php?error=email_taken");
        exit();
    }

    // 3. Jika aman, baru insert
    $sql = "INSERT INTO users (nama_lengkap, username, email, password, role) VALUES ('$nama', '$username', '$email', '$password', '$role')";

    if (mysqli_query($conn, $sql)) {
        header("Location: login.php?status=success");
    } else {
        echo "Error: " . $sql . "<br>" . mysqli_error($conn);
    }
}
?>
