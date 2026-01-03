<?php include 'app/views/layouts/header.php'; ?>

<main class="container" style="margin-top: 30px; min-height: 500px;">
    <div class="auth-box" style="max-width: 600px; margin: auto; padding: 30px;">
        <h2 class="auth-title" style="text-align: center; margin-bottom: 25px;">Thông Tin Tài Khoản</h2>
        
        <?php if(!empty($success)): ?>
            <div class="alert alert-success" style="margin-bottom: 20px;"><?= $success ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group" style="margin-bottom: 15px;">
                <label class="form-label">Email (Không thể thay đổi)</label>
                <input type="text" class="form-input" value="<?= htmlspecialchars($user['email']) ?>" disabled style="background: #222; cursor: not-allowed;">
            </div>
            
            <div class="form-group" style="margin-bottom: 15px;">
                <label class="form-label">Họ và Tên</label>
                <input type="text" name="ho_ten" class="form-input" value="<?= htmlspecialchars($user['ho_ten']) ?>" required>
            </div>

            <div class="form-group" style="margin-bottom: 15px;">
                <label class="form-label">Số điện thoại</label>
                <input type="text" name="sdt" class="form-input" value="<?= htmlspecialchars($user['sdt'] ?? '') ?>" placeholder="Chưa cập nhật">
            </div>

            <div class="form-group" style="margin-bottom: 25px;">
                <label class="form-label">Địa chỉ nhận hàng</label>
                <textarea name="dia_chi" class="form-input" style="height: 80px; padding: 10px;"><?= htmlspecialchars($user['dia_chi'] ?? '') ?></textarea>
            </div>

            <button type="submit" class="btn-primary" style="width: 100%;">LƯU THAY ĐỔI</button>
        </form>

        <div style="margin-top: 20px; text-align: center;">
            <a href="index.php?ctrl=auth&act=orders" style="color: #D4AF37; text-decoration: underline;">Xem lịch sử đơn hàng của tôi</a>
        </div>
    </div>
</main>

<?php include 'app/views/layouts/footer.php'; ?>