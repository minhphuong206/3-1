<?php
// app/controllers/AdminController.php
require_once 'app/models/ProductModel.php';
require_once 'app/models/UserModel.php';
require_once 'app/models/OrderModel.php';

class AdminController {
    private $productModel;
    private $userModel;
    private $orderModel;

    public function __construct() {
        if (!isset($_SESSION['admin_id'])) {
            header("Location: index.php?ctrl=admin_auth&act=login");
            exit;
        }
        $database = new Database();
        $db = $database->connect();
        $this->productModel = new ProductModel($db);
        $this->userModel = new UserModel($db);
        $this->orderModel = new OrderModel();
    }

    /* ============================================================
       1. CÁC TRANG HIỂN THỊ (VIEW) - ĐÃ TÁCH RIÊNG
       ============================================================ */

    public function dashboard() {
        $totalOrders = $this->productModel->getTotalOrders();
        $totalRevenue = $this->productModel->getTotalRevenue();
        $todayRevenue = $this->productModel->getTodayRevenue();
    $monthRevenue = $this->productModel->getMonthRevenue();
    $paymentStats = $this->productModel->getPaymentStats();
        $allUsers = $this->userModel->getAllUsers();
        $paymentStats = $this->productModel->getPaymentStats(); 
    $recentOrders = $this->orderModel->getAllOrders();
    $totalUsersCount = $this->orderModel->getAllUsersCount();
    $allUsers = array_fill(0, $totalUsersCount, null);
        require_once 'app/views/admin/index.php'; 
    }

    // app/controllers/AdminController.php

public function manage_products() {
    $categories = $this->productModel->getAllCategories();
    $productsByCat = [];
    
    foreach ($categories as $cat) {
        // Lấy danh sách sản phẩm theo từng danh mục
        $prods = $this->productModel->getProductsForAdmin($cat['ma_danh_muc']);
        
        foreach ($prods as &$p) {
            // Lấy biến thể và gán vào mảng sản phẩm
            $p['variants'] = $this->productModel->getVariantsByProduct($p['ma_san_pham']);
        }
        // QUAN TRỌNG: Giải phóng biến tham chiếu để không gây lỗi "type int" ở View
        unset($p); 
        
        // Đưa vào mảng với cấu trúc rõ ràng
        $productsByCat[] = [
            'ma_dm' => $cat['ma_danh_muc'],
            'ten_dm' => $cat['ten_danh_muc'],
            'items' => $prods
        ];
    }
    require_once 'app/views/admin/products.php';
}

   public function manage_users() {
    // Lấy dữ liệu từ UserModel
    $allUsers = $this->userModel->getAllUsers();
    // Gọi file view đã tách riêng
    require_once 'app/views/admin/users.php';
}

// 2. Hàm xử lý khóa/mở khóa tài khoản
public function lock_user() {
    $id = $_GET['id'] ?? null;
    $status = $_GET['status'] ?? 0;
    
    if ($id) {
        $this->userModel->toggleUserLock($id, $status); 
        $_SESSION['admin_toast'] = ($status == 1) ? "Đã khóa tài khoản khách hàng!" : "Đã mở lại tài khoản!";
    }
    
    // QUAN TRỌNG: Điều hướng về đúng act manage_users
    header("Location: index.php?ctrl=admin&act=manage_users");
    exit;
}

    public function manage_orders() {
        $allOrdersList = $this->orderModel->getAllOrders();
        require_once 'app/views/admin/orders.php';
    }

    /* ============================================================
       2. XỬ LÝ SẢN PHẨM & BIẾN THỂ
       ============================================================ */

    public function add_product() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $specs = [
                'cpu' => $_POST['spec_cpu'] ?? '',
                'ram' => $_POST['spec_ram'] ?? '',
                'o_cung' => $_POST['spec_storage'] ?? '',
                'man_hinh' => $_POST['spec_screen'] ?? '',
            ];
            $data = [
                'ten_san_pham' => $_POST['ten_san_pham'],
                'ma_danh_muc'  => $_POST['ma_danh_muc'],
                'ma_thuong_hieu' => $_POST['ma_thuong_hieu'],
                'mo_ta'        => $_POST['mo_ta'],
                'thong_so_ky_thuat' => json_encode($specs, JSON_UNESCAPED_UNICODE),
                'tag'          => $_POST['tag'],
                'kich_hoat'    => $_POST['kich_hoat'],
                'cpu'                   => $_POST['cpu'] ?? '',
            'pin'                   => $_POST['pin'] ?? '',
            'trong_luong'           => $_POST['trong_luong'] ?? '',
            'kich_thuoc_man_hinh'   => $_POST['kich_thuoc_man_hinh'] ?? '',
            'cong_nghe_man_hinh'    => $_POST['cong_nghe_man_hinh'] ?? '',
            'camera_sau'            => $_POST['camera_sau'] ?? '',
            'camera_truoc'          => $_POST['camera_truoc'] ?? '',
            'nfc'                   => $_POST['nfc'] ?? '',
            'the_sim'               => $_POST['the_sim'] ?? '',
            'phan_giai_man_hinh'    => $_POST['phan_giai_man_hinh'] ?? '',
                'mau_sac'      => $_POST['mau_sac'] ?? '',
                'ram'                   => $_POST['ram'] ?? '',      // Cột mới ở bienthe
            'dung_luong'            => $_POST['dung_luong'] ?? '',
                'gia_ban'      => $_POST['gia_ban'] ?? 0,
                'muc_giam_gia' => $_POST['muc_giam_gia'] ?? 0,
                'so_luong_ton' => $_POST['so_luong_ton'] ?? 0
            ];

            $processedImages = [];
            if (!empty($_FILES['anh_san_pham']['name'][0])) {
                $files = $_FILES['anh_san_pham'];
                $mainIdx = $_POST['main_image_index'] ?? 0;
                for ($i = 0; $i < count($files['name']); $i++) {
                    $fileName = time() . '_' . basename($files['name'][$i]);
                    if (move_uploaded_file($files['tmp_name'][$i], "public/images/" . $fileName)) {
                        $processedImages[] = ['name' => $fileName, 'is_main' => ($i == $mainIdx ? 1 : 0)];
                    }
                }
            }

            if ($this->productModel->addProductFull($data, $processedImages)) {
                // 1. Gán thông báo THÀNH CÔNG
                $_SESSION['admin_toast'] = "Thêm sản phẩm thành công!";
                
                // 2. Chuyển hướng VÀ DỪNG CODE NGAY LẬP TỨC
                header("Location: index.php?ctrl=admin&act=manage_products");
                exit; 
            } else {
                // 3. Nếu thất bại -> Gán thông báo lỗi (Để debug)
                $_SESSION['admin_toast'] = "Lỗi: Không thể thêm vào CSDL!";
                // Ở lại trang thêm để sửa
                // header("Location: index.php?ctrl=admin&act=add_product"); 
                // Hoặc về danh sách kèm lỗi
                header("Location: index.php?ctrl=admin&act=manage_products");
                exit;
            }
        
        }
        $categories = $this->productModel->getAllCategories();
        $brands = $this->productModel->getAllBrands();
        require_once 'app/views/admin/add_product.php';
    }

    public function edit_product() {
    $id = $_GET['id'] ?? null;
    if (!$id) {
        header("Location: index.php?ctrl=admin&act=manage_products");
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Thu thập tất cả các cột dữ liệu mới đã bóc tách từ form
        $data = [
            'ma_san_pham'           => $id,
            'ten_san_pham'          => $_POST['ten_san_pham'],
            'ma_danh_muc'           => $_POST['ma_danh_muc'],
            'ma_thuong_hieu'        => $_POST['ma_thuong_hieu'],
            'mo_ta'                 => $_POST['mo_ta'],
            'tag'                   => $_POST['tag'],
            'kich_hoat'             => $_POST['kich_hoat'],
            
            // Các cột thông số kỹ thuật mới
            'kich_thuoc_man_hinh'   => $_POST['kich_thuoc_man_hinh'] ?? '',
            'cong_nghe_man_hinh'    => $_POST['cong_nghe_man_hinh'] ?? '',
            'phan_giai_man_hinh'    => $_POST['phan_giai_man_hinh'] ?? '',
            'camera_sau'            => $_POST['camera_sau'] ?? '',
            'camera_truoc'          => $_POST['camera_truoc'] ?? '',
            'chip_set'              => $_POST['chip_set'] ?? '',
            'cpu'                   => $_POST['cpu'] ?? '',
            'nfc'                   => $_POST['nfc'] ?? '',
            'pin'                   => $_POST['pin'] ?? '',
            'the_sim'               => $_POST['the_sim'] ?? '',
            'trong_luong'           => $_POST['trong_luong'] ?? ''
        ];

        // 1. Cập nhật thông tin cơ bản và thông số kỹ thuật vào bảng sanpham
        if ($this->productModel->updateProduct($data)) {
            
            // 2. Xử lý cập nhật ảnh chính nếu có thay đổi (Logic cũ của bạn)
            $new_main_id = $_POST['main_image_id'] ?? null;
            if ($new_main_id) {
                $this->productModel->resetMainImage($id);
                $this->productModel->setMainImage($new_main_id);
            }

            // 3. Xử lý upload thêm ảnh mới nếu có (nếu bạn có input file anh_san_pham[])
            if (!empty($_FILES['anh_san_pham']['name'][0])) {
                $files = $_FILES['anh_san_pham'];
                for ($i = 0; $i < count($files['name']); $i++) {
                    if ($files['error'][$i] === UPLOAD_ERR_OK) {
                        $fileName = time() . '_' . basename($files['name'][$i]);
                        if (move_uploaded_file($files['tmp_name'][$i], "public/images/" . $fileName)) {
                            $this->productModel->addImage($id, $fileName, 0); // Ảnh phụ
                        }
                    }
                }
            }

            $_SESSION['admin_toast'] = "Cập nhật sản phẩm thành công!";
        } else {
            $_SESSION['admin_toast'] = "Lỗi: Không thể cập nhật dữ liệu!";
        }

        // Quay lại trang quản lý sản phẩm
        header("Location: index.php?ctrl=admin&act=manage_products&ma_danh_muc=" . $_POST['ma_danh_muc']);
        exit;
    }

    // LẤY DỮ LIỆU ĐỂ ĐỔ VÀO FORM (GET)
    $product = $this->productModel->getProductById($id);
    if (!$product) {
        die("Sản phẩm không tồn tại!");
    }

    // Tìm ID của ảnh chính hiện tại để hiển thị radio button checked
    $product['main_image_id'] = 0;
    foreach (($product['images'] ?? []) as $img) {
        if (($img['is_main'] ?? 0) == 1) {
            $product['main_image_id'] = $img['ma_hinh_anh'];
            break;
        }
    }

    $categories = $this->productModel->getAllCategories();
    $brands = $this->productModel->getAllBrands();
    
    // Gọi view sửa sản phẩm
    require_once 'app/views/admin/edit_product.php';
}

    public function delete_product() {
        $id = $_GET['id'] ?? null;
        
        // Gọi Model xóa
        if ($id && $this->productModel->deleteProduct($id)) {
            $_SESSION['admin_toast'] = "Đã xóa sản phẩm thành công!";
        } else {
            $_SESSION['admin_toast'] = "Xóa thất bại! Vui lòng thử lại.";
        }
        
        // Quay về trang danh sách
        header("Location: index.php?ctrl=admin&act=manage_products");
        exit;
    }
    public function delete_variant() {
        $id = $_GET['id'] ?? null;
        
        if ($id && $this->productModel->deleteVariant($id)) {
            $_SESSION['admin_toast'] = "Đã xóa biến thể thành công!";
        } else {
            $_SESSION['admin_toast'] = "Xóa biến thể thất bại!";
        }
        
        // Quay về trang danh sách để cập nhật lại dữ liệu
        header("Location: index.php?ctrl=admin&act=manage_products");
        exit;
    }
   // Sửa hàm edit_variant trong app/controllers/AdminController.php
public function edit_variant() {
    $id = $_GET['id'] ?? null;
    if (!$id) { header("Location: index.php?ctrl=admin&act=manage_products"); exit; }

    $variant = $this->productModel->getVariantById($id);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $data = [
            'mau_sac'      => $_POST['mau_sac'],
            'ram'          => $_POST['ram'],
            'dung_luong'   => $_POST['dung_luong'],
            'gia_ban'      => $_POST['gia_ban'],
            'muc_giam_gia' => $_POST['muc_giam_gia'] ?? 0,
            'so_luong_ton' => $_POST['so_luong_ton'] ?? 0
        ];

        if ($this->productModel->updateVariant($id, $data)) {
            
            // --- PHẦN XỬ LÝ ẢNH MỚI ---
            if (isset($_FILES['anh_bien_the']) && $_FILES['anh_bien_the']['error'][0] === UPLOAD_ERR_OK) {
                $file = $_FILES['anh_bien_the'];
                $fileName = $file['name'][0];
                $fileTmp = $file['tmp_name'][0];
                
                // Tạo tên file duy nhất
                $newFileName = "variant_" . $id . "_" . time() . "_" . $fileName;
                
                if (move_uploaded_file($fileTmp, "public/images/" . $newFileName)) {
                    // Gọi hàm updateVariantImage vừa tạo ở Bước 1
                    $this->productModel->updateVariantImage($variant['ma_san_pham'], $id, $newFileName);
                }
            }
            // --------------------------

            $_SESSION['admin_toast'] = "Cập nhật biến thể thành công!";
            header("Location: index.php?ctrl=admin&act=edit_variant&id=" . $id);
            exit;
        } else {
            $_SESSION['admin_toast'] = "Lỗi khi cập nhật!";
        }
    }
    require_once 'app/views/admin/edit_variant.php';
}
    /* ============================================================
       3. XỬ LÝ KHÁCH HÀNG & ĐƠN HÀNG
       ============================================================ */

    

    public function update_order_status() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Lấy ID từ GET (theo code cũ của bạn) hoặc từ POST (nếu bạn để input hidden)
        $id = $_GET['id'] ?? $_POST['ma_don_hang'] ?? null;
        
        $status = $_POST['tt_don_hang'] ?? '';
        $payment = $_POST['tt_thanh_toan'] ?? '';

        if ($id) {
            // Hàm updateStatusAdmin trong OrderModel mà tôi đã viết cho bạn
            // sẽ tự động thực hiện: Update DB -> Check trạng thái -> Trừ/Cộng kho
            $result = $this->orderModel->updateStatusAdmin($id, $status, $payment);

            if ($result) {
                $_SESSION['admin_toast'] = "Đã cập nhật đơn hàng #$id thành công!";
            } else {
                $_SESSION['admin_error'] = "Không thể cập nhật đơn hàng.";
            }
        }
        
        header("Location: index.php?ctrl=admin&act=manage_orders");
        exit;
    }
}
    public function get_brands_by_cat() {
    $catId = $_GET['cat_id'] ?? null;
    if ($catId) {
        $brands = $this->productModel->getBrandsByCategory($catId);
        // Trả về dữ liệu dạng JSON
        header('Content-Type: application/json');
        echo json_encode($brands);
    }
    exit;
}
public function add_variant() {
    $productId = $_GET['product_id'] ?? null;
    if (!$productId) { 
        header("Location: index.php?ctrl=admin&act=manage_products"); 
        exit; 
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // 1. Get Color from POST and Trim whitespace
        $mauSac = trim($_POST['mau_sac']); 

        $data = [
            'mau_sac'      => $mauSac,
            'ram'          => $_POST['ram'],
            'dung_luong'   => $_POST['dung_luong'],
            'gia_ban'      => $_POST['gia_ban'],
            'muc_giam_gia' => $_POST['muc_giam_gia'] ?? 0,
            'so_luong_ton' => $_POST['so_luong_ton'] ?? 0
        ];

        // 2. Add Variant -> Get new ID
        $newVariantId = $this->productModel->addVariant($productId, $data);

        if ($newVariantId) {
            // 3. Upload Images
            if (!empty($_FILES['anh_bien_the']['name'][0])) {
                $files = $_FILES['anh_bien_the'];
                $mainImgIdx = isset($_POST['variant_main_idx']) ? intval($_POST['variant_main_idx']) : 0;
                
                $count = count($files['name']);
                for ($i = 0; $i < $count; $i++) {
                    // Unique file name
                    $newFileName = "variant_" . $newVariantId . "_" . time() . "_" . $files['name'][$i];
                    
                    if (move_uploaded_file($files['tmp_name'][$i], "public/images/" . $newFileName)) {
                        $isMain = ($i == $mainImgIdx) ? 1 : 0;
                        
                        // 4. CALL NEW IMAGE FUNCTION WITH COLOR
                        // Note: You must ensure 'addImageFull' exists in ProductModel as described below
                        $this->productModel->addImageFull(
                            $productId, 
                            $newFileName, 
                            $isMain, 
                            $newVariantId, 
                            $mauSac // Pass the color here
                        );
                    }
                }
            }
            
            // 5. Success Notification
            $_SESSION['admin_toast'] = "Thêm biến thể màu $mauSac thành công!";
            
            // Redirect back to add_variant form to add more or finish
            header("Location: index.php?ctrl=admin&act=add_variant&product_id=" . $productId);
            exit;
        } else {
             $_SESSION['admin_toast'] = "Lỗi khi thêm biến thể!";
        }
    }
    
    $product = $this->productModel->getProductById($productId);
    require_once 'app/views/admin/add_variant.php';
}

public function bulk_delete_products() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['product_ids'])) {
        $ids = $_POST['product_ids']; // Mảng các ID được chọn
        if ($this->productModel->deleteMultipleProducts($ids)) {
            $_SESSION['admin_toast'] = "Đã xóa " . count($ids) . " sản phẩm được chọn!";
        }
    }
    header("Location: index.php?ctrl=admin&act=manage_products");
    exit;
}

public function bulk_delete_variants() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['variant_ids'])) {
        $ids = $_POST['variant_ids'];
        if ($this->productModel->deleteMultipleVariants($ids)) {
            $_SESSION['admin_toast'] = "Đã xóa " . count($ids) . " biến thể!";
        }
    }
    header("Location: index.php?ctrl=admin&act=manage_products");
    exit;
}
public function manage_categories() {
    $categories = $this->productModel->getAllCategoriesAdmin();
    require_once 'app/views/admin/categories.php';
}

public function save_category() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $id = $_POST['ma_dm'] ?? null;
        $ten_dm = $_POST['ten_danh_muc'];
        $thu_tu = $_POST['thu_tu'];
        $hien_thi = isset($_POST['hien_thi']) ? 1 : 0;
        
        // Lấy chuỗi thương hiệu từ textarea
        $brand_str = $_POST['danh_sach_thuong_hieu'] ?? '';

        $data = ['ten_dm' => $ten_dm, 'thu_tu' => $thu_tu, 'hien_thi' => $hien_thi];

        if ($id && $id != "") {
            $this->productModel->updateCategory($id, $data);
            $newCatId = $id;
        } else {
            $this->productModel->addCategory($data);
            $newCatId = $this->db->lastInsertId(); // Lấy ID danh mục vừa tạo
        }

        // Xử lý chuỗi thương hiệu nếu có nhập
        if (!empty($brand_str)) {
            // Tách chuỗi bằng dấu phẩy thành mảng
            $brands = explode(',', $brand_str);
            // Gọi model để lưu
            $this->productModel->addBrandsToCategory($newCatId, $brands);
        }

        $_SESSION['admin_toast'] = "Đã lưu danh mục và các thương hiệu mới!";
    }
    header("Location: index.php?ctrl=admin&act=manage_categories");
    exit;
}

public function delete_category() {
    $id = $_GET['id'] ?? null;
    if ($id) {
        // Có thể thêm bước kiểm tra sản phẩm tại đây trước khi xóa
        $this->productModel->deleteCategory($id);
        $_SESSION['admin_toast'] = "Đã xóa danh mục!";
    }
    header("Location: index.php?ctrl=admin&act=manage_categories");
    exit;
}
public function index() {
    // Lấy sản phẩm nổi bật (Featured) - Giữ nguyên nếu cần
    $featuredProducts = $this->productModel->getFeaturedProducts(); 

    // Lấy sản phẩm theo danh mục đã sắp xếp
    $allCategoryData = $this->productModel->getProductsBySortedCategories();

    require_once 'app/views/home/index.php';
}
}