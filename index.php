<?php
// index.php
session_start();
date_default_timezone_set('Asia/Ho_Chi_Minh');
require_once 'app/config/db.php';

$ctrl = $_GET['ctrl'] ?? 'home';
$act = $_GET['act'] ?? 'index';

switch ($ctrl) {
    case 'home':
        require_once 'app/controllers/HomeController.php';
        $controller = new HomeController();
        break;
    case 'product':
        require_once 'app/controllers/ProductController.php';
        $controller = new ProductController();
        break;
    case 'cart':
        require_once 'app/controllers/CartController.php';
        $controller = new CartController();
        break;
    case 'auth':
        require_once 'app/controllers/AuthController.php';
        $controller = new AuthController();
        break;
    case 'admin_auth':
        require_once 'app/controllers/AdminAuthController.php';
        $controller = new AdminAuthController();
        break;
    case 'admin': // Thêm case điều khiển Dashboard
        require_once 'app/controllers/AdminController.php';
        $controller = new AdminController();
        break;
    default:
        require_once 'app/controllers/HomeController.php';
        $controller = new HomeController();
        break;
}

if (method_exists($controller, $act)) {
    $controller->$act();
} else {
    echo "<div style='text-align:center; padding:50px; color:white; background:#111;'><h1>404 - Không tìm thấy trang</h1><a href='index.php' style='color:#D4AF37'>Về trang chủ</a></div>";
}