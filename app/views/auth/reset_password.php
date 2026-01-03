<?php include 'app/views/layouts/header.php'; ?>
<main class="auth-wrapper">
    <div class="auth-box">
        <h2 class="auth-title">Xác Thực OTP</h2>
        <p class="auth-desc">Mã OTP đã được gửi đến: <b><?= htmlspecialchars($email) ?></b></p>

        <?php if (!empty($error)): ?>
            <div class="alert alert-error"><?= $error ?></div>
        <?php endif; ?>
        <?php if (!empty($success)): ?>
            <div class="alert alert-success"><?= $success ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label class="form-label">Nhập mã OTP (6 số)</label>
                <input type="text" name="otp" class="form-input" maxlength="6" placeholder="______" required style="text-align: center; letter-spacing: 5px; font-size: 20px;">
            </div>
            <div class="form-group">
                <label class="form-label">Mật khẩu mới</label>
                <input type="password" name="new_password" class="form-input" placeholder="Tối thiểu 6 ký tự" required>
            </div>
            <div class="form-group">
                <label class="form-label">Xác nhận mật khẩu</label>
                <input type="password" name="confirm_password" class="form-input" placeholder="Nhập lại mật khẩu mới" required>
            </div>
            <button type="submit" class="btn-primary" style="width: 100%;">ĐỔI MẬT KHẨU</button>
        </form>
    </div>
</main>
<?php include 'app/views/layouts/footer.php'; ?>