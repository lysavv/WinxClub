<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk - Jejak Negeri</title>
    <link rel="stylesheet" href="style.css?v=1.4">
    <style>
        body.auth-page {
            margin: 0;
            padding: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: url('img/Dieng.jpg') center center / cover no-repeat fixed;
            position: relative;
            overflow-x: hidden;
        }

        .auth-overlay {
            position: fixed; /* Fixed to cover whole screen always */
            top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(15, 23, 42, 0.4);
            backdrop-filter: blur(8px);
            z-index: 1;
        }

        .auth-card {
            position: relative;
            z-index: 10;
            width: 90%;
            max-width: 450px;
            background: rgba(255, 255, 255, 0.07);
            backdrop-filter: blur(30px);
            border: 1px solid rgba(255, 255, 255, 0.15);
            border-radius: 40px;
            padding: 60px;
            text-align: center;
            box-shadow: 0 40px 100px rgba(0,0,0,0.3);
            margin: 40px 0; /* Add margin for scroll room */
        }

        .auth-header h2 {
            font-family: 'Playfair Display', serif;
            font-size: 3rem;
            font-weight: 800;
            color: #ffffff;
            margin-bottom: 10px;
            letter-spacing: -1px;
        }

        .auth-header p {
            color: rgba(255, 255, 255, 0.6);
            font-size: 0.95rem;
            margin-bottom: 45px;
        }

        .input-box {
            margin-bottom: 25px;
            text-align: left;
        }

        .input-box label {
            display: block;
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 2px;
            color: var(--accent);
            margin-bottom: 10px;
            margin-left: 5px;
        }

        .input-field {
            width: 100%;
            padding: 15px 0;
            background: transparent;
            border: none;
            border-bottom: 1.5px solid rgba(255, 255, 255, 0.2);
            color: #ffffff;
            font-size: 1rem;
            outline: none;
            transition: all 0.3s ease;
        }

        .input-field::placeholder {
            color: rgba(255, 255, 255, 0.3);
        }

        .input-field:focus {
            border-bottom-color: var(--accent);
        }

        .login-btn {
            width: 100%;
            padding: 18px;
            background: #ffffff;
            color: var(--primary);
            border: none;
            border-radius: 15px;
            font-weight: 800;
            font-size: 1rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            cursor: pointer;
            margin-top: 25px;
            transition: 0.3s;
            box-shadow: 0 15px 30px rgba(0,0,0,0.2);
        }

        .login-btn:hover {
            transform: translateY(-3px);
            background: var(--accent);
            color: #ffffff;
            box-shadow: 0 20px 40px rgba(2, 132, 199, 0.3);
        }

        .register-cta {
            text-align: center;
            margin-top: 35px;
            font-size: 0.9rem;
            color: rgba(255, 255, 255, 0.5);
        }

        .register-cta a {
            color: var(--accent);
            text-decoration: none;
            font-weight: 700;
        }

        .floating-back {
            position: absolute;
            top: 40px;
            left: 40px;
            color: white;
            text-decoration: none;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            font-size: 0.9rem;
            opacity: 0.7;
            transition: 0.3s;
            z-index: 20;
        }
        
        .floating-back:hover {
            opacity: 1;
            transform: translateX(-5px);
        }
    </style>
</head>
<body class="auth-page">
    
    <div class="auth-overlay"></div>

    <a href="beranda.php" class="floating-back">
        <i class="fas fa-arrow-left"></i> Kembali ke Beranda
    </a>

    <div class="auth-card">
        <div class="auth-header">
            <h2>Masuk<span>.</span></h2>
            <p>Selamat datang kembali di Dieng</p>
        </div>

        <form action="login_process.php" method="POST">
            <?php if(isset($_GET['status']) && $_GET['status'] == 'failed'): ?>
                <p style="color: #ff4d4d; font-size: 0.85rem; margin-bottom: 20px; font-weight: 600;">Username atau password salah.</p>
            <?php endif; ?>
            <?php if(isset($_GET['status']) && $_GET['status'] == 'success'): ?>
                <p style="color: #10b981; font-size: 0.85rem; margin-bottom: 20px; font-weight: 600;">Akun berhasil dibuat! Silakan masuk.</p>
            <?php endif; ?>

            <div class="input-box">
                <label>Username</label>
                <input type="text" name="username" class="input-field" placeholder="Masukkan username" required>
            </div>

            <div class="input-box">
                <label>Password</label>
                <input type="password" name="password" class="input-field" placeholder="••••••••" required>
            </div>

            <button type="submit" class="login-btn">Masuk Sekarang</button>
        </form>

        <div class="register-cta">
            Belum punya akun? <a href="register.php">Daftar Sekarang</a>
        </div>
    </div>

</body>
</html>
