<?php require_once 'app/views/layouts/header.php'; ?>

<main class="container" style="margin-top: 100px; margin-bottom: 50px;">
    <div style="max-width: 600px; margin: 0 auto; background: #1a1a1a; padding: 30px; border-radius: 10px; border: 1px solid #333;">
        
        <h2 style="color: var(--gold-color); text-align: center; margin-bottom: 30px;">HỒ SƠ CÁ NHÂN</h2>

        <?php if (!empty($success)): ?>
            <div class="alert alert-success" style="background: rgba(46, 204, 113, 0.1); border: 1px solid #2ecc71; color: #2ecc71; padding: 10px; margin-bottom: 20px; border-radius: 5px; text-align: center;">
                <?= $success ?>
            </div>
        <?php endif; ?>

        <form action="index.php?ctrl=auth&act=profile" method="POST" id="profileForm">
            
            <div class="form-group" style="margin-bottom: 15px;">
                <label style="color: #ccc; display: block; margin-bottom: 5px;">Họ và tên:</label>
                <input type="text" name="ho_ten" class="form-control" value="<?= htmlspecialchars($user['ho_ten']) ?>" required
                       style="width: 100%; padding: 10px; background: #252525; border: 1px solid #444; color: white; border-radius: 5px;">
            </div>

            <div class="form-group" style="margin-bottom: 15px;">
                <label style="color: #ccc; display: block; margin-bottom: 5px;">Email:</label>
                <input type="email" value="<?= htmlspecialchars($user['email']) ?>" readonly
                       style="width: 100%; padding: 10px; background: #111; border: 1px solid #333; color: #888; border-radius: 5px; cursor: not-allowed;">
            </div>

            <div class="form-group" style="margin-bottom: 15px;">
                <label style="color: #ccc; display: block; margin-bottom: 5px;">Số điện thoại:</label>
                <input type="text" name="sdt" class="form-control" value="<?= htmlspecialchars($user['sdt'] ?? '') ?>" required
                       style="width: 100%; padding: 10px; background: #252525; border: 1px solid #444; color: white; border-radius: 5px;">
            </div>

            <div class="form-group" style="margin-bottom: 15px;">
                <label style="color: #ccc; display: block; margin-bottom: 5px;">Cập nhật địa chỉ mới:</label>
                
                <?php if(!empty($user['dia_chi'])): ?>
                    <p style="color: #888; font-size: 13px; margin-bottom: 10px;">
                        <i class="fa-solid fa-location-dot"></i> Hiện tại: <?= htmlspecialchars($user['dia_chi']) ?>
                    </p>
                <?php endif; ?>

                <input type="hidden" name="dia_chi" id="full_address" value="<?= htmlspecialchars($user['dia_chi'] ?? '') ?>">

                <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 10px; margin-bottom: 10px;">
                    <select id="tinh" class="form-control" style="padding: 10px; background: #252525; border: 1px solid #444; color: white; border-radius: 5px;">
                        <option value="0">Tỉnh/Thành</option>
                    </select>
                    <select id="quan" class="form-control" style="padding: 10px; background: #252525; border: 1px solid #444; color: white; border-radius: 5px;">
                        <option value="0">Quận/Huyện</option>
                    </select>
                    <select id="phuong" class="form-control" style="padding: 10px; background: #252525; border: 1px solid #444; color: white; border-radius: 5px;">
                        <option value="0">Phường/Xã</option>
                    </select>
                </div>
                <input type="text" id="dia_chi_cu_the" placeholder="Số nhà, tên đường..." 
                       style="width: 100%; padding: 10px; background: #252525; border: 1px solid #444; color: white; border-radius: 5px;">
            </div>

            <button type="submit" style="width: 100%; padding: 12px; background: var(--gold-gradient); border: none; color: black; font-weight: bold; border-radius: 5px; cursor: pointer; margin-top: 10px;">
                CẬP NHẬT THÔNG TIN
            </button>
        </form>
    </div>
</main>

<script>
// --- LOGIC API ĐỊA CHỈ (COPY TỪ GIOHANG.PHP) ---
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
    });

// Chọn Tỉnh -> Load Quận
document.getElementById("tinh").addEventListener('change', function() {
    const idTinh = this.value;
    const quan = document.getElementById("quan");
    const phuong = document.getElementById("phuong");
    quan.innerHTML = '<option value="0">Quận Huyện</option>';
    phuong.innerHTML = '<option value="0">Phường Xã</option>';
    
    if(idTinh != "0") {
        fetch(`${host}/2/${idTinh}.htm`).then(res => res.json()).then(data => {
            if (data.error === 0) {
                let html = '<option value="0">Quận Huyện</option>';
                data.data.forEach(val => html += `<option value="${val.id}" data-name="${val.full_name}">${val.full_name}</option>`);
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
        fetch(`${host}/3/${idQuan}.htm`).then(res => res.json()).then(data => {
            if (data.error === 0) {
                let html = '<option value="0">Phường Xã</option>';
                data.data.forEach(val => html += `<option value="${val.id}" data-name="${val.full_name}">${val.full_name}</option>`);
                phuong.innerHTML = html;
            }
        });
    }
});

// Xử lý trước khi submit form để ghép địa chỉ
document.getElementById('profileForm').addEventListener('submit', function(e) {
    const tinh = document.getElementById('tinh');
    const quan = document.getElementById('quan');
    const phuong = document.getElementById('phuong');
    const duong = document.getElementById('dia_chi_cu_the');
    
    // Nếu người dùng có chọn lại địa chỉ mới thì mới cập nhật
    if (tinh.value != "0" && quan.value != "0" && phuong.value != "0" && duong.value.trim() !== "") {
        const tName = tinh.options[tinh.selectedIndex].getAttribute('data-name');
        const qName = quan.options[quan.selectedIndex].getAttribute('data-name');
        const pName = phuong.options[phuong.selectedIndex].getAttribute('data-name');
        const full = `${duong.value}, ${pName}, ${qName}, ${tName}`;
        document.getElementById('full_address').value = full;
    } 
    // Nếu không chọn gì cả, giữ nguyên value cũ của input hidden (địa chỉ cũ)
});
</script>

<?php require_once 'app/views/layouts/footer.php'; ?>