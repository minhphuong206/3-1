<?php
// app/models/OrderModel.php
require_once 'app/config/db.php';

class OrderModel {
    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->connect();
    }

    /* ============================================================
       1. DÀNH CHO KHÁCH HÀNG (MUA HÀNG & THANH TOÁN)
       ============================================================ */

    public function createOrder($data) {
        $sql = "INSERT INTO donhang (
                    ma_don_hang_code, ho_ten_nguoi_nhan, sdt_nguoi_nhan, 
                    dia_chi_giao, ghi_chu, tong_tien, pt_thanh_toan, 
                    tt_thanh_toan, tt_don_hang, ngay_dat
                ) VALUES (
                    :code, :name, :phone, 
                    :addr, :note, :total, :method, 
                    'Chưa thanh toán', 'Chờ duyệt', NOW()
                )";
        
        try {
            $this->conn->beginTransaction();
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([
                ':code'   => $data['code'],
                ':name'   => $data['name'],
                ':phone'  => $data['phone'],
                ':addr'   => $data['addr'],
                ':note'   => $data['note'],
                ':total'  => $data['total'],
                ':method' => $data['method']
            ]);

            $orderId = $this->conn->lastInsertId();

            if(isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
                $sqlDetail = "INSERT INTO chitietdonhang (ma_don_hang, ma_bien_the, so_luong, don_gia) 
                              VALUES (:ma_don, :ma_bien_the, :so_luong, :don_gia)";
                $stmtDetail = $this->conn->prepare($sqlDetail);

                foreach ($_SESSION['cart'] as $item) {
                    $maBienThe = $this->getVariantId($item['id'], $item['color'], $item['storage']);
                    if ($maBienThe) {
                        $stmtDetail->execute([
                            ':ma_don'      => $orderId,
                            ':ma_bien_the' => $maBienThe,
                            ':so_luong'    => $item['quantity'],
                            ':don_gia'     => $item['price']
                        ]);
                    }
                }
            }
            $this->conn->commit();
            return $orderId;
        } catch (PDOException $e) {
            $this->conn->rollBack();
            error_log("Order creation error: " . $e->getMessage());
            return false;
        }
    }

    public function updateStatus($orderCode, $status) {
        $tt_thanh_toan = ($status == 'Paid') ? 'Đã thanh toán' : 'Thanh toán thất bại';
        $tt_don_hang = ($status == 'Paid') ? 'Đang chuẩn bị hàng' : 'Đã hủy';

        $sql = "UPDATE donhang SET tt_thanh_toan = :tt, tt_don_hang = :tt_dh WHERE ma_don_hang_code = :code";
        $stmt = $this->conn->prepare($sql);
        $result = $stmt->execute([':tt' => $tt_thanh_toan, ':tt_dh' => $tt_don_hang, ':code' => $orderCode]);
        
        if ($result && $status == 'Paid') {
            $order = $this->getOrderByCode($orderCode);
            if ($order) $this->updateInventory($order['ma_don_hang'], 'decrease');
        }
        
        return $result;
    }

    /* ============================================================
       2. DÀNH CHO ADMIN (QUẢN LÝ ĐƠN HÀNG & TỒN KHO)
       ============================================================ */

    public function getAllOrders() {
        $sql = "SELECT * FROM donhang ORDER BY ngay_dat DESC";
        return $this->conn->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getOrderDetails($ma_don_hang) {
        $sql = "SELECT ct.*, bt.mau_sac, bt.dung_luong, sp.ten_san_pham 
                FROM chitietdonhang ct
                JOIN bienthesanpham bt ON ct.ma_bien_the = bt.ma_bien_the
                JOIN sanpham sp ON bt.ma_san_pham = sp.ma_san_pham
                WHERE ct.ma_don_hang = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$ma_don_hang]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateStatusAdmin($id, $tt_don_hang, $tt_thanh_toan) {
        $oldOrder = $this->getOrderById($id);
        if (!$oldOrder) return false;

        // Tự động cập nhật thanh toán khi hoàn tất (Dành cho COD)
        if ($tt_don_hang === 'Hoàn tất') {
            $tt_thanh_toan = 'Đã thanh toán';
        }

        $sql = "UPDATE donhang SET tt_don_hang = ?, tt_thanh_toan = ? WHERE ma_don_hang = ?";
        $stmt = $this->conn->prepare($sql);
        $result = $stmt->execute([$tt_don_hang, $tt_thanh_toan, $id]);

        if ($result) {
            // SỬA: Đổi tên hàm gọi thành updateInventory cho khớp bên dưới
            if ($oldOrder['tt_don_hang'] !== 'Đang giao' && $tt_don_hang === 'Đang giao') {
                $this->updateInventory($id, 'decrease');
            }
            if ($oldOrder['tt_don_hang'] === 'Đang giao' && $tt_don_hang === 'Đã hủy') {
                $this->updateInventory($id, 'increase');
            }
        }
        return $result;
    }

    // SỬA: Đổi tên hàm từ updateStock thành updateInventory để fix lỗi Fatal Error
    private function updateInventory($orderId, $action = 'decrease') {
        $details = $this->getOrderDetails($orderId);
        foreach ($details as $item) {
            if ($action == 'decrease') {
                $sql = "UPDATE bienthesanpham SET so_luong_ton = so_luong_ton - ? WHERE ma_bien_the = ?";
            } else {
                $sql = "UPDATE bienthesanpham SET so_luong_ton = so_luong_ton + ? WHERE ma_bien_the = ?";
            }
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$item['so_luong'], $item['ma_bien_the']]);
        }
    }

    /* ============================================================
       3. BÁO CÁO DOANH THU & THỐNG KÊ
       ============================================================ */

    public function getTotalRevenue() {
        $sql = "SELECT SUM(tong_tien) as total FROM donhang 
                WHERE tt_don_hang = 'Hoàn tất' OR tt_thanh_toan = 'Đã thanh toán'";
        $res = $this->conn->query($sql)->fetch(PDO::FETCH_ASSOC);
        return $res['total'] ?? 0;
    }

    public function getTodayRevenue() {
        $sql = "SELECT SUM(tong_tien) as total FROM donhang 
                WHERE (tt_don_hang = 'Hoàn tất' OR tt_thanh_toan = 'Đã thanh toán') 
                AND DATE(ngay_dat) = CURDATE()";
        $res = $this->conn->query($sql)->fetch(PDO::FETCH_ASSOC);
        return $res['total'] ?? 0;
    }

    public function getPaymentStats() {
        $sql = "SELECT pt_thanh_toan, COUNT(*) as so_luong FROM donhang GROUP BY pt_thanh_toan";
        return $this->conn->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllUsersCount() {
        $sql = "SELECT COUNT(*) as total FROM khachhang"; 
        $res = $this->conn->query($sql)->fetch(PDO::FETCH_ASSOC);
        return $res['total'] ?? 0;
    }

    /* ============================================================
       4. HELPER
       ============================================================ */

    public function getOrderById($id) {
        $sql = "SELECT * FROM donhang WHERE ma_don_hang = ? LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    private function getVariantId($productId, $color, $storage) {
        $sql = "SELECT ma_bien_the FROM bienthesanpham WHERE ma_san_pham = ? AND dung_luong = ? AND mau_sac LIKE ? LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$productId, $storage, "%$color%"]);
        $res = $stmt->fetch(PDO::FETCH_ASSOC);
        return $res ? $res['ma_bien_the'] : null;
    }

    public function getOrderByCode($orderCode) {
        $sql = "SELECT * FROM donhang WHERE ma_don_hang_code = :code LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':code' => $orderCode]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public function getOrdersByCustomerId($customerId) {
    $sql = "SELECT * FROM donhang WHERE ma_khach_hang = ? ORDER BY ngay_dat DESC";
    $stmt = $this->conn->prepare($sql);
    $stmt->execute([$customerId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
}