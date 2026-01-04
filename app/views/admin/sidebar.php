<?php
    // Chỉ giữ lại 1 đoạn khai báo hàm duy nhất
    if (!function_exists('can_view_menu')) {
        function can_view_menu($perm) {
            // Nếu là Super Admin (role = 1) -> Luôn thấy hết
            if (isset($_SESSION['admin_role']) && $_SESSION['admin_role'] == 1) {
                return true;
            }
            // Nếu là Nhân viên -> Kiểm tra trong mảng quyền
            $myPerms = $_SESSION['admin_permissions'] ?? [];
            return in_array($perm, $myPerms);
        }
    }

    $act = $_GET['act'] ?? 'dashboard';
?>

<aside class="sidebar">
    <div style="font-size: 24px; font-weight: 900; color: #D4AF37; margin-bottom: 30px; text-align: center; letter-spacing: 2px;">
        PHƯƠNG STORE
    </div>

    <div style="text-align: center; margin-bottom: 40px; padding-bottom: 20px; border-bottom: 1px solid #222;">
        <a href="index.php?ctrl=admin&act=my_profile" style="text-decoration: none; display: block;" title="Xem hồ sơ & Đổi ảnh">
            <?php 
                $avt = $_SESSION['admin_avatar'] ?? 'default_admin.png';
                // Logic tìm ảnh thông minh
                $avtSrc = "public/images/admin/$avt";
                if(!file_exists($avtSrc)) {
                    $avtSrc = file_exists("public/images/$avt") ? "public/images/$avt" : "public/images/admin/default_admin.png";
                }
            ?>
            <img src="<?= $avtSrc ?>" 
                 style="width: 60px; height: 60px; border-radius: 50%; border: 2px solid #D4AF37; object-fit: cover; margin-bottom: 10px; transition: 0.3s;">
            
            <h4 style="color: #eee; margin: 0; font-size: 16px; transition: 0.3s;"><?= htmlspecialchars($_SESSION['admin_name'] ?? 'Admin') ?></h4>
            <small style="color: #666; font-size: 12px; text-transform: uppercase;">
                <?= (isset($_SESSION['admin_role']) && $_SESSION['admin_role'] == 1) ? 'Super Admin' : 'Nhân viên' ?>
            </small>
        </a>
    </div>

    <nav>
        <?php if (isset($_SESSION['admin_role']) && $_SESSION['admin_role'] == 1): ?>
            <a href="index.php?ctrl=admin&act=dashboard" class="admin-nav-link <?= $act == 'dashboard' ? 'active' : '' ?>">
                <i class="fa-solid fa-house" style="margin-right: 15px; width: 25px;"></i> Tổng quan
            </a>
        <?php endif; ?>

        <?php if (can_view_menu('products')): ?>
            <a href="index.php?ctrl=admin&act=manage_categories" class="admin-nav-link <?= $act == 'manage_categories' ? 'active' : '' ?>">
                <i class="fa-solid fa-list" style="margin-right: 15px; width: 25px;"></i> Danh mục
            </a>
            <a href="index.php?ctrl=admin&act=manage_products" class="admin-nav-link <?= in_array($act, ['manage_products', 'add_product', 'edit_product']) ? 'active' : '' ?>">
                <i class="fa-solid fa-box" style="margin-right: 15px; width: 25px;"></i> Sản phẩm
            </a>
        <?php endif; ?>

        <?php if (can_view_menu('orders')): ?>
            <a href="index.php?ctrl=admin&act=manage_orders" class="admin-nav-link <?= $act == 'manage_orders' ? 'active' : '' ?>">
                <i class="fa-solid fa-file-invoice-dollar" style="margin-right: 15px; width: 25px;"></i> Đơn hàng
            </a>
        <?php endif; ?>

        <?php if (can_view_menu('users')): ?>
            <a href="index.php?ctrl=admin&act=manage_users" class="admin-nav-link <?= $act == 'manage_users' ? 'active' : '' ?>">
                <i class="fa-solid fa-users" style="margin-right: 15px; width: 25px;"></i> Khách hàng
            </a>
        <?php endif; ?>

        <?php if (can_view_menu('reviews')): ?>
            <a href="index.php?ctrl=admin&act=reviews" class="admin-nav-link <?= $act == 'reviews' ? 'active' : '' ?>">
                <i class="fa-solid fa-comments" style="margin-right: 15px; width: 25px;"></i> Đánh giá
            </a>
        <?php endif; ?>

        <?php if (isset($_SESSION['admin_role']) && $_SESSION['admin_role'] == 1): ?>
            <a href="index.php?ctrl=admin&act=staff" class="admin-nav-link <?= $act == 'staff' ? 'active' : '' ?>" style="color: #D4AF37;">
                <i class="fa-solid fa-user-shield" style="margin-right: 15px; width: 25px;"></i> Phân quyền
            </a>
        <?php endif; ?>

        <a href="index.php?ctrl=admin&act=logout" class="admin-nav-link" style="color: #ff4d4f; margin-top: 30px;">
            <i class="fa-solid fa-power-off" style="margin-right: 15px; width: 25px;"></i> Đăng xuất
        </a>
    </nav>
</aside>