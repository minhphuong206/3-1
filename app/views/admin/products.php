<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý sản phẩm | PhươngSTORE</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="public/css/admin.css"> 
    
    <style>
        /* Giữ nguyên CSS Toast của bạn */
        .toast-msg {
            position: fixed; top: 30px; right: 20px; z-index: 99999;
            background: #000; color: #2ecc71; border: 1px solid #2ecc71; 
            border-left: 5px solid #2ecc71; padding: 15px 25px; border-radius: 5px; 
            font-weight: bold; font-size: 14px; box-shadow: 0 5px 20px rgba(0,0,0,0.5);
            display: flex; align-items: center; gap: 12px;
            transform: translateX(120%); opacity: 0;
            animation: slideInRight 0.5s cubic-bezier(0.68, -0.55, 0.27, 1.55) forwards;
        }
        @keyframes slideInRight { 0% { transform: translateX(120%); opacity: 0; } 100% { transform: translateX(0); opacity: 1; } }
        @keyframes slideOutRight { 0% { transform: translateX(0); opacity: 1; } 100% { transform: translateX(120%); opacity: 0; } }

        /* CSS Mới cho chức năng Xóa hàng loạt */
        .bulk-actions-bar {
            display: none;
            background: rgba(231, 76, 60, 0.15);
            border: 1px solid var(--danger);
            padding: 15px 25px;
            border-radius: 10px;
            margin-bottom: 25px;
            align-items: center;
            justify-content: space-between;
            animation: fadeIn 0.3s ease;
        }
        .btn-bulk-delete {
            background: var(--danger);
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            font-weight: bold;
            cursor: pointer;
            transition: 0.3s;
        }
        .btn-bulk-delete:hover { background: #c0392b; transform: translateY(-2px); }
        .checkbox-custom { width: 18px; height: 18px; cursor: pointer; accent-color: var(--primary); }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(-10px); } to { opacity: 1; transform: translateY(0); } }
    </style>
</head>
<body class="admin-body">

<?php include 'sidebar.php'; ?>

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

<main class="main-content-admin">
    <section class="admin-section active">
        
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:30px;">
            <h1 style="margin:0;">Sản phẩm hệ thống</h1>
            <a href="index.php?ctrl=admin&act=add_product" class="btn-primary">+ THÊM MỚI</a>
        </div>

        <form action="index.php?ctrl=admin&act=bulk_delete_products" method="POST" id="form-bulk-product">
            <div id="bulk-bar" class="bulk-actions-bar">
                <div style="color: #eee;">
                    <i class="fa-solid fa-circle-exclamation" style="color:var(--danger); margin-right:10px;"></i>
                    Đang chọn <span id="selected-count" style="font-weight:900; color:var(--danger);">0</span> sản phẩm
                </div>
                <button type="submit" class="btn-bulk-delete" onclick="return confirm('CẢNH BÁO: Bạn có chắc muốn xóa các sản phẩm đã chọn?\nDữ liệu không thể khôi phục!')">
                    <i class="fa-solid fa-trash-can"></i> XÓA MỤC ĐÃ CHỌN
                </button>
            </div>

            <?php foreach ($productsByCat as $cat): ?>
                <div class="cat-card">
                    <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:25px;">
                        <div style="font-size:24px; font-weight:800; color:var(--primary);">
                            <i class="fa-solid fa-folder-open"></i> <?= htmlspecialchars($cat['ten_dm']) ?>
                        </div>
                        <label style="color:#888; font-size:14px; cursor:pointer;">
                            <input type="checkbox" class="select-all-cat checkbox-custom"> Chọn tất cả trong mục này
                        </label>
                    </div>

                    <table class="table-admin prod-table">
                        <thead>
                            <tr>
                                <th width="40"></th>
                                <th>Ảnh</th>
                                <th>Tên sản phẩm</th>
                                <th>Giá gốc</th>
                                <th>Giảm</th>
                                <th>Giá bán</th>
                                <th>Kho</th>
                                <th>Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($cat['items'] as $p): ?>
                                <tr>
                                    <td>
                                        <input type="checkbox" name="product_ids[]" value="<?= $p['ma_san_pham'] ?>" class="prod-checkbox checkbox-custom">
                                    </td>
                                    <td><img src="public/images/<?= !empty($p['url_anh']) ? $p['url_anh'] : 'no-image.png' ?>" class="prod-img-admin"></td>
                                    <td style="font-weight:700; color:#fff;"><?= htmlspecialchars($p['ten_san_pham']) ?></td>
                                    <td style="text-decoration:line-through; color:#555;"><?= number_format($p['gia_min']) ?>đ</td>
                                    <td style="color:var(--danger); font-weight:800;"><?= $p['max_giam'] ?>%</td>
                                    <td style="color:var(--primary); font-weight:900;"><?= number_format($p['gia_min'] * (1 - ($p['max_giam'] / 100))) ?>đ</td>
                                    <td><?= $p['tong_kho'] ?></td>
                                    <td>
                                        <div style="display:flex; gap:15px; align-items:center; justify-content:center;">
                                            <a href="index.php?ctrl=admin&act=edit_product&id=<?= $p['ma_san_pham'] ?>" style="color:var(--primary);" title="Sửa sản phẩm"><i class="fa-solid fa-pen-to-square"></i></a>
                                            <a href="index.php?ctrl=admin&act=delete_product&id=<?= $p['ma_san_pham'] ?>" 
                                               onclick="return confirm('Xóa sản phẩm này và toàn bộ biến thể?');" 
                                               style="color:var(--danger);" title="Xóa"><i class="fa-solid fa-trash-can"></i></a>
                                            <i class="fa-solid fa-chevron-down" style="cursor:pointer; color:#888;" onclick="toggleVariants('row-var-<?= $p['ma_san_pham'] ?>', this)"></i>
                                        </div>
                                    </td>
                                </tr>

                               <tr id="row-var-<?= $p['ma_san_pham'] ?>" style="display:none; background:#080808;">
    <td colspan="8" style="padding: 30px;">
        <div style="display:flex; justify-content:space-between; margin-bottom:20px; align-items:center;">
            <span style="color:var(--primary); font-weight:bold; font-size:16px; letter-spacing:1px;">PHÂN LOẠI THEO MÀU SẮC (SẮP XẾP DUNG LƯỢNG)</span>
            <div style="display:flex; gap:10px;">
                <button type="button" class="btn-bulk-delete" style="font-size:12px; padding:6px 12px;" onclick="deleteSelectedVariants(<?= $p['ma_san_pham'] ?>)">
                    Xóa biến thể đã chọn
                </button>
                <a href="index.php?ctrl=admin&act=add_variant&product_id=<?= $p['ma_san_pham'] ?>" class="btn-primary" style="font-size:13px; padding:6px 15px;">+ THÊM BIẾN THỂ</a>
            </div>
        </div>

        <?php if(empty($p['variants'])): ?>
            <p style="text-align:center; color:#555;">Chưa có biến thể nào.</p>
        <?php else: 
            // 1. Nhóm các biến thể theo màu sắc
            $groupedVariants = [];
            foreach ($p['variants'] as $v) {
                $groupedVariants[$v['mau_sac']][] = $v;
            }
            
            foreach ($groupedVariants as $colorName => $variantsByColor): 
                // 2. Logic sắp xếp dung lượng từ nhỏ đến lớn
                usort($variantsByColor, function($a, $b) {
                    // Trích xuất số từ chuỗi dung lượng (ví dụ: "128GB" -> 128, "1TB" -> 1024)
                    $valA = (int)filter_var($a['dung_luong'], FILTER_SANITIZE_NUMBER_INT);
                    $valB = (int)filter_var($b['dung_luong'], FILTER_SANITIZE_NUMBER_INT);
                    
                    // Xử lý đơn vị TB nếu có (1TB = 1024GB)
                    if (stripos($a['dung_luong'], 'TB') !== false) $valA *= 1024;
                    if (stripos($b['dung_luong'], 'TB') !== false) $valB *= 1024;
                    
                    return $valA - $valB;
                });
        ?>
            <div class="color-group" style="margin-bottom: 25px; border: 1px solid #222; border-radius: 8px; overflow: hidden;">
                <div style="background: #111; padding: 10px 20px; border-bottom: 1px solid #222; display: flex; align-items: center; gap: 10px;">
                    <i class="fa-solid fa-palette" style="color: var(--primary);"></i>
                    <span style="color: #fff; font-weight: bold;">Màu: <?= htmlspecialchars($colorName) ?></span>
                </div>
                
                <table style="width:100%; font-size:14px;" id="table-var-<?= $p['ma_san_pham'] ?>">
                    <thead>
                        <tr style="color: #666; font-size: 12px;">
                            <th width="40"></th>
                            <th width="60">Ảnh</th>
                            <th>Dung lượng</th>
                            <th>Giá bán</th>
                            <th>Kho</th>
                            <th style="text-align:right;">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($variantsByColor as $v): ?>
                            <tr>
                                <td width="40"><input type="checkbox" class="var-checkbox-<?= $p['ma_san_pham'] ?> checkbox-custom" value="<?= $v['ma_bien_the'] ?>"></td>
                                <td style="padding:10px 0;">
                                    <img src="public/images/<?= !empty($v['url_anh_bien_the']) ? $v['url_anh_bien_the'] : $p['url_anh'] ?>" 
                                         style="width:40px; height:40px; object-fit:cover; border-radius:4px;">
                                </td>
                                <td style="color:var(--primary); font-weight: 700;"><?= $v['dung_luong'] ?></td>
                                <td><?= number_format($v['gia_ban'] * (1 - ($v['muc_giam_gia']/100))) ?>đ</td>
                                <td><?= $v['so_luong_ton'] ?> máy</td>
                                <td style="text-align:right;">
                                    <a href="index.php?ctrl=admin&act=edit_variant&id=<?= $v['ma_bien_the'] ?>" style="color:var(--primary); margin-right:15px;"><i class="fa-solid fa-pen"></i></a>
                                    <a href="index.php?ctrl=admin&act=delete_variant&id=<?= $v['ma_bien_the'] ?>" onclick="return confirm('Xóa?');" style="color:var(--danger);"><i class="fa-solid fa-xmark"></i></a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endforeach; endif; ?>
    </td>
</tr>
                        
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endforeach; ?>
        </form>
    </section>
</main>

<script>
    // 1. Toggle hiển thị biến thể
    function toggleVariants(id, icon) {
        let r = document.getElementById(id);
        if (r) {
            if (r.style.display === "none") {
                r.style.display = "table-row";
                icon.classList.replace('fa-chevron-down', 'fa-chevron-up');
            } else {
                r.style.display = "none";
                icon.classList.replace('fa-chevron-up', 'fa-chevron-down');
            }
        }
    }

    // 2. Xử lý thanh công cụ xóa sản phẩm hàng loạt
    const mainCheckboxes = document.querySelectorAll('.prod-checkbox');
    const bulkBar = document.getElementById('bulk-bar');
    const selectedCountTxt = document.getElementById('selected-count');

    function updateBulkBar() {
        const checked = document.querySelectorAll('.prod-checkbox:checked');
        if (checked.length > 0) {
            bulkBar.style.display = 'flex';
            selectedCountTxt.innerText = checked.length;
        } else {
            bulkBar.style.display = 'none';
        }
    }

    mainCheckboxes.forEach(cb => cb.addEventListener('change', updateBulkBar));

    // 3. Chọn tất cả sản phẩm trong 1 danh mục
    document.querySelectorAll('.select-all-cat').forEach(headerCb => {
        headerCb.addEventListener('change', function() {
            const container = this.closest('.cat-card');
            const cbs = container.querySelectorAll('.prod-checkbox');
            cbs.forEach(c => {
                c.checked = this.checked;
            });
            updateBulkBar();
        });
    });

    // 4. Hàm xóa hàng loạt biến thể (Gửi POST ngầm)
    function deleteSelectedVariants(productId) {
        const selected = document.querySelectorAll('.var-checkbox-' + productId + ':checked');
        if (selected.length === 0) {
            alert("Vui lòng chọn ít nhất một biến thể để xóa!");
            return;
        }

        if (confirm("Bạn có chắc muốn xóa " + selected.length + " biến thể đã chọn?")) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = 'index.php?ctrl=admin&act=bulk_delete_variants';

            selected.forEach(cb => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'variant_ids[]';
                input.value = cb.value;
                form.appendChild(input);
            });

            document.body.appendChild(form);
            form.submit();
        }
    }
</script>
</body>
</html>