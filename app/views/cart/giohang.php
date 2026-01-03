<?php 
// app/views/cart/giohang.php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once 'app/views/layouts/header.php'; 

// --- LOGIC: LẤY DỮ LIỆU ĐIỀN TỰ ĐỘNG ---
$saved = $_SESSION['saved_cart_data'] ?? [];

// 1. Họ tên: Ưu tiên dữ liệu nhập dở > Dữ liệu tài khoản > Rỗng
$valName = $saved['ho_ten'] ?? $_SESSION['ho_ten'] ?? '';

// 2. SĐT: Ưu tiên dữ liệu nhập dở > Dữ liệu tài khoản > Rỗng
// (Lưu ý: session login của bạn lưu key là 'user_phone' hoặc 'sdt' tùy vào AuthController, ở đây mình check cả 2)
$valPhone = $saved['so_dien_thoai'] ?? $_SESSION['user_phone'] ?? $_SESSION['sdt'] ?? '';

// 3. Email
$valEmail = $saved['email'] ?? $_SESSION['user_email'] ?? $_SESSION['email'] ?? '';

// 4. Ghi chú
$valNote = $saved['ghi_chu'] ?? '';

// 5. Địa chỉ (Chỉ lấy được phần ghi chú địa chỉ nếu có, vì dropdown cần JS phức tạp hơn)
// Ở đây mình để trống phần địa chỉ để khách chọn lại cho chính xác
?>

<style>
    .cart-page-header { margin-bottom: 30px; border-bottom: 1px solid #333; padding-bottom: 15px; }
    .cart-page-header h3 { font-size: 24px; text-transform: uppercase; margin: 0; background: var(--gold-gradient); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
    
    .cart-layout-grid { display: grid; grid-template-columns: 2fr 1fr; gap: 30px; align-items: start; }
    
    /* Cột trái */
    .cart-left-section { display: flex; flex-direction: column; gap: 20px; }
    .cart-list-header { background: #1a1a1a; padding: 15px; border-radius: 8px; display: flex; justify-content: space-between; border: 1px solid #333; }
    
    .cart-item-card { background: #1a1a1a; border: 1px solid #333; border-radius: 8px; padding: 20px; display: flex; align-items: center; gap: 20px; position: relative; }
    .item-image img { width: 80px; height: 80px; object-fit: cover; border-radius: 6px; border: 1px solid #444; }
    .item-info { flex: 1; }
    .item-name { font-weight: 600; color: #fff; margin-bottom: 5px; display: block; font-size: 15px; }
    .item-variant { font-size: 12px; color: #888; background: #252525; padding: 3px 8px; border-radius: 4px; }
    .price-real { color: var(--accent-red); font-weight: bold; font-size: 16px; }
    
    .qty-control-sm { display: flex; border: 1px solid #444; border-radius: 4px; width: fit-content; margin-top: 10px; }
    .qty-btn-sm { width: 25px; height: 25px; background: #333; color: #fff; display: flex; align-items: center; justify-content: center; font-size: 12px; }
    .qty-input-sm { width: 30px; height: 25px; background: transparent; border: none; color: #fff; text-align: center; font-size: 13px; }
    
    .btn-delete { color: #666; font-size: 18px; margin-left: 10px; transition: 0.3s; }
    .btn-delete:hover { color: var(--accent-red); }

    /* Form khách hàng */
    .customer-info-block, .payment-method-block { background: #1a1a1a; padding: 25px; border-radius: 8px; border: 1px solid #333; }
    .block-title { font-size: 16px; font-weight: 700; text-transform: uppercase; margin-bottom: 20px; border-bottom: 1px solid #333; padding-bottom: 10px; color: var(--gold-color); }
    
    .form-group { margin-bottom: 15px; }
    .form-control { width: 100%; padding: 12px; background: #252525; border: 1px solid #444; border-radius: 6px; color: #fff; outline: none; transition: 0.3s; }
    .form-control:focus { border-color: var(--gold-color); background: #000; }
    .error-message { color: #ff4d4f; font-size: 12px; margin-top: 5px; display: none; }
    .form-control.error { border-color: #ff4d4f; }

    /* Địa chỉ */
    .address-grid { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 10px; }
    
    /* Cột phải */
    .cart-right-section { position: sticky; top: 100px; }
    .cart-summary-box { background: #1a1a1a; padding: 25px; border-radius: 8px; border: 1px solid #333; }
    .summary-row { display: flex; justify-content: space-between; margin-bottom: 15px; color: #ccc; font-size: 14px; }
    .total-row { display: flex; justify-content: space-between; margin-top: 20px; padding-top: 20px; border-top: 1px solid #333; font-weight: bold; font-size: 18px; color: #fff; }
    .total-price { color: var(--accent-red); font-size: 24px; }
    
    .btn-checkout { width: 100%; background: var(--accent-red); color: white; padding: 15px; border: none; border-radius: 6px; font-weight: 800; text-transform: uppercase; margin-top: 20px; cursor: pointer; transition: 0.3s; }
    .btn-checkout:hover { background: #be123c; box-shadow: 0 5px 15px rgba(225, 29, 72, 0.4); }

    /* Radio button */
    .payment-option { display: flex; align-items: center; padding: 12px; border: 1px solid #444; border-radius: 6px; margin-bottom: 10px; cursor: pointer; transition: 0.3s; }
    .payment-option:hover { background: #252525; border-color: #666; }
    .payment-option input { margin-right: 10px; accent-color: var(--gold-color); }
    .payment-option i { font-size: 20px; margin-right: 10px; width: 25px; text-align: center; }

    @media (max-width: 768px) {
        .cart-layout-grid { grid-template-columns: 1fr; }
        .address-grid { grid-template-columns: 1fr; }
    }
</style>

<main class="container" style="margin-top: 100px; margin-bottom: 50px;">
    
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-error" style="background: rgba(255,0,0,0.1); border: 1px solid red; color: #ff6666; padding: 15px; margin-bottom: 20px; border-radius: 8px;">
            <i class="fa-solid fa-circle-exclamation"></i> <?= htmlspecialchars($_SESSION['error']) ?>
        </div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <div class="cart-page-header">
        <h3>GIỎ HÀNG <small style="font-size: 14px; color: #888;">(<?= isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0 ?> sản phẩm)</small></h3>
    </div>

    <?php if(empty($_SESSION['cart'])): ?>
        <div class="empty-cart-box" style="text-align: center; padding: 50px; background: #1a1a1a; border-radius: 12px; border: 1px solid #333;">
            <i class="fa-solid fa-cart-arrow-down" style="font-size: 60px; color: #444; margin-bottom: 20px;"></i>
            <p style="color: #ccc; font-size: 16px;">Giỏ hàng của bạn đang trống!</p>
            <a href="index.php" style="display: inline-block; margin-top: 20px; padding: 10px 30px; background: var(--gold-gradient); color: black; font-weight: bold; border-radius: 50px;">QUAY LẠI MUA SẮM</a>
        </div>
    <?php else: ?>
        
        <form action="index.php?ctrl=cart&act=checkout" method="POST" id="checkoutForm">
            <div class="cart-layout-grid">
                
                <div class="cart-left-section">
                    
                    <div class="cart-list">
                        <?php 
                        $totalMoney = 0;
                        foreach($_SESSION['cart'] as $key => $item): 
                            $subtotal = $item['price'] * $item['quantity'];
                            $totalMoney += $subtotal;
                            $imgUrl = (strpos($item['image'], 'http') !== 0) ? 'public/images/' . $item['image'] : $item['image'];
                        ?>
                        <div class="cart-item-card">
                            <div class="item-image">
                                <img src="<?= $imgUrl ?>" alt="<?= htmlspecialchars($item['name']) ?>">
                            </div>
                            <div class="item-info">
                                <a href="#" class="item-name"><?= htmlspecialchars($item['name']) ?></a>
                                <div class="item-variant">
                                    Phân loại: <?= htmlspecialchars($item['color']) ?> / <?= htmlspecialchars($item['storage']) ?>
                                </div>
                                <div class="qty-control-sm">
                                    <a href="index.php?ctrl=cart&act=update&key=<?= $key ?>&qty=<?= $item['quantity'] - 1 ?>" class="qty-btn-sm">-</a>
                                    <input type="text" value="<?= $item['quantity'] ?>" class="qty-input-sm" readonly>
                                    <a href="index.php?ctrl=cart&act=update&key=<?= $key ?>&qty=<?= $item['quantity'] + 1 ?>" class="qty-btn-sm">+</a>
                                </div>
                            </div>
                            <div style="text-align: right;">
                                <div class="price-real"><?= number_format($item['price'], 0, ',', '.') ?>đ</div>
                                <a href="index.php?ctrl=cart&act=remove&key=<?= $key ?>" class="btn-delete" onclick="return confirm('Xóa sản phẩm này?')">
                                    <i class="fa-solid fa-trash-can"></i>
                                </a>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="customer-info-block">
                        <div class="block-title">Thông tin giao hàng</div>
                        
                        <div class="form-group">
                            <input type="text" name="ho_ten" id="ho_ten" class="form-control" 
                                   placeholder="Họ và tên người nhận" 
                                   value="<?= htmlspecialchars($valName) ?>">
                            <div class="error-message" id="error_ho_ten">Vui lòng nhập họ tên</div>
                        </div>

                        <div class="form-group">
                            <input type="text" name="so_dien_thoai" id="so_dien_thoai" class="form-control" 
                                   placeholder="Số điện thoại liên hệ" 
                                   value="<?= htmlspecialchars($valPhone) ?>">
                            <div class="error-message" id="error_so_dien_thoai">Vui lòng nhập số điện thoại</div>
                        </div>

                        <div class="form-group">
                            <input type="email" name="email" id="email" class="form-control" 
                                   placeholder="Email (để nhận hóa đơn)" 
                                   value="<?= htmlspecialchars($valEmail) ?>">
                        </div>

                        <div class="block-title" style="margin-top: 20px; font-size: 14px;">Địa chỉ nhận hàng</div>
                        
                        <div class="radio-group" style="margin-bottom: 15px;">
                            <label style="margin-right: 20px; color: #ccc; cursor: pointer;">
                                <input type="radio" name="nhan_hang" value="home" checked onchange="toggleAddress(this.value)"> Giao tận nơi
                            </label>
                            <label style="color: #ccc; cursor: pointer;">
                                <input type="radio" name="nhan_hang" value="store" onchange="toggleAddress(this.value)"> Nhận tại cửa hàng
                            </label>
                        </div>

                        <div id="address-container">
                            <input type="hidden" name="dia_chi" id="full_address" value="">
                            
                            <div class="address-grid">
                                <div>
                                    <select class="form-control" id="tinh" name="tinh">
                                        <option value="0">Tỉnh/Thành</option>
                                    </select>
                                    <div class="error-message" id="error_tinh">Chọn Tỉnh</div>
                                </div>
                                <div>
                                    <select class="form-control" id="quan" name="quan">
                                        <option value="0">Quận/Huyện</option>
                                    </select>
                                    <div class="error-message" id="error_quan">Chọn Quận</div>
                                </div>
                                <div>
                                    <select class="form-control" id="phuong" name="phuong">
                                        <option value="0">Phường/Xã</option>
                                    </select>
                                    <div class="error-message" id="error_phuong">Chọn Phường</div>
                                </div>
                            </div>
                            
                            <div class="form-group" style="margin-top: 10px;">
                                <input type="text" id="dia_chi_cu_the" class="form-control" placeholder="Số nhà, tên đường...">
                                <div class="error-message" id="error_dia_chi_cu_the">Nhập địa chỉ cụ thể</div>
                            </div>
                        </div>

                        <div class="form-group" style="margin-top: 15px;">
                            <textarea name="ghi_chu" class="form-control" placeholder="Ghi chú thêm (VD: Giao giờ hành chính)"><?= htmlspecialchars($valNote) ?></textarea>
                        </div>
                    </div>

                    <div class="payment-method-block">
                        <div class="block-title">Phương thức thanh toán</div>
                        
                        <label class="payment-option">
                            <input type="radio" name="thanh_toan" value="cod" checked>
                            <i class="fa-solid fa-money-bill-wave" style="color: #4ade80;"></i>
                            <span>Thanh toán khi nhận hàng (COD)</span>
                        </label>

                        <label class="payment-option">
                            <input type="radio" name="thanh_toan" value="momo">
                            <i class="fa-solid fa-qrcode" style="color: #a50064;"></i>
                            <span>Thanh toán qua Ví MoMo</span>
                        </label>
                    </div>

                </div>

                <div class="cart-right-section">
                    <div class="cart-summary-box">
                        <div class="block-title">Chi tiết thanh toán</div>
                        
                        <div class="summary-row">
                            <span>Tạm tính:</span>
                            <span><?= number_format($totalMoney, 0, ',', '.') ?>đ</span>
                        </div>
                        <div class="summary-row">
                            <span>Giảm giá:</span>
                            <span>0đ</span>
                        </div>
                        <div class="summary-row">
                            <span>Phí vận chuyển:</span>
                            <span style="color: #4ade80;">Miễn phí</span>
                        </div>
                        
                        <div class="total-row">
                            <span>Tổng tiền:</span>
                            <span class="total-price"><?= number_format($totalMoney, 0, ',', '.') ?>đ</span>
                        </div>

                        <button type="submit" class="btn-checkout">ĐẶT HÀNG NGAY</button>
                        
                        <div style="text-align: center; margin-top: 15px; font-size: 12px; color: #666;">
                            Bằng việc đặt hàng, bạn đồng ý với điều khoản của PhươngSTORE.
                        </div>
                    </div>
                </div>

            </div>
        </form>
    <?php endif; ?>
</main>

<script>
// 1. API TỈNH THÀNH (ESGOO)
const host = "https://esgoo.net/api-tinhthanh";

// Load Tỉnh
fetch(host + '/1/0.htm')
    .then(response => response.json())
    .then(data => {
        if (data.error === 0) {
            let html = '<option value="0">Tỉnh Thành</option>';
            data.data.forEach(val => {
                html += `<option value="${val.id}" data-name="${val.full_name}">${val.full_name}</option>`;
            });
            document.getElementById("tinh").innerHTML = html;
        }
    })
    .catch(err => console.error(err));

// Chọn Tỉnh -> Load Quận
document.getElementById("tinh").addEventListener('change', function() {
    const idTinh = this.value;
    const quan = document.getElementById("quan");
    const phuong = document.getElementById("phuong");
    
    quan.innerHTML = '<option value="0">Quận Huyện</option>';
    phuong.innerHTML = '<option value="0">Phường Xã</option>';
    
    if(idTinh != "0") {
        fetch(`${host}/2/${idTinh}.htm`)
            .then(response => response.json())
            .then(data => {
                if (data.error === 0) {
                    let html = '<option value="0">Quận Huyện</option>';
                    data.data.forEach(val => {
                        html += `<option value="${val.id}" data-name="${val.full_name}">${val.full_name}</option>`;
                    });
                    quan.innerHTML = html;
                }
            });
    }
});

// Chọn Quận -> Load Phường
document.getElementById("quan").addEventListener('change', function() {
    const idQuan = this.value;
    const phuong = document.getElementById("phuong");
    
    phuong.innerHTML = '<option value="0">Phường Xã</option>';
    
    if(idQuan != "0") {
        fetch(`${host}/3/${idQuan}.htm`)
            .then(response => response.json())
            .then(data => {
                if (data.error === 0) {
                    let html = '<option value="0">Phường Xã</option>';
                    data.data.forEach(val => {
                        html += `<option value="${val.id}" data-name="${val.full_name}">${val.full_name}</option>`;
                    });
                    phuong.innerHTML = html;
                }
            });
    }
});

// 2. TOGGLE NHẬN TẠI CỬA HÀNG
function toggleAddress(val) {
    const addrDiv = document.getElementById('address-container');
    if(val === 'store') {
        addrDiv.style.display = 'none';
        document.getElementById('full_address').value = "Nhận tại cửa hàng";
    } else {
        addrDiv.style.display = 'block';
        document.getElementById('full_address').value = ""; // Reset để validate lại
    }
}

// 3. VALIDATE FORM
document.getElementById('checkoutForm').addEventListener('submit', function(e) {
    let isValid = true;
    
    // Reset lỗi
    document.querySelectorAll('.error-message').forEach(el => el.style.display = 'none');
    document.querySelectorAll('.form-control').forEach(el => el.classList.remove('error'));

    const name = document.getElementById('ho_ten');
    const phone = document.getElementById('so_dien_thoai');
    const method = document.querySelector('input[name="nhan_hang"]:checked').value;

    if(name.value.trim() === "") {
        showError('error_ho_ten', name);
        isValid = false;
    }

    if(phone.value.trim() === "") {
        showError('error_so_dien_thoai', phone);
        isValid = false;
    }

    // Validate địa chỉ nếu chọn giao tận nơi
    if(method === 'home') {
        const tinh = document.getElementById('tinh');
        const quan = document.getElementById('quan');
        const phuong = document.getElementById('phuong');
        const duong = document.getElementById('dia_chi_cu_the');

        if(tinh.value == "0") { showError('error_tinh', tinh); isValid = false; }
        if(quan.value == "0") { showError('error_quan', quan); isValid = false; }
        if(phuong.value == "0") { showError('error_phuong', phuong); isValid = false; }
        if(duong.value.trim() === "") { showError('error_dia_chi_cu_the', duong); isValid = false; }

        if(isValid) {
            // Ghép địa chỉ
            const tName = tinh.options[tinh.selectedIndex].getAttribute('data-name');
            const qName = quan.options[quan.selectedIndex].getAttribute('data-name');
            const pName = phuong.options[phuong.selectedIndex].getAttribute('data-name');
            const full = `${duong.value}, ${pName}, ${qName}, ${tName}`;
            document.getElementById('full_address').value = full;
        }
    }

    if(!isValid) {
        e.preventDefault();
        // Cuộn lên đầu form
        document.querySelector('.customer-info-block').scrollIntoView({ behavior: 'smooth' });
    }
});

function showError(id, input) {
    document.getElementById(id).style.display = 'block';
    input.classList.add('error');
}
</script>

<?php 
// Xóa session data cũ sau khi đã load form xong (để lần sau vào mới sạch sẽ)
if(isset($_SESSION['saved_cart_data'])) {
    unset($_SESSION['saved_cart_data']);
}
require_once 'app/views/layouts/footer.php'; 
?>