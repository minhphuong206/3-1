<?php include 'app/views/layouts/header.php'; ?>

<style>
    /* CSS TRANG DANH MỤC */
    .category-container { max-width: 1200px; margin: 30px auto; padding: 0 15px; min-height: 600px; }
    .breadcrumbs { margin-bottom: 20px; color: #888; font-size: 14px; }
    .breadcrumbs a { color: #aaa; text-decoration: none; }
    .category-header { border-bottom: 2px solid #333; padding-bottom: 20px; margin-bottom: 30px; }
    .category-title h2 { color: #D4AF37; text-transform: uppercase; margin: 0 0 15px 0; font-size: 28px; }

    /* BỘ LỌC GIÁ */
    .filter-wrapper { display: flex; flex-wrap: wrap; gap: 12px; align-items: center; }
    .quick-filter-btn {
        background: #1a1a1a; color: #ccc; border: 1px solid #444;
        padding: 8px 18px; border-radius: 20px; font-size: 14px;
        cursor: pointer; transition: 0.3s; text-decoration: none;
    }
    .quick-filter-btn:hover, .quick-filter-btn.active {
        border-color: #D4AF37; color: #D4AF37;
    }
    .quick-filter-btn.active { background: #D4AF37; color: #000; font-weight: bold; }

    /* GRID & CARD */
    .product-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(240px, 1fr)); gap: 25px; }
    .product-card { background: #1a1a1a; border: 1px solid #333; border-radius: 10px; overflow: hidden; transition: 0.3s; display: flex; flex-direction: column; }
    .product-card:hover { transform: translateY(-5px); border-color: #D4AF37; }
    
    /* KHUNG ẢNH NỀN TRẮNG */
    .product-img-box {
        width: 100%; height: 210px; background: #fff;
        display: flex; align-items: center; justify-content: center;
        padding: 15px; position: relative; border-bottom: 1px solid #222;
    }
    .product-img-box img { max-width: 100%; max-height: 100%; object-fit: contain; transition: 0.4s; }
    .product-card:hover .product-img-box img { transform: scale(1.08); }

    .discount-badge {
        position: absolute; top: 10px; right: 10px;
        background: #d70018; color: white; font-size: 12px;
        font-weight: bold; padding: 3px 8px; border-radius: 4px; z-index: 2;
    }

    .product-info { padding: 15px; flex-grow: 1; display: flex; flex-direction: column; }
    .product-name { font-size: 16px; font-weight: 600; color: #fff; margin: 0 0 10px 0; min-height: 44px; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
    
    /* --- CSS GIÁ (ĐÃ SỬA) --- */
    .product-price {
        display: flex;
        align-items: baseline; /* Căn chân chữ */
        gap: 10px; /* Khoảng cách giữa 2 giá */
        margin-top: auto; /* Đẩy xuống đáy */
    }
    .price-new { 
        color: #D4AF37; /* Màu vàng Gold */
        font-size: 18px; 
        font-weight: bold; 
    }
    .price-old { 
        color: #666; /* Màu xám */
        font-size: 14px; 
        text-decoration: line-through; /* Gạch ngang */
    }
</style>

<main class="category-container">
    <div class="breadcrumbs">
        <a href="index.php">Trang chủ</a> 
        <i class="fa-solid fa-chevron-right" style="font-size: 10px; margin: 0 8px;"></i>
        <span style="color: #D4AF37;"><?= htmlspecialchars($currentCategoryName) ?></span>
    </div>

    <div class="category-header">
        <div class="category-title">
            <h2><?= htmlspecialchars($currentCategoryName) ?></h2>
        </div>

        <div class="filter-wrapper">
            <span style="color: #888; font-size: 14px;"><i class="fa-solid fa-filter"></i> Lọc giá:</span>
            <?php 
                $cMin = $_GET['min'] ?? ''; $cMax = $_GET['max'] ?? ''; $catId = $_GET['id'] ?? 0;
                $ranges = [
                    ['label' => 'Tất cả', 'min' => '', 'max' => ''],
                    ['label' => 'Dưới 2 triệu', 'min' => '0', 'max' => '2000000'],
                    ['label' => '2 - 5 triệu', 'min' => '2000000', 'max' => '5000000'],
                    ['label' => '5 - 10 triệu', 'min' => '5000000', 'max' => '10000000'],
                    ['label' => '10 - 20 triệu', 'min' => '10000000', 'max' => '20000000'],
                    ['label' => 'Trên 20 triệu', 'min' => '20000000', 'max' => '1000000000'],
                ];
                foreach($ranges as $r):
                    $active = ($cMin === $r['min'] && $cMax === $r['max']) ? 'active' : '';
                    $url = "index.php?ctrl=product&act=category&id=$catId";
                    if($r['min'] !== '') $url .= "&min={$r['min']}&max={$r['max']}";
            ?>
                <a href="<?= $url ?>" class="quick-filter-btn <?= $active ?>"><?= $r['label'] ?></a>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="product-grid">
        <?php if (!empty($products)): ?>
            <?php foreach ($products as $sp): ?>
                <?php 
                    // [LOGIC MỚI] Coi giá DB là giá gốc -> Tính giá bán
                    $giaGoc = floatval($sp['gia_ban']);
                    $giamGia = floatval($sp['muc_giam_gia']);
                    
                    // Tính giá bán sau khi giảm
                    $giaBan = $giaGoc * (1 - $giamGia / 100);
                    
                    $anh = !empty($sp['url_anh']) ? "public/images/".$sp['url_anh'] : "public/images/default.png";
                ?>
                <div class="product-card">
                    <a href="index.php?ctrl=product&act=detail&id=<?= $sp['ma_san_pham'] ?>" style="text-decoration: none;">
                        <div class="product-img-box">
                            <?php if ($giamGia > 0): ?>
                                <span class="discount-badge">-<?= (int)$giamGia ?>%</span>
                            <?php endif; ?>
                            <img src="<?= $anh ?>" alt="<?= htmlspecialchars($sp['ten_san_pham']) ?>" onerror="this.src='public/images/default.png'">
                        </div>
                        <div class="product-info">
                            <h3 class="product-name"><?= htmlspecialchars($sp['ten_san_pham']) ?></h3>
                            <div class="product-price">
                                <span class="price-new"><?= number_format($giaBan, 0, ',', '.') ?>đ</span>
                                
                                <?php if ($giamGia > 0): ?>
                                    <span class="price-old"><?= number_format($giaGoc, 0, ',', '.') ?>đ</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </a>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div style="grid-column: 1/-1; text-align: center; padding: 80px; color: #666;">
                <i class="fa-solid fa-box-open" style="font-size: 50px; margin-bottom: 15px;"></i>
                <p>Không có sản phẩm nào trong khoảng giá này.</p>
            </div>
        <?php endif; ?>
    </div>
</main>

<?php include 'app/views/layouts/footer.php'; ?>