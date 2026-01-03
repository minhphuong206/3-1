<?php 
// app/views/auth/register.php
include 'app/views/layouts/header.php'; 
?>

<main class="auth-wrapper">
    <div class="auth-box">
        <h2 class="auth-title">Đăng Ký</h2>
        <p class="auth-desc">Tham gia PhươngSTORE để nhận nhiều ưu đãi VIP</p>

        <?php if (!empty($success)): ?>
            <div class="alert alert-success">
                <i class="fa-solid fa-circle-check"></i>
                <span><?= $success ?></span>
            </div>
        <?php endif; ?>

        <?php if (isset($errors['system'])): ?>
            <div class="alert alert-error">
                <i class="fa-solid fa-triangle-exclamation"></i>
                <span><?= $errors['system'] ?></span>
            </div>
        <?php endif; ?>

        <form method="POST" action="index.php?ctrl=auth&act=register">
            <div class="form-group">
                <label class="form-label">Họ và Tên</label>
                <input type="text" name="full_name" class="form-input" 
                       value="<?= htmlspecialchars($old['full_name'] ?? '') ?>" 
                       placeholder="Ví dụ: Nguyễn Văn A">
                <?php if (isset($errors['full_name'])): ?>
                    <span class="error-msg"><?= $errors['full_name'] ?></span>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label class="form-label">Địa chỉ Email</label>
                <input type="email" name="email" class="form-input" 
                       value="<?= htmlspecialchars($old['email'] ?? '') ?>" 
                       placeholder="example@gmail.com">
                <?php if (isset($errors['email'])): ?>
                    <span class="error-msg"><?= $errors['email'] ?></span>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label class="form-label">Số điện thoại</label>
                <input type="text" name="phone" class="form-input" 
                       value="<?= htmlspecialchars($old['phone'] ?? '') ?>" 
                       placeholder="09xx xxx xxx">
                <?php if (isset($errors['phone'])): ?>
                    <span class="error-msg"><?= $errors['phone'] ?></span>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label class="form-label">Mật khẩu</label>
                <input type="password" name="password" class="form-input" 
                       placeholder="Tối thiểu 6 ký tự">
                <?php if (isset($errors['password'])): ?>
                    <span class="error-msg"><?= $errors['password'] ?></span>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label class="form-label">Xác nhận mật khẩu</label>
                <input type="password" name="confirm_password" class="form-input" 
                       placeholder="Nhập lại mật khẩu giống trên">
                <?php if (isset($errors['confirm_password'])): ?>
                    <span class="error-msg"><?= $errors['confirm_password'] ?></span>
                <?php endif; ?>
            </div>

            <button type="submit" class="btn-primary" style="width: 100%; cursor: pointer;">
                <i class="fa-solid fa-user-plus"></i> ĐĂNG KÝ TÀI KHOẢN
            </button>
        </form>

        <p class="auth-switch">
            Đã là thành viên? <a href="index.php?ctrl=auth&act=login">Đăng nhập tại đây</a>
        </p>
    </div>
</main>

<?php 
if (file_exists('app/views/layouts/footer.php')) {
    include 'app/views/layouts/footer.php';
}
?>