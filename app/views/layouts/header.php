<?php
// --- LOGIC PHP: LẤY DANH MỤC TỪ DB ---
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($conn_header)) {
    require_once 'app/config/db.php';
    $db_header = new Database();
    $conn_header = $db_header->connect();
}

try {
    $sql_header = "SELECT ma_danh_muc, ten_danh_muc FROM danhmuc WHERE hien_thi = 1 ORDER BY thu_tu ASC";
    $stmt_header = $conn_header->prepare($sql_header);
    $stmt_header->execute();
    $dsDanhMuc = $stmt_header->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $dsDanhMuc = [];
}

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
        /* --- CSS HEADER TỔNG THỂ --- */
        header {
            background: #050505;
            border-bottom: 1px solid #222;
            padding: 12px 0;
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .header-container {
            display: flex; 
            justify-content: space-between; 
            align-items: center;
        }

        .header-nav { 
            display: flex; 
            align-items: center; 
            gap: 15px; 
        }

        /* --- THANH TÌM KIẾM --- */
        .search-box {
            flex: 1;
            max-width: 450px;
            margin: 0 20px;
        }

        .search-box form {
            display: flex;
            align-items: center;
            position: relative;
        }

        .search-input {
            width: 100%;
            height: 40px;
            padding: 0 50px 0 20px;
            background: #1a1a1a;
            border: 1px solid #333;
            border-radius: 30px;
            color: #fff;
            outline: none;
            font-size: 14px;
            transition: 0.3s ease;
        }

        .search-input:focus {
            border-color: #D4AF37;
            background: #222;
        }

        .search-btn {
            position: absolute;
            right: 4px;
            top: 50%;
            transform: translateY(-50%);
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: #D4AF37;
            border: none;
            cursor: pointer;
            /* CĂN GIỮA TUYỆT ĐỐI */
            display: grid;
            place-items: center;
            padding: 0;
            margin: 0;
            color: #000;
            transition: 0.2s ease;
        }

        .search-btn:hover {
            background: #fff;
            transform: translateY(-50%) scale(1.05);
        }

        .search-btn i {
            font-size: 14px;
            line-height: 1;
            display: block;
        }

        /* --- DROPDOWN & USER --- */
        .dropdown { position: relative; display: inline-block; }
        
        .dropdown-toggle {
            background: transparent;
            color: #fff;
            padding: 8px 12px;
            font-size: 14px;
            font-weight: 600;
            border: 1px solid #444;
            border-radius: 20px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: 0.2s;
            white-space: nowrap; /* Không cho xuống dòng tên */
        }

        .dropdown-toggle:hover, .dropdown-toggle.active {
            border-color: #D4AF37;
            color: #D4AF37;
        }

        .dropdown-menu {
            display: none;
            position: absolute;
            top: 125%;
            left: 0;
            background-color: #1a1a1a;
            min-width: 200px;
            box-shadow: 0px 8px 24px rgba(0,0,0,0.8);
            z-index: 9999;
            border: 1px solid #333;
            border-radius: 8px;
            overflow: hidden;
        }

        .show { display: block !important; animation: fadeIn 0.2s ease-out; }

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
            padding-left: 20px;
            transition: 0.2s;
        }

        .user-menu { right: 0; left: auto; }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>

<header>
    <div class="container header-container">
        
        <a href="index.php" class="logo" style="text-decoration: none; display: flex; align-items: center; gap: 8px;">
            <span style="font-size: 22px; font-weight: 800; color: #fff; letter-spacing: -1px;">
                PHƯƠNG<span style="color: #D4AF37;">STORE</span>
            </span>
        </a>

        <div class="search-box">
            <form action="index.php" method="GET">
                <input type="hidden" name="ctrl" value="product">
                <input type="hidden" name="act" value="search">
                <input type="text" name="keyword" class="search-input" placeholder="Bạn tìm gì hôm nay?..." required>
                <button type="submit" class="search-btn">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </button>
            </form>
        </div>

        <nav class="header-nav">
            
            <a href="index.php" class="nav-link" style="color: #fff; text-decoration: none; font-weight: 600; font-size: 14px;">Trang chủ</a>

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
                    <?php endif; ?>
                </div>
            </div>

            <a href="index.php?ctrl=cart" class="cart-btn" style="position: relative; color: #fff; font-size: 18px; margin: 0 5px;">
                <i class="fa-solid fa-cart-shopping"></i>
                <?php if($cartCount > 0): ?>
                    <span style="position: absolute; top: -8px; right: -10px; background: #d70018; color: #fff; font-size: 10px; padding: 2px 5px; border-radius: 10px; font-weight: bold;">
                        <?= $cartCount ?>
                    </span>
                <?php endif; ?>
            </a>

            <div class="user-action">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <div class="dropdown">
                        <div class="dropdown-toggle" onclick="toggleDropdown('userDropdown')">
                            <i class="fa-regular fa-user" style="color: #D4AF37;"></i> 
                            <?= htmlspecialchars($_SESSION['user_name'] ?? 'Khách hàng') ?>
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
                    <a href="index.php?ctrl=auth&act=login" style="color: #fff; text-decoration: none; font-weight: 600; font-size: 13px; background: rgba(212,175,55,0.1); border: 1px solid #D4AF37; padding: 7px 15px; border-radius: 20px;">
                        Đăng nhập
                    </a>
                <?php endif; ?>
            </div>

        </nav>
    </div>
</header>

<script>
    function toggleDropdown(dropdownId) {
        var dropdown = document.getElementById(dropdownId);
        var isClosed = !dropdown.classList.contains('show');

        // Đóng tất cả menu đang mở trước
        var allDropdowns = document.getElementsByClassName("dropdown-menu");
        for (var i = 0; i < allDropdowns.length; i++) {
            allDropdowns[i].classList.remove('show');
        }
        var allToggles = document.getElementsByClassName("dropdown-toggle");
        for (var i = 0; i < allToggles.length; i++) {
            allToggles[i].classList.remove('active');
        }

        if (isClosed) {
            dropdown.classList.add('show');
            event.currentTarget.classList.add('active'); 
        }
        event.stopPropagation();
    }

    window.onclick = function(event) {
        if (!event.target.matches('.dropdown-toggle') && !event.target.closest('.dropdown-toggle')) {
            var dropdowns = document.getElementsByClassName("dropdown-menu");
            for (var i = 0; i < dropdowns.length; i++) {
                if (dropdowns[i].classList.contains('show')) {
                    dropdowns[i].classList.remove('show');
                }
            }
            var toggles = document.getElementsByClassName("dropdown-toggle");
            for (var i = 0; i < toggles.length; i++) {
                toggles[i].classList.remove('active');
            }
        }
    }
</script>

</body>
</html>