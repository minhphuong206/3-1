<?php
// app/models/UserModel.php

class UserModel {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    /* ============================================================
       PHẦN 1: ĐĂNG NHẬP & ĐĂNG KÝ (Essential for AuthController)
       ============================================================ */

    // 1. Kiểm tra đăng nhập (Giải quyết lỗi Fatal Error tại dòng 48)
    public function checkLogin($email, $password) {
        $stmt = $this->conn->prepare("SELECT * FROM khachhang WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['mat_khau'])) {
            if ($user['bi_khoa'] == 1) {
                return "Tài khoản của bạn đã bị khóa!";
            }
            
            // Khởi tạo Session khách hàng đúng theo logic của bạn
            $_SESSION['user_id'] = $user['ma_khach_hang'];
            $_SESSION['user_name'] = $user['ho_ten'];
            $_SESSION['role'] = 'customer';
            
            return true;
        }
        return "Email hoặc mật khẩu không chính xác!";
    }

    // 2. Đăng ký tài khoản mới
    public function register($data) {
        $sql = "INSERT INTO khachhang (email, mat_khau, ho_ten, sdt, bi_khoa) VALUES (?, ?, ?, ?, 0)";
        $stmt = $this->conn->prepare($sql);
        
        $hashedPass = password_hash($data['password'], PASSWORD_DEFAULT);
        
        return $stmt->execute([
            $data['email'], 
            $hashedPass, 
            $data['full_name'], 
            $data['phone']
        ]);
    }

    // 3. Kiểm tra email tồn tại
    public function checkEmailExists($email) {
        $stmt = $this->conn->prepare("SELECT ma_khach_hang FROM khachhang WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->rowCount() > 0;
    }

    /* ============================================================
       PHẦN 2: LOGIC QUÊN MẬT KHẨU (OTP & HẠN DÙNG)
       ============================================================ */

    // 4. Lưu mã OTP và thời gian hết hạn (ví dụ: +15 phút)
    public function saveResetOTP($email, $otp, $expiry) {
        $sql = "UPDATE khachhang SET token_khoi_phuc = ?, han_token = ? WHERE email = ?";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$otp, $expiry, $email]);
    }

    // 5. Kiểm tra OTP: Phải khớp mã VÀ còn hạn dùng
    public function verifyOTP($email, $otp) {
        $sql = "SELECT ma_khach_hang FROM khachhang 
                WHERE email = ? AND token_khoi_phuc = ? AND han_token > NOW()";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$email, $otp]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // 6. Cập nhật mật khẩu mới và xóa token
    public function updatePassword($email, $newPassword) {
        $hashedPass = password_hash($newPassword, PASSWORD_DEFAULT);
        $sql = "UPDATE khachhang SET mat_khau = ?, token_khoi_phuc = NULL, han_token = NULL WHERE email = ?";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$hashedPass, $email]);
    }

    /* ============================================================
       PHẦN 3: DÀNH CHO ADMIN (QUẢN LÝ)
       ============================================================ */

    public function getAllUsers() {
        $sql = "SELECT ma_khach_hang, email, ho_ten, sdt, dia_chi, bi_khoa FROM khachhang ORDER BY ma_khach_hang DESC";
        $stmt = $this->conn->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function toggleUserLock($id, $status) {
        $sql = "UPDATE khachhang SET bi_khoa = ? WHERE ma_khach_hang = ?";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$status, $id]);
    }
}
?>