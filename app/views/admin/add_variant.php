<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Thêm biến thể | PhươngSTORE</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="public/css/admin.css">
    <style>
        /* CSS cho Form đẹp hơn */
        body { background: #000; color: #fff; font-family: 'Segoe UI', sans-serif; margin: 0; padding: 20px; }
        .container { max-width: 900px; margin: 0 auto; background: #0a0a0a; padding: 40px; border-radius: 20px; border: 1px solid rgba(212, 175, 55, 0.2); }
        .header { display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #222; padding-bottom: 20px; margin-bottom: 30px; }
        .section-title { font-size: 14px; color: #fff; margin: 25px 0 15px 0; padding-left: 10px; border-left: 3px solid #d4af37; text-transform: uppercase; letter-spacing: 1px; }
        .admin-label { display: block; font-size: 10px; color: #d4af37; font-weight: 800; margin-bottom: 8px; text-transform: uppercase; }
        .admin-input { width: 100%; padding: 12px; background: #050505; border: 1px solid #222; color: #fff; border-radius: 8px; box-sizing: border-box; transition: 0.3s; margin-bottom: 5px; }
        .admin-input:focus { border-color: #d4af37; outline: none; background: #000; }
        .error-msg { color: #ff4d4f; font-size: 11px; margin-top: 2px; display: none; }
        .price-display-box { background: #111; padding: 20px; border-radius: 12px; border: 1px dashed #d4af37; margin-top: 20px; text-align: center; }
        #variant_preview { display: flex; gap: 10px; margin-top: 15px; flex-wrap: wrap; }
        #variant_preview img { width: 80px; height: 80px; object-fit: cover; border-radius: 8px; border: 2px solid #222; cursor: pointer; transition: 0.2s; }
        #variant_preview img.selected { border-color: #d4af37; transform: scale(1.05); box-shadow: 0 0 10px rgba(212,175,55,0.5); }
        .btn-submit { background: #d4af37; color: #000; border: none; padding: 18px; border-radius: 10px; font-weight: 900; cursor: pointer; width: 100%; font-size: 16px; transition: 0.3s; text-transform: uppercase; }
        .btn-submit:hover { background: #fff; box-shadow: 0 0 15px rgba(212,175,55,0.4); }
        .btn-back { color: #888; text-decoration: none; font-size: 13px; display: flex; align-items: center; gap: 5px; }
        .btn-back:hover { color: #d4af37; }

        /* TOAST THÔNG BÁO */
        .toast-msg {
    position: fixed; 
    top: 30px; 
    right: 20px; /* Vị trí bên phải */
    z-index: 99999;
    
    background: #000; 
    color: #2ecc71; 
    border: 1px solid #2ecc71;
    border-left: 5px solid #2ecc71; /* Điểm nhấn viền trái */
    
    padding: 15px 25px; 
    border-radius: 5px; 
    font-weight: bold;
    font-size: 14px;
    box-shadow: 0 5px 20px rgba(0,0,0,0.5);
    
    display: flex; 
    align-items: center; 
    gap: 12px;

    /* BAN ĐẦU ẨN Ở BÊN PHẢI MÀN HÌNH */
    transform: translateX(120%); 
    opacity: 0;
    
    /* CHẠY HIỆU ỨNG KHI XUẤT HIỆN */
    animation: slideInRight 0.5s cubic-bezier(0.68, -0.55, 0.27, 1.55) forwards;
}
       @keyframes slideInRight {
    0% { transform: translateX(120%); opacity: 0; }
    100% { transform: translateX(0); opacity: 1; }
}

/* Hiệu ứng trượt ra (lúc tắt) */
@keyframes slideOutRight {
    0% { transform: translateX(0); opacity: 1; }
    100% { transform: translateX(120%); opacity: 0; }
}
    </style>
</head>
<body class="admin-body">

<?php if (isset($_SESSION['admin_toast'])): ?>
    <div id="toast-notification" class="toast-msg">
        <i class="fa-solid fa-circle-check" style="font-size: 22px;"></i>
        <span><?= $_SESSION['admin_toast'] ?></span>
    </div>
    
    <?php unset($_SESSION['admin_toast']); // Xóa session ngay lập tức ?>
    
    <script>
        // Đợi 3 giây (3000ms)
        setTimeout(() => {
            const toast = document.getElementById('toast-notification');
            if (toast) {
                // 1. Gán animation trượt ra
                toast.style.animation = 'slideOutRight 0.5s forwards';
                
                // 2. Đợi animation chạy xong (0.5s) thì xóa hẳn khỏi HTML
                setTimeout(() => {
                    toast.remove();
                }, 500); 
            }
        }, 3000); 
    </script>
<?php endif; ?>

<main class="main-content-admin">
    
    <?php if (isset($_SESSION['admin_toast'])): ?>
        <div id="toast-notification" class="toast-msg">
            <i class="fa-solid fa-circle-check" style="font-size: 20px;"></i>
            <span><?= $_SESSION['admin_toast'] ?></span>
        </div>
        <?php unset($_SESSION['admin_toast']); ?>
        <script>
            setTimeout(() => {
            const t = document.getElementById('toast-notification');
            if(t) { 
                t.style.transition = "opacity 0.5s ease";
                t.style.opacity = 0; 
                setTimeout(() => t.remove(), 500); 
            }
        }, 3000);
        </script>
    <?php endif; ?>

    <div class="container">
        <div class="header">
            <div>
                <h1 style="color:#d4af37; margin:0; letter-spacing: 2px;">THÊM BIẾN THỂ</h1>
                <p style="color:#666; margin: 5px 0 0 0;">
                    Sản phẩm: <span style="color:#fff; font-weight:bold; font-size: 16px;"><?= htmlspecialchars($product['ten_san_pham']) ?></span>
                </p>
                <input type="hidden" id="hidden_brand" value="<?= htmlspecialchars($product['ten_thuong_hieu'] ?? '') ?>">
                <input type="hidden" id="hidden_cat" value="<?= htmlspecialchars($product['ten_danh_muc'] ?? '') ?>">
            </div>
            <a href="index.php?ctrl=admin&act=manage_products" class="btn-back">
                <i class="fa-solid fa-arrow-left"></i> QUAY LẠI
            </a>
        </div>

        <form action="index.php?ctrl=admin&act=add_variant&product_id=<?= $product['ma_san_pham'] ?>" 
              method="POST" id="variantForm" enctype="multipart/form-data" novalidate>
            
            <div class="section-title">Thông tin phiên bản</div>
            <div style="display:grid; grid-template-columns: 1fr 1fr; gap:20px;">
                <div>
                    <label class="admin-label">Màu sắc (*)</label>
                    <input type="text" name="mau_sac" class="admin-input" placeholder="VD: Titan Sa Mạc" required>
                    <div class="error-msg">Vui lòng nhập màu sắc!</div>
                </div>
                <div>
                    <label class="admin-label">Dung lượng (*)</label>
                    <select name="dung_luong" id="dung_luong_select" class="admin-input" required>
                        <option value="">-- Đang nạp dữ liệu... --</option>
                    </select>
                    <div class="error-msg">Vui lòng chọn mức dung lượng!</div>
                </div>
            </div>

            <div class="section-title">Giá & Tồn kho</div>
            <div style="display:grid; grid-template-columns: 1fr 1fr 1fr; gap:15px;">
                <div>
                    <label class="admin-label">Giá bán (đ)</label>
                    <input type="number" name="gia_ban" id="v_gia" class="admin-input" oninput="calcVPrice()" required min="1000">
                    <div class="error-msg">Giá phải từ 1.000đ!</div>
                </div>
                <div>
                    <label class="admin-label">Giảm (%)</label>
                    <input type="number" name="muc_giam_gia" id="v_giam" class="admin-input" value="0" min="0" max="100" oninput="calcVPrice()">
                </div>
                <div>
                    <label class="admin-label">Kho hàng (*)</label>
                    <input type="number" name="so_luong_ton" class="admin-input" value="10" min="0" required>
                    <div class="error-msg">Số lượng không hợp lệ!</div>
                </div>
            </div>

            <div class="price-display-box">
                <span style="font-size:11px; color:#888; text-transform: uppercase;">GIÁ HIỂN THỊ ĐẾN KHÁCH HÀNG:</span><br>
                <span id="v_display" style="font-size:32px; font-weight:900; color:#d4af37;">0</span> 
                <span style="color:#d4af37;">VNĐ</span>
            </div>

            <div class="section-title">Hình ảnh riêng cho màu này</div>
            <div style="margin-bottom: 30px;">
                <label class="admin-label">Tải lên bộ sưu tập ảnh (Click ảnh để chọn ảnh chính)</label>
                <input type="file" name="anh_bien_the[]" id="file_variant" class="admin-input" multiple onchange="previewVariantImages()" required>
                <div class="error-msg">Vui lòng tải lên ít nhất 1 ảnh!</div>
                
                <div id="variant_preview"></div>
                <input type="hidden" name="variant_main_idx" id="v_main_idx" value="0">
            </div>

            <div style="border-top: 1px solid #333; padding-top: 30px; text-align: center;">
                <button type="submit" class="btn-submit">
                    XÁC NHẬN THÊM BIẾN THỂ
                </button>
            </div>
        </form>
    </div>
</main>

<script>
    // 1. Tính giá bán
    function calcVPrice() {
        let gia = document.getElementById('v_gia').value || 0;
        let giam = document.getElementById('v_giam').value || 0;
        let total = gia * (1 - giam/100);
        document.getElementById('v_display').innerText = new Intl.NumberFormat('vi-VN').format(Math.round(total));
    }

    // 2. LOGIC TỰ ĐỘNG LIST DUNG LƯỢNG (Dựa trên Hãng/Danh mục)
    document.addEventListener('DOMContentLoaded', function() {
        const storageSelect = document.getElementById('dung_luong_select');
        
        // Lấy dữ liệu từ input hidden (An toàn hơn in trực tiếp vào JS)
        const brandName = document.getElementById('hidden_brand').value.toUpperCase();
        const categoryName = document.getElementById('hidden_cat').value.toUpperCase();
        
        console.log("Detect:", categoryName, brandName); // Debug xem nó nhận diện đúng không

        let options = []; 

        if (categoryName.includes("ĐIỆN THOẠI") || categoryName.includes("PHONE")) {
            if (brandName.includes("APPLE") || brandName.includes("IPHONE")) {
                options = ["64GB", "128GB", "256GB", "512GB", "1TB"];
            } else if (brandName.includes("SAMSUNG")) {
                options = ["128GB", "256GB", "512GB", "1TB"];
            } else {
                options = ["64GB", "128GB", "256GB", "512GB"]; // Xiaomi, Oppo...
            }
        } else if (categoryName.includes("LAPTOP") || categoryName.includes("MACBOOK")) {
            options = ["8GB/256GB", "8GB/512GB", "16GB/512GB", "16GB/1TB", "32GB/1TB", "32GB/2TB"];
        } else {
            // Mặc định cho Phụ kiện hoặc khác
            options = ["Tiêu chuẩn", "FullBox", "Combo"];
        }

        // Render ra Select Box
        storageSelect.innerHTML = '<option value="">-- Chọn phiên bản --</option>';
        options.forEach(opt => {
            let el = document.createElement('option');
            el.value = opt;
            el.text = opt;
            storageSelect.add(el);
        });

        // 3. Validation Form
        const inputs = document.querySelectorAll('.admin-input[required]');
        inputs.forEach(input => {
            input.addEventListener('invalid', function(e) {
                e.preventDefault();
                this.style.borderColor = '#ff4d4f';
                const error = this.nextElementSibling;
                if (error && error.classList.contains('error-msg')) error.style.display = 'block';
            });
            input.addEventListener('input', function() {
                if (this.validity.valid) {
                    this.style.borderColor = '#222';
                    const error = this.nextElementSibling;
                    if (error && error.classList.contains('error-msg')) error.style.display = 'none';
                }
            });
        });

        const form = document.getElementById('variantForm');
        form.addEventListener('submit', function(event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
                const err = form.querySelector(':invalid');
                if (err) err.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        });
    });

    // 4. Preview ảnh
    function previewVariantImages() {
        const area = document.getElementById('variant_preview');
        area.innerHTML = "";
        const files = document.getElementById('file_variant').files;
        for (let i = 0; i < files.length; i++) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const img = document.createElement('img');
                img.src = e.target.result;
                if (i === 0) img.classList.add('selected'); 
                img.onclick = function() {
                    document.querySelectorAll('#variant_preview img').forEach(el => el.classList.remove('selected'));
                    this.classList.add('selected');
                    document.getElementById('v_main_idx').value = i;
                };
                area.appendChild(img);
            }
            reader.readAsDataURL(files[i]);
        }
    }
</script>
</body>
</html>