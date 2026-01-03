<?php 
// app/views/auth/login.php
include 'app/views/layouts/header.php'; 
?>

<?php if (isset($_SESSION['login_toast_msg'])): ?>
    <div id="login-toast">
        <i class="fa-solid fa-circle-exclamation"></i>
        <span><?= $_SESSION['login_toast_msg'] ?></span>
    </div>
    <?php unset($_SESSION['login_toast_msg']); // Xóa session ngay ?>
    
    <style>
        #login-toast {
            position: fixed;
            top: 100px; /* Cách top để không che header */
            right: -350px; /* Ẩn bên phải */
            background-color: #fff;
            color: #333;
            padding: 15px 25px;
            border-left: 5px solid #d4af37; /* Màu vàng Gold */
            box-shadow: 0 5px 15px rgba(0,0,0,0.3);
            border-radius: 4px;
            display: flex;
            align-items: center;
            gap: 15px;
            z-index: 99999;
            transition: right 0.5s cubic-bezier(0.68, -0.55, 0.27, 1.55);
            font-weight: 600;
            min-width: 250px;
        }
        #login-toast i { color: #d4af37; font-size: 20px; }
        #login-toast.show { right: 20px; }
    </style>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const toast = document.getElementById("login-toast");
            // Trượt ra
            setTimeout(() => toast.classList.add("show"), 100);
            // Tự tắt sau 3.5s
            setTimeout(() => toast.classList.remove("show"), 3500);
        });
    </script>
<?php endif; ?>

<main class="auth-wrapper">
    <div class="auth-box">
        <div style="text-align: right; margin-bottom: -20px;">
            <a href="index.php?ctrl=admin_auth&act=login" class="admin-switch-btn" title="Dành cho Quản trị viên" style="color: #666; font-size: 12px; text-decoration: none;">
                <i class="fa-solid fa-shield-halved"></i> Admin
            </a>
        </div>

        <h2 class="auth-title">Đăng Nhập</h2>
        <p class="auth-desc">Chào mừng bạn trở lại với PhươngSTORE</p>

        <?php if (!empty($error)): ?>
            <div class="alert alert-error">
                <i class="fa-solid fa-circle-exclamation"></i>
                <span><?= $error ?></span>
            </div>
        <?php endif; ?>

        <form method="POST" action="index.php?ctrl=auth&act=login">
            
            <?php if(isset($_GET['redirect'])): ?>
                <input type="hidden" name="redirect" value="<?= htmlspecialchars($_GET['redirect']) ?>">
            <?php endif; ?>

            <div class="form-group">
                <label class="form-label">Email tài khoản</label>
                <input type="email" name="email" class="form-input" 
                       value="<?= htmlspecialchars($email_old ?? '') ?>" 
                       placeholder="Nhập email của bạn..." required>
            </div>

            <div class="form-group">
                <label class="form-label">Mật khẩu</label>
                <input type="password" name="password" class="form-input" 
                       placeholder="Nhập mật khẩu..." required>
            </div>

            <div class="auth-links">
                <label>
                    <input type="checkbox" style="accent-color: var(--gold-color);"> Nhớ đăng nhập
                </label>
                <a href="index.php?ctrl=auth&act=forgot_password">Quên mật khẩu?</a>
            </div>

            <button type="submit" class="btn-primary" style="width: 100%; cursor: pointer;">
                <i class="fa-solid fa-right-to-bracket"></i> ĐĂNG NHẬP NGAY
            </button>
        </form>

        <p class="auth-switch">
            Bạn chưa có tài khoản? <a href="index.php?ctrl=auth&act=register">Đăng ký thành viên</a>
        </p>
    </div>
</main>

<?php include 'app/views/layouts/footer.php'; ?>