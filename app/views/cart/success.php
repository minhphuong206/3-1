<?php
// app/views/cart/success.php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once 'app/views/layouts/header.php';

$orderCode = $_GET['code'] ?? '';
?>

<div class="container" style="margin-top: 100px; margin-bottom: 50px;">
    <div class="success-page" style="text-align: center; padding: 50px 20px; background: white; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
        
        <div style="font-size: 80px; color: #4CAF50; margin-bottom: 20px;">
            <i class="fa-solid fa-circle-check"></i>
        </div>
        
        <h1 style="color: #4CAF50; margin-bottom: 20px;">ĐẶT HÀNG THÀNH CÔNG!</h1>
        
        <?php if($orderCode): ?>
            <div style="font-size: 18px; margin-bottom: 15px;">
                Mã đơn hàng: <strong style="color: #a50064;"><?= htmlspecialchars($orderCode) ?></strong>
            </div>
        <?php endif; ?>
        
        <div style="max-width: 600px; margin: 0 auto 30px; padding: 20px; background: #f9f9f9; border-radius: 8px;">
            <p style="margin-bottom: 10px; color: #555;">
                <i class="fa-solid fa-check" style="color: #4CAF50;"></i> Đơn hàng của bạn đã được tiếp nhận.
            </p>
            <p style="margin-bottom: 10px; color: #555;">
                <i class="fa-solid fa-check" style="color: #4CAF50;"></i> Chúng tôi sẽ liên hệ với bạn trong thời gian sớm nhất.
            </p>
            <p style="color: #555;">
                <i class="fa-solid fa-check" style="color: #4CAF50;"></i> Cảm ơn bạn đã mua sắm tại PhuongStore!
            </p>
        </div>
        
        <div style="display: flex; justify-content: center; gap: 15px; flex-wrap: wrap;">
            <a href="index.php" class="btn-primary" style="display: inline-block; padding: 12px 30px; background: #a50064; color: white; text-decoration: none; border-radius: 5px; font-weight: 600;">
                <i class="fa-solid fa-home"></i> VỀ TRANG CHỦ
            </a>
            
            <a href="index.php?ctrl=product" class="btn-primary" style="display: inline-block; padding: 12px 30px; background: #007bff; color: white; text-decoration: none; border-radius: 5px; font-weight: 600;">
                <i class="fa-solid fa-shopping-bag"></i> TIẾP TỤC MUA SẮM
            </a>
        </div>
        
        <div style="margin-top: 30px; padding: 20px; background: #fff5f9; border-radius: 8px; max-width: 500px; margin: 30px auto 0;">
            <h4 style="color: #a50064; margin-bottom: 10px;">
                <i class="fa-solid fa-headset"></i> Hỗ trợ khách hàng
            </h4>
            <p style="color: #666; margin-bottom: 5px;">
                Hotline: <strong>1900 1234</strong>
            </p>
            <p style="color: #666;">
                Email: <strong>support@phuongstore.com</strong>
            </p>
        </div>
    </div>
</div>

<?php require_once 'app/views/layouts/footer.php'; ?>