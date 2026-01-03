<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý đơn hàng | PhươngSTORE</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="public/css/admin.css"> 
</head>
<body class="admin-body"> 

<?php include 'sidebar.php'; ?>

<main class="main-content-admin">
    <section class="admin-section active">
        <h1>Quản lý đơn hàng</h1>
        <div class="cat-card">
            <table class="table-admin order-table">
                <thead>
                    <tr>
                        <th>Mã ĐH</th>
                        <th>Người nhận</th>
                        <th>Tổng tiền</th>
                        <th>Thanh toán</th>
                        <th>Trạng thái</th>
                        <th style="text-align: center;">Xem</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($allOrdersList as $order): ?>
                    <tr>
                        <td style="color:var(--primary); font-weight:bold;"><?= $order['ma_don_hang_code'] ?></td>
                        <td style="font-weight:700; color:#fff;"><?= htmlspecialchars($order['ho_ten_nguoi_nhan']) ?></td>
                        <td style="font-weight:900; color:var(--primary);"><?= number_format($order['tong_tien']) ?>đ</td>
                        <td>
                            <span style="font-weight:800; color:<?= $order['tt_thanh_toan'] == 'Đã thanh toán' ? '#00ff00' : '#ff4d4f' ?>">
                                <?= $order['tt_thanh_toan'] ?>
                            </span>
                        </td>
                        <td>
                            <form action="index.php?ctrl=admin&act=update_order_status&id=<?= $order['ma_don_hang'] ?>" method="POST" style="display:flex; gap:10px;">
                                <select name="tt_don_hang" class="admin-input" style="padding:10px; font-size:18px; width:auto;">
                                    <?php $statuses = ['Chờ duyệt', 'Đã duyệt', 'Đang giao', 'Hoàn tất', 'Đã hủy'];
                                    foreach($statuses as $st): ?>
                                        <option value="<?= $st ?>" <?= $order['tt_don_hang'] == $st ? 'selected' : '' ?>><?= $st ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <input type="hidden" name="tt_thanh_toan" value="<?= $order['tt_thanh_toan'] ?>">
                                <button type="submit" class="btn-primary" style="padding:10px 15px;"><i class="fa-solid fa-check"></i></button>
                            </form>
                        </td>
                        <td style="text-align: center;">
                            <i class="fa-solid fa-eye" style="color:var(--primary); cursor:pointer; font-size:28px;"></i>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </section>
</main>
</body>
</html>