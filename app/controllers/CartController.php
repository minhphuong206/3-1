<?php
// app/controllers/CartController.php

require_once 'app/config/db.php';
require_once 'app/core/MomoPayment.php';

class CartController {
    private $cartModel;

    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        require_once 'app/config/db.php'; // Đảm bảo đã load DB
        require_once 'app/models/CartModel.php'; // Load Model mới
        $db = new Database();
        $this->cartModel = new CartModel($db->connect());
    }
    
    // ==============================================
    // 1. TRANG GIỎ HÀNG
    // ==============================================
    public function index() {
        if (isset($_SESSION['user_id'])) {
            // Nếu đã đăng nhập, luôn lấy giỏ hàng mới nhất từ DB đổ ra Session để hiển thị
            $_SESSION['cart'] = $this->cartModel->getCartItems($_SESSION['user_id']);
        }
        // Kiểm tra file view
        $viewPath = 'app/views/cart/giohang.php';
        if (file_exists($viewPath)) {
            require_once $viewPath;
        } else {
            require_once 'app/views/giohang.php';
        }
    }

    // ==============================================
    // 2. THÊM VÀO GIỎ (API JSON)
    // ==============================================
    // ==============================================
    // 2. THÊM VÀO GIỎ (API JSON) - ĐÃ SỬA
    // ==============================================
    public function add() {
        header('Content-Type: application/json');

        try {
            $id = $_GET['id'] ?? 0;
            $qty = (int)($_GET['so_luong'] ?? 1);
            $color = $_GET['mau_sac'] ?? '';
            $storage = $_GET['dung_luong'] ?? '';

            if (!$id) {
                echo json_encode(['status' => 'error', 'message' => 'Thiếu ID sản phẩm']);
                return;
            }

            // 1. Lấy thông tin sản phẩm và biến thể để tính giá
            $db = new Database();
            $conn = $db->connect();

            // a. Lấy tên sp
            $stmt = $conn->prepare("SELECT ten_san_pham FROM sanpham WHERE ma_san_pham = ?");
            $stmt->execute([$id]);
            $product = $stmt->fetch();

            if (!$product) {
                echo json_encode(['status' => 'error', 'message' => 'Sản phẩm không tồn tại']);
                return;
            }

            // b. Tìm biến thể (Variant)
            $sqlVar = "SELECT ma_bien_the, gia_ban, muc_giam_gia, dung_luong, mau_sac 
                       FROM bienthesanpham WHERE ma_san_pham = ?";
            $params = [$id];

            if (!empty($storage)) {
                $sqlVar .= " AND dung_luong = ?";
                $params[] = $storage;
            }
            if (!empty($color)) {
                $sqlVar .= " AND mau_sac = ?";
                $params[] = $color;
            }
            $sqlVar .= " LIMIT 1";

            $stmtVar = $conn->prepare($sqlVar);
            $stmtVar->execute($params);
            $variant = $stmtVar->fetch();

            // Fallback: Nếu không tìm thấy đúng màu/dung lượng, lấy biến thể đầu tiên
            if (!$variant) {
                $stmtFallback = $conn->prepare("SELECT ma_bien_the, gia_ban, muc_giam_gia, dung_luong, mau_sac FROM bienthesanpham WHERE ma_san_pham = ? LIMIT 1");
                $stmtFallback->execute([$id]);
                $variant = $stmtFallback->fetch();
                
                if (!$variant) {
                    echo json_encode(['status' => 'error', 'message' => 'Sản phẩm tạm hết hàng.']);
                    return;
                }
            }

            // c. Lấy ảnh
            $stmtImg = $conn->prepare("SELECT url_anh FROM anhsanpham WHERE ma_san_pham = ? ORDER BY la_anh_chinh DESC LIMIT 1");
            $stmtImg->execute([$id]);
            $image = $stmtImg->fetch();

            // d. Chuẩn bị dữ liệu để lưu
            $price = $variant['gia_ban'];
            $discount = $variant['muc_giam_gia'] ?? 0;
            $finalPrice = $price * (1 - $discount / 100);
            
            $useColor = $color ?: ($variant['mau_sac'] ?? '');
            $useStorage = $storage ?: ($variant['dung_luong'] ?? '');

            // --- [PHẦN QUAN TRỌNG ĐÃ ĐƯỢC BỔ SUNG] ---
            if (isset($_SESSION['user_id'])) {
                // TRƯỜNG HỢP 1: ĐÃ ĐĂNG NHẬP -> GỌI MODEL LƯU DB
                $this->cartModel->addToCartDB(
                    $_SESSION['user_id'], 
                    $id, 
                    $qty, 
                    $useColor, 
                    $useStorage
                );
                // Đồng bộ lại Session để hiển thị đúng
                $_SESSION['cart'] = $this->cartModel->getCartItems($_SESSION['user_id']);

            } else {
                // TRƯỜNG HỢP 2: CHƯA ĐĂNG NHẬP -> LƯU SESSION (Logic cũ)
                $cartKey = $id . '_' . md5($useColor . $useStorage);

                if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];

                if (isset($_SESSION['cart'][$cartKey])) {
                    $_SESSION['cart'][$cartKey]['quantity'] += $qty;
                } else {
                    $_SESSION['cart'][$cartKey] = [
                        'id' => $id,
                        'variant_id' => $variant['ma_bien_the'],
                        'name' => $product['ten_san_pham'],
                        'image' => $image['url_anh'] ?? 'default.png',
                        'price' => $finalPrice,
                        'quantity' => $qty,
                        'color' => $useColor,
                        'storage' => $useStorage,
                        'max_qty' => 100 
                    ];
                }
            }
            // ------------------------------------------

            echo json_encode([
                'status' => 'success',
                'message' => 'Đã thêm vào giỏ hàng!',
                'total_items' => count($_SESSION['cart'])
            ]);

        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => 'Lỗi: ' . $e->getMessage()]);
        }
    }
    // ==============================================
    // 3. CẬP NHẬT / XÓA
    // ==============================================
    public function remove() {
        $id = $_GET['id'] ?? 0; 
        $color = $_GET['color'] ?? ''; 
        $storage = $_GET['storage'] ?? '';
        // Lưu ý: View của bạn cần truyền đúng tham số id, color, storage thay vì 'key' hash cũ

        if (isset($_SESSION['user_id'])) {
            $this->cartModel->removeItemDB($_SESSION['user_id'], $id, $color, $storage);
            $_SESSION['cart'] = $this->cartModel->getCartItems($_SESSION['user_id']);
        } else {
            // Xóa theo key session (Cần đảm bảo View truyền đúng key)
            $key = $_GET['key'] ?? ''; 
            if (isset($_SESSION['cart'][$key])) unset($_SESSION['cart'][$key]);
        }
        header("Location: index.php?ctrl=cart");
        exit;
    }

    public function update() {
        // Khuyên dùng POST và Ajax cho chuyên nghiệp, nhưng nếu dùng GET như cũ:
        $id = $_GET['id'] ?? 0;
        $qty = (int)($_GET['qty'] ?? 1);
        $color = $_GET['color'] ?? '';
        $storage = $_GET['storage'] ?? '';

        if (isset($_SESSION['user_id'])) {
            $this->cartModel->updateQuantityDB($_SESSION['user_id'], $id, $color, $storage, $qty);
            $_SESSION['cart'] = $this->cartModel->getCartItems($_SESSION['user_id']);
        } else {
            $key = $_GET['key'] ?? '';
            if (isset($_SESSION['cart'][$key])) {
                if ($qty <= 0) unset($_SESSION['cart'][$key]);
                else $_SESSION['cart'][$key]['quantity'] = $qty;
            }
        }
        header("Location: index.php?ctrl=cart");
        exit;
    }

    // ==============================================
    // 4. CHECKOUT (XỬ LÝ ĐẶT HÀNG) - ĐÃ CẬP NHẬT
    // ==============================================
    public function checkout() {
        // --- 1. KIỂM TRA ĐĂNG NHẬP (CODE BẠN YÊU CẦU) ---
        if (!isset($_SESSION['user_id'])) {
            // Lưu lại dữ liệu khách vừa nhập (Họ tên, địa chỉ, sdt...) vào Session
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $_SESSION['saved_cart_data'] = $_POST;
            }
            
            // Gán thông báo hiển thị bên trang Login
            $_SESSION['login_toast_msg'] = "Khách hàng hãy đăng nhập!";
            
            // Chuyển hướng sang đăng nhập kèm tín hiệu redirect
            header("Location: index.php?ctrl=auth&act=login&redirect=cart");
            exit;
        }

        // --- 2. NẾU ĐÃ ĐĂNG NHẬP THÌ XỬ LÝ TIẾP ---
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            try {
                if (empty($_SESSION['cart'])) {
                    header("Location: index.php?ctrl=cart");
                    exit;
                }

                // Lấy dữ liệu form
                $hoTen = trim($_POST['ho_ten'] ?? '');
                $sdt = trim($_POST['so_dien_thoai'] ?? '');
                $diaChi = trim($_POST['dia_chi'] ?? '');
                $ghiChu = trim($_POST['ghi_chu'] ?? '');
                $method = $_POST['thanh_toan'] ?? 'cod';
                $email = trim($_POST['email'] ?? '');

                if (empty($hoTen) || empty($sdt) || empty($diaChi)) {
                    $_SESSION['error'] = "Vui lòng nhập đủ thông tin nhận hàng.";
                    header("Location: index.php?ctrl=cart");
                    exit;
                }

                $totalMoney = 0;
                foreach ($_SESSION['cart'] as $item) {
                    $totalMoney += $item['price'] * $item['quantity'];
                }

                $orderCode = 'ORD_' . time();

                // Lưu Session tạm
                $_SESSION['temp_order'] = [
                    'code' => $orderCode,
                    'name' => $hoTen,
                    'phone' => $sdt,
                    'email' => $email,
                    'address' => $diaChi,
                    'note' => $ghiChu,
                    'total' => $totalMoney,
                    'method' => $method,
                    'cart' => $_SESSION['cart'],
                    'status' => 'pending'
                ];

                // --- PHÂN NHÁNH THANH TOÁN ---
                if ($method == 'momo') {
                    if (!class_exists('MomoPayment')) {
                        throw new Exception("Chưa load được class MomoPayment.");
                    }

                    $momo = new MomoPayment();
                    $result = $momo->createPayment($orderCode, $totalMoney);

                    if (isset($result['payUrl'])) {
                        header("Location: " . $result['payUrl']);
                        exit;
                    } else {
                        echo "<div style='font-family:sans-serif; padding:30px; text-align:center;'>";
                        echo "<h2 style='color:red'>LỖI KẾT NỐI MOMO</h2>";
                        echo "<p>Hệ thống không nhận được link thanh toán.</p>";
                        if(isset($result['message'])) echo "<p><b>Lỗi từ MoMo:</b> " . $result['message'] . "</p>";
                        echo "<hr><p>Chi tiết dữ liệu trả về:</p>";
                        echo "<pre style='text-align:left; background:#f4f4f4; padding:15px; display:inline-block;'>" . print_r($result, true) . "</pre>";
                        echo "<br><br><a href='index.php?ctrl=cart' style='padding:10px 20px; background:#333; color:white; text-decoration:none'>Quay lại Giỏ hàng</a>";
                        echo "</div>";
                        die();
                    }

                } else {
                    // COD
                    if ($this->saveOrderToDatabase()) {
                        unset($_SESSION['cart']);
                        unset($_SESSION['temp_order']);
                        
                        // Xóa dữ liệu form tạm nếu có
                        if (isset($_SESSION['saved_cart_data'])) {
                            unset($_SESSION['saved_cart_data']);
                        }

                        header("Location: index.php?ctrl=cart&act=success&code=$orderCode");
                        exit;
                    } else {
                        throw new Exception("Lỗi lưu đơn hàng vào CSDL.");
                    }
                }

            } catch (Exception $e) {
                $_SESSION['error'] = "Lỗi hệ thống: " . $e->getMessage();
                header("Location: index.php?ctrl=cart");
                exit;
            }
        } else {
            header("Location: index.php?ctrl=cart");
        }
    }

    // ==============================================
    // 5. XỬ LÝ MOMO RETURN
    // ==============================================
    public function return_momo() {
        if (isset($_GET['resultCode'])) {
            $resultCode = $_GET['resultCode'];
            $orderCode = $_GET['orderId'] ?? '';

            if ($resultCode == '0') {
                if (isset($_SESSION['temp_order'])) {
                    $_SESSION['temp_order']['status'] = 'paid';
                    
                    if ($this->saveOrderToDatabase()) {
                        unset($_SESSION['cart']);
                        unset($_SESSION['temp_order']);
                        if (isset($_SESSION['saved_cart_data'])) unset($_SESSION['saved_cart_data']);
                        header("Location: index.php?ctrl=cart&act=success&code=$orderCode");
                        exit;
                    } else {
                        echo "Thanh toán thành công nhưng lỗi lưu đơn hàng. Vui lòng liên hệ Admin.";
                    }
                } else {
                    echo "Giao dịch thành công! Mã đơn: $orderCode. (Session đã hết hạn)";
                }
            } else {
                $_SESSION['error'] = "Thanh toán MoMo thất bại hoặc bị hủy (Mã lỗi: $resultCode).";
                header("Location: index.php?ctrl=cart");
                exit;
            }
        } else {
            header("Location: index.php");
        }
    }

    // ==============================================
    // 6. TRANG THÀNH CÔNG
    // ==============================================
    public function success() {
        $code = $_GET['code'] ?? '';
        if (file_exists('app/views/cart/success.php')) {
            require_once 'app/views/cart/success.php';
        } else {
            echo "<div style='text-align:center; padding: 50px; font-family: sans-serif;'>";
            echo "<h1 style='color:green; font-size: 30px;'>ĐẶT HÀNG THÀNH CÔNG! <i class='fa-solid fa-check-circle'></i></h1>";
            echo "<p style='font-size: 18px;'>Mã đơn hàng của bạn: <strong>$code</strong></p>";
            echo "<p>Cảm ơn bạn đã mua sắm tại Phương Store.</p>";
            echo "<a href='index.php' style='display:inline-block; margin-top:20px; padding:10px 20px; background:#d70018; color:white; text-decoration:none; border-radius:5px;'>Tiếp tục mua hàng</a>";
            echo "</div>";
        }
    }

    // ==============================================
    // 7. HÀM LƯU DB (Private)
    // ==============================================
    private function saveOrderToDatabase() {
        if (!isset($_SESSION['temp_order'])) return false;

        $db = new Database();
        $conn = $db->connect();

        $order = $_SESSION['temp_order'];
        $customerId = $_SESSION['user_id'] ?? null; // Sửa: lấy đúng user_id từ session login

        try {
            $conn->beginTransaction();

            $ptThanhToan = ($order['method'] == 'momo') ? 'MoMo' : 'COD';
            $ttThanhToan = ($order['status'] == 'paid') ? 'Đã thanh toán' : 'Chưa thanh toán';
            $ttDonHang = 'Chờ duyệt';

            // Insert DONHANG
            $sqlOrder = "INSERT INTO donhang 
                (ma_don_hang_code, ma_khach_hang, ho_ten_nguoi_nhan, sdt_nguoi_nhan, 
                 tong_tien, pt_thanh_toan, tt_thanh_toan, tt_don_hang, dia_chi_giao, ghi_chu) 
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            
            $stmt = $conn->prepare($sqlOrder);
            $stmt->execute([
                $order['code'],
                $customerId,
                $order['name'],
                $order['phone'],
                $order['total'],
                $ptThanhToan,
                $ttThanhToan,
                $ttDonHang,
                $order['address'],
                $order['note']
            ]);

            $lastId = $conn->lastInsertId();

            // Insert CHITIETDONHANG
            $sqlDetail = "INSERT INTO chitietdonhang (ma_don_hang, ma_bien_the, so_luong, don_gia) VALUES (?, ?, ?, ?)";
            $stmtDetail = $conn->prepare($sqlDetail);

            foreach ($order['cart'] as $item) {
                $variantId = $item['variant_id'] ?? null;
                if (!$variantId) {
                    $stmtFind = $conn->prepare("SELECT ma_bien_the FROM bienthesanpham WHERE ma_san_pham = ? AND mau_sac = ? AND dung_luong = ? LIMIT 1");
                    $stmtFind->execute([$item['id'], $item['color'], $item['storage']]);
                    $v = $stmtFind->fetch();
                    $variantId = $v['ma_bien_the'] ?? null;
                }

                if ($variantId) {
                    $stmtDetail->execute([
                        $lastId,
                        $variantId,
                        $item['quantity'],
                        $item['price']
                    ]);
                }
            }

            $conn->commit();
            return true;

        } catch (Exception $e) {
            $conn->rollBack();
            error_log("DB Error: " . $e->getMessage());
            return false;
        }
    }
}
?>