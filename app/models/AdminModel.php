<?php
// app/models/AdminModel.php

class AdminModel {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Kiểm tra đăng nhập Admin
    public function checkAdminLogin($username, $password) {
        $sql = "SELECT * FROM admin WHERE ten_dang_nhap = ? LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$username]);
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($admin) {
            // Nếu bạn dùng password_hash thì dùng password_verify
            // Nếu đồ án đang để mật khẩu thô thì dùng: if($password == $admin['mat_khau'])
            if (password_verify($password, $admin['mat_khau']) || $password == $admin['mat_khau']) {
                
                // Thiết lập Session riêng cho Admin
                $_SESSION['admin_id'] = $admin['ma_admin'];
                $_SESSION['admin_user'] = $admin['ten_dang_nhap'];
                $_SESSION['admin_name'] = $admin['ho_ten'];
                $_SESSION['admin_role'] = 'superadmin';

                return true;
            }
        }
        return "Tên đăng nhập hoặc mật khẩu Admin không đúng!";
    }
}