<?php
// 1. HÀM HỖ TRỢ (ĐẶT Ở ĐẦU FILE)
if (!function_exists('convert_vi_to_en')) {
    function convert_vi_to_en($str) {
        if (!$str) return '';
        $str = preg_replace("/(à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ)/", "a", $str);
        $str = preg_replace("/(è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ)/", "e", $str);
        $str = preg_replace("/(ì|í|ị|ỉ|ĩ)/", "i", $str);
        $str = preg_replace("/(ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ)/", "o", $str);
        $str = preg_replace("/(ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ)/", "u", $str);
        $str = preg_replace("/(ỳ|ý|ỵ|ỷ|ỹ)/", "y", $str);
        $str = preg_replace("/(đ)/", "d", $str);
        $str = preg_replace("/(À|Á|Ạ|Ả|Ã|Â|Ầ|Ấ|Ậ|Ẩ|Ẫ|Ă|Ằ|Ắ|Ặ|Ẳ|Ẵ)/", "A", $str);
        $str = preg_replace("/(È|É|Ẹ|Ẻ|Ẽ|Ê|Ề|Ế|Ệ|Ể|Ễ)/", "E", $str);
        $str = preg_replace("/(Ì|Í|Ị|Ỉ|Ĩ)/", "I", $str);
        $str = preg_replace("/(Ò|Ó|Ọ|Ỏ|Õ|Ô|Ồ|Ố|Ộ|Ổ|Ỗ|Ơ|Ờ|Ớ|Ợ|Ở|Ỡ)/", "O", $str);
        $str = preg_replace("/(Ù|Ú|Ụ|Ủ|Ũ|Ư|Ừ|Ứ|Ự|Ử|Ữ)/", "U", $str);
        $str = preg_replace("/(Ỳ|Ý|Ỵ|Ỷ|Ỹ)/", "Y", $str);
        $str = preg_replace("/(Đ)/", "D", $str);
        return strtolower(trim($str));
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($tenSanPham) ?> | PhươngSTORE</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="public/css/style.css">
    
    <style>
        /* --- CSS CHO GIÁ TIỀN (THEO YÊU CẦU CỦA BẠN) --- */
        .pd-price-group {
            display: flex;           /* Xếp hàng ngang */
            align-items: baseline;   /* Căn chân chữ cho đều */
            gap: 15px;               /* Khoảng cách giữa giá mới và cũ */
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 1px solid #333;
        }

        .pd-price-show {
            font-size: 30px;         /* Chữ to */
            font-weight: 800;        /* In đậm */
            color: #D4AF37;          /* MÀU VÀNG (GOLD) cho giá mới */
        }

        .pd-price-del {
            font-size: 16px;         /* Chữ nhỏ hơn */
            color: #888;             /* Màu xám */
            text-decoration: line-through; /* GẠCH NGANG giá cũ */
        }

        .badge-sale {
            background: #d70018;     /* Màu đỏ */
            color: white;
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
            align-self: center;      /* Căn giữa theo chiều dọc */
        }

        /* --- CÁC CSS KHÁC ĐỂ TRANG ĐẸP HƠN --- */
        
        /* Layout chia cột */
        .pd-wrapper {
            display: grid;
            grid-template-columns: 55% 40%;
            gap: 5%;
            margin-top: 30px;
            margin-bottom: 50px;
        }

        /* Gallery Ảnh */
        .pd-gallery-main {
            position: relative;
            background: #fff;
            border-radius: 12px;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 400px;
            border: 1px solid #333;
        }
        .pd-gallery-main img {
            max-width: 90%; max-height: 90%; object-fit: contain;
        }
        .nav-btn {
            position: absolute; top: 50%; transform: translateY(-50%);
            background: rgba(0,0,0,0.3); color: white; border: none;
            width: 40px; height: 40px; border-radius: 50%; cursor: pointer; z-index: 2;
        }
        .nav-btn:hover { background: #D4AF37; color: black; }
        .prev-btn { left: 10px; }
        .next-btn { right: 10px; }

        .pd-thumbnails { display: flex; gap: 10px; margin-top: 15px; overflow-x: auto; }
        .pd-thumb {
            width: 70px; height: 70px; border: 2px solid #333;
            border-radius: 8px; overflow: hidden; cursor: pointer;
            background: #fff; opacity: 0.6; transition: 0.3s;
        }
        .pd-thumb.active, .pd-thumb:hover { border-color: #D4AF37 !important; opacity: 1; }

        /* Thông tin sản phẩm */
        .pd-title { font-size: 28px; font-weight: 800; color: white; margin-bottom: 10px; }
        .pd-meta { color: #ccc; font-size: 14px; margin-bottom: 20px; display: flex; gap: 15px; }

        /* Nút chọn màu/dung lượng */
        .variant-group { margin-bottom: 20px; }
        .variant-label { display: block; margin-bottom: 10px; font-weight: 600; color: #ccc; }
        .variant-options { display: flex; flex-wrap: wrap; gap: 10px; }
        .opt-btn {
            background: #1a1a1a; color: #ccc; border: 1px solid #333;
            padding: 10px 20px; border-radius: 6px; cursor: pointer; transition: 0.3s;
        }
        .opt-btn:hover { border-color: #D4AF37; color: white; }
        .opt-btn.active {
            border-color: #D4AF37; background: #D4AF37; color: black; font-weight: bold;
        }

        /* Nút Mua */
        .pd-actions { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-top: 20px; }
        .btn-add-cart, .btn-buy-now {
            padding: 15px; border-radius: 8px; border: none; cursor: pointer;
            font-weight: bold; text-transform: uppercase;
            display: flex; flex-direction: column; align-items: center; justify-content: center;
        }
        .btn-add-cart {
            background: #333; color: #D4AF37; border: 1px solid #D4AF37; flex-direction: row; gap: 10px;
        }
        .btn-add-cart:hover { background: rgba(212, 175, 55, 0.1); }
        .btn-buy-now { background: linear-gradient(45deg, #d70018, #ff4d4f); color: white; }
        .btn-buy-now:hover { box-shadow: 0 0 15px rgba(215, 0, 24, 0.5); }

        /* Số lượng */
        .qty-box { display: inline-flex; border: 1px solid #333; border-radius: 6px; overflow: hidden; }
        .qty-btn { background: #333; color: white; border: none; width: 35px; height: 35px; cursor: pointer; }
        .qty-input { width: 50px; background: black; border: none; color: white; text-align: center; font-weight: bold; }

        /* Modal & Toast */
        .modal-overlay {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(0,0,0,0.8); z-index: 9999;
            display: none; justify-content: center; align-items: center;
        }
        .modal-content {
            background: #1a1a1a; padding: 25px; border-radius: 12px;
            width: 90%; max-width: 500px; border: 1px solid #333; color: white;
        }
        .modal-product-info { display: flex; gap: 15px; margin-bottom: 20px; padding-bottom: 15px; border-bottom: 1px solid #333; }
        .modal-img { width: 80px; height: 80px; object-fit: cover; border-radius: 6px; border: 1px solid #444; }
        
        #toast-box { position: fixed; top: 20px; right: 20px; z-index: 99999; }
        .toast {
            background: #1a1a1a; color: #fff; border-left: 4px solid #D4AF37;
            padding: 15px 20px; margin-bottom: 10px; border-radius: 4px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.5); display: flex; align-items: center; gap: 15px;
            animation: slideIn 0.5s ease;
        }
        @keyframes slideIn { from { transform: translateX(100%); } to { transform: translateX(0); } }

        /* Responsive */
        @media (max-width: 768px) {
            .pd-wrapper { grid-template-columns: 1fr; }
            .pd-gallery-main { height: 300px; }
        }
    </style>
</head>
<body class="has-header">

    <?php include 'app/views/layouts/header.php'; ?>

    <main class="container">
        <div class="breadcrumbs" style="margin-top: 20px; font-size: 14px; color: #888;">
            <a href="index.php" style="color: #ccc;">Trang chủ</a> 
            <i class="fa-solid fa-chevron-right" style="font-size: 10px; margin: 0 5px;"></i>
            <a href="#" style="color: #ccc;">Sản phẩm</a>
            <i class="fa-solid fa-chevron-right" style="font-size: 10px; margin: 0 5px;"></i>
            <span style="color: #D4AF37;"><?= htmlspecialchars($tenSanPham) ?></span>
        </div>

        <div class="pd-wrapper">
            <div class="pd-left">
                <div class="pd-gallery">
                    <div class="pd-gallery-main">
                        <button class="nav-btn prev-btn" onclick="navigateGallery(-1)"><i class="fa-solid fa-chevron-left"></i></button>
                        <?php 
                            $mainImgUrlRaw = isset($images[0]['url_anh']) ? $images[0]['url_anh'] : 'default.png';
                            $mainImgDisplay = (strpos($mainImgUrlRaw, 'http') !== 0) ? 'public/images/' . $mainImgUrlRaw : $mainImgUrlRaw;
                        ?>
                        <img id="mainImage" src="<?= $mainImgDisplay ?>" alt="<?= htmlspecialchars($tenSanPham) ?>">
                        <button class="nav-btn next-btn" onclick="navigateGallery(1)"><i class="fa-solid fa-chevron-right"></i></button>
                    </div>

                    <div class="pd-thumbnails" id="thumbList">
                        <?php if(!empty($images)): ?>
                            <?php foreach($images as $index => $img): 
                                $urlHienThi = (strpos($img['url_anh'], 'http') !== 0) ? 'public/images/' . $img['url_anh'] : $img['url_anh'];
                            ?>
                                <div class="pd-thumb <?= ($index === 0) ? 'active' : '' ?>" 
                                     onclick="jumpToImage(<?= $index ?>)" 
                                     data-index="<?= $index ?>">
                                    <img src="<?= $urlHienThi ?>" style="width: 100%; height: 100%; object-fit: cover; pointer-events: none;">
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="pd-specs-mini" style="margin-top: 20px; display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                    <?php 
                        $quickSpecs = [
                            ['icon' => 'fa-battery-full', 'label' => 'Pin', 'value' => $product['pin'] ?? 'N/A'],
                            ['icon' => 'fa-microchip', 'label' => 'CPU', 'value' => $product['cpu'] ?? 'N/A'],
                            ['icon' => 'fa-expand', 'label' => 'Màn hình', 'value' => $product['kich_thuoc_man_hinh'] ?? 'N/A'],
                            ['icon' => 'fa-weight-hanging', 'label' => 'Trọng lượng', 'value' => $product['trong_luong'] ?? 'N/A']
                        ];
                        foreach($quickSpecs as $spec): ?>
                            <div style="background: #1a1a1a; padding: 12px; border-radius: 8px; border: 1px solid #333; display: flex; align-items: center; gap: 12px;">
                                <i class="fa-solid <?= $spec['icon'] ?>" style="color: #D4AF37; font-size: 18px; width: 20px;"></i>
                                <div>
                                    <div style="color: #888; font-size: 11px;"><?= $spec['label'] ?></div>
                                    <div style="color: #fff; font-size: 13px; font-weight: 600;"><?= htmlspecialchars($spec['value']) ?></div>
                                </div>
                            </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="pd-right">
                <form action="index.php" method="GET" id="productForm">
                    <input type="hidden" name="ctrl" value="cart">
                    <input type="hidden" name="act" value="add">
                    <input type="hidden" name="id" value="<?= $id ?>">
                    <input type="hidden" name="mau_sac" id="selectedColorInput" value="<?= !empty($arrMauSac) ? $arrMauSac[0] : '' ?>">
                    <input type="hidden" name="dung_luong" id="selectedStorageInput" value="<?= !empty($arrDungLuong) ? $arrDungLuong[0] : '' ?>">
                    <input type="hidden" name="is_buy_now" id="isBuyNow" value="0">

                    <h1 class="pd-title"><?= htmlspecialchars($tenSanPham) ?></h1>
                    
                   <div class="pd-meta">
                        <span>
                            <i class="fa-solid fa-star" style="color: #FFD700;"></i> 
                            <?= number_format($ratingInfo['avg_rating'], 1) ?> (<?= $ratingInfo['total_reviews'] ?> đánh giá)
                        </span>
                        <span>|</span>
                        <span>Mã SP: PS-<?= $id ?></span>
                        <span>|</span>
                        <span style="color: #4ade80;"><i class="fa-solid fa-check-circle"></i> Sẵn hàng</span>
                    </div>

                    <div class="pd-price-group">
                        <div class="pd-price-show" id="displayPrice"><?= number_format($giaMoi, 0, ',', '.') ?>đ</div>
                        
                        <div class="pd-price-del" id="displayOldPrice" style="<?= ($mucGiam == 0) ? 'display:none;' : '' ?>">
                            <?= number_format($giaGoc, 0, ',', '.') ?>đ
                        </div>
                        
                        <div class="badge-sale" id="displayBadge" style="<?= ($mucGiam == 0) ? 'display:none;' : '' ?>">
                            -<?= $mucGiam ?>%
                        </div>
                    </div>

                    <?php if(!empty($arrDungLuong)): ?>
                    <div class="variant-group">
                        <span class="variant-label">Chọn dung lượng:</span>
                        <div class="variant-options" id="storageOptions">
                            <?php foreach($arrDungLuong as $i => $dl): ?>
                                <button type="button" class="opt-btn <?= ($i == 0) ? 'active' : '' ?>" 
                                        onclick="selectOption(this, 'storage', '<?= htmlspecialchars($dl) ?>')">
                                    <?= htmlspecialchars($dl) ?>
                                </button>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>

                    <?php if(!empty($arrMauSac)): ?>
                    <div class="variant-group">
                        <span class="variant-label">Chọn màu sắc:</span>
                        <div class="variant-options" id="colorOptions">
                            <?php 
                            foreach($arrMauSac as $i => $mau): 
                                $imgIndex = 0; 
                                $colorBtnNorm = convert_vi_to_en($mau);
                                foreach($images as $key => $imgDb) {
                                    if(!empty($imgDb['mau_sac'])) {
                                        $colorImgNorm = convert_vi_to_en($imgDb['mau_sac']);
                                        if($colorImgNorm === $colorBtnNorm) {
                                            $imgIndex = $key; break; 
                                        }
                                    }
                                }
                            ?>
                                <button type="button" class="opt-btn <?= ($i == 0) ? 'active' : '' ?>" 
                                        data-img-index="<?= $imgIndex ?>"
                                        onclick="selectOption(this, 'color', '<?= htmlspecialchars($mau) ?>')">
                                    <?= htmlspecialchars($mau) ?>
                                </button>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>

                    <div class="quantity-wrapper">
                        <span class="variant-label">Số lượng:</span>
                        <div class="qty-box">
                            <button type="button" class="qty-btn" onclick="updateQty(-1)">-</button>
                            <input type="number" name="so_luong" id="qtyInput" class="qty-input" value="1" min="1" readonly>
                            <button type="button" class="qty-btn" onclick="updateQty(1)">+</button>
                        </div>
                    </div>

                    <div class="pd-actions">
                        <button type="button" class="btn-add-cart" onclick="setBuyNow(0); openModal()">
                            <i class="fa-solid fa-cart-plus"></i>
                            <span>Thêm vào giỏ</span>
                        </button>
                        <button type="button" class="btn-buy-now" onclick="setBuyNow(1); openModal()">
                            <span style="font-size: 18px;">MUA NGAY</span>
                            <span style="font-size: 12px; font-weight: normal;">(Giao tận nơi hoặc nhận tại cửa hàng)</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
        
        <section class="pd-details-section">
            <div class="pd-desc-content" style="background: #1a1a1a; padding: 30px; border-radius: 12px; border: 1px solid #333;">
                <h2 class="section-title" style="margin: 0 0 20px 0; font-size: 24px; text-align: left; color: #D4AF37;">Đặc điểm nổi bật</h2>
                <div class="description-text" style="color: #ccc; line-height: 1.8;">
                    <?= nl2br(htmlspecialchars($moTa ?? 'Thông tin đang được cập nhật...')) ?>
                </div>
            </div>

            <div class="pd-specs-table" style="margin-top: 30px;">
                <h3 style="color: white; margin-bottom: 20px; font-size: 20px; border-left: 4px solid #D4AF37; padding-left: 10px;">Thông số kỹ thuật chi tiết</h3>
                <table class="specs-table" style="width: 100%; border-collapse: collapse; background: #1a1a1a; border-radius: 8px; overflow: hidden;">
                    <?php 
                    $labels = [
                        'kich_thuoc_man_hinh' => 'Kích thước',
                        'cong_nghe_man_hinh'  => 'Công nghệ màn hình',
                        'phan_giai_man_hinh'  => 'Độ phân giải',
                        'chip_set'            => 'Vi xử lý (Chipset)',
                        'cpu'                 => 'CPU',
                        'ram'                 => 'RAM',
                        'bo_nho_trong'        => 'Bộ nhớ trong',
                        'camera_sau'          => 'Camera sau',
                        'camera_truoc'        => 'Camera trước',
                        'pin'                 => 'Pin',
                        'the_sim'             => 'SIM',
                        'nfc'                 => 'NFC',
                        'trong_luong'         => 'Trọng lượng'
                    ];
                    $hasSpecs = false;
                    foreach($labels as $col => $label): 
                        if(!empty($product[$col])): 
                            $hasSpecs = true;
                    ?>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.05);">
                                <td style="padding: 15px; color: #888; width: 35%; background: rgba(255,255,255,0.02);"><?= $label ?></td>
                                <td style="padding: 15px; font-weight: 500; color: #fff;"><?= htmlspecialchars($product[$col]) ?></td>
                            </tr>
                        <?php endif; 
                    endforeach; 
                    
                    if(!$hasSpecs): ?>
                        <tr><td colspan="2" style="padding: 20px; text-align: center; color: #666;">Thông số đang được cập nhật...</td></tr>
                    <?php endif; ?>
                </table>
            </div>
        </section>

        <div class="product-rating-overview" style="background: #1a1a1a; padding: 20px; border-radius: 8px; border: 1px solid #333; text-align: center; margin-top: 40px;">
            <h3 style="color:white;">Đánh giá sản phẩm</h3>
            <div class="rating-summary">
                <strong style="font-size: 24px; color: #f1c40f;">
                    <?php echo number_format($ratingInfo['avg_rating'], 1); ?> / 5 <i class="fa fa-star"></i>
                </strong>
                <p style="color:#ccc;">(<?php echo $ratingInfo['total_reviews']; ?> lượt đánh giá)</p>
            </div>
        </div>

        <?php if(isset($_SESSION['user_id'])): ?>
            <div class="review-form" style="background: #1a1a1a; padding: 20px; border-radius: 8px; margin-top: 20px; border: 1px solid #333;">
                <h4 style="color: #D4AF37; margin-bottom: 15px;">Viết đánh giá của bạn</h4>
                <form action="index.php?ctrl=product&act=submit_review" method="POST">
                    <input type="hidden" name="ma_san_pham" value="<?php echo $product['ma_san_pham']; ?>">
                    
                    <div class="form-group" style="margin-bottom: 15px;">
                        <label style="color: #ccc; display: block; margin-bottom: 5px;">Chọn số sao:</label>
                        <select name="so_sao" class="form-control" style="width: 100%; max-width: 200px; padding: 10px; background: black; color: white; border: 1px solid #444; border-radius: 4px;">
                            <option value="5">5 Sao (Tuyệt vời)</option>
                            <option value="4">4 Sao (Tốt)</option>
                            <option value="3">3 Sao (Bình thường)</option>
                            <option value="2">2 Sao (Tệ)</option>
                            <option value="1">1 Sao (Rất tệ)</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label style="color: #ccc; display: block; margin-bottom: 5px;">Nội dung:</label>
                        <textarea name="noi_dung" class="form-control" rows="3" required placeholder="Chia sẻ cảm nhận của bạn..." style="width:100%; background:black; color:white; border:1px solid #444; padding:10px; border-radius:4px;"></textarea>
                    </div>
                    <button type="submit" style="background: #D4AF37; color: black; border: none; padding: 10px 20px; border-radius: 4px; font-weight: bold; cursor: pointer; margin-top: 10px;">Gửi đánh giá</button>
                </form>
            </div>
        <?php else: ?>
            <div class="alert alert-info" style="margin-top: 20px; padding: 15px; background: #252525; border: 1px solid #444; color: #ccc; border-radius: 6px; text-align: center;">
                Vui lòng <a href="index.php?ctrl=auth&act=login" style="color: #D4AF37; font-weight: bold;">đăng nhập</a> để đánh giá sản phẩm này.
            </div>
        <?php endif; ?>

        <div class="review-list" style="margin-top: 30px;">
            <?php foreach($reviews as $rv): ?>
                <div class="review-item" style="background: #1a1a1a; padding: 15px; border-radius: 8px; margin-bottom: 15px; border: 1px solid #333;">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                        <strong style="color: white;"><?php echo htmlspecialchars($rv['ho_ten']); ?></strong>
                        <small style="color: #666;"><?php echo date('d/m/Y', strtotime($rv['ngay_tao'])); ?></small>
                    </div>
                    <div style="color: #f1c40f; font-size: 14px; margin-bottom: 8px;">
                        <?php for($i=1; $i<=5; $i++) echo ($i <= $rv['so_sao']) ? '★' : '☆'; ?>
                    </div>
                    <p style="color: #ccc; font-size: 14px; line-height: 1.5;"><?php echo nl2br(htmlspecialchars($rv['noi_dung'])); ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    </main>

    <div class="modal-overlay" id="cartModal">
        <div class="modal-content">
            <div class="modal-header">
                <span class="modal-title">Tùy chọn sản phẩm</span>
                <button class="btn-close-modal" onclick="closeModal()"><i class="fa-solid fa-xmark"></i></button>
            </div>
            <div class="modal-body">
                <div class="modal-product-info">
                    <img id="modalImg" src="<?= $mainImgDisplay ?>" class="modal-img">
                    <div class="modal-details">
                        <h3 style="font-size: 16px; margin-bottom: 5px;"><?= htmlspecialchars($tenSanPham) ?></h3>
                        <div class="modal-price" id="modalPrice" style="color: #d70018; font-size: 18px; font-weight: bold;"><?= number_format($giaMoi, 0, ',', '.') ?>đ</div>
                    </div>
                </div>

                <?php if(!empty($arrDungLuong)): ?>
                <div class="variant-group" style="margin-top: 15px;">
                    <span class="variant-label" style="font-size: 13px; color: #ccc;">Dung lượng:</span>
                    <div class="variant-options" id="modalStorageOptions">
                        <?php foreach($arrDungLuong as $i => $dl): ?>
                            <button type="button" class="opt-btn modal-opt-btn" 
                                    data-type="storage" 
                                    data-value="<?= htmlspecialchars($dl) ?>"
                                    onclick="selectModalOption(this, 'storage', '<?= htmlspecialchars($dl) ?>')">
                                <?= htmlspecialchars($dl) ?>
                            </button>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>

                <?php if(!empty($arrMauSac)): ?>
                <div class="variant-group" style="margin-top: 10px;">
                    <span class="variant-label" style="font-size: 13px; color: #ccc;">Màu sắc:</span>
                    <div class="variant-options" id="modalColorOptions">
                        <?php 
                        foreach($arrMauSac as $i => $mau): 
                            $imgIndex = 0;
                            $colorBtnNorm = convert_vi_to_en($mau);
                            foreach($images as $key => $imgDb) {
                                if(!empty($imgDb['mau_sac'])) {
                                    $colorImgNorm = convert_vi_to_en($imgDb['mau_sac']);
                                    if($colorImgNorm === $colorBtnNorm) { $imgIndex = $key; break; }
                                }
                            }
                        ?>
                            <button type="button" class="opt-btn modal-opt-btn" 
                                    data-type="color" 
                                    data-value="<?= htmlspecialchars($mau) ?>"
                                    data-img-index="<?= $imgIndex ?>"
                                    onclick="selectModalOption(this, 'color', '<?= htmlspecialchars($mau) ?>')">
                                <?= htmlspecialchars($mau) ?>
                            </button>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>

                <div class="quantity-wrapper" style="margin-top: 15px; justify-content: space-between; align-items: center; display: flex;">
                    <span class="variant-label" style="font-size: 13px; color: #ccc;">Số lượng:</span>
                    <div class="qty-box">
                        <button type="button" class="qty-btn" onclick="updateModalQty(-1)">-</button>
                        <input type="number" id="modalQtyInput" class="qty-input" value="1" min="1" readonly>
                        <button type="button" class="qty-btn" onclick="updateModalQty(1)">+</button>
                    </div>
                </div>

                <div class="modal-footer" style="margin-top: 20px;">
                    <button class="btn-confirm-add" onclick="confirmAddToCart()" style="width: 100%; padding: 15px; background: #D4AF37; border: none; border-radius: 8px; font-weight: bold; cursor: pointer; color: black; text-transform: uppercase;">
                        XÁC NHẬN
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div id="toast-box"></div>

    <script>
        const galleryImages = <?php echo json_encode($jsImgArray ?? []); ?>;
        const variantPrices = <?php echo json_encode($priceData ?? []); ?>;
        const formatter = new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' });
        let currentIndex = 0;

        // --- 1. GALLERIES ---
        function jumpToImage(index) {
            if (index === undefined || index === null) return;
            currentIndex = parseInt(index);
            const mainImg = document.getElementById('mainImage');
            const modalImg = document.getElementById('modalImg');
            
            if (galleryImages[currentIndex]) {
                if(mainImg) mainImg.src = galleryImages[currentIndex];
                if(modalImg) modalImg.src = galleryImages[currentIndex];
                updateThumbnailUI();
            }
        }

        function updateThumbnailUI() {
            document.querySelectorAll('.pd-thumb').forEach((thumb, idx) => {
                if (idx === currentIndex) {
                    thumb.classList.add('active');
                    thumb.style.borderColor = '#D4AF37';
                } else {
                    thumb.classList.remove('active');
                    thumb.style.borderColor = '#333';
                }
            });
        }

        function navigateGallery(step) {
            currentIndex += step;
            if (currentIndex >= galleryImages.length) currentIndex = 0;
            else if (currentIndex < 0) currentIndex = galleryImages.length - 1;
            jumpToImage(currentIndex);
        }

        // --- 2. GIAO DIỆN CHÍNH ---
        function selectOption(btn, type, value) {
            const container = btn.parentElement;
            Array.from(container.children).forEach(child => child.classList.remove('active'));
            btn.classList.add('active');

            if (type === 'color') {
                const inp = document.getElementById('selectedColorInput');
                if(inp) inp.value = value;
                const imgIdx = btn.getAttribute('data-img-index');
                if (imgIdx !== null && imgIdx !== "") jumpToImage(parseInt(imgIdx));
            } else if (type === 'storage') {
                const inp = document.getElementById('selectedStorageInput');
                if(inp) inp.value = value;
                updatePriceDisplay(value);
            }
        }

        function updatePriceDisplay(storage) {
            if (variantPrices[storage]) {
                const data = variantPrices[storage];
                const pNew = formatter.format(data.price_new).replace('₫', 'đ');
                const pOld = formatter.format(data.price_old).replace('₫', 'đ');
                
                const elPrice = document.getElementById('displayPrice');
                const elOld = document.getElementById('displayOldPrice');
                const elBadge = document.getElementById('displayBadge');
                const elModalPrice = document.getElementById('modalPrice');
                
                if(elPrice) elPrice.innerText = pNew;
                if(elModalPrice) elModalPrice.innerText = pNew;

                if (data.discount > 0) {
                    if(elOld) { elOld.innerText = pOld; elOld.style.display = 'block'; }
                    if(elBadge) { elBadge.innerText = '-' + data.discount + '%'; elBadge.style.display = 'block'; }
                } else {
                    if(elOld) elOld.style.display = 'none';
                    if(elBadge) elBadge.style.display = 'none';
                }
            }
        }

        function updateQty(delta) {
            const input = document.getElementById('qtyInput');
            if(input) {
                let val = parseInt(input.value) + delta;
                input.value = (val < 1) ? 1 : (val > 10 ? 10 : val);
            }
        }

        // --- 3. LOGIC MODAL ---
        function setBuyNow(val) {
            const el = document.getElementById('isBuyNow');
            if(el) el.value = val;
        }

        function openModal() {
            try {
                const curColor = document.getElementById('selectedColorInput').value;
                const curStorage = document.getElementById('selectedStorageInput').value;
                const curQty = document.getElementById('qtyInput').value;

                const modalQty = document.getElementById('modalQtyInput');
                if(modalQty) modalQty.value = curQty;

                syncModalButtons('modalColorOptions', curColor);
                syncModalButtons('modalStorageOptions', curStorage);

                const modal = document.getElementById('cartModal');
                if(modal) modal.style.display = 'flex';
            } catch (e) {
                console.error("Lỗi JS:", e);
                alert("Lỗi trình duyệt. Vui lòng tải lại trang.");
            }
        }

        function syncModalButtons(containerId, activeValue) {
            const container = document.getElementById(containerId);
            if(container) {
                const btns = container.querySelectorAll('.modal-opt-btn');
                btns.forEach(btn => {
                    if(btn.getAttribute('data-value') == activeValue) {
                        btn.classList.add('active');
                    } else {
                        btn.classList.remove('active');
                    }
                });
            }
        }

        function selectModalOption(btn, type, value) {
            const container = btn.parentElement;
            Array.from(container.children).forEach(child => child.classList.remove('active'));
            btn.classList.add('active');

            if (type === 'color') {
                document.getElementById('selectedColorInput').value = value;
                const mainCon = document.getElementById('colorOptions');
                if(mainCon) {
                    mainCon.querySelectorAll('.opt-btn').forEach(b => {
                        if(b.innerText.trim() == value) b.classList.add('active');
                        else b.classList.remove('active');
                    });
                }
                const imgIdx = btn.getAttribute('data-img-index');
                if (imgIdx !== null && imgIdx !== "") jumpToImage(parseInt(imgIdx));

            } else if (type === 'storage') {
                document.getElementById('selectedStorageInput').value = value;
                const mainCon = document.getElementById('storageOptions');
                if(mainCon) {
                    mainCon.querySelectorAll('.opt-btn').forEach(b => {
                        if(b.innerText.trim() == value) b.classList.add('active');
                        else b.classList.remove('active');
                    });
                }
                updatePriceDisplay(value);
            }
        }

        function updateModalQty(delta) {
            const input = document.getElementById('modalQtyInput');
            const mainInput = document.getElementById('qtyInput');
            if(input && mainInput) {
                let val = parseInt(input.value) + delta;
                val = (val < 1) ? 1 : (val > 10 ? 10 : val);
                input.value = val;
                mainInput.value = val;
            }
        }

        function closeModal() {
            const modal = document.getElementById('cartModal');
            if(modal) modal.style.display = 'none';
        }

        function confirmAddToCart() {
            const color = document.getElementById('selectedColorInput').value;
            const hasColorOptions = <?php echo !empty($arrMauSac) ? 'true' : 'false'; ?>;

            if (hasColorOptions && !color) {
                showToast('Lỗi', 'Vui lòng chọn màu sắc!');
                return;
            }

            const btnConfirm = document.querySelector('.btn-confirm-add');
            const oldText = btnConfirm.innerText;
            btnConfirm.innerText = "ĐANG XỬ LÝ...";
            btnConfirm.disabled = true;

            const form = document.getElementById('productForm');
            const formData = new URLSearchParams(new FormData(form)).toString();

            fetch('index.php?' + formData)
                .then(res => {
                    if (!res.ok) throw new Error("Lỗi kết nối Server");
                    return res.text();
                })
                .then(text => {
                    try {
                        return JSON.parse(text);
                    } catch(e) {
                        throw new Error("Phản hồi lỗi từ Server: " + text.substring(0, 50));
                    }
                })
                .then(data => {
                    btnConfirm.innerText = oldText;
                    btnConfirm.disabled = false;

                    if (data.status === 'success') {
                        const isBuyNow = document.getElementById('isBuyNow').value;
                        if (isBuyNow == 1) {
                            window.location.href = 'index.php?ctrl=cart';
                        } else {
                            closeModal();
                            showToast('Thành công', data.message);
                            updateCartCountHeader(data.total_items);
                        }
                    } else {
                        alert(data.message);
                    }
                })
                .catch(err => {
                    console.error(err);
                    btnConfirm.innerText = oldText;
                    btnConfirm.disabled = false;
                    alert("Lỗi: " + err.message);
                });
        }

        function updateCartCountHeader(count) {
            const badge = document.querySelector('.header-actions .icon-btn .badge');
            if (badge) {
                badge.innerText = count;
                badge.style.transform = "scale(1.5)";
                setTimeout(() => badge.style.transform = "scale(1)", 200);
            }
        }

        function showToast(title, msg) {
            const toastBox = document.getElementById('toast-box');
            if(!toastBox) return;
            const toast = document.createElement('div');
            toast.className = 'toast success';
            toast.innerHTML = `<i class="fa-solid fa-circle-check"></i> <div><b>${title}</b><p>${msg}</p></div>`;
            toastBox.appendChild(toast);
            setTimeout(() => toast.remove(), 3000);
        }
    </script>

    <?php include 'app/views/layouts/footer.php'; ?>
</body>
</html>