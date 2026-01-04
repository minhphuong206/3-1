<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Phân quyền Nhân viên | PhươngSTORE Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="public/css/admin.css">
    
    <style>
        .staff-form-container {
            display: grid; grid-template-columns: 200px 1fr; gap: 30px; align-items: start;
        }
        /* Style cho Avatar Upload */
        .avatar-upload-box {
            text-align: center;
        }
        .avatar-preview {
            width: 150px; height: 150px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px dashed #444;
            cursor: pointer;
            transition: 0.3s;
        }
        .avatar-preview:hover {
            border-color: var(--gold-color);
            filter: brightness(1.2);
        }
        .upload-hint {
            font-size: 12px; color: #888; margin-top: 10px;
        }
        
        /* Grid form input */
        .staff-input-grid {
            display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;
        }
        
        .badge-perm {
            background: #333; color: var(--gold-color); 
            border: 1px solid var(--gold-color);
            padding: 2px 8px; border-radius: 4px; font-size: 11px; margin-right: 4px; display: inline-block; margin-bottom: 4px;
        }
    </style>
</head>
<body class="admin-body">

<?php include 'sidebar.php'; ?>

<?php if (isset($_SESSION['admin_toast'])): ?>
    <div id="toast-notification" class="toast-msg">
        <i class="fa-solid fa-circle-check"></i>
        <span><?= $_SESSION['admin_toast'] ?></span>
    </div>
    <?php unset($_SESSION['admin_toast']); ?>
    <script>setTimeout(() => { document.getElementById('toast-notification')?.remove(); }, 3000);</script>
<?php endif; ?>

<main class="main-content-admin">
    <section class="admin-section active">
        <h1 style="margin-bottom:30px;"><i class="fa-solid fa-user-shield"></i> Quản lý & Phân quyền</h1>

        <div class="cat-card">
            <h3 style="color: var(--gold-color); margin-bottom: 20px; border-bottom: 1px solid #333; padding-bottom: 10px;">
                Thêm nhân viên mới
            </h3>
            
            <form action="index.php?ctrl=admin&act=staff" method="POST" enctype="multipart/form-data">
                <div class="staff-form-container">
                    
                    <div class="avatar-upload-box">
                        <label for="avatarInput">
                            <img src="public/images/admin/default_admin.png" id="avatarPreview" class="avatar-preview" title="Bấm để chọn ảnh đại diện">
                        </label>
                        <input type="file" name="avatar" id="avatarInput" style="display: none;" accept="image/*" onchange="previewImage(this)">
                        <div class="upload-hint"><i class="fa-solid fa-camera"></i> Bấm vào ảnh để chọn</div>
                    </div>

                    <div class="info-inputs">
                        <div class="staff-input-grid">
                            <div class="form-group">
                                <label style="color: #ccc;">Họ và tên:</label>
                                <input type="text" name="fullname" class="form-control" required placeholder="VD: Nguyễn Văn A">
                            </div>
                            <div class="form-group">
                                <label style="color: #ccc;">Tên đăng nhập:</label>
                                <input type="text" name="username" class="form-control" required placeholder="VD: staff01">
                            </div>
                            <div class="form-group">
                                <label style="color: #ccc;">Mật khẩu:</label>
                                <input type="password" name="password" class="form-control" required placeholder="Nhập mật khẩu...">
                            </div>
                        </div>

                        <div class="form-group" style="margin-bottom: 20px;">
                            <label style="font-weight: bold; color: #fff; display: block; margin-bottom: 10px;">Cấp quyền truy cập:</label>
                            <div style="display: flex; flex-wrap: wrap; gap: 15px; background: #222; padding: 15px; border-radius: 8px;">
                                <label style="color:#ccc; cursor:pointer;"><input type="checkbox" name="permissions[]" value="products"> Sản phẩm</label>
                                <label style="color:#ccc; cursor:pointer;"><input type="checkbox" name="permissions[]" value="orders"> Đơn hàng</label>
                                <label style="color:#ccc; cursor:pointer;"><input type="checkbox" name="permissions[]" value="users"> Khách hàng</label>
                                <label style="color:#ccc; cursor:pointer;"><input type="checkbox" name="permissions[]" value="reviews"> Đánh giá</label>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary"><i class="fa-solid fa-plus"></i> Tạo tài khoản</button>
                    </div>
                </div>
            </form>
        </div>

        <div class="cat-card" style="margin-top: 30px;">
            <h3 style="margin-bottom: 20px;">Danh sách nhân viên</h3>
            <table class="table-admin order-table">
                <thead>
                    <tr>
                        <th width="60">Ảnh</th>
                        <th>Họ tên / Username</th>
                        <th>Quyền hạn</th>
                        <th width="150" class="text-center">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(!empty($staffs)): ?>
                        <?php foreach($staffs as $s): ?>
                        <tr>
                            <td>
                                <img src="public/images/<?= !empty($s['anh_admin']) ? $s['anh_admin'] : 'default_admin.png' ?>" 
                                     style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover; border: 1px solid #555;">
                            </td>
                            <td>
                                <div style="font-weight: bold; color: #fff;"><?= htmlspecialchars($s['ho_ten']) ?></div>
                                <div style="font-size: 13px; color: var(--gold-color);">@<?= htmlspecialchars($s['ten_dang_nhap']) ?></div>
                            </td>
                            <td>
                                <?php 
                                    if(!empty($s['permissions'])) {
                                        $perms = explode(',', $s['permissions']);
                                        foreach($perms as $p) echo "<span class='badge-perm'>$p</span>";
                                    } else {
                                        echo "<span style='color: #555; font-style: italic;'>Không có quyền</span>";
                                    }
                                ?>
                            </td>
                            <td style="text-align: center;">
                                <button onclick="resetPassword(<?= $s['ma_admin'] ?>, '<?= $s['ho_ten'] ?>')" 
                                        style="background: #3498db; border: none; color: white; padding: 5px 10px; border-radius: 4px; cursor: pointer; margin-right: 5px;" 
                                        title="Đổi mật khẩu">
                                    <i class="fa-solid fa-key"></i>
                                </button>

                                <a href="index.php?ctrl=admin&act=delete_staff&id=<?= $s['ma_admin'] ?>" 
                                   onclick="return confirm('Xóa nhân viên này?')" 
                                   style="background: #e74c3c; color: white; padding: 5px 10px; border-radius: 4px; text-decoration: none;" 
                                   title="Xóa nhân viên">
                                    <i class="fa-solid fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="4" class="text-center">Chưa có nhân viên nào.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>
</main>

<form id="resetPassForm" action="index.php?ctrl=admin&act=reset_password_staff" method="POST" style="display: none;">
    <input type="hidden" name="id" id="reset_id">
    <input type="hidden" name="new_pass" id="reset_pass">
</form>

<script>
    // 1. Xem trước ảnh khi chọn
    function previewImage(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('avatarPreview').src = e.target.result;
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    // 2. Xử lý Reset mật khẩu bằng Prompt
    function resetPassword(id, name) {
        let newPass = prompt("Nhập mật khẩu mới cho nhân viên: " + name);
        if (newPass != null && newPass.trim() !== "") {
            document.getElementById('reset_id').value = id;
            document.getElementById('reset_pass').value = newPass;
            document.getElementById('resetPassForm').submit();
        }
    }
</script>

</body>
</html>