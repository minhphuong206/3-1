<aside class="sidebar">
    <div style="font-size: 24px; font-weight: 900; color: #D4AF37; margin-bottom: 50px; text-align: center; letter-spacing: 2px;">
        PHƯƠNG STORE
    </div>
    <nav>
        <?php $act = $_GET['act'] ?? 'dashboard'; ?>
        <a href="index.php?ctrl=admin&act=dashboard" class="admin-nav-link <?= $act == 'dashboard' ? 'active' : '' ?>">
            <i class="fa-solid fa-house"></i> Tổng quan
        </a>
        <a href="index.php?ctrl=admin&act=manage_products" class="admin-nav-link <?= $act == 'manage_products' ? 'active' : '' ?>">
            <i class="fa-solid fa-box"></i> Sản phẩm
        </a>
        <a href="index.php?ctrl=admin&act=manage_users" class="admin-nav-link <?= $act == 'manage_users' ? 'active' : '' ?>">
            <i class="fa-solid fa-users"></i> Khách hàng
        </a>
        <a href="index.php?ctrl=admin&act=manage_orders" class="admin-nav-link <?= $act == 'manage_orders' ? 'active' : '' ?>">
            <i class="fa-solid fa-file-invoice-dollar"></i> Đơn hàng
        </a>
        <a href="index.php?ctrl=admin&act=manage_categories" class="admin-nav-link <?= $act == 'manage_categories' ? 'active' : '' ?>">
            <i class="fa-solid fa-file-invoice-dollar"></i> Danh mục
        </a>
        <a href="index.php?ctrl=admin_auth&act=logout" class="admin-nav-link" style="color: #ff4d4f; margin-top: 30px;">
            <i class="fa-solid fa-power-off"></i> Đăng xuất
        </a>
    </nav>
</aside>

