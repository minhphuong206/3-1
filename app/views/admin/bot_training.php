<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Huấn luyện Chatbot | Admin</title>
    <link rel="stylesheet" href="public/css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="admin-body">
    <?php include 'app/views/admin/sidebar.php'; ?>
    
    <main class="main-content-admin">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:30px;">
            <h1>Huấn luyện Chatbot (Câu trả lời mẫu)</h1>
            <button onclick="openModal()" class="btn-primary">
                <i class="fa-solid fa-plus"></i> Thêm câu mẫu
            </button>
        </div>

        <?php if (isset($_SESSION['admin_toast'])): ?>
            <div class="alert-success" style="background:#d4edda; color:#155724; padding:10px; border-radius:5px; margin-bottom:15px;">
                <?= $_SESSION['admin_toast']; unset($_SESSION['admin_toast']); ?>
            </div>
        <?php endif; ?>

        <div class="cat-card">
            <table class="table-admin">
                <thead>
                    <tr>
                        <th width="50">ID</th>
                        <th width="30%">Từ khóa (Câu hỏi)</th>
                        <th>Bot trả lời</th>
                        <th width="100">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($dataList)): ?>
                        <?php foreach ($dataList as $row): ?>
                        <tr>
                            <td>#<?= $row['ma_du_lieu'] ?></td>
                            <td>
                                <span style="background:#333; color:#fff; padding:3px 8px; border-radius:4px; font-size:13px;">
                                    <?= htmlspecialchars($row['cau_hoi']) ?>
                                </span>
                            </td>
                            <td><?= nl2br(htmlspecialchars($row['tra_loi'])) ?></td>
                            <td>
                                <div style="display:flex; gap:10px;">
                                    <button onclick="openModal(<?= $row['ma_du_lieu'] ?>, '<?= htmlspecialchars(addslashes($row['cau_hoi'])) ?>', '<?= htmlspecialchars(addslashes($row['tra_loi'])) ?>')" class="btn-icon-edit">
                                        <i class="fa-solid fa-pen"></i>
                                    </button>
                                    <a href="index.php?ctrl=admin&act=delete_training&id=<?= $row['ma_du_lieu'] ?>" 
                                       onclick="return confirm('Xóa câu mẫu này?')" class="btn-icon-delete">
                                        <i class="fa-solid fa-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="4" style="text-align:center;">Chưa có dữ liệu huấn luyện.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>

    <dialog id="trainingModal" class="modal-dark">
        <div class="modal-header">
            <h3 id="modalTitle">Thêm dữ liệu huấn luyện</h3>
            <span class="close-modal" onclick="document.getElementById('trainingModal').close()">&times;</span>
        </div>
        <form action="index.php?ctrl=admin&act=save_training" method="POST">
            <input type="hidden" name="ma_du_lieu" id="ma_du_lieu">
            
            <div style="margin-bottom:15px;">
                <label class="admin-label">Khi khách hỏi (Từ khóa):</label>
                <input type="text" name="cau_hoi" id="cau_hoi" class="admin-input" placeholder="Ví dụ: xin chào, địa chỉ, bảo hành..." required>
                <small style="color:#aaa;">* Nhập từ khóa ngắn gọn, viết thường.</small>
            </div>

            <div style="margin-bottom:20px;">
                <label class="admin-label">Bot sẽ trả lời:</label>
                <textarea name="tra_loi" id="tra_loi" class="admin-input" rows="5" placeholder="Nhập câu trả lời của Bot..." required></textarea>
            </div>

            <div style="text-align:right; display:flex; gap:10px; justify-content:flex-end;">
                <button type="button" class="btn-cancel" onclick="document.getElementById('trainingModal').close()">Hủy</button>
                <button type="submit" class="btn-primary">LƯU DỮ LIỆU</button>
            </div>
        </form>
    </dialog>

    <script>
        function openModal(id = '', question = '', reply = '') {
            document.getElementById('ma_du_lieu').value = id;
            document.getElementById('cau_hoi').value = question;
            document.getElementById('tra_loi').value = reply;
            
            document.getElementById('modalTitle').innerText = id ? 'Sửa dữ liệu' : 'Thêm dữ liệu mới';
            document.getElementById('trainingModal').showModal();
        }
    </script>
</body>
</html>