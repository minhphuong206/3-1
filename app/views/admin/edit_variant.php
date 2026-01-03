<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Sửa biến thể | PhươngSTORE</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="public/css/admin.css">
    <style>
        /* CSS tương tự trang Add */
        body { background: #000; color: #fff; font-family: 'Segoe UI', sans-serif; margin: 0; padding: 20px; }
        .container { max-width: 900px; margin: 0 auto; background: #0a0a0a; padding: 40px; border-radius: 20px; border: 1px solid rgba(212, 175, 55, 0.2); }
        .header { display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #222; padding-bottom: 20px; margin-bottom: 30px; }
        .section-title { font-size: 14px; color: #fff; margin: 25px 0 15px 0; padding-left: 10px; border-left: 3px solid #d4af37; text-transform: uppercase; letter-spacing: 1px; }
        .admin-label { display: block; font-size: 10px; color: #d4af37; font-weight: 800; margin-bottom: 8px; text-transform: uppercase; }
        .admin-input { width: 100%; padding: 12px; background: #050505; border: 1px solid #222; color: #fff; border-radius: 8px; box-sizing: border-box; transition: 0.3s; margin-bottom: 5px; }
        .admin-input:focus { border-color: #d4af37; outline: none; background: #000; }
        .btn-submit { background: #d4af37; color: #000; border: none; padding: 15px; border-radius: 8px; font-weight: 900; cursor: pointer; width: 100%; font-size: 16px; margin-top:20px; }
        .btn-submit:hover { background: #fff; }
        .btn-back { color: #888; text-decoration: none; font-size: 13px; display: flex; align-items: center; gap: 5px; }
        
        /* Toast */
        .toast-msg { position: fixed; top: 30px; right: 20px; z-index: 99999; background: #000; color: #2ecc71; border: 1px solid #2ecc71; padding: 15px 25px; border-radius: 5px; font-weight: bold; display: flex; align-items: center; gap: 10px; transform: translateX(120%); animation: slideInRight 0.5s forwards; }
        @keyframes slideInRight { 0% { transform: translateX(120%); opacity: 0; } 100% { transform: translateX(0); opacity: 1; } }
        @keyframes slideOutRight { 0% { transform: translateX(0); opacity: 1; } 100% { transform: translateX(120%); opacity: 0; } }
    </style>
</head>
<body class="admin-body">

<?php include 'sidebar.php'; ?>

<?php if (isset($_SESSION['admin_toast'])): ?>
    <div id="toast-notification" class="toast-msg">
        <i class="fa-solid fa-circle-check" style="font-size: 20px;"></i>
        <span><?= $_SESSION['admin_toast'] ?></span>
    </div>
    <?php unset($_SESSION['admin_toast']); ?>
    <script>
        setTimeout(() => {
            const t = document.getElementById('toast-notification');
            if(t) { t.style.animation = 'slideOutRight 0.5s forwards'; setTimeout(() => t.remove(), 500); }
        }, 3000);
    </script>
<?php endif; ?>

<main class="main-content-admin">
    <div class="container">
        <div class="header">
            <div>
                <h1 style="color:#d4af37; margin:0;">CẬP NHẬT BIẾN THỂ</h1>
                <p style="color:#666; margin: 5px 0 0 0;">
                    Sản phẩm: <strong style="color:#fff;"><?= htmlspecialchars($variant['ten_san_pham']) ?></strong>
                </p>
                <input type="hidden" id="hidden_brand" value="<?= htmlspecialchars($variant['ten_thuong_hieu']) ?>">
                <input type="hidden" id="hidden_cat" value="<?= htmlspecialchars($variant['ten_danh_muc']) ?>">
                <input type="hidden" id="current_storage" value="<?= htmlspecialchars($variant['dung_luong']) ?>">
            </div>
            <a href="index.php?ctrl=admin&act=manage_products" class="btn-back">
                <i class="fa-solid fa-arrow-left"></i> QUAY LẠI
            </a>
        </div>

        <form action="" method="POST" enctype="multipart/form-data">
            
            <div class="section-title">Thông tin cơ bản</div>
            <div style="display:grid; grid-template-columns: 1fr 1fr; gap:20px;">
                <div>
                    <label class="admin-label">Màu sắc</label>
                    <input type="text" name="mau_sac" class="admin-input" value="<?= htmlspecialchars($variant['mau_sac']) ?>" required>
                </div>
                <div>
                    <label class="admin-label">Dung lượng</label>
                    <select name="dung_luong" id="dung_luong_select" class="admin-input" required>
                        <option value="<?= htmlspecialchars($variant['dung_luong']) ?>"><?= htmlspecialchars($variant['dung_luong']) ?></option>
                    </select>
                </div>
            </div>

            <div class="section-title">Giá & Kho</div>
            <div style="display:grid; grid-template-columns: 1fr 1fr 1fr; gap:15px;">
                <div>
                    <label class="admin-label">Giá bán (đ)</label>
                    <input type="number" name="gia_ban" class="admin-input" value="<?= $variant['gia_ban'] ?>" required>
                </div>
                <div>
                    <label class="admin-label">Giảm (%)</label>
                    <input type="number" name="muc_giam_gia" class="admin-input" value="<?= $variant['muc_giam_gia'] ?>">
                </div>
                <div>
                    <label class="admin-label">Tồn kho</label>
                    <input type="number" name="so_luong_ton" class="admin-input" value="<?= $variant['so_luong_ton'] ?>" required>
                </div>
            </div>

            <div class="section-title">Hình ảnh</div>
            <div style="display: flex; align-items: center; gap: 20px;">
                <div>
                    <label class="admin-label">Ảnh hiện tại:</label>
                    <?php 
                        $imgUrl = !empty($variant['url_anh']) ? 'public/images/' . $variant['url_anh'] : 'public/images/default.png';
                    ?>
                    <img src="<?= $imgUrl ?>" style="width: 80px; height: 80px; object-fit: cover; border-radius: 8px; border: 1px solid #333;">
                </div>
                <div style="flex: 1;">
                    <label class="admin-label">Thay đổi ảnh (Chọn ảnh mới để thay thế):</label>
                    <input type="file" name="anh_bien_the[]" class="admin-input">
                </div>
            </div>

            <button type="submit" class="btn-submit">LƯU THAY ĐỔI</button>
        </form>
    </div>
</main>

<script>
    // JS Tự động load list dung lượng và select đúng giá trị cũ
    document.addEventListener('DOMContentLoaded', function() {
        const brand = document.getElementById('hidden_brand').value.toUpperCase();
        const cat = document.getElementById('hidden_cat').value.toUpperCase();
        const currentVal = document.getElementById('current_storage').value;
        const select = document.getElementById('dung_luong_select');
        
        let opts = [];
        if (cat.includes('ĐIỆN THOẠI') || cat.includes('PHONE')) {
            if (brand.includes('APPLE') || brand.includes('IPHONE')) opts = ["64GB", "128GB", "256GB", "512GB", "1TB"];
            else if (brand.includes('SAMSUNG')) opts = ["128GB", "256GB", "512GB", "1TB"];
            else opts = ["64GB", "128GB", "256GB", "512GB"];
        } else if (cat.includes('LAPTOP')) {
            opts = ["8GB/256GB", "8GB/512GB", "16GB/512GB", "16GB/1TB", "32GB/1TB"];
        } else {
            opts = ["Tiêu chuẩn", "FullBox"];
        }

        select.innerHTML = '';
        opts.forEach(o => {
            let op = document.createElement('option');
            op.value = op.text = o;
            if(o === currentVal) op.selected = true; // Chọn lại giá trị cũ
            select.add(op);
        });
        
        // Nếu giá trị cũ không nằm trong list tự động (do nhập tay hoặc list đổi), thêm nó vào
        if (select.value !== currentVal) {
             let op = document.createElement('option');
             op.value = op.text = currentVal;
             op.selected = true;
             select.add(op);
        }
    });
</script>

</body>
</html>