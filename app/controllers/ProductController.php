<?php
// app/controllers/ProductController.php

class ProductController {
    private $productModel;
    private $db;

    public function __construct() {
        // 1. NHÚNG FILE DATABASE
        if (file_exists('app/config/db.php')) {
            require_once 'app/config/db.php';
        } elseif (file_exists('app/models/db.php')) {
            require_once 'app/models/db.php';
        } elseif (file_exists('db.php')) {
            require_once 'db.php';
        }

        // 2. NHÚNG FILE MODEL
        if (file_exists('app/models/ProductModel.php')) {
            require_once 'app/models/ProductModel.php';
        }

        // 3. KHỞI TẠO KẾT NỐI
        $database = new Database();
        $this->db = $database->connect(); 
        
        $this->productModel = new ProductModel($this->db);
    }

    public function detail() {
    // --- BƯỚC 1: LẤY ID VÀ DỮ LIỆU ---
    $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
    $product = $this->productModel->getProductById($id);

    // Nếu không tìm thấy sản phẩm
    if (!$product) {
        echo "<div style='text-align:center; padding:50px;'>";
        echo "<h3>Sản phẩm không tồn tại!</h3>";
        echo "<a href='index.php'>Quay lại trang chủ</a>";
        echo "</div>";
        return;
    }

    // --- BƯỚC 2: XỬ LÝ LOGIC (Chuẩn bị dữ liệu cho View) ---

    // A. Thông tin cơ bản
    $tenSanPham = $product['ten_san_pham'];
    $moTa = $product['mo_ta'];
    $maDanhMuc = $product['ma_danh_muc']; // Dùng cho breadcrumbs
    
    // B. Gom nhóm thông số kỹ thuật
    $thongSo = [
        'Công nghệ màn hình' => $product['cong_nghe_man_hinh'],
        'Độ phân giải'       => $product['phan_giai_man_hinh'],
        'Kích thước'         => $product['kich_thuoc_man_hinh'],
        'Chipset'            => $product['chip_set'],
        'CPU'                => $product['cpu'],
        'Thẻ SIM'            => $product['the_sim'],
        'Trọng lượng'        => $product['trong_luong'],
        'Pin'                => $product['pin'],
        'Camera sau'         => $product['camera_sau'],
        'Camera trước'       => $product['camera_truoc']
    ];

    if (!empty($product['thong_so_ky_thuat'])) {
        $jsonSpecs = json_decode($product['thong_so_ky_thuat'], true);
        if (is_array($jsonSpecs)) {
            $thongSo = array_merge($thongSo, $jsonSpecs);
        }
    }

    // C. Xử lý hình ảnh & TẠO DỮ LIỆU CHO JS (ĐÃ SỬA)
    $rawImages = $product['images'] ?? [];
    $images = [];
    $checkDuplicate = []; 

    // Lọc ảnh trùng lặp nhưng giữ lại cấu trúc dữ liệu
    foreach ($rawImages as $img) {
        if (!in_array($img['url_anh'], $checkDuplicate)) {
            $checkDuplicate[] = $img['url_anh'];
            $images[] = $img;
        }
    }

    // Nếu không có ảnh nào, dùng ảnh mặc định
    if (empty($images)) {
        $images[] = ['url_anh' => 'default.png', 'la_anh_chinh' => 1, 'mau_sac' => null];
    }

    // [QUAN TRỌNG] Tạo mảng đường dẫn ảnh dành riêng cho JavaScript
    // Biến này sẽ được dùng trong View tại dòng: const galleryImages = ...
    $jsImgArray = [];
    foreach ($images as $img) {
        // Kiểm tra xem ảnh là link online (http) hay ảnh trong thư mục public
        $url = (strpos($img['url_anh'], 'http') === 0) ? $img['url_anh'] : 'public/images/' . $img['url_anh'];
        $jsImgArray[] = $url;
    }

    // D. Xử lý Biến thể, Giá bán & TẠO DATA GIÁ CHO JS (ĐÃ SỬA)
    $variants = $product['variants'] ?? [];
    
    // Tạo mảng dữ liệu giá cho JavaScript để cập nhật khi bấm nút dung lượng
    // Biến này sẽ được dùng trong View tại dòng: const variantPrices = ...
    $priceData = [];
    foreach ($variants as $var) {
        $gGoc = $var['gia_ban'];
        $mGiam = isset($var['muc_giam_gia']) ? intval($var['muc_giam_gia']) : 0;
        $gMoi = $gGoc * (100 - $mGiam) / 100;
        
        // Key là dung lượng (VD: '256GB'), Value là thông tin giá
        if (!empty($var['dung_luong'])) {
            $priceData[$var['dung_luong']] = [
                'price_new' => $gMoi,
                'price_old' => $gGoc,
                'discount'  => $mGiam
            ];
        }
    }

    // Tính toán giá hiển thị mặc định (Lấy biến thể đầu tiên)
    $firstVariant = !empty($variants) ? $variants[0] : null;
    if ($firstVariant) {
        $giaGoc = $firstVariant['gia_ban'];
        $mucGiam = isset($firstVariant['muc_giam_gia']) ? intval($firstVariant['muc_giam_gia']) : 0;
        $giaMoi = $giaGoc * (100 - $mucGiam) / 100;
    } else {
        $giaGoc = 0; $mucGiam = 0; $giaMoi = 0;
    }

    // E. Tách danh sách Màu và Dung lượng (Để render nút bấm HTML)
    $arrDungLuong = [];
    $arrMauSac = [];
    if (!empty($variants)) {
        // array_filter để loại bỏ giá trị rỗng/null
        $arrDungLuong = array_unique(array_filter(array_column($variants, 'dung_luong')));
        $arrMauSac = array_unique(array_filter(array_column($variants, 'mau_sac')));
    }

    // --- BƯỚC 3: GỌI VIEW HIỂN THỊ ---
    if (file_exists('app/views/products/chitietsanpham.php')) {
        require_once 'app/views/products/chitietsanpham.php';
    } elseif (file_exists('app/views/product/chitietsanpham.php')) {
        require_once 'app/views/product/chitietsanpham.php';
    } else {
        echo "Lỗi: Không tìm thấy file view chitietsanpham.php.";
    }
}
// app/controllers/ProductController.php

 // app/controllers/ProductController.php

public function category() {
    $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
    
    // Lấy giá trị min, max từ URL (nếu không có thì để mặc định rất rộng)
    $min = (isset($_GET['min']) && $_GET['min'] !== '') ? floatval($_GET['min']) : 0;
    $max = (isset($_GET['max']) && $_GET['max'] !== '') ? floatval($_GET['max']) : 1000000000;

    // Lấy danh sách danh mục để hiện tên trên breadcrumbs
    $categories = $this->productModel->getAllCategories();
    $currentCategoryName = "Danh mục";
    foreach($categories as $cat) {
        if ($cat['ma_danh_muc'] == $id) {
            $currentCategoryName = $cat['ten_danh_muc'];
            break;
        }
    }

    // Gọi Model với tham số lọc giá
    $products = $this->productModel->getProductsByCategory($id, $min, $max);

    require_once 'app/views/products/category.php';
}
    public function search() {
        $keyword = $_GET['keyword'] ?? '';
        
        if (!empty($keyword)) {
            $products = $this->productModel->searchProducts($keyword);
            $title = "Kết quả tìm kiếm: " . htmlspecialchars($keyword);
        } else {
            $products = [];
            $title = "Vui lòng nhập từ khóa";
        }

        // Tái sử dụng giao diện category để hiển thị kết quả tìm kiếm cho nhanh
        $currentCategoryName = $title; 
        include 'app/views/products/category.php';
    }
}
?>