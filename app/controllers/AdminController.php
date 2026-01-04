<?php
// app/controllers/AdminController.php
// app/controllers/AdminController.php
require_once 'app/models/ProductModel.php';
require_once 'app/models/UserModel.php';
require_once 'app/models/OrderModel.php';
require_once 'app/models/AdminModel.php';
require_once 'app/models/ChatModel.php'; //
class AdminController {
    private $productModel;
    private $userModel;
    private $orderModel;
    private $adminModel;
    private $db;
    public function __construct() {
        $act = $_GET['act'] ?? 'dashboard';
        if ($act !== 'login' && !isset($_SESSION['admin_id'])) {
            header("Location: index.php?ctrl=admin&act=login");
            exit;
        }
        $database = new Database();
        $db = $database->connect();
        $this->db = $db;
        $this->productModel = new ProductModel($db);
        $this->userModel = new UserModel($db);
        $this->orderModel = new OrderModel();
        $this->adminModel = new AdminModel($db); 
        
    }

    private function checkAccess($requiredPerm) {
        // 1. Nếu là Super Admin (role = 1) -> Cho qua hết
        if (isset($_SESSION['admin_role']) && $_SESSION['admin_role'] == 1) {
            return true;
        }

        // 2. Nếu là Nhân viên -> Kiểm tra mảng quyền
        $myPerms = $_SESSION['admin_permissions'] ?? [];
        if (in_array($requiredPerm, $myPerms)) {
            return true;
        }

        // 3. Không có quyền -> Chặn
        echo "<div style='font-family: sans-serif; text-align: center; margin-top: 50px;'>";
        echo "<h1 style='color: red; font-size: 80px;'><i class='fa-solid fa-ban'></i></h1>";
        echo "<h2>BẠN KHÔNG CÓ QUYỀN TRUY CẬP TRANG NÀY!</h2>";
        echo "<p>Vui lòng liên hệ Admin cấp cao.</p>";
        echo "<a href='index.php?ctrl=admin&act=dashboard' style='padding: 10px 20px; background: #333; color: white; text-decoration: none; border-radius: 5px;'>Quay lại Dashboard</a>";
        echo "</div>";
        exit;
    }
   public function login() {
        // 1. Nếu đã đăng nhập từ trước -> Điều hướng ngay
        if (isset($_SESSION['admin_id'])) {
            if (isset($_SESSION['admin_role']) && $_SESSION['admin_role'] == 1) {
                header("Location: index.php?ctrl=admin&act=dashboard");
            } else {
                $this->redirectStaff(); // Hàm tự viết bên dưới để điều hướng nhân viên
            }
            exit;
        }

        // 2. Xử lý khi bấm nút Đăng nhập
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $user = $_POST['username'];
            $pass = $_POST['password'];
            
            // Gọi Model kiểm tra
            $admin = $this->adminModel->checkLogin($user, $pass);

            if ($admin) {
                // Lưu thông tin vào Session
                $_SESSION['admin_id'] = $admin['ma_admin'];
                $_SESSION['admin_name'] = $admin['ho_ten'];
                $_SESSION['admin_avatar'] = $admin['anh_admin'];
                $_SESSION['admin_role'] = $admin['role']; 
                // Chuyển chuỗi quyền thành mảng
                $_SESSION['admin_permissions'] = !empty($admin['permissions']) ? explode(',', $admin['permissions']) : [];

                // --- LOGIC ĐIỀU HƯỚNG THÔNG MINH ---
                if ($_SESSION['admin_role'] == 1) {
                    // Nếu là Super Admin -> Vào trang chủ Dashboard
                    header("Location: index.php?ctrl=admin&act=dashboard");
                } else {
                    // Nếu là Nhân viên -> Tìm trang phù hợp để đẩy vào
                    $this->redirectStaff();
                }
                exit;
            } else {
                $error = "Tên đăng nhập hoặc mật khẩu không đúng!";
                require_once 'app/views/admin/login.php';
            }
        } else {
            require_once 'app/views/admin/login.php';
        }
    }
    private function redirectStaff() {
        $perms = $_SESSION['admin_permissions'] ?? [];
        
        if (in_array('orders', $perms)) {
            header("Location: index.php?ctrl=admin&act=manage_orders");
        } elseif (in_array('products', $perms)) {
            header("Location: index.php?ctrl=admin&act=manage_products");
        } elseif (in_array('users', $perms)) {
            header("Location: index.php?ctrl=admin&act=manage_users");
        } elseif (in_array('reviews', $perms)) {
            header("Location: index.php?ctrl=admin&act=reviews");
        } else {
            // Trường hợp tài khoản nhân viên nhưng không được tích quyền nào
            echo "<div style='text-align:center; margin-top:50px;'>";
            echo "<h3>Tài khoản chưa được cấp quyền truy cập!</h3>";
            echo "<a href='index.php?ctrl=admin&act=logout'>Đăng xuất</a>";
            echo "</div>";
            session_destroy();
        }
        exit;
    }

    public function logout() {
        session_destroy();
        header("Location: index.php?ctrl=admin&act=login");
        exit;
    }

    // Trang quản lý nhân viên (Chỉ Super Admin)
    public function staff() {
        if (!isset($_SESSION['admin_role']) || $_SESSION['admin_role'] != 1) {
            $this->checkAccess('super_admin');
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $u = $_POST['username'];
            $p = $_POST['password'];
            $n = $_POST['fullname'];
            $perms = $_POST['permissions'] ?? []; 
            
            // Xử lý Upload Avatar
            $avatarName = 'default_admin.png'; // Ảnh mặc định
            if (!empty($_FILES['avatar']['name'])) {
                $file = $_FILES['avatar'];
                if ($file['error'] === UPLOAD_ERR_OK) {
                    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
                    $newName = "staff_" . time() . "." . $ext;
                    if (move_uploaded_file($file['tmp_name'], "public/images/" . $newName)) {
                        $avatarName = $newName;
                    }
                }
            }

            // Gọi Model thêm mới
            if ($this->adminModel->addStaff($u, $p, $n, $perms, $avatarName)) {
                $_SESSION['admin_toast'] = "Đã thêm nhân viên mới!";
            } else {
                $_SESSION['admin_toast'] = "Lỗi: Tên đăng nhập đã tồn tại!";
            }
            header("Location: index.php?ctrl=admin&act=staff");
            exit;
        }

        $staffs = $this->adminModel->getAllStaff();
        require_once 'app/views/admin/staff.php';
    }
    public function reset_password_staff() {
        // Chỉ Super Admin mới được reset
        if (!isset($_SESSION['admin_role']) || $_SESSION['admin_role'] != 1) {
            header("Location: index.php?ctrl=admin&act=dashboard");
            exit;
        }

        $id = $_POST['id'] ?? null;
        $newPass = $_POST['new_pass'] ?? null;

        if ($id && $newPass) {
            $this->adminModel->resetStaffPassword($id, $newPass);
            $_SESSION['admin_toast'] = "Đã đổi mật khẩu nhân viên thành công!";
        }
        header("Location: index.php?ctrl=admin&act=staff");
        exit;
    }
    
    public function delete_staff() {
         if (isset($_SESSION['admin_role']) && $_SESSION['admin_role'] == 1 && isset($_GET['id'])) {
             $this->adminModel->deleteStaff($_GET['id']);
             $_SESSION['admin_toast'] = "Đã xóa nhân viên!";
         }
         header("Location: index.php?ctrl=admin&act=staff");
         exit;
    }
    /* ============================================================
       1. CÁC TRANG HIỂN THỊ (VIEW) - ĐÃ TÁCH RIÊNG
       ============================================================ */

    public function dashboard() {
        if (!isset($_SESSION['admin_role']) || $_SESSION['admin_role'] != 1) {
            // Nếu là nhân viên mà lạc vào đây -> Đẩy về đúng chỗ
            $this->redirectStaff(); 
        }
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
    $this->checkAccess('products');
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
    $this->checkAccess('users');
    // Lấy dữ liệu từ UserModel
    $allUsers = $this->userModel->getAllUsers();
    // Gọi file view đã tách riêng
    require_once 'app/views/admin/users.php';
}

// 2. Hàm xử lý khóa/mở khóa tài khoản
public function lock_user() {
    $this->checkAccess('users');
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
        $this->checkAccess('orders');
        $allOrdersList = $this->orderModel->getAllOrders();
        require_once 'app/views/admin/orders.php';
    }

    /* ============================================================
       2. XỬ LÝ SẢN PHẨM & BIẾN THỂ
       ============================================================ */

    public function add_product() {
        $this->checkAccess('products');
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
    $this-> checkAccess('products');
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
        $this->checkAccess('products');
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
        $this->checkAccess('products');
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
    $this->checkAccess('products');
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
        $this->checkAccess('orders');
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
    $this->checkAccess('products');
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
    $this->checkAccess('products');
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
    $this->checkAccess('products');
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
    $this->checkAccess('products');
    $categories = $this->productModel->getAllCategoriesAdmin();
    require_once 'app/views/admin/categories.php';
}

public function save_category() {
    $this->checkAccess('products');
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
    $this->checkAccess('products');
    $id = $_GET['id'] ?? null;
    if ($id) {
        // Có thể thêm bước kiểm tra sản phẩm tại đây trước khi xóa
        $this->productModel->deleteCategory($id);
        $_SESSION['admin_toast'] = "Đã xóa danh mục!";
    }
    header("Location: index.php?ctrl=admin&act=manage_categories");
    exit;
}
public function reviews() {
    $this->checkAccess('reviews');
    // Gọi hàm lấy tất cả review từ Model (đã viết ở Bước 1)
    $reviews = $this->productModel->getAllReviews(); 
    require_once 'app/views/admin/reviews.php';
}

public function delete_review() {
    $this->checkAccess('reviews');
    if (isset($_GET['id'])) {
        $id = $_GET['id'];
        $this->productModel->deleteReview($id);
    }
    header("Location: index.php?ctrl=admin&act=reviews");
}
public function my_profile() {
        $id = $_SESSION['admin_id'];

        // Xử lý khi upload ảnh
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
                $file = $_FILES['avatar'];
                $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
                
                // Đặt tên file: admin_{id}_{time}.jpg
                $newName = "admin_" . $id . "_" . time() . "." . $ext;
                $uploadDir = "public/images/admin/";

                if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

                if (move_uploaded_file($file['tmp_name'], $uploadDir . $newName)) {
                    // 1. Cập nhật Database
                    $this->adminModel->updateAdminAvatar($id, $newName);
                    
                    // 2. Cập nhật ngay vào Session để Sidebar đổi hình luôn
                    $_SESSION['admin_avatar'] = $newName;
                    
                    $_SESSION['admin_toast'] = "Đã cập nhật ảnh đại diện mới!";
                }
            }
            header("Location: index.php?ctrl=admin&act=my_profile");
            exit;
        }

        // Lấy thông tin admin hiện tại để hiển thị
        $myInfo = $this->adminModel->getAdminById($id);
        require_once 'app/views/admin/my_profile.php';
    }
    // ... Các function cũ ...

    // 1. Hiển thị danh sách lịch sử chat
   // 1. Hiển thị danh sách lịch sử chat
   public function chats() {
        // Không cần gọi $this->checkAdmin() vì __construct đã check login rồi
        $chatModel = new ChatModel($this->db);
        $chats = $chatModel->getAllChats();
        require_once 'app/views/admin/chats.php'; 
    }

    // 2. Xử lý cập nhật nội dung trả lời
    public function update_chat() {
        // Khởi tạo model
        $chatModel = new ChatModel($this->db);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Lấy ID tin nhắn và Nội dung mới từ form
            $id = $_POST['ma_chat'];
            $newResponse = $_POST['tra_loi']; // Tên input trong form là 'tra_loi'
            
            // Gọi Model để cập nhật vào Database
            $chatModel->updateChatResponse($id, $newResponse);
            
            // Cập nhật xong thì quay lại trang danh sách
            header("Location: index.php?ctrl=admin&act=chats");
            exit;
        }
    }
    public function delete_chat() {
        $chatModel = new ChatModel($this->db);
        if (isset($_GET['id'])) {
            $chatModel->deleteChat($_GET['id']);
        }
        header("Location: index.php?ctrl=admin&act=chats");
    }
    // ... (Các hàm cũ giữ nguyên) ...

    // ====================================================
    // QUẢN LÝ HUẤN LUYỆN CHATBOT (Training Data)
    // ====================================================

    public function bot_training() {
        $chatModel = new ChatModel($this->db);
        $dataList = $chatModel->getAllTrainingData();
        require_once 'app/views/admin/bot_training.php';
    }

    public function save_training() {
        $chatModel = new ChatModel($this->db);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['ma_du_lieu'] ?? '';
            $question = trim($_POST['cau_hoi']);
            $reply = trim($_POST['tra_loi']);

            if (!empty($question) && !empty($reply)) {
                if ($id) {
                    // Cập nhật
                    $chatModel->updateTrainingData($id, $question, $reply);
                    $_SESSION['admin_toast'] = "Đã cập nhật câu trả lời mẫu!";
                } else {
                    // Thêm mới
                    $chatModel->addTrainingData($question, $reply);
                    $_SESSION['admin_toast'] = "Đã thêm câu huấn luyện mới!";
                }
            }
        }
        header("Location: index.php?ctrl=admin&act=bot_training");
        exit;
    }

    public function delete_training() {
        $chatModel = new ChatModel($this->db);
        if (isset($_GET['id'])) {
            $chatModel->deleteTrainingData($_GET['id']);
            $_SESSION['admin_toast'] = "Đã xóa dữ liệu huấn luyện!";
        }
        header("Location: index.php?ctrl=admin&act=bot_training");
        exit;
    }
} // Kết thúc class

