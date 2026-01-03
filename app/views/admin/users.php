<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8"><title>Khách hàng | PhươngSTORE</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="public/css/admin.css">
</head>
<body class="admin-body">
<?php include 'sidebar.php'; ?>
<main class="main-content-admin">
    <section class="admin-section active">
        <h1>Quản lý khách hàng</h1>
        <div class="cat-card">
            <table class="table-admin">
                <thead><tr><th>Mã KH</th><th>Họ và tên</th><th>Liên hệ</th><th>Địa chỉ</th><th>Trạng thái</th><th style="text-align: right;">Hành động</th></tr></thead>
                <tbody>
                    <?php foreach ($allUsers as $user): ?>
                    <tr>
                        <td style="color: var(--primary); font-weight: bold;">#<?= $user['ma_khach_hang'] ?></td>
                        <td style="font-weight: 700;"><?= htmlspecialchars($user['ho_ten']) ?></td>
                        <td>
                            <div style="font-weight: 600;"><?= $user['email'] ?></div>
                            <div style="color: var(--text-muted); font-size: 18px;"><?= $user['sdt'] ?? 'Trống' ?></div>
                        </td>
                        <td style="color: #888; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;"><?= $user['dia_chi'] ?? 'Chưa cập nhật' ?></td>
                        <td><span style="font-weight: 800; color: <?= $user['bi_khoa'] ? '#ff4d4f' : '#00ff00' ?>"><?= $user['bi_khoa'] ? 'KHÓA' : 'MỞ' ?></span></td>
                        <td style="text-align: right;"><a href="index.php?ctrl=admin&act=lock_user&id=<?= $user['ma_khach_hang'] ?>&status=<?= $user['bi_khoa'] ? 0 : 1 ?>" class="btn-primary" style="padding: 10px 20px; font-size: 16px; background: <?= $user['bi_khoa'] ? '#00ff00' : '#ff4d4f' ?>; color: <?= $user['bi_khoa'] ? '#000' : '#fff' ?>;"><?= $user['bi_khoa'] ? 'MỞ' : 'KHÓA' ?></a></td>
                    </tr><?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </section>
</main>
</body></html>