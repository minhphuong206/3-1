<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Sửa sản phẩm | PhươngSTORE</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background: #000; color: #fff; font-family: 'Segoe UI', sans-serif; margin: 0; padding: 20px; }
        .edit-container { max-width: 1000px; margin: 30px auto; background: #0a0a0a; padding: 40px; border-radius: 20px; border: 1px solid #d4af3733; }
        .admin-label { display: block; font-size: 10px; color: #d4af37; font-weight: 800; margin-bottom: 8px; text-transform: uppercase; }
        .admin-input { width: 100%; padding: 12px; background: #050505; border: 1px solid #222; color: #fff; border-radius: 8px; box-sizing: border-box; margin-bottom: 5px; transition: 0.3s; }
        .admin-input:focus { border-color: #d4af37; outline: none; background: #000; }
        .section-title { font-size: 15px; color: #fff; margin: 25px 0 15px 0; padding-left: 10px; border-left: 3px solid #d4af37; text-transform: uppercase; letter-spacing: 1px; }
        .grid-form { display: grid; grid-template-columns: 1fr 1fr; gap: 40px; }
        
        /* Bẫy lỗi chữ đỏ */
        .error-msg { color: #ff4d4f; font-size: 10px; font-weight: bold; margin-bottom: 15px; display: none; }
        .was-validated .admin-input:invalid { border-color: #ff4d4f !important; background: #1a0000; }
        .was-validated .admin-input:invalid + .error-msg { display: block; }

        /* Quản lý ảnh */
        .admin-thumb { width: 100px; height: 100px; object-fit: cover; border-radius: 8px; border: 2px solid #222; cursor: pointer; transition: 0.2s; }
        .admin-thumb.selected { border-color: #d4af37 !important; transform: scale(1.05); box-shadow: 0 0 10px #d4af3755; }
        
        /* Khung giá */
        .price-box-edit { background: #111; padding: 25px; border-radius: 15px; border: 1px dashed #d4af37; margin-top: 20px; }
        .btn-update { background: #d4af37; color: #000; padding: 18px 40px; border: none; border-radius: 10px; font-weight: 900; cursor: pointer; font-size: 16px; transition: 0.3s; width: 100%; }
        .btn-update:hover { background: #fff; transform: translateY(-2px); }
    </style>
</head>
<body>

<div class="edit-container">
    <div style="display:flex; justify-content: space-between; align-items: center; margin-bottom: 30px; border-bottom: 1px solid #222; padding-bottom: 20px;">
        <div>
            <h2 style="color:#d4af37; margin:0; letter-spacing: 2px;">CHỈNH SỬA SẢN PHẨM</h2>
            <p style="color:#666; margin:5px 0 0 0;">ID Sản phẩm: #<?= $product['ma_san_pham'] ?? 'N/A' ?></p>
        </div>
        <a href="index.php?ctrl=admin&act=dashboard" style="color:#888; text-decoration:none; font-size: 13px;"><i class="fa-solid fa-arrow-left"></i> QUAY LẠI</a>
    </div>

    <form action="index.php?ctrl=admin&act=edit_product&id=<?= $product['ma_san_pham'] ?>" method="POST" id="editForm" novalidate>
        <div class="grid-form">
            <div>
                <div class="section-title">Thông tin cơ bản</div>
                <label class="admin-label">Tên sản phẩm (*)</label>
                <input type="text" name="ten_san_pham" class="admin-input" value="<?= htmlspecialchars($product['ten_san_pham'] ?? '') ?>" required>
                <div class="error-msg">Tên sản phẩm không được để trống!</div>

                <div style="display:grid; grid-template-columns: 1fr 1fr; gap:15px;">
                    <div>
                        <label class="admin-label">Danh mục (*)</label>
                        <select name="ma_danh_muc" id="cat_select" class="admin-input" required onchange="updateEditStorage()">
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?= $cat['ma_danh_muc'] ?>" <?= ($cat['ma_danh_muc'] == ($product['ma_danh_muc'] ?? '')) ? 'selected' : '' ?>>
                                    <?= $cat['ten_danh_muc'] ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label class="admin-label">Thương hiệu (*)</label>
                        <select name="ma_thuong_hieu" id="brand_select" class="admin-input" required onchange="updateEditStorage()">
                            <?php foreach ($brands as $br): ?>
                                <option value="<?= $br['ma_thuong_hieu'] ?>" <?= ($br['ma_thuong_hieu'] == ($product['ma_thuong_hieu'] ?? '')) ? 'selected' : '' ?>>
                                    <?= $br['ten_thuong_hieu'] ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <label class="admin-label">Trạng thái hiển thị</label>
                <select name="kich_hoat" class="admin-input" required>
                    <option value="1" <?= ($product['kich_hoat'] ?? 1) == 1 ? 'selected' : '' ?>>Đang kinh doanh</option>
                    <option value="0" <?= ($product['kich_hoat'] ?? 1) == 0 ? 'selected' : '' ?>>Tạm ẩn / Ngừng bán</option>
                </select>

                <label class="admin-label">Phân loại Tag (*)</label>
                <select name="tag" class="admin-input" required>
                    <option value="normal" <?= ($product['tag'] ?? '') == 'normal' ? 'selected' : '' ?>>Bình thường</option>
                    <option value="hot" <?= ($product['tag'] ?? '') == 'hot' ? 'selected' : '' ?>>HOT 🔥</option>
                    <option value="new" <?= ($product['tag'] ?? '') == 'new' ? 'selected' : '' ?>>NEW ✨</option>
                </select>
            </div>

            <div>
                <div class="section-title">Thông số & Tag</div>
                <div style="display:grid; grid-template-columns: 1fr 1fr; gap:10px;">
                    <div>
                        <label class="admin-label">CPU (*)</label>
                        <input type="text" name="spec_cpu" class="admin-input" value="<?= htmlspecialchars($product['specs']['cpu'] ?? '') ?>" required>
                        <div class="error-msg">Nhập CPU!</div>
                    </div>
                    <div>
                        <label class="admin-label">RAM (*)</label>
                        <input type="text" name="spec_ram" class="admin-input" value="<?= htmlspecialchars($product['specs']['ram'] ?? '') ?>" required>
                        <div class="error-msg">Nhập RAM!</div>
                    </div>
                    <div style="grid-column: span 2;">
                        <label class="admin-label">Dung lượng (*)</label>
                        <select name="dung_luong" id="dung_luong_select" class="admin-input" required></select>
                        <div class="error-msg">Chọn dung lượng!</div>
                    </div>
                </div>

                <div style="margin-top: 15px;">
                    <label class="admin-label">Mô tả sản phẩm (*)</label>
                    <textarea name="mo_ta" class="admin-input" rows="5" required><?= htmlspecialchars($product['mo_ta'] ?? '') ?></textarea>
                    <div class="error-msg">Mô tả không được để trống!</div>
                </div>
            </div>
        </div>

        <div class="section-title">Quản lý hình ảnh sản phẩm</div>
        <p style="font-size: 10px; color: #555; margin-bottom: 10px;">* Click vào ảnh để chọn làm ảnh đại diện chính của sản phẩm</p>
        <div style="display: flex; gap: 15px; flex-wrap: wrap; background: #111; padding: 20px; border-radius: 12px; border: 1px solid #333;">
            <?php 
            $images = $product['images'] ?? [];
            foreach ($images as $img): 
                $isMain = ($img['is_main'] ?? 0) == 1;
            ?>
                <div style="position: relative; cursor: pointer;">
                    <img src="public/images/<?= $img['url_anh'] ?>" 
                         class="admin-thumb <?= $isMain ? 'selected' : '' ?>" 
                         onclick="selectImage(<?= $img['ma_hinh_anh'] ?? 0 ?>, this)">
                    <?php if($isMain): ?>
                        <span style="position: absolute; top: -5px; right: -5px; background: #d4af37; color: #000; font-size: 8px; padding: 2px 5px; border-radius: 5px; font-weight: bold;">CHÍNH</span>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
        <input type="hidden" name="main_image_id" id="input_main_id" value="<?= $product['main_image_id'] ?? '' ?>">

        <div class="section-title">Giá & Tồn kho (Biến thể đầu)</div>
        <div class="price-box-edit">
            <div style="display:grid; grid-template-columns: 1fr 1fr 1fr; gap:20px;">
                <div>
                    <label class="admin-label">Giá bán gốc (đ)</label>
                    <input type="number" name="gia_ban" id="edit_gia" class="admin-input" value="<?= $product['variants'][0]['gia_ban'] ?? 0 ?>" oninput="calcPrice()" required min="1000">
                    <div class="error-msg">Giá từ 1.000đ!</div>
                </div>
                <div>
                    <label class="admin-label">Giảm giá (%)</label>
                    <input type="number" name="muc_giam_gia" id="edit_giam" class="admin-input" value="<?= $product['variants'][0]['muc_giam_gia'] ?? 0 ?>" min="0" max="100" oninput="calcPrice()">
                </div>
                <div>
                    <label class="admin-label">Số lượng kho</label>
                    <input type="number" name="so_luong_ton" class="admin-input" value="<?= $product['variants'][0]['so_luong_ton'] ?? 0 ?>" min="0" step="1" required>
                    <div class="error-msg">Nhập số nguyên >= 0!</div>
                </div>
            </div>
            <div style="margin-top: 15px; border-top: 1px solid #222; padding-top: 15px;">
                <span style="font-size:11px; color:#888;">GIÁ SAU GIẢM:</span><br>
                <span id="display_price" style="font-size:32px; font-weight:900; color:#d4af37;">0</span> <span style="color:#d4af37;">VNĐ</span>
            </div>
        </div>

        <div style="margin-top: 40px; text-align: center; border-top: 1px solid #333; padding-top: 30px;">
            <button type="submit" class="btn-update">LƯU TẤT CẢ THAY ĐỔI</button>
        </div>
    </form>
</div>

<script>
    function calcPrice() {
        let gia = document.getElementById('edit_gia').value || 0;
        let giam = document.getElementById('edit_giam').value || 0;
        let final = gia * (1 - giam/100);
        document.getElementById('display_price').innerText = new Intl.NumberFormat().format(Math.round(final));
    }

    function selectImage(id, el) {
        document.querySelectorAll('.admin-thumb').forEach(img => img.classList.remove('selected'));
        el.classList.add('selected');
        document.getElementById('input_main_id').value = id;
    }

    function updateEditStorage() {
        const brand = document.getElementById('brand_select');
        const storage = document.getElementById('dung_luong_select');
        const currentVal = "<?= $product['variants'][0]['dung_luong'] ?? '' ?>";
        const brandName = brand.options[brand.selectedIndex].text.toUpperCase();
        
        let options = (brandName.includes("APPLE")) ? ["64GB", "128GB", "256GB", "512GB", "1TB (1024GB)", "2TB (2048GB)"] : 
                      (brandName.includes("SAMSUNG")) ? ["64GB", "128GB", "256GB", "512GB", "1TB (1024GB)"] : ["128GB", "256GB", "512GB", "1TB", "8GB RAM", "16GB RAM"];

        storage.innerHTML = '<option value="">-- Chọn dung lượng --</option>';
        options.forEach(opt => {
            const el = document.createElement('option');
            el.value = el.text = opt;
            if (opt === currentVal) el.selected = true;
            storage.add(el);
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        calcPrice();
        updateEditStorage();
        const form = document.getElementById('editForm');
        form.addEventListener('submit', function(e) {
            if (!form.checkValidity()) {
                e.preventDefault(); e.stopPropagation();
                form.classList.add('was-validated');
                const err = form.querySelector(':invalid');
                if (err) err.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        });
    });
</script>
</body>
</html>