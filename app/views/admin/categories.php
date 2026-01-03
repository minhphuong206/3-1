<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý danh mục | PhươngSTORE</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="public/css/admin.css">
</head>
<body class="admin-body">
<?php include 'sidebar.php'; ?>

<main class="main-content-admin">
    <section class="admin-section active">
        <h1 style="margin-bottom: 30px;">Quản lý danh mục & Thương hiệu</h1>

        <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 30px;">
            
            <div class="cat-card">
                <h3 id="form-title" style="color:var(--primary); margin-bottom:20px;">Thêm danh mục mới</h3>
                <form action="index.php?ctrl=admin&act=save_category" method="POST" id="cat-form">
                    <input type="hidden" name="ma_dm" id="input-ma-dm">
                    
                    <div style="margin-bottom:15px;">
                        <label style="display:block; color:#888; margin-bottom:5px;">Tên danh mục</label>
                        <input type="text" name="ten_danh_muc" id="input-ten-dm" required 
                               placeholder="Ví dụ: Điện thoại, Laptop..."
                               style="width:100%; padding:10px; background:#111; border:1px solid #333; color:#fff; border-radius:5px;">
                    </div>

                    <div style="margin-bottom:15px;">
                        <label style="display:block; color:#888; margin-bottom:5px;">Thêm thương hiệu mới</label>
                        <textarea name="danh_sach_thuong_hieu" id="input-brands"
                                  placeholder="Apple, Samsung, Xiaomi..." 
                                  style="width:100%; height: 80px; padding:10px; background:#111; border:1px solid #333; color:#fff; border-radius:5px; resize: none;"></textarea>
                        <small style="color:#666; font-style: italic;">* Nhập các tên thương hiệu cách nhau bằng dấu phẩy (,)</small>
                    </div>

                    <div style="margin-bottom:15px;">
                        <label style="display:block; color:#888; margin-bottom:5px;">Thứ tự sắp xếp (Trang chủ)</label>
                        <input type="number" name="thu_tu" id="input-thu-tu" value="0" 
                               style="width:100%; padding:10px; background:#111; border:1px solid #333; color:#fff; border-radius:5px;">
                    </div>

                    <div style="margin-bottom:20px;">
                        <label style="color:#eee; cursor:pointer; display:flex; align-items:center; gap:10px;">
                            <input type="checkbox" name="hien_thi" id="input-hien-thi" checked style="width:18px; height:18px; accent-color:var(--primary);">
                            Hiển thị danh mục này ra trang chủ
                        </label>
                    </div>

                    <div style="display:flex; gap:10px;">
                        <button type="submit" class="btn-primary" style="flex:1;">LƯU DỮ LIỆU</button>
                        <button type="button" onclick="resetCatForm()" id="btn-cancel" style="display:none; flex:1; background:#333; color:#fff; border:none; border-radius:5px;">HỦY SỬA</button>
                    </div>
                </form>
            </div>

            <div class="cat-card">
                <table class="table-admin">
                    <thead>
                        <tr>
                            <th width="50">TT</th>
                            <th>Tên danh mục</th>
                            <th>Thương hiệu hiện có</th>
                            <th width="80">Trạng thái</th>
                            <th width="100" style="text-align:right;">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(!empty($categories)): ?>
                            <?php foreach ($categories as $c): ?>
                            <tr>
                                <td style="color:#555;"><?= $c['thu_tu'] ?></td>
                                <td>
                                    <strong style="color:var(--primary); font-size:16px;"><?= htmlspecialchars($c['ten_danh_muc']) ?></strong>
                                </td>
                                <td>
                                    <?php 
                                        // Giả sử bạn đã JOIN lấy thương hiệu hoặc dùng hàm Model trong Controller
                                        $brands = $this->productModel->getBrandsByCategory($c['ma_danh_muc']);
                                        if(!empty($brands)){
                                            foreach($brands as $b){
                                                echo '<span style="display:inline-block; background:#222; padding:2px 8px; border-radius:4px; font-size:11px; margin:2px; color:#aaa; border:1px solid #333;">'.$b['ten_thuong_hieu'].'</span>';
                                            }
                                        } else {
                                            echo '<span style="color:#444; font-size:12px;">Chưa có thương hiệu</span>';
                                        }
                                    ?>
                                </td>
                                <td>
                                    <?php if($c['hien_thi']): ?>
                                        <i class="fa-solid fa-eye" style="color:#2ecc71;" title="Đang hiện"></i>
                                    <?php else: ?>
                                        <i class="fa-solid fa-eye-slash" style="color:#e74c3c;" title="Đang ẩn"></i>
                                    <?php endif; ?>
                                </td>
                                <td style="text-align:right;">
                                    <button onclick="editCategory(<?= htmlspecialchars(json_encode($c)) ?>)" 
                                            style="background:none; border:none; color:#3498db; cursor:pointer; margin-right:15px;">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </button>
                                    <a href="index.php?ctrl=admin&act=delete_category&id=<?= $c['ma_danh_muc'] ?>" 
                                       onclick="return confirm('Xóa danh mục này sẽ ảnh hưởng đến sản phẩm liên quan. Bạn có chắc không?')"
                                       style="color:var(--danger);"><i class="fa-solid fa-trash-can"></i></a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="5" style="text-align:center; padding:30px; color:#555;">Chưa có danh mục nào được tạo.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

        </div>
    </section>
</main>

<script>
    // Hàm khi bấm vào nút Sửa
    function editCategory(data) {
        document.getElementById('form-title').innerText = "Chỉnh sửa danh mục: " + data.ten_danh_muc;
        document.getElementById('input-ma-dm').value = data.ma_danh_muc;
        document.getElementById('input-ten-dm').value = data.ten_danh_muc;
        document.getElementById('input-thu-tu').value = data.thu_tu;
        document.getElementById('input-hien-thi').checked = (data.hien_thi == 1);
        
        // Hiện nút hủy
        document.getElementById('btn-cancel').style.display = "block";
        // Cuộn lên đầu form
        window.scrollTo({ top: 0, behavior: 'smooth' });
        
        // Thông báo nhỏ cho người dùng biết textarea chỉ dành cho thêm MỚI
        document.getElementById('input-brands').placeholder = "Nhập thêm thương hiệu mới cho danh mục này (nếu có)...";
    }

    // Reset form về trạng thái Thêm mới
    function resetCatForm() {
        document.getElementById('form-title').innerText = "Thêm danh mục mới";
        document.getElementById('cat-form').reset();
        document.getElementById('input-ma-dm').value = "";
        document.getElementById('btn-cancel').style.display = "none";
        document.getElementById('input-brands').placeholder = "Apple, Samsung, Xiaomi...";
    }
</script>

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

</body>
</html>