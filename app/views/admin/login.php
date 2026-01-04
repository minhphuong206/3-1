<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>PhươngSTORE | Admin Authentication</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="public/css/style.css">
    <style>
        .admin-login-body {
            background: radial-gradient(circle at center, #1a1a1a 0%, #050505 100%);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .admin-card {
            width: 400px;
            background: rgba(20, 20, 20, 0.9);
            border: 1px solid #D4AF37;
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 0 40px rgba(212, 175, 55, 0.15);
            backdrop-filter: blur(10px);
        }
    </style>
</head>
<body class="admin-login-body">

<div class="admin-card">
    <div style="text-align: left; margin-bottom: 20px;">
        <a href="index.php?ctrl=auth&act=login" style="color: #D4AF37; text-decoration: none; font-size: 13px;">
    <i class="fa-solid fa-arrow-left"></i> Khách hàng
</a>
    </div>

    <center>
        <div style="background: var(--gold-gradient); color: black; padding: 4px 12px; border-radius: 50px; font-size: 11px; font-weight: 800; margin-bottom: 15px; display: inline-block;">
            HỆ THỐNG QUẢN TRỊ
        </div>
        <h2 style="color:white; margin-bottom: 30px; letter-spacing: 2px;">Phương<span style="color:#D4AF37">STORE</span></h2>
    </center>

    <?php if (isset($error) && $error): ?>
        <div class="alert alert-error" style="margin-bottom: 20px;">
            <i class="fa-solid fa-shield-virus"></i> <?= $error ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="index.php?ctrl=admin&act=login">
        <div class="form-group">
            <label class="form-label">Tên đăng nhập</label>
            <input type="text" name="username" class="form-input" placeholder="Admin ID..." required>
        </div>

        <div class="form-group">
            <label class="form-label">Mật khẩu hệ thống</label>
            <input type="password" name="password" class="form-input" placeholder="••••••••" required>
        </div>

        <button type="submit" class="btn-primary" style="width: 100%; margin-top: 10px; padding: 15px;">
            XÁC THỰC TRUY CẬP
        </button>
    </form>
</div>

</body>
</html>