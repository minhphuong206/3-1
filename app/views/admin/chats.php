<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Lịch sử Chat | PhươngSTORE</title>
    <link rel="stylesheet" href="public/css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="admin-body">
    <?php include 'app/views/admin/sidebar.php'; ?>
    
    <main class="main-content-admin">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:30px;">
            <h1>Lịch sử Chat (10 tin mới nhất)</h1>
        </div>

        <div class="cat-card">
            <table class="table-admin">
                <thead>
                    <tr>
                        <th width="50">ID</th>
                        <th width="200">Khách hàng</th>
                        <th>Câu hỏi</th>
                        <th>Bot Trả lời</th>
                        <th width="150">Thời gian</th>
                        <th width="80" style="text-align: center;">Xóa</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($chats)): ?>
                        <?php foreach ($chats as $chat): ?>
                        <tr>
                            <td style="color:#666;">#<?= $chat['ma_chat'] ?></td>
                            <td>
                                <?php if($chat['ho_ten']): ?>
                                    <div style="color:var(--primary); font-weight:bold;"><?= htmlspecialchars($chat['ho_ten']) ?></div>
                                    <div style="color:#777; font-size:13px;"><?= htmlspecialchars($chat['email']) ?></div>
                                <?php else: ?>
                                    <span style="color:#999; font-style:italic;">Khách vãng lai</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="question-box">
                                    "<?= htmlspecialchars($chat['cau_hoi']) ?>"
                                </div>
                            </td>
                            <td>
                                <div class="response-box">
                                    <?= htmlspecialchars_decode($chat['tra_loi']) ?> 
                                </div>
                            </td>
                            <td style="color:#aaa; font-size:14px;">
                                <?= date('d/m/Y H:i', strtotime($chat['thoi_gian'])) ?>
                            </td>
                            <td style="text-align: center;">
                                <a href="index.php?ctrl=admin&act=delete_chat&id=<?= $chat['ma_chat'] ?>" 
                                   onclick="return confirm('Bạn chắc chắn muốn xóa hội thoại này?')" 
                                   class="btn-icon-delete" title="Xóa">
                                    <i class="fa-solid fa-trash-can"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="6" style="text-align:center; color:#777; padding: 20px;">Chưa có lịch sử chat nào.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>
</body>
</html>