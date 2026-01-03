<?php 
// app/views/home/index.php
include 'app/views/layouts/header.php'; 
?>

<style>
    /* 1. KHUNG ẢNH NỀN TRẮNG (Giống Category) */
    .product-img-box {
        width: 100%;
        height: 200px;
        background: #ffffff;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 15px;
        position: relative; /* Quan trọng để định vị nhãn giảm giá */
        border-bottom: 1px solid #eee;
        border-radius: 8px 8px 0 0;
        overflow: hidden;
    }

    .product-img-box img.p-img {
        max-width: 100%;
        max-height: 100%;
        object-fit: contain;
        transition: transform 0.3s ease;
        margin: 0;
        width: auto;
    }

    .product-card:hover .product-img-box img.p-img {
        transform: scale(1.08);
    }

    /* 2. NHÃN GIẢM GIÁ / HOT (Chuẩn style Category) */
    .discount-badge {
        position: absolute;
        top: 10px;
        right: 10px;
        background: #d70018; /* Màu đỏ CellphoneS */
        color: white;
        font-size: 12px;
        font-weight: bold;
        padding: 3px 8px;
        border-radius: 4px;
        z-index: 2;
        box-shadow: 0 2px 4px rgba(0,0,0,0.2);
    }

    /* Nếu là HOT thì dùng màu vàng */
    .discount-badge.hot {
        background: #D4AF37; 
        color: black;
    }

    /* 3. CĂN CHỈNH THẺ SẢN PHẨM */
    .product-card {
        padding-top: 0; 
        border: 1px solid #333; /* Viền tối màu giống theme */
        background: #1a1a1a;
        border-radius: 8px;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        transition: 0.3s;
    }
    
    .product-card:hover {
        box-shadow: 0 5px 15px rgba(212, 175, 55, 0.2); /* Highlight vàng nhẹ khi hover */
        border-color: #D4AF37;
    }
    
    .p-name, .price-box, .btn-cart-outline {
        padding: 0 15px;
    }
    
    .p-name { margin-top: 15px; min-height: 40px; }
    .p-name a { color: #fff; text-decoration: none; font-weight: 600; font-size: 15px; }
    
    .btn-cart-outline { 
        margin-bottom: 15px; 
        width: calc(100% - 30px); 
        margin-left: 15px;
        background: transparent;
        border: 1px solid #555;
        color: #ccc;
        padding: 8px;
        border-radius: 4px;
        cursor: pointer;
        transition: 0.2s;
    }
    
    .btn-cart-outline:hover {
        background: #D4AF37;
        color: black;
        border-color: #D4AF37;
    }
</style>

<main>
    <section class="hero">
        <div class="hero-content">
            <div class="hero-text">
                <span class="hero-tag">SẢN PHẨM HOT NHẤT</span>
                <h1 class="hero-title">iPhone 15 Pro <br> Max Titanium</h1>
                <p class="hero-desc">Chip A17 Pro. Thiết kế khung viền Titan bền bỉ, nhẹ nhàng. Nút Tác Vụ hoàn toàn mới.</p>
                <button class="btn-primary" onclick="window.location.href='index.php?ctrl=product&act=detail&id=1'">Mua Ngay - Từ 28.990k</button>
            </div>
            <div class="hero-img">
                <div class="glow"></div>
                <img src="public/images/iphone15pro.png" alt="iPhone 15 Pro Max" >
            </div>
        </div>
    </section>

    <section class="container" style="padding-top: 40px;">
        <h2 class="section-title">Sản Phẩm Nổi Bật</h2>
        <div class="product-grid">
            <?php if (!empty($featuredProducts)): ?>
                <?php foreach ($featuredProducts as $sp): ?>
                    <?php 
                        $giaGoc = $sp['gia_ban'] ?? 0;
                        $mucGiam = isset($sp['muc_giam_gia']) ? intval($sp['muc_giam_gia']) : 0;
                        $giaMoi = $giaGoc * (100 - $mucGiam) / 100;
                        $duongDanAnh = (strpos($sp['url_anh'], 'http') === 0) ? $sp['url_anh'] : 'public/images/' . $sp['url_anh'];
                    ?>
                    <div class="product-card">
                        <a href="index.php?ctrl=product&act=detail&id=<?= $sp['ma_san_pham'] ?>" style="text-decoration: none;">
                            <div class="product-img-box">
                                <?php if ($mucGiam > 0): ?>
                                    <span class="discount-badge">-<?= $mucGiam ?>%</span>
                                <?php else: ?>
                                    <span class="discount-badge hot">Hot</span>
                                <?php endif; ?>
                                
                                <img src="<?= $duongDanAnh ?>" alt="<?= htmlspecialchars($sp['ten_san_pham']) ?>" class="p-img">
                            </div>
                        </a>
                        
                        <h3 class="p-name">
                            <a href="index.php?ctrl=product&act=detail&id=<?= $sp['ma_san_pham'] ?>">
                                <?= htmlspecialchars($sp['ten_san_pham']) ?>
                            </a>
                        </h3>
                        
                        <div class="price-box">
                            <?php if ($mucGiam > 0): ?>
                                <span class="p-old-price" style="text-decoration: line-through; color: #888; margin-right: 8px; font-size: 0.9em;">
                                    <?= number_format($giaGoc, 0, ',', '.') ?>đ
                                </span>
                                <span class="p-price" style="color: #d70018; font-weight: bold;">
                                    <?= number_format($giaMoi, 0, ',', '.') ?>đ
                                </span>
                            <?php else: ?>
                                <span class="p-price" style="color: #D4AF37; font-weight: bold;">
                                    <?= number_format($giaGoc, 0, ',', '.') ?>đ
                                </span>
                            <?php endif; ?>
                        </div>
                        
                        <button class="btn-cart-outline" onclick="window.location.href='index.php?ctrl=product&act=detail&id=<?= $sp['ma_san_pham'] ?>'">
                            <i class="fa-solid fa-gear"></i> Chi tiết
                        </button>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </section>

    <?php if (!empty($allCategoryData)): ?>
        <?php foreach ($allCategoryData as $index => $cat): ?>
            <?php if (empty($cat['products'])) continue; ?>

            <section class="products <?= ($index % 2 != 0) ? 'bg-darker' : '' ?>" style="padding: 40px 0;">
                <div class="container">
                    <h2 class="section-title">
                        <?php 
                            $icon = "fa-layer-group";
                            $tenDanhMucLower = mb_strtolower($cat['ten_danh_muc']);
                            if(strpos($tenDanhMucLower, 'laptop') !== false) $icon = "fa-laptop";
                            elseif(strpos($tenDanhMucLower, 'điện thoại') !== false) $icon = "fa-mobile-screen-button";
                            elseif(strpos($tenDanhMucLower, 'phụ kiện') !== false) $icon = "fa-headphones";
                        ?>
                        <i class="fa-solid <?= $icon ?>" style="margin-right: 10px;"></i>
                        <?= htmlspecialchars($cat['ten_danh_muc']) ?>
                    </h2>
                    
                    <div class="product-grid">
                        <?php foreach ($cat['products'] as $sp): ?>
                            <?php 
                                $giaGoc = $sp['gia_ban'] ?? 0;
                                $mucGiam = isset($sp['muc_giam_gia']) ? intval($sp['muc_giam_gia']) : 0;
                                $giaMoi = $giaGoc * (100 - $mucGiam) / 100;
                                $duongDanAnh = (strpos($sp['url_anh'], 'http') === 0) ? $sp['url_anh'] : 'public/images/' . $sp['url_anh'];
                            ?>
                            <div class="product-card">
                                <a href="index.php?ctrl=product&act=detail&id=<?= $sp['ma_san_pham'] ?>" style="text-decoration: none;">
                                    <div class="product-img-box">
                                        <?php if ($mucGiam > 0): ?>
                                            <span class="discount-badge">-<?= $mucGiam ?>%</span>
                                        <?php endif; ?>

                                        <img src="<?= $duongDanAnh ?>" alt="<?= htmlspecialchars($sp['ten_san_pham']) ?>" class="p-img">
                                    </div>
                                </a>

                                <h3 class="p-name">
                                    <a href="index.php?ctrl=product&act=detail&id=<?= $sp['ma_san_pham'] ?>">
                                        <?= htmlspecialchars($sp['ten_san_pham']) ?>
                                    </a>
                                </h3>

                                <div class="price-box">
                                    <?php if ($mucGiam > 0): ?>
                                        <span class="p-old-price" style="text-decoration: line-through; color: #888; font-size: 0.9em;">
                                            <?= number_format((float)$giaGoc, 0, ',', '.') ?>đ
                                        </span>
                                        <span class="p-price" style="color: #d70018; font-weight: bold;">
                                            <?= number_format((float)$giaMoi, 0, ',', '.') ?>đ
                                        </span>
                                    <?php else: ?>
                                        <span class="p-price" style="color: #D4AF37; font-weight: bold;">
                                            <?= number_format((float)$giaGoc, 0, ',', '.') ?>đ
                                        </span>
                                    <?php endif; ?>
                                </div>

                                <button class="btn-cart-outline" onclick="window.location.href='index.php?ctrl=product&act=detail&id=<?= $sp['ma_san_pham'] ?>'">
                                    <i class="fa-solid fa-gear"></i> Chi tiết
                                </button>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </section>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="container" style="text-align: center; padding: 60px 0;">
            <p style="color:#888;">Hệ thống đang đồng bộ dữ liệu sản phẩm...</p>
        </div>
    <?php endif; ?>
</main>

<?php 
if (file_exists('app/views/layouts/footer.php')) {
    include 'app/views/layouts/footer.php';
} else {
    echo '</body></html>';
}
?>