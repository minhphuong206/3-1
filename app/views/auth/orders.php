<?php include 'app/views/layouts/header.php'; ?>

<main class="container" style="margin-top: 40px; min-height: 600px;">
    <h2 class="section-title" style="text-align: left; margin-bottom: 30px;">Lịch Sử Mua Hàng</h2>

    <?php if (empty($orders)): ?>
        <div style="text-align: center; padding: 50px; background: #111; border-radius: 8px;">
            <i class="fa-solid fa-box-open" style="font-size: 50px; color: #444; margin-bottom: 15px;"></i>
            <p style="color: #888;">Bạn chưa có đơn hàng nào.</p>
            <a href="index.php" class="btn-primary" style="margin-top: 15px; display: inline-block; text-decoration: none;">MUA SẮM NGAY</a>
        </div>
    <?php else: ?>
        <div class="table-responsive" style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; background: #111; border-radius: 10px; overflow: hidden;">
                <thead style="background: #1a1a1a; color: #D4AF37;">
                    <tr>
                        <th style="padding: 15px; text-align: left;">Mã Đơn</th>
                        <th style="padding: 15px; text-align: left;">Ngày Đặt</th>
                        <th style="padding: 15px; text-align: left;">Tổng Tiền</th>
                        <th style="padding: 15px; text-align: left;">Thanh Toán</th>
                        <th style="padding: 15px; text-align: left;">Trạng Thái</th>
                        <th style="padding: 15px; text-align: center;">Chi Tiết</th>
                    </tr>
                </thead>
                <tbody style="color: #eee;">
                    <?php foreach ($orders as $o): ?>
                        <tr style="border-bottom: 1px solid #222;">
                            <td style="padding: 15px; font-weight: bold;"><?= $o['ma_don_hang_code'] ?></td>
                            <td style="padding: 15px;"><?= date('d/m/Y H:i', strtotime($o['ngay_dat'])) ?></td>
                            <td style="padding: 15px; color: #D4AF37; font-weight: bold;"><?= number_format($o['tong_tien'], 0, ',', '.') ?>đ</td>
                            <td style="padding: 15px;">
                                <span style="font-size: 12px; padding: 3px 8px; border-radius: 4px; background: <?= $o['tt_thanh_toan'] == 'Đã thanh toán' ? '#1b4332; color: #74c69d;' : '#441a1a; color: #ff8b8b;' ?>">
                                    <?= $o['tt_thanh_toan'] ?>
                                </span>
                            </td>
                            <td style="padding: 15px;">
                                <span style="font-size: 12px; font-weight: bold; color: <?= $o['tt_don_hang'] == 'Hoàn tất' ? '#4ade80' : ($o['tt_don_hang'] == 'Đã hủy' ? '#ff6b6b' : '#fbbf24') ?>;">
                                    <?= $o['tt_don_hang'] ?>
                                </span>
                            </td>
                            <td style="padding: 15px; text-align: center;">
                                <a href="index.php?ctrl=cart&act=order_detail&id=<?= $o['ma_don_hang'] ?>" style="color: #aaa;"><i class="fa-solid fa-eye"></i></a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</main>

<?php include 'app/views/layouts/footer.php'; ?>