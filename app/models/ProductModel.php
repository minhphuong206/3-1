<?php
// app/models/ProductModel.php

class ProductModel {
    private $db;

    public function __construct($db) { 
        $this->db = $db; 
    }

    /* ============================================================
       PHẦN 1: DÀNH CHO TRANG CHỦ (HOME & SEARCH)
       ============================================================ */

    // Lấy chi tiết sản phẩm kèm ảnh và biến thể (Sửa Alias để khớp View)
   public function getProductById($id) {
        // CẬP NHẬT: Select tất cả các cột mới thay vì dùng chung thong_so_ky_thuat
        $sql = "SELECT sp.*, dm.ten_danh_muc, th.ten_thuong_hieu 
                FROM sanpham sp
                LEFT JOIN danhmuc dm ON sp.ma_danh_muc = dm.ma_danh_muc
                LEFT JOIN thuonghieu th ON sp.ma_thuong_hieu = th.ma_thuong_hieu
                WHERE sp.ma_san_pham = ?";
                
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($product) {
            // Lấy ảnh
            // Đã thêm cột mau_sac vào SELECT
            $stmtImg = $this->db->prepare("SELECT ma_anh as ma_hinh_anh, url_anh, la_anh_chinh as is_main, ma_bien_the, mau_sac FROM anhsanpham WHERE ma_san_pham = ?");
            $stmtImg->execute([$id]);
            $product['images'] = $stmtImg->fetchAll(PDO::FETCH_ASSOC);

            // Lấy biến thể (Có thêm cột RAM trong bảng bienthesanpham)
            $stmtVar = $this->db->prepare("SELECT * FROM bienthesanpham WHERE ma_san_pham = ?");
            $stmtVar->execute([$id]);
            $product['variants'] = $stmtVar->fetchAll(PDO::FETCH_ASSOC);
        }
        return $product;
    }
    // Thêm hàm này vào ProductModel.php
// Add this to app/models/ProductModel.php

public function addImageFull($productId, $url, $isMain, $variantId, $mauSac) {
    // Insert including the mau_sac column
    $sql = "INSERT INTO anhsanpham (ma_san_pham, url_anh, la_anh_chinh, ma_bien_the, mau_sac) 
            VALUES (?, ?, ?, ?, ?)";
            
    $stmt = $this->db->prepare($sql);
    return $stmt->execute([
        $productId, 
        $url, 
        $isMain, 
        $variantId, 
        $mauSac 
    ]);
}
 // --- BỔ SUNG HÀM NÀY ĐỂ TRANG QUẢN LÝ SẢN PHẨM KHÔNG BỊ LỖI ---
    // --- HÀM QUAN TRỌNG: Lấy danh sách sản phẩm hiển thị Admin ---
    public function getAllProductsGrouped() {
        $cats = $this->getAllCategories(); 
        $result = [];

        foreach($cats as $cat) {
            $sql = "SELECT sp.*, th.ten_thuong_hieu 
                    FROM sanpham sp
                    LEFT JOIN thuonghieu th ON sp.ma_thuong_hieu = th.ma_thuong_hieu
                    WHERE sp.ma_danh_muc = ? ORDER BY sp.ma_san_pham DESC";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$cat['ma_danh_muc']]);
            $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach($products as &$p) {
                // Lấy ảnh
                $stmtImg = $this->db->prepare("SELECT url_anh FROM anhsanpham WHERE ma_san_pham = ? AND la_anh_chinh = 1 LIMIT 1");
                $stmtImg->execute([$p['ma_san_pham']]);
                $img = $stmtImg->fetch();
                $p['hinh_anh'] = $img ? $img['url_anh'] : 'default.png';

                // Lấy biến thể và tính toán số liệu
                $stmtVar = $this->db->prepare("SELECT bt.*, (SELECT url_anh FROM anhsanpham WHERE ma_bien_the = bt.ma_bien_the LIMIT 1) as url_anh_bien_the FROM bienthesanpham bt WHERE ma_san_pham = ?");
                $stmtVar->execute([$p['ma_san_pham']]);
                $variants = $stmtVar->fetchAll(PDO::FETCH_ASSOC);
                $p['variants'] = $variants;

                $giaMin = 0; $maxGiam = 0; $tongKho = 0;
                if (!empty($variants)) {
                    $giaMin = $variants[0]['gia_ban'];
                    foreach ($variants as $v) {
                        if ($v['gia_ban'] < $giaMin) $giaMin = $v['gia_ban'];
                        if ($v['muc_giam_gia'] > $maxGiam) $maxGiam = $v['muc_giam_gia'];
                        $tongKho += $v['so_luong_ton'];
                    }
                }
                $p['gia_min'] = $giaMin;
                $p['max_giam'] = $maxGiam;
                $p['tong_kho'] = $tongKho;
            }

            $result[] = [
                'ten_dm' => $cat['ten_danh_muc'],
                'ma_dm'  => $cat['ma_danh_muc'],
                'items'  => $products // Key 'items' này khớp với View của bạn
            ];
        }
        return $result;
    }
   
  
    // app/models/ProductModel.php

public function getProductsByCategory($catId, $min = 0, $max = 1000000000) {
    // Chúng ta lấy giá thấp nhất của biến thể (MIN(bt.gia_ban)) làm giá hiển thị
    // Sau đó dùng HAVING để lọc theo giá đó
    $sql = "SELECT sp.*, MIN(bt.gia_ban) as gia_ban, MAX(bt.muc_giam_gia) as muc_giam_gia, 
            (SELECT url_anh FROM anhsanpham WHERE ma_san_pham = sp.ma_san_pham ORDER BY la_anh_chinh DESC LIMIT 1) as url_anh 
            FROM sanpham sp 
            LEFT JOIN bienthesanpham bt ON sp.ma_san_pham = bt.ma_san_pham 
            WHERE sp.ma_danh_muc = ? AND sp.kich_hoat = 1
            GROUP BY sp.ma_san_pham 
            HAVING gia_ban >= ? AND gia_ban <= ?
            ORDER BY sp.ma_san_pham DESC";
            
    $stmt = $this->db->prepare($sql);
    // Truyền đủ 3 tham số: ID danh mục, Giá thấp nhất, Giá cao nhất
    $stmt->execute([$catId, $min, $max]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

    public function getProductsByTag($tag, $limit = 4) {
        $sql = "SELECT sp.*, MIN(bt.gia_ban) as gia_ban, MAX(bt.muc_giam_gia) as muc_giam_gia, 
                (SELECT url_anh FROM anhsanpham WHERE ma_san_pham = sp.ma_san_pham ORDER BY la_anh_chinh DESC LIMIT 1) as url_anh 
                FROM sanpham sp 
                LEFT JOIN bienthesanpham bt ON sp.ma_san_pham = bt.ma_san_pham 
                WHERE sp.tag = ? AND sp.kich_hoat = 1
                GROUP BY sp.ma_san_pham LIMIT " . (int)$limit;
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$tag]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /* ============================================================
       PHẦN 2: QUẢN LÝ DANH MỤC & THƯƠNG HIỆU
       ============================================================ */

    public function getAllCategories() {
        return $this->db->query("SELECT * FROM danhmuc")->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllBrands() {
        return $this->db->query("SELECT * FROM thuonghieu")->fetchAll(PDO::FETCH_ASSOC);
    }

    /* ============================================================
       PHẦN 3: QUẢN LÝ SẢN PHẨM (ADMIN)
       ============================================================ */

    public function getProductsForAdmin($catId) {
        $sql = "SELECT sp.ma_san_pham, sp.ten_san_pham, sp.tag, sp.kich_hoat,
                       IFNULL(MIN(bt.gia_ban), 0) as gia_min, 
                       IFNULL(MAX(bt.muc_giam_gia), 0) as max_giam,
                       IFNULL(SUM(bt.so_luong_ton), 0) as tong_kho, 
                       (SELECT url_anh FROM anhsanpham WHERE ma_san_pham = sp.ma_san_pham ORDER BY la_anh_chinh DESC LIMIT 1) as url_anh 
                FROM sanpham sp
                LEFT JOIN bienthesanpham bt ON sp.ma_san_pham = bt.ma_san_pham
                WHERE sp.ma_danh_muc = ?
                GROUP BY sp.ma_san_pham";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$catId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function addProductFull($data, $images) {
        try {
            $this->db->beginTransaction();

            // 1. INSERT VÀO BẢNG sanpham (Các cột đã bóc tách)
            $sqlSP = "INSERT INTO sanpham (
                        ten_san_pham, ma_danh_muc, ma_thuong_hieu, mo_ta, tag, kich_hoat,
                        kich_thuoc_man_hinh, cong_nghe_man_hinh, phan_giai_man_hinh, 
                        camera_sau, camera_truoc, chip_set, cpu, nfc, pin, the_sim, trong_luong
                      ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            
            $stmtSP = $this->db->prepare($sqlSP);
            $stmtSP->execute([
                $data['ten_san_pham'], $data['ma_danh_muc'], $data['ma_thuong_hieu'], 
                $data['mo_ta'], $data['tag'], $data['kich_hoat'],
                $data['kich_thuoc_man_hinh'], $data['cong_nghe_man_hinh'], $data['phan_giai_man_hinh'],
                $data['camera_sau'], $data['camera_truoc'], $data['chip_set'], $data['cpu'],
                $data['nfc'], $data['pin'], $data['the_sim'], $data['trong_luong']
            ]);
            $productId = $this->db->lastInsertId();

            // 2. INSERT BIẾN THỂ ĐẦU TIÊN (Lưu RAM và ROM vào đây)
            $sqlBT = "INSERT INTO bienthesanpham (ma_san_pham, mau_sac, ram, dung_luong, gia_ban, muc_giam_gia, so_luong_ton) 
                      VALUES (?, ?, ?, ?, ?, ?, ?)";
            $this->db->prepare($sqlBT)->execute([
                $productId, 
                $data['mau_sac'], 
                $data['ram'],        // Cột mới RAM
                $data['dung_luong'],  // Đây là ROM
                $data['gia_ban'], 
                $data['muc_giam_gia'], 
                $data['so_luong_ton']
            ]);

            // 3. LƯU ẢNH (Giữ nguyên)
            if (!empty($images)) {
                $sqlImg = "INSERT INTO anhsanpham (ma_san_pham, url_anh, la_anh_chinh) VALUES (?, ?, ?)";
                $stmtImg = $this->db->prepare($sqlImg);
                foreach ($images as $img) {
                    $stmtImg->execute([$productId, $img['name'], $img['is_main']]);
                }
            }

            $this->db->commit();
            return true;
        } catch (Exception $e) { 
            $this->db->rollBack(); 
            error_log($e->getMessage());
            return false; 
        }
    }

    public function updateProduct($data) {
        // UPDATE các cột thông số chung
        $sqlSP = "UPDATE sanpham SET 
                    ten_san_pham=?, ma_danh_muc=?, ma_thuong_hieu=?, mo_ta=?, tag=?, kich_hoat=?,
                    kich_thuoc_man_hinh=?, cong_nghe_man_hinh=?, phan_giai_man_hinh=?,
                    camera_sau=?, camera_truoc=?, chip_set=?, cpu=?, nfc=?, pin=?, the_sim=?, trong_luong=?
                  WHERE ma_san_pham=?";
        
        return $this->db->prepare($sqlSP)->execute([
            $data['ten_san_pham'], $data['ma_danh_muc'], $data['ma_thuong_hieu'], 
            $data['mo_ta'], $data['tag'], $data['kich_hoat'],
            $data['kich_thuoc_man_hinh'], $data['cong_nghe_man_hinh'], $data['phan_giai_man_hinh'],
            $data['camera_sau'], $data['camera_truoc'], $data['chip_set'], $data['cpu'],
            $data['nfc'], $data['pin'], $data['the_sim'], $data['trong_luong'],
            $data['ma_san_pham']
        ]);
    }

    public function deleteProduct($id) {
        // Xóa sản phẩm cha (Database nên cài đặt Cascade để tự xóa biến thể con)
        $sql = "DELETE FROM sanpham WHERE ma_san_pham = ?";
        return $this->db->prepare($sql)->execute([$id]);
    }
    public function deleteVariant($id) {
    // Bước 1: Lấy thông tin biến thể để biết ID sản phẩm (dùng để reset ảnh chính nếu cần)
    $stmtGet = $this->db->prepare("SELECT ma_san_pham FROM bienthesanpham WHERE ma_bien_the = ?");
    $stmtGet->execute([$id]);
    $variant = $stmtGet->fetch(PDO::FETCH_ASSOC);

    if ($variant) {
        $productId = $variant['ma_san_pham'];

        // Bước 2: Xóa hình ảnh CỦA RIÊNG biến thể này trong bảng anhsanpham
        // (Để tránh việc biến thể mất rồi mà ảnh vẫn hiện)
        $this->db->prepare("DELETE FROM anhsanpham WHERE ma_bien_the = ?")->execute([$id]);

        // Bước 3: Xóa biến thể
        $sql = "DELETE FROM bienthesanpham WHERE ma_bien_the = ?";
        $result = $this->db->prepare($sql)->execute([$id]);

        // Bước 4: Kiểm tra xem sản phẩm còn ảnh chính không? 
        // Nếu vừa xóa mất ảnh chính (của biến thể này), thì phải chọn ảnh khác làm ảnh chính
        $stmtCheckMain = $this->db->prepare("SELECT COUNT(*) FROM anhsanpham WHERE ma_san_pham = ? AND la_anh_chinh = 1");
        $stmtCheckMain->execute([$productId]);
        if ($stmtCheckMain->fetchColumn() == 0) {
            // Tự động set ảnh đầu tiên còn lại làm ảnh chính
            $this->db->prepare("UPDATE anhsanpham SET la_anh_chinh = 1 WHERE ma_san_pham = ? LIMIT 1")->execute([$productId]);
        }

        return $result;
    }
    return false;
}

    /* ============================================================
       PHẦN 4: QUẢN LÝ BIẾN THỂ (VARIANTS)
       ============================================================ */

    public function getVariantById($id) {
        // Đã thêm subquery lấy url_anh để hiển thị trong trang Sửa
        $sql = "SELECT bt.*, sp.ten_san_pham, th.ten_thuong_hieu, dm.ten_danh_muc,
                (SELECT url_anh FROM anhsanpham WHERE ma_bien_the = bt.ma_bien_the LIMIT 1) as url_anh
                FROM bienthesanpham bt 
                JOIN sanpham sp ON bt.ma_san_pham = sp.ma_san_pham
                JOIN thuonghieu th ON sp.ma_thuong_hieu = th.ma_thuong_hieu
                JOIN danhmuc dm ON sp.ma_danh_muc = dm.ma_danh_muc
                WHERE bt.ma_bien_the = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    // Thêm hàm này vào ProductModel.php để sửa lỗi Fatal Error
    // app/models/ProductModel.php
public function getBrandsByCategory($catId) {
    $sql = "SELECT * FROM thuonghieu WHERE ma_danh_muc = ?";
    $stmt = $this->db->prepare($sql);
    $stmt->execute([$catId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
// Thêm vào class ProductModel trong file app/models/ProductModel.php
public function updateVariantImage($productId, $variantId, $newFileName) {
    // 1. Kiểm tra xem biến thể này đã có bản ghi ảnh nào chưa
    $sqlCheck = "SELECT ma_anh FROM anhsanpham WHERE ma_bien_the = ? LIMIT 1";
    $stmt = $this->db->prepare($sqlCheck);
    $stmt->execute([$variantId]);
    $existing = $stmt->fetch();

    if ($existing) {
        // 2. Nếu có rồi -> UPDATE tên file mới
        $sql = "UPDATE anhsanpham SET url_anh = ? WHERE ma_anh = ?";
        return $this->db->prepare($sql)->execute([$newFileName, $existing['ma_anh']]);
    } else {
        // 3. Nếu chưa có -> INSERT mới (để la_anh_chinh = 1 cho biến thể này)
        $sql = "INSERT INTO anhsanpham (ma_san_pham, url_anh, la_anh_chinh, ma_bien_the) VALUES (?, ?, 1, ?)";
        return $this->db->prepare($sql)->execute([$productId, $newFileName, $variantId]);
    }
}
public function getVariantsByProduct($productId) {
    // Câu lệnh lấy ảnh ưu tiên theo ma_bien_the, nếu NULL thì tìm theo mau_sac khớp với sản phẩm đó
    $sql = "SELECT bt.*, 
                   (SELECT url_anh FROM anhsanpham 
                    WHERE (ma_bien_the = bt.ma_bien_the) 
                    OR (ma_san_pham = bt.ma_san_pham AND mau_sac = bt.mau_sac)
                    LIMIT 1) as url_anh_bien_the
            FROM bienthesanpham bt 
            WHERE bt.ma_san_pham = ?";
    $stmt = $this->db->prepare($sql);
    $stmt->execute([$productId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
public function getTotalOrders() {
    return $this->db->query("SELECT COUNT(*) FROM donhang")->fetchColumn();
}

public function getTotalRevenue() {
    return $this->db->query("SELECT SUM(tong_tien) FROM donhang WHERE tt_thanh_toan = 'Đã thanh toán'")->fetchColumn();
}
    public function addVariant($productId, $data) {
        $sql = "INSERT INTO bienthesanpham (ma_san_pham, mau_sac, ram, dung_luong, gia_ban, muc_giam_gia, so_luong_ton) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        $this->db->prepare($sql)->execute([
            $productId, $data['mau_sac'], $data['ram'], $data['dung_luong'], 
            $data['gia_ban'], $data['muc_giam_gia'], $data['so_luong_ton']
        ]);
        return $this->db->lastInsertId();
    }

    public function updateVariant($id, $data) {
        $sql = "UPDATE bienthesanpham SET mau_sac=?, ram=?, dung_luong=?, gia_ban=?, muc_giam_gia=?, so_luong_ton=? WHERE ma_bien_the=?";
        return $this->db->prepare($sql)->execute([
            $data['mau_sac'], $data['ram'], $data['dung_luong'], $data['gia_ban'], 
            $data['muc_giam_gia'], $data['so_luong_ton'], $id
        ]);
    }

    

    /* ============================================================
       PHẦN 5: QUẢN LÝ HÌNH ẢNH CHI TIẾT
       ============================================================ */

    public function resetMainImage($productId, $variantId = null) {
        if ($variantId) {
            // Reset ảnh chính cho 1 màu cụ thể
            $sql = "UPDATE anhsanpham SET la_anh_chinh = 0 WHERE ma_bien_the = ?";
            return $this->db->prepare($sql)->execute([$variantId]);
        }
        // Reset ảnh chính chung của sản phẩm
        $sql = "UPDATE anhsanpham SET la_anh_chinh = 0 WHERE ma_san_pham = ? AND ma_bien_the IS NULL";
        return $this->db->prepare($sql)->execute([$productId]);
    }

    public function setMainImage($imageId) {
        $sql = "UPDATE anhsanpham SET la_anh_chinh = 1 WHERE ma_anh = ?";
        return $this->db->prepare($sql)->execute([$imageId]);
    }

    public function addImage($productId, $url, $isMain = 0, $variantId = null) {
        $sql = "INSERT INTO anhsanpham (ma_san_pham, url_anh, la_anh_chinh, ma_bien_the) VALUES (?, ?, ?, ?)";
        return $this->db->prepare($sql)->execute([$productId, $url, $isMain, $variantId]);
    }
    // 1. Lấy doanh thu hôm nay
public function getTodayRevenue() {
    $sql = "SELECT SUM(tong_tien) FROM donhang WHERE DATE(ngay_dat) = CURDATE() AND tt_thanh_toan = 'Đã thanh toán'";
    return $this->db->query($sql)->fetchColumn() ?: 0;
}

// 2. Lấy doanh thu tháng này
public function getMonthRevenue() {
    $sql = "SELECT SUM(tong_tien) FROM donhang WHERE MONTH(ngay_dat) = MONTH(CURDATE()) AND YEAR(ngay_dat) = YEAR(CURDATE()) AND tt_thanh_toan = 'Đã thanh toán'";
    return $this->db->query($sql)->fetchColumn() ?: 0;
}

// 3. Thống kê số lượng theo phương thức thanh toán
public function getPaymentStats() {
    $sql = "SELECT pt_thanh_toan, COUNT(*) as so_luong FROM donhang GROUP BY pt_thanh_toan";
    return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
}
public function deleteMultipleProducts($ids) {
    // $ids là một mảng các ID: [1, 2, 3]
    $placeholders = str_repeat('?,', count($ids) - 1) . '?';
    $sql = "DELETE FROM sanpham WHERE ma_san_pham IN ($placeholders)";
    return $this->db->prepare($sql)->execute($ids);
}

public function deleteMultipleVariants($ids) {
    $placeholders = str_repeat('?,', count($ids) - 1) . '?';
    $sql = "DELETE FROM bienthesanpham WHERE ma_bien_the IN ($placeholders)";
    return $this->db->prepare($sql)->execute($ids);
}
// app/models/ProductModel.php

// Lấy danh sách danh mục cho Admin (hiện cả mục đang ẩn)
public function getAllCategoriesAdmin() {
    $sql = "SELECT * FROM danhmuc ORDER BY thu_tu ASC";
    return $this->db->query($sql)->fetchAll();
}

// Thêm danh mục mới
public function addCategory($data) {
    $sql = "INSERT INTO danhmuc (ten_danh_muc, thu_tu, hien_thi) VALUES (?, ?, ?)";
    return $this->db->prepare($sql)->execute([
        $data['ten_dm'], 
        $data['thu_tu'], 
        $data['hien_thi']
    ]);
}

// Cập nhật danh mục
public function updateCategory($id, $data) {
    $sql = "UPDATE danhmuc SET ten_danh_muc = ?, thu_tu = ?, hien_thi = ? WHERE ma_danh_muc = ?";
    return $this->db->prepare($sql)->execute([
        $data['ten_dm'], 
        $data['thu_tu'], 
        $data['hien_thi'], 
        $id
    ]);
}

// Xóa danh mục
public function deleteCategory($id) {
    // Lưu ý: Trong SQL của bạn, bảng 'sanpham' có khóa ngoại tới 'danhmuc' (ON DELETE SET NULL)
    // Nên khi xóa danh mục, các sản phẩm liên quan sẽ bị set ma_danh_muc = NULL.
    $sql = "DELETE FROM danhmuc WHERE ma_danh_muc = ?";
    return $this->db->prepare($sql)->execute([$id]);
}
public function getProductsBySortedCategories() {
    // 1. Lấy danh mục
    try {
        $sql_cat = "SELECT * FROM danhmuc WHERE hien_thi = 1 ORDER BY thu_tu ASC";
        $categories = $this->db->query($sql_cat)->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        $sql_cat = "SELECT * FROM danhmuc ORDER BY ma_danh_muc ASC";
        $categories = $this->db->query($sql_cat)->fetchAll(PDO::FETCH_ASSOC);
    }

    foreach ($categories as &$cat) {
        // 2. Lấy sản phẩm - SỬA LỖI KHÔNG HIỆN SẢN PHẨM MỚI
        $sql_prod = "SELECT p.*, 
                    MIN(b.gia_ban) as gia_ban, 
                    b.muc_giam_gia, 
                    a.url_anh 
             FROM sanpham p
             JOIN bienthesanpham b ON p.ma_san_pham = b.ma_san_pham  -- Đổi LEFT JOIN thành JOIN
             LEFT JOIN anhsanpham a ON p.ma_san_pham = a.ma_san_pham AND a.la_anh_chinh = 1
             WHERE p.ma_danh_muc = ? AND p.kich_hoat = 1
             GROUP BY p.ma_san_pham
             ORDER BY p.ma_san_pham DESC
             LIMIT 10";
        
        $stmt = $this->db->prepare($sql_prod);
        $stmt->execute([$cat['ma_danh_muc']]);
        $cat['products'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    return $categories;
}
// Thêm danh sách thương hiệu mới vào danh mục
public function addBrandsToCategory($catId, $brandNamesArray) {
    $sql = "INSERT INTO thuonghieu (ten_thuong_hieu, ma_danh_muc) VALUES (?, ?)";
    $stmt = $this->db->prepare($sql);
    foreach ($brandNamesArray as $name) {
        $name = trim($name);
        if (!empty($name)) {
            $stmt->execute([$name, $catId]);
        }
    }
}
}
