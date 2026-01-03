<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Báo cáo tổng quan | PhươngSTORE</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="public/css/admin.css"> 
</head>
<body class="admin-body"> 

<?php include 'sidebar.php'; ?>

<main class="main-content-admin">
    <section class="admin-section active">
        <h1>Báo cáo tổng quan</h1>
        
        <div class="stat-grid">
            <div class="stat-card">
                <small>Tổng đơn hàng</small>
                <h2><?= $totalOrders ?></h2>
                
                <div onclick="togglePaymentDetail()" style="cursor:pointer; color:var(--primary); font-weight:bold; margin-top:20px; font-size:20px;">
                    Xem chi tiết PTTT <i class="fa-solid fa-chevron-down" id="arrow-icon"></i>
                </div>
                
                <div id="payment-detail" style="display:none; margin-top:25px; text-align:left; border-top:1px solid #333; padding-top:15px;">
                    <?php foreach($paymentStats as $ps): ?>
                        <div style="display:flex; justify-content:space-between; padding:12px 0; font-size:22px;">
                            <span style="color:#888;"><?= $ps['pt_thanh_toan'] ?></span>
                            <span style="color:var(--primary); font-weight:bold;"><?= $ps['so_luong'] ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="stat-card">
                <small>Doanh thu hôm nay</small>
                <h2 style="color:#00ff00;"><?= number_format($todayRevenue) ?>đ</h2>
            </div>

            <div class="stat-card">
                <small>Tổng doanh thu</small>
                <h2 style="color:var(--primary);"><?= number_format($totalRevenue) ?>đ</h2>
            </div>

            <div class="stat-card">
                <small>Thành viên</small>
                <h2><?= count($allUsers) ?></h2>
            </div>
        </div>

        <div class="dashboard-grid">
            
            <div class="cat-card">
                <div style="font-size:24px; font-weight:800; color:var(--primary); margin-bottom:25px;">
                    <i class="fa-solid fa-clock-rotate-left"></i> Đơn hàng vừa đặt
                </div>
                <table class="table-admin order-table">
                    <thead>
                        <tr>
                            <th>Mã đơn</th>
                            <th>Khách hàng</th>
                            <th>Tổng tiền</th>
                            <th>Trạng thái</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach (array_slice($recentOrders, 0, 5) as $ro): ?>
                        <tr>
                            <td style="color:var(--primary); font-weight:bold;"><?= $ro['ma_don_hang_code'] ?></td>
                            <td style="font-weight:600;"><?= htmlspecialchars($ro['ho_ten_nguoi_nhan']) ?></td>
                            <td style="font-weight:900;"><?= number_format($ro['tong_tien']) ?>đ</td>
                            <td>
                                <span style="background:#222; padding:8px 15px; border-radius:8px; font-weight:bold; font-size:14px;">
                                    <?= $ro['tt_don_hang'] ?>
                                </span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <a href="index.php?ctrl=admin&act=manage_orders" style="display:block; text-align:center; margin-top:25px; font-size:18px; color:#888; text-decoration:none;">Xem tất cả đơn hàng →</a>
            </div>

            <div class="cat-card">
                <div style="font-size:24px; font-weight:800; color:var(--primary); margin-bottom:25px;">
                    <i class="fa-solid fa-chart-pie"></i> Tỷ lệ thanh toán
                </div>
                <div style="padding: 20px 0;">
                    <?php foreach ($paymentStats as $ps): ?>
                        <div style="margin-bottom:25px;">
                            <div style="display:flex; justify-content:space-between; font-size:18px; margin-bottom:10px; font-weight:600;">
                                <span><?= $ps['pt_thanh_toan'] ?></span>
                                <span style="color:var(--primary);"><?= round(($ps['so_luong'] / max(1, $totalOrders)) * 100) ?>%</span>
                            </div>
                            <div style="width:100%; height:10px; background:#000; border-radius:10px; border:1px solid #222;">
                                <div style="width:<?= ($ps['so_luong'] / max(1, $totalOrders)) * 100 ?>%; height:100%; background:var(--primary); border-radius:10px; box-shadow: 0 0 10px rgba(212,175,55,0.3);"></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

        </div>
    </section>
</main>

<script>
function togglePaymentDetail() {
    const detail = document.getElementById('payment-detail');
    const icon = document.getElementById('arrow-icon');
    if (detail.style.display === "none") {
        detail.style.display = "block";
        icon.classList.replace('fa-chevron-down', 'fa-chevron-up');
    } else {
        detail.style.display = "none";
        icon.classList.replace('fa-chevron-up', 'fa-chevron-down');
    }
}
</script>

</body>
</html>