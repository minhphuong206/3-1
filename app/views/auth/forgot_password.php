<?php include 'app/views/layouts/header.php'; ?>
<main class="auth-wrapper">
    <div class="auth-box">
        <h2 class="auth-title">Quên Mật Khẩu</h2>
        <p class="auth-desc">Nhập email của bạn để nhận mã OTP xác thực.</p>

        <?php if (!empty($error)): ?>
            <div class="alert alert-error"><i class="fa-solid fa-circle-exclamation"></i> <?= $error ?></div>
        <?php endif; ?>

        <form method="POST" action="index.php?ctrl=auth&act=forgot_password">
            <div class="form-group">
                <label class="form-label">Địa chỉ Email</label>
                <input type="email" name="email" class="form-input" placeholder="example@gmail.com" required>
            </div>
            <button type="submit" class="btn-primary" style="width: 100%;">GỬI MÃ XÁC THỰC OTP</button>
        </form>

        <p class="auth-switch">
            <a href="index.php?ctrl=auth&act=login"><i class="fa-solid fa-arrow-left"></i> Quay lại đăng nhập</a>
        </p>
    </div>
</main>
<?php include 'app/views/layouts/footer.php'; ?>