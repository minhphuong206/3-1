<?php
// app/controllers/AdminAuthController.php
require_once 'app/config/db.php';
require_once 'app/models/AdminModel.php';

class AdminAuthController {
    private $adminModel;
    private $db;

    public function __construct() {
        $database = new Database();
        $this->db = $database->connect();
        $this->adminModel = new AdminModel($this->db);
    }

    public function login() {
        if (isset($_SESSION['admin_id'])) {
            header("Location: index.php?ctrl=admin&act=dashboard");
            exit;
        }

        $error = '';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = trim($_POST['username']);
            $password = $_POST['password'];
            
            // [SỬA LẠI TÊN HÀM CHO KHỚP VỚI MODEL]
            $admin = $this->adminModel->checkLogin($username, $password);

            if ($admin) {
                // [CẬP NHẬT] Lưu Session đầy đủ cho Phân quyền
                $_SESSION['admin_id'] = $admin['ma_admin'];
                $_SESSION['admin_user'] = $admin['ten_dang_nhap'];
                $_SESSION['admin_name'] = $admin['ho_ten'];
                $_SESSION['admin_role'] = $admin['role']; // 1 hoặc 0
                $_SESSION['admin_permissions'] = !empty($admin['permissions']) ? explode(',', $admin['permissions']) : [];

                header("Location: index.php?ctrl=admin&act=dashboard"); 
                exit;
            } else {
                $error = "Tên đăng nhập hoặc mật khẩu không đúng!";
            }
        }
        require_once 'app/views/admin/login.php';
    }

    public function logout() {
        session_destroy(); // Hủy toàn bộ session cho nhanh
        header("Location: index.php?ctrl=admin&act=login"); 
        exit;
    }
}
?>