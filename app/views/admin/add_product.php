<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Thêm Sản Phẩm Mới | PhươngSTORE</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="public/css/admin.css">
    <style>
        body { background: #000; color: #fff; font-family: 'Segoe UI', sans-serif; margin: 0; padding: 20px; }
        .container { max-width: 1200px; margin: 0 auto; background: #0a0a0a; padding: 40px; border-radius: 20px; border: 1px solid rgba(212, 175, 55, 0.2); }
        .header { display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #222; padding-bottom: 20px; margin-bottom: 30px; }
        .grid-main { display: grid; grid-template-columns: 1fr 1fr; gap: 40px; }
        .section-title { font-size: 14px; color: #fff; margin: 25px 0 15px 0; padding-left: 10px; border-left: 3px solid #d4af37; text-transform: uppercase; letter-spacing: 1px; }
        .admin-label { display: block; font-size: 10px; color: #d4af37; font-weight: 800; margin-bottom: 8px; text-transform: uppercase; }
        .admin-input { width: 100%; padding: 12px; background: #050505; border: 1px solid #222; color: #fff; border-radius: 8px; box-sizing: border-box; transition: 0.3s; margin-bottom: 5px; }
        .admin-input:focus { border-color: #d4af37; outline: none; background: #000; }
        .error-msg { color: #ff4d4f; font-size: 11px; margin-top: 2px; display: none; }
        .price-display-box { background: #111; padding: 20px; border-radius: 12px; border: 1px dashed #d4af37; margin-top: 20px; text-align: center; }
        #preview_area { display: flex; gap: 10px; margin-top: 15px; flex-wrap: wrap; }
        #preview_area img { width: 80px; height: 80px; object-fit: cover; border-radius: 8px; border: 2px solid #222; cursor: pointer; }
        #preview_area img.selected { border-color: #d4af37; transform: scale(1.05); }
        .btn-submit { background: #d4af37; color: #000; border: none; padding: 18px; border-radius: 10px; font-weight: 900; cursor: pointer; width: 100%; font-size: 16px; transition: 0.3s; text-transform: uppercase; }
        .btn-submit:hover { background: #fff; box-shadow: 0 0 15px rgba(212,175,55,0.4); }
        .btn-back { color: #888; text-decoration: none; font-size: 13px; display: flex; align-items: center; gap: 5px; }
        .btn-back:hover { color: #d4af37; }
        .spec-group { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 10px; }
    </style>
</head>
<body>

<div class="container">
    <div class="header">
        <div>
            <h1 style="color:#d4af37; margin:0; letter-spacing: 2px;">THÊM SẢN PHẨM MỚI</h1>
            <p style="color:#666; margin: 5px 0 0 0;">Cập nhật hệ thống bẫy lỗi thông số kỹ thuật</p>
        </div>
        <a href="index.php?ctrl=admin&act=manage_products" class="btn-back"><i class="fa-solid fa-arrow-left"></i> QUAY LẠI</a>
    </div>

    <form action="index.php?ctrl=admin&act=add_product" method="POST" enctype="multipart/form-data" id="addForm" novalidate>
        <div class="grid-main">
            <div>
                <div class="section-title">Thông tin định danh</div>
                <label class="admin-label">Tên sản phẩm (*)</label>
                <input type="text" name="ten_san_pham" class="admin-input" placeholder="VD: iPhone 16 Pro Max" required>
                <div class="error-msg">Vui lòng nhập tên sản phẩm!</div>

                <div class="spec-group" style="margin-top:15px;">
                    <div>
                        <label class="admin-label">Danh mục (*)</label>
                        <select name="ma_danh_muc" id="cat_select" class="admin-input" required>
                            <option value="">-- Chọn danh mục --</option>
                            <?php if (!empty($categories)): foreach ($categories as $cat): ?>
                                <option value="<?= $cat['ma_danh_muc'] ?>"><?= $cat['ten_danh_muc'] ?></option>
                            <?php endforeach; endif; ?>
                        </select>
                        <div class="error-msg">Vui lòng chọn danh mục!</div>
                    </div>
                    <div>
                        <label class="admin-label">Thương hiệu (*)</label>
                        <select name="ma_thuong_hieu" id="brand_select" class="admin-input" required>
                            <option value="">-- Chọn danh mục trước --</option>
                        </select>
                        <div class="error-msg">Vui lòng chọn thương hiệu!</div>
                    </div>
                </div>

                <div class="section-title">Biến thể mặc định</div>
                <div class="spec-group">
                    <div>
                        <label class="admin-label">Màu sắc (*)</label>
                        <input type="text" name="mau_sac" class="admin-input" placeholder="VD: Titan Sa Mạc" required>
                        <div class="error-msg">Nhập màu sắc!</div>
                    </div>
                    <div>
                        <label class="admin-label">RAM (*)</label>
                        <input type="text" name="ram" class="admin-input" placeholder="VD: 8GB" required>
                        <div class="error-msg">Nhập dung lượng RAM!</div>
                    </div>
                </div>
                <div class="spec-group">
                    <div>
                        <label class="admin-label">Bộ nhớ trong (ROM) (*)</label>
                        <select name="dung_luong" id="storage_select" class="admin-input" required>
                            <option value="">-- Chọn thương hiệu trước --</option>
                        </select>
                        <div class="error-msg">Chọn bộ nhớ trong!</div>
                    </div>
                    <div>
                        <label class="admin-label">Kho hàng (*)</label>
                        <input type="number" name="so_luong_ton" class="admin-input" value="10" min="0" required>
                        <div class="error-msg">Nhập số lượng tồn kho!</div>
                    </div>
                </div>

                <div class="spec-group">
                    <div>
                        <label class="admin-label">Giá gốc (đ) (*)</label>
                        <input type="number" name="gia_ban" id="inp_gia" class="admin-input" oninput="calcPrice()" required min="1000">
                        <div class="error-msg">Giá gốc từ 1.000đ!</div>
                    </div>
                    <div>
                        <label class="admin-label">Giảm (%)</label>
                        <input type="number" name="muc_giam_gia" id="inp_giam" class="admin-input" value="0" min="0" max="100" oninput="calcPrice()">
                    </div>
                </div>

                <div class="price-display-box">
                    <span id="price_display" style="font-size:32px; font-weight:900; color:#d4af37;">0</span> <span style="color:#d4af37;">VNĐ</span>
                </div>
            </div>

            <div>
                <div class="section-title">Thông số phần cứng chi tiết</div>
                
                <div class="spec-group">
                    <div>
                        <label class="admin-label">Màn hình (*)</label>
                        <input type="text" name="kich_thuoc_man_hinh" class="admin-input" placeholder="VD: 6.7 inch" required>
                        <div class="error-msg">Nhập kích thước màn hình!</div>
                    </div>
                    <div>
                        <label class="admin-label">Công nghệ màn (*)</label>
                        <input type="text" name="cong_nghe_man_hinh" class="admin-input" placeholder="VD: OLED" required>
                        <div class="error-msg">Nhập công nghệ màn hình!</div>
                    </div>
                </div>

                <div class="spec-group">
                    <div>
                        <label class="admin-label">Độ phân giải (*)</label>
                        <input type="text" name="phan_giai_man_hinh" class="admin-input" placeholder="VD: Super Retina" required>
                        <div class="error-msg">Nhập độ phân giải!</div>
                    </div>
                    <div>
                        <label class="admin-label">Chipset (*)</label>
                        <input type="text" name="chip_set" class="admin-input" placeholder="VD: A18 Pro" required>
                        <div class="error-msg">Nhập tên Chipset!</div>
                    </div>
                </div>

                <div class="spec-group">
                    <div>
                        <label class="admin-label">CPU (Tốc độ) (*)</label>
                        <input type="text" name="cpu" class="admin-input" placeholder="VD: 6 nhân" required>
                        <div class="error-msg">Nhập thông tin CPU!</div>
                    </div>
                    <div>
                        <label class="admin-label">Dung lượng Pin (*)</label>
                        <input type="text" name="pin" class="admin-input" placeholder="VD: 4500 mAh" required>
                        <div class="error-msg">Nhập dung lượng pin!</div>
                    </div>
                </div>

                <div class="spec-group">
                    <div>
                        <label class="admin-label">Camera sau (*)</label>
                        <input type="text" name="camera_sau" class="admin-input" placeholder="VD: 48MP/12MP" required>
                        <div class="error-msg">Nhập thông số camera sau!</div>
                    </div>
                    <div>
                        <label class="admin-label">Camera trước (*)</label>
                        <input type="text" name="camera_truoc" class="admin-input" placeholder="VD: 12MP" required>
                        <div class="error-msg">Nhập camera trước!</div>
                    </div>
                </div>

                <div class="spec-group">
                    <div>
                        <label class="admin-label">Thẻ SIM (*)</label>
                        <input type="text" name="the_sim" class="admin-input" placeholder="VD: 2 SIM" required>
                        <div class="error-msg">Nhập loại SIM hỗ trợ!</div>
                    </div>
                    <div>
                        <label class="admin-label">Trọng lượng (*)</label>
                        <input type="text" name="trong_luong" class="admin-input" placeholder="VD: 221g" required>
                        <div class="error-msg">Nhập trọng lượng máy!</div>
                    </div>
                </div>

                <div class="section-title">Hình ảnh & Mô tả</div>
                <label class="admin-label">Ảnh sản phẩm (*)</label>
                <input type="file" name="anh_san_pham[]" id="file_img" class="admin-input" multiple onchange="previewImages()" required>
                <div class="error-msg">Vui lòng chọn ít nhất 1 ảnh!</div>
                <div id="preview_area"></div>
                <input type="hidden" name="main_image_index" id="main_idx" value="0">

                <div style="margin-top:15px;">
                    <label class="admin-label">Mô tả sản phẩm (*)</label>
                    <textarea name="mo_ta" class="admin-input" rows="4" placeholder="Nhập mô tả..." required></textarea>
                    <div class="error-msg">Vui lòng nhập mô tả sản phẩm!</div>
                </div>
                
                <div class="spec-group" style="margin-top:15px;">
                    <div>
                        <label class="admin-label">Trạng thái</label>
                        <select name="kich_hoat" class="admin-input">
                            <option value="1">Kinh doanh</option>
                            <option value="0">Tạm ẩn</option>
                        </select>
                    </div>
                    <div>
                        <label class="admin-label">Tag</label>
                        <select name="tag" class="admin-input">
                            <option value="normal">Thường</option>
                            <option value="hot">HOT</option>
                            <option value="new">NEW</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <div style="margin-top:50px; text-align: center; border-top: 1px solid #333; padding-top: 30px;">
            <button type="submit" class="btn-submit">XÁC NHẬN THÊM SẢN PHẨM</button>
        </div>
    </form>
</div>

<script>
    // Toàn bộ logic Script giữ nguyên như cũ, hệ thống tự động bẫy lỗi các input [required]
    function calcPrice() {
        const gia = document.getElementById('inp_gia').value || 0;
        const giam = document.getElementById('inp_giam').value || 0;
        const total = gia * (1 - giam/100);
        document.getElementById('price_display').innerText = new Intl.NumberFormat('vi-VN').format(Math.round(total));
    }

    document.addEventListener('DOMContentLoaded', function() {
        const catSelect = document.getElementById('cat_select');
        const brandSelect = document.getElementById('brand_select');
        const storageSelect = document.getElementById('storage_select');

        catSelect.addEventListener('change', function() {
            const catId = this.value;
            brandSelect.innerHTML = '<option value="">Đang tải...</option>';
            if(catId) {
                fetch(`index.php?ctrl=admin&act=get_brands_by_cat&cat_id=${catId}`)
                    .then(r => r.json())
                    .then(data => {
                        brandSelect.innerHTML = '<option value="">-- Chọn thương hiệu --</option>';
                        data.forEach(b => {
                            const opt = document.createElement('option');
                            opt.value = b.ma_thuong_hieu; opt.textContent = b.ten_thuong_hieu;
                            brandSelect.appendChild(opt);
                        });
                    });
            }
        });

        brandSelect.addEventListener('change', function() {
            const brand = this.options[this.selectedIndex].text.toUpperCase();
            let options = brand.includes("APPLE") ? ["64GB", "128GB", "256GB", "512GB", "1TB"] : ["128GB", "256GB", "512GB", "1TB"];
            storageSelect.innerHTML = '<option value="">-- Chọn bộ nhớ --</option>';
            options.forEach(opt => {
                let el = document.createElement('option');
                el.value = opt; el.text = opt;
                storageSelect.add(el);
            });
        });

        // TỰ ĐỘNG BẪY LỖI CHO TẤT CẢ CÁC INPUT REQUIRED
        const inputs = document.querySelectorAll('.admin-input[required], textarea[required], select[required]');
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

        document.getElementById('addForm').addEventListener('submit', function(e) {
            if (!this.checkValidity()) {
                e.preventDefault();
                const firstInvalid = this.querySelector(':invalid');
                if(firstInvalid) firstInvalid.scrollIntoView({behavior: "smooth", block: "center"});
            }
        });
    });

    function previewImages() {
        const area = document.getElementById('preview_area');
        area.innerHTML = "";
        const files = document.getElementById('file_img').files;
        for (let i = 0; i < files.length; i++) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const img = document.createElement('img');
                img.src = e.target.result;
                if (i === 0) img.classList.add('selected');
                img.onclick = function() {
                    document.querySelectorAll('#preview_area img').forEach(el => el.classList.remove('selected'));
                    this.classList.add('selected');
                    document.getElementById('main_idx').value = i;
                };
                area.appendChild(img);
            }
            reader.readAsDataURL(files[i]);
        }
    }
</script>
</body>
</html>