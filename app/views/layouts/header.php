<?php
// --- LOGIC PHP: LẤY DANH MỤC TỪ DB (GIỮ NGUYÊN) ---
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($conn_header)) {
    require_once 'app/config/db.php';
    $db_header = new Database();
    $conn_header = $db_header->connect();
}

try {
    // Lấy danh mục hiển thị
    $sql_header = "SELECT ma_danh_muc, ten_danh_muc FROM danhmuc WHERE hien_thi = 1 ORDER BY thu_tu ASC";
    $stmt_header = $conn_header->prepare($sql_header);
    $stmt_header->execute();
    $dsDanhMuc = $stmt_header->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $dsDanhMuc = [];
}

// Đếm giỏ hàng
$cartCount = 0;
if (isset($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $item) {
        $cartCount += $item['quantity'];
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PhươngSTORE - Công nghệ đỉnh cao</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="public/css/style.css">

    <style>
        /* --- CSS CHO DROPDOWN CLICK --- */
        .header-nav { display: flex; align-items: center; gap: 15px; }
        
        /* Container chung cho các nút dropdown */
        .dropdown { position: relative; display: inline-block; }
        
        /* Nút bấm mở menu */
        .dropdown-toggle {
            background: transparent;
            color: #fff;
            padding: 8px 12px;
            font-size: 15px;
            font-weight: 600;
            border: 1px solid #444;
            border-radius: 6px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.2s ease;
            user-select: none; /* Chống bôi đen khi click nhanh */
        }

        /* Hiệu ứng khi đang mở hoặc rê chuột vào nút */
        .dropdown-toggle:hover, .dropdown-toggle.active {
            border-color: #D4AF37;
            color: #D4AF37;
        }

        /* Nội dung menu xổ xuống (Mặc định ẩn) */
        .dropdown-menu {
            display: none; /* Ẩn đi */
            position: absolute;
            top: 125%;
            left: 0;
            background-color: #1a1a1a;
            min-width: 220px;
            box-shadow: 0px 8px 24px rgba(0,0,0,0.8);
            z-index: 9999;
            border: 1px solid #333;
            border-radius: 8px;
            overflow: hidden;
        }

        /* Class này được JS thêm vào để hiện menu */
        .show {
            display: block !important;
            animation: fadeIn 0.2s ease-out;
        }

        /* Link bên trong menu */
        .dropdown-menu a {
            color: #e5e5e5;
            padding: 12px 16px;
            text-decoration: none;
            display: block;
            font-size: 14px;
            border-bottom: 1px solid #2a2a2a;
        }
        .dropdown-menu a:hover {
            background-color: #333;
            color: #D4AF37;
            padding-left: 20px; /* Hiệu ứng đẩy chữ */
            transition: 0.2s;
        }

        /* Riêng cho menu user bên phải */
        .user-menu {
            right: 0;
            left: auto; /* Canh lề phải */
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>

<header style="background: #050505; border-bottom: 1px solid #222; padding: 15px 0; position: sticky; top: 0; z-index: 1000;">
    <div class="container" style="display: flex; justify-content: space-between; align-items: center;">
        
        <a href="index.php" class="logo" style="text-decoration: none; display: flex; align-items: center; gap: 10px;">
            <span style="font-size: 24px; font-weight: 800; color: #fff; letter-spacing: -1px;">
                PHƯƠNG<span style="color: #D4AF37;">STORE</span>
            </span>
        </a>

        <div class="search-box" style="flex: 1; max-width: 500px; margin: 0 20px; position: relative;">
            <form action="index.php" method="GET">
                <input type="hidden" name="ctrl" value="product">
                <input type="hidden" name="act" value="search">
                <input type="text" name="keyword" placeholder="Tìm kiếm sản phẩm..." required
                       style="width: 100%; padding: 10px 20px; padding-right: 50px; background: #1a1a1a; border: 1px solid #333; border-radius: 30px; color: #fff; outline: none;">
                <button type="submit" style="position: absolute; right: 5px; top: 50%; transform: translateY(-50%); width: 36px; height: 36px; border-radius: 50%; background: #D4AF37; border: none; cursor: pointer;">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </button>
            </form>
        </div>

        <nav class="header-nav">
            
            <a href="index.php" class="nav-link" style="color: #fff; text-decoration: none; font-weight: 600; font-size: 15px;">Trang chủ</a>

            <div class="dropdown">
                <div class="dropdown-toggle" onclick="toggleDropdown('categoryDropdown')">
                    <i class="fa-solid fa-bars"></i> Danh mục <i class="fa-solid fa-caret-down" style="font-size: 10px;"></i>
                </div>
                <div id="categoryDropdown" class="dropdown-menu">
                    <?php if (!empty($dsDanhMuc)): ?>
                        <?php foreach ($dsDanhMuc as $dm): ?>
                            <a href="index.php?ctrl=product&act=category&id=<?= $dm['ma_danh_muc'] ?>">
                                <?= htmlspecialchars($dm['ten_danh_muc']) ?>
                            </a>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <a href="#">Đang cập nhật...</a>
                    <?php endif; ?>
                </div>
            </div>

            <a href="index.php?ctrl=cart" class="cart-btn" style="position: relative; color: #fff; font-size: 20px; margin-left: 10px;">
                <i class="fa-solid fa-cart-shopping"></i>
                <?php if($cartCount > 0): ?>
                    <span style="position: absolute; top: -8px; right: -10px; background: #d70018; color: #fff; font-size: 11px; padding: 2px 6px; border-radius: 10px;">
                        <?= $cartCount ?>
                    </span>
                <?php endif; ?>
            </a>

            <div class="user-action" style="margin-left: 10px;">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <div class="dropdown">
                        <div class="dropdown-toggle" onclick="toggleDropdown('userDropdown')">
                            <i class="fa-regular fa-user"></i> 
                            <?= htmlspecialchars(explode(' ', $_SESSION['user_name'] ?? 'Me')[0]) ?>
                        </div>
                        <div id="userDropdown" class="dropdown-menu user-menu">
                            <a href="index.php?ctrl=auth&act=profile"><i class="fa-solid fa-id-card"></i> Hồ sơ</a>
                            <a href="index.php?ctrl=auth&act=orders"><i class="fa-solid fa-box"></i> Đơn hàng</a>
                            <?php if (isset($_SESSION['role']) && $_SESSION['role'] == 1): ?>
                                <a href="index.php?ctrl=admin" style="color: #4ade80;"><i class="fa-solid fa-user-shield"></i> Trang Admin</a>
                            <?php endif; ?>
                            <a href="index.php?ctrl=auth&act=logout" style="color: #ff6b6b; border-top: 1px solid #333;"><i class="fa-solid fa-right-from-bracket"></i> Đăng xuất</a>
                        </div>
                    </div>
                <?php else: ?>
                    <a href="index.php?ctrl=auth&act=login" style="color: #fff; text-decoration: none; font-weight: 600; font-size: 14px; background: rgba(255,255,255,0.1); padding: 8px 15px; border-radius: 20px;">
                        Đăng nhập
                    </a>
                <?php endif; ?>
            </div>

        </nav>
    </div>
</header>

<script>
    /* * Hàm bật tắt menu 
     * dropdownId: ID của menu cần bật (categoryDropdown hoặc userDropdown)
     */
    function toggleDropdown(dropdownId) {
        var dropdown = document.getElementById(dropdownId);
        
        // Kiểm tra xem menu này có đang mở không
        var isClosed = !dropdown.classList.contains('show');

        // Bước 1: Đóng TẤT CẢ các menu đang mở trước đã (để tránh bị mở chồng chéo)
        var allDropdowns = document.getElementsByClassName("dropdown-menu");
        for (var i = 0; i < allDropdowns.length; i++) {
            allDropdowns[i].classList.remove('show');
        }
        var allToggles = document.getElementsByClassName("dropdown-toggle");
        for (var i = 0; i < allToggles.length; i++) {
            allToggles[i].classList.remove('active');
        }

        // Bước 2: Nếu menu này đang đóng thì mở nó ra
        if (isClosed) {
            dropdown.classList.add('show');
            // Thêm class active cho nút bấm để sáng màu lên
            event.currentTarget.classList.add('active'); 
        }
        
        // Ngăn sự kiện click lan ra ngoài (để không bị window.onclick bắt ngay lập tức)
        event.stopPropagation();
    }

    /* * Sự kiện click ra ngoài màn hình -> Đóng tất cả menu
     */
    window.onclick = function(event) {
        // Nếu người dùng click vào nơi KHÔNG PHẢI là nút dropdown
        if (!event.target.matches('.dropdown-toggle') && !event.target.closest('.dropdown-toggle')) {
            var dropdowns = document.getElementsByClassName("dropdown-menu");
            for (var i = 0; i < dropdowns.length; i++) {
                var openDropdown = dropdowns[i];
                if (openDropdown.classList.contains('show')) {
                    openDropdown.classList.remove('show');
                }
            }
            
            // Tắt trạng thái active của nút bấm
            var toggles = document.getElementsByClassName("dropdown-toggle");
            for (var i = 0; i < toggles.length; i++) {
                toggles[i].classList.remove('active');
            }
        }
    }
</script>

</body>
</html>