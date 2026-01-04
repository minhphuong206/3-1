<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý đánh giá | PhươngSTORE</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="public/css/admin.css"> 
    
    <style>
        /* CSS cho Toast thông báo (giống hệt trang products.php) */
        .toast-msg {
            position: fixed; top: 30px; right: 20px; z-index: 99999;
            background: #000; color: #2ecc71; border: 1px solid #2ecc71; 
            border-left: 5px solid #2ecc71; padding: 15px 25px; border-radius: 5px; 
            font-weight: bold; font-size: 14px; box-shadow: 0 5px 20px rgba(0,0,0,0.5);
            display: flex; align-items: center; gap: 12px;
            transform: translateX(120%); opacity: 0;
            animation: slideInRight 0.5s cubic-bezier(0.68, -0.55, 0.27, 1.55) forwards;
        }
        @keyframes slideInRight { 0% { transform: translateX(120%); opacity: 0; } 100% { transform: translateX(0); opacity: 1; } }
        @keyframes slideOutRight { 0% { transform: translateX(0); opacity: 1; } 100% { transform: translateX(120%); opacity: 0; } }

        /* CSS riêng cho sao đánh giá */
        .star-rating { color: #f1c40f; font-size: 14px; }
        .star-empty { color: #444; }
    </style>
</head>
<body class="admin-body">

<?php include 'sidebar.php'; ?>

<?php if (isset($_SESSION['admin_toast'])): ?>
    <div id="toast-notification" class="toast-msg">
        <i class="fa-solid fa-circle-check" style="font-size: 22px;"></i>
        <span><?= $_SESSION['admin_toast'] ?></span>
    </div>
    <?php unset($_SESSION['admin_toast']); ?>
    <script>
        setTimeout(() => {
            const toast = document.getElementById('toast-notification');
            if (toast) {
                toast.style.animation = 'slideOutRight 0.5s forwards';
                setTimeout(() => { toast.remove(); }, 500); 
            }
        }, 3000); 
    </script>
<?php endif; ?>

<main class="main-content-admin">
    <section class="admin-section active">
        
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:30px;">
            <h1 style="margin:0;">Quản lý đánh giá khách hàng</h1>
        </div>

        <div class="cat-card">
            <div style="font-size:24px; font-weight:800; color:var(--primary); margin-bottom:25px;">
                <i class="fa-solid fa-comments"></i> Danh sách đánh giá
            </div>

            <?php if (empty($reviews)): ?>
                <p style="text-align:center; color:#888; padding: 20px;">Chưa có đánh giá nào.</p>
            <?php else: ?>
                <table class="table-admin order-table">
                    <thead>
                        <tr>
                            <th width="50">ID</th>
                            <th>Khách hàng</th>
                            <th>Sản phẩm</th>
                            <th width="120">Số sao</th>
                            <th>Nội dung</th>
                            <th width="150">Ngày đánh giá</th>
                            <th width="80">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($reviews as $rv): ?>
                            <tr>
                                <td style="color:#888;">#<?= $rv['ma_danh_gia'] ?></td>
                                
                                <td style="font-weight:600;">
                                    <i class="fa-solid fa-user" style="color:var(--primary); margin-right:5px;"></i>
                                    <?= htmlspecialchars($rv['ho_ten']) ?>
                                </td>
                                
                                <td style="color:#fff;">
                                    <?= htmlspecialchars($rv['ten_san_pham']) ?>
                                </td>
                                
                                <td>
                                    <div class="star-rating">
                                        <?php 
                                        for($i=1; $i<=5; $i++) {
                                            if($i <= $rv['so_sao']) {
                                                echo '<i class="fa-solid fa-star"></i>';
                                            } else {
                                                echo '<i class="fa-solid fa-star star-empty"></i>';
                                            }
                                        } 
                                        ?>
                                    </div>
                                </td>
                                
                                <td style="color:#ccc; font-style:italic;">
                                    "<?= htmlspecialchars($rv['noi_dung']) ?>"
                                </td>
                                
                                <td style="font-size:14px; color:#888;">
                                    <?= date('d/m/Y H:i', strtotime($rv['ngay_tao'])) ?>
                                </td>
                                
                                <td style="text-align:center;">
                                    <a href="index.php?ctrl=admin&act=delete_review&id=<?= $rv['ma_danh_gia'] ?>" 
                                       onclick="return confirm('Bạn có chắc muốn xóa đánh giá này không?');" 
                                       style="color:var(--danger);" 
                                       title="Xóa đánh giá">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>

    </section>
</main>

</body>
</html>