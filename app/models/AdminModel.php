<?php
// app/models/AdminModel.php

class AdminModel {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    // 1. Kiểm tra đăng nhập
    public function checkLogin($username, $password) {
        $sql = "SELECT * FROM admin WHERE ten_dang_nhap = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$username]);
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($admin && $admin['mat_khau'] == $password) {
            return $admin;
        }
        return false;
    }

    // 2. Lấy danh sách nhân viên
    public function getAllStaff() {
        $sql = "SELECT * FROM admin WHERE role = 0 ORDER BY ma_admin DESC";
        return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    // 3. Thêm nhân viên mới
    public function addStaff($username, $password, $fullname, $permissions, $avatar) {
        // Kiểm tra trùng tên
        $stmt = $this->db->prepare("SELECT ma_admin FROM admin WHERE ten_dang_nhap = ?");
        $stmt->execute([$username]);
        if($stmt->fetch()) return false;

        $permString = !empty($permissions) ? implode(',', $permissions) : '';
        
        $sql = "INSERT INTO admin (ten_dang_nhap, mat_khau, ho_ten, role, permissions, anh_admin) 
                VALUES (?, ?, ?, 0, ?, ?)";
        return $this->db->prepare($sql)->execute([$username, $password, $fullname, $permString, $avatar]);
    }

    // 4. Xóa nhân viên
    public function deleteStaff($id) {
        $sql = "DELETE FROM admin WHERE ma_admin = ? AND role = 0";
        return $this->db->prepare($sql)->execute([$id]);
    }

    // 5. Reset mật khẩu nhân viên
    public function resetStaffPassword($id, $newPass) {
        $sql = "UPDATE admin SET mat_khau = ? WHERE ma_admin = ? AND role = 0";
        return $this->db->prepare($sql)->execute([$newPass, $id]);
    }

    // 6. Lấy thông tin 1 admin theo ID (Dùng cho trang Profile)
    public function getAdminById($id) {
        $sql = "SELECT * FROM admin WHERE ma_admin = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // 7. Cập nhật Avatar (Dùng cho trang Profile)
    public function updateAdminAvatar($id, $avatar) {
        $sql = "UPDATE admin SET anh_admin = ? WHERE ma_admin = ?";
        return $this->db->prepare($sql)->execute([$avatar, $id]);
    }
}
?>