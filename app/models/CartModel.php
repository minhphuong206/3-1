<?php
// app/models/CartModel.php

class CartModel {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    // 1. Lấy hoặc tạo giỏ hàng cho user
    public function getCartIdByUserId($userId) {
        // Kiểm tra xem user đã có giỏ hàng chưa
        $stmt = $this->db->prepare("SELECT ma_gio_hang FROM giohang WHERE ma_khach_hang = ? LIMIT 1");
        $stmt->execute([$userId]);
        $cart = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($cart) {
            return $cart['ma_gio_hang'];
        } else {
            // Nếu chưa có, tạo mới
            $stmt = $this->db->prepare("INSERT INTO giohang (ma_khach_hang, ngay_cap_nhat) VALUES (?, NOW())");
            $stmt->execute([$userId]);
            return $this->db->lastInsertId();
        }
    }

    // 2. Tìm ID biến thể dựa trên màu và dung lượng (Logic giống OrderModel)
    public function getVariantId($productId, $color, $storage) {
        $sql = "SELECT ma_bien_the FROM bienthesanpham WHERE ma_san_pham = ? AND dung_luong = ? AND mau_sac LIKE ? LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$productId, $storage, "%$color%"]);
        $res = $stmt->fetch(PDO::FETCH_ASSOC);
        return $res ? $res['ma_bien_the'] : null;
    }

    // 3. Thêm sản phẩm vào DB
    public function addToCartDB($userId, $productId, $qty, $color, $storage) {
        $cartId = $this->getCartIdByUserId($userId);
        $variantId = $this->getVariantId($productId, $color, $storage);

        if (!$variantId) return false; // Không tìm thấy biến thể

        // Kiểm tra sản phẩm đã có trong giỏ chưa
        $stmt = $this->db->prepare("SELECT ma_ct_gio, so_luong FROM chitietgiohang WHERE ma_gio_hang = ? AND ma_bien_the = ?");
        $stmt->execute([$cartId, $variantId]);
        $item = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($item) {
            // Có rồi thì cộng dồn số lượng
            $newQty = $item['so_luong'] + $qty;
            $sql = "UPDATE chitietgiohang SET so_luong = ? WHERE ma_ct_gio = ?";
            $this->db->prepare($sql)->execute([$newQty, $item['ma_ct_gio']]);
        } else {
            // Chưa có thì thêm mới
            $sql = "INSERT INTO chitietgiohang (ma_gio_hang, ma_bien_the, so_luong) VALUES (?, ?, ?)";
            $this->db->prepare($sql)->execute([$cartId, $variantId, $qty]);
        }
        return true;
    }

    // 4. Lấy danh sách sản phẩm để hiển thị ra View
    public function getCartItems($userId) {
        $cartId = $this->getCartIdByUserId($userId);
        
        $sql = "SELECT ct.*, bt.gia_ban, bt.muc_giam_gia, bt.mau_sac, bt.dung_luong, bt.so_luong_ton, 
                       sp.ten_san_pham, sp.ma_san_pham,
                       (SELECT url_anh FROM anhsanpham WHERE ma_bien_the = bt.ma_bien_the LIMIT 1) as hinh_anh_bt,
                       (SELECT url_anh FROM anhsanpham WHERE ma_san_pham = sp.ma_san_pham AND la_anh_chinh=1 LIMIT 1) as hinh_anh_chinh
                FROM chitietgiohang ct
                JOIN bienthesanpham bt ON ct.ma_bien_the = bt.ma_bien_the
                JOIN sanpham sp ON bt.ma_san_pham = sp.ma_san_pham
                WHERE ct.ma_gio_hang = ?";
                
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$cartId]);
        $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Chuẩn hóa dữ liệu cho giống Session Cart để View không bị lỗi
        $result = [];
        foreach ($items as $item) {
            $price = $item['gia_ban'] * (1 - $item['muc_giam_gia'] / 100);
            $img = !empty($item['hinh_anh_bt']) ? $item['hinh_anh_bt'] : $item['hinh_anh_chinh'];
            
            // Format giống Session
            $key = $item['ma_san_pham'] . '-' . ($item['mau_sac'] ?? '') . '-' . ($item['dung_luong'] ?? '');
            
            $result[$key] = [
                'id'       => $item['ma_san_pham'],
                'name'     => $item['ten_san_pham'],
                'image'    => $img,
                'price'    => $price,
                'quantity' => $item['so_luong'],
                'color'    => $item['mau_sac'],
                'storage'  => $item['dung_luong'],
                'max_qty'  => $item['so_luong_ton']
            ];
        }
        return $result;
    }

    // 5. Cập nhật số lượng
    public function updateQuantityDB($userId, $productId, $color, $storage, $qty) {
        $cartId = $this->getCartIdByUserId($userId);
        $variantId = $this->getVariantId($productId, $color, $storage);
        
        if ($qty <= 0) {
            $this->removeItemDB($userId, $productId, $color, $storage);
        } else {
            $sql = "UPDATE chitietgiohang SET so_luong = ? WHERE ma_gio_hang = ? AND ma_bien_the = ?";
            $this->db->prepare($sql)->execute([$qty, $cartId, $variantId]);
        }
    }

    // 6. Xóa sản phẩm
    public function removeItemDB($userId, $productId, $color, $storage) {
        $cartId = $this->getCartIdByUserId($userId);
        $variantId = $this->getVariantId($productId, $color, $storage);
        
        $sql = "DELETE FROM chitietgiohang WHERE ma_gio_hang = ? AND ma_bien_the = ?";
        $this->db->prepare($sql)->execute([$cartId, $variantId]);
    }

    // 7. Đồng bộ Session vào DB (Dùng khi Login)
    public function syncSessionToDB($userId, $sessionCart) {
        if (empty($sessionCart)) return;

        foreach ($sessionCart as $item) {
            $this->addToCartDB(
                $userId, 
                $item['id'], 
                $item['quantity'], 
                $item['color'], 
                $item['storage']
            );
        }
    }
}
?>