<?php
// app/controllers/AuthController.php

require_once 'app/models/UserModel.php';
require_once 'app/libs/send_mail.php';

class AuthController {
    private $userModel;
    private $db;

    public function __construct() {
        // 1. Kết nối Database
        if (file_exists('app/config/db.php')) {
            require_once 'app/config/db.php';
        }
        
        $database = new Database();
        $this->db = $database->connect(); 

        // 2. Khởi tạo UserModel
        if (file_exists('app/models/UserModel.php')) {
            require_once 'app/models/UserModel.php';
            $this->userModel = new UserModel($this->db);
        }
    }

    // ==============================================
    // 1. ĐĂNG NHẬP (LOGIN)
    // ==============================================
    // ==============================================
    // 1. ĐĂNG NHẬP (LOGIN) - ĐÃ CẬP NHẬT
    // ==============================================
    public function login() {
        if (isset($_SESSION['user_id'])) {
            header("Location: index.php");
            exit;
        }

        $error = '';
        $email_old = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email']);
            $password = $_POST['password'];
            $email_old = $email;

            // Kiểm tra đăng nhập thông qua Model
            $result = $this->userModel->checkLogin($email, $password);

            if ($result === true) {
                
                // --- PHẦN SỬA ĐỔI: ĐIỀU HƯỚNG THÔNG MINH ---
                // Lấy tham số redirect từ URL (GET) hoặc từ Form (POST - nếu có input hidden)
                $redirect = $_GET['redirect'] ?? $_POST['redirect'] ?? '';

                if ($redirect === 'cart') {
                    // Nếu tín hiệu là 'cart' -> Quay về Giỏ hàng
                    header("Location: index.php?ctrl=cart");
                } else {
                    // Mặc định -> Về Trang chủ
                    header("Location: index.php");
                }
                exit;
                // -------------------------------------------

            } else {
                $error = $result; // Gán chuỗi thông báo lỗi
            }
        }
        require_once 'app/views/auth/login.php';
    }
    // ==============================================
    // 2. ĐĂNG KÝ (REGISTER) - Có lấy ảnh Admin gửi Mail
    // ==============================================
    public function register() {
    if (isset($_SESSION['user_id'])) {
        header("Location: index.php");
        exit;
    }

    $errors = [];
    $success = '';
    $old = [];

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $data = [
            'full_name' => trim($_POST['full_name']),
            'email'     => trim($_POST['email']),
            'phone'     => trim($_POST['phone']),
            'password'  => $_POST['password']
        ];
        $confirmPass = $_POST['confirm_password'];
        $old = $data;

        // 1. Kiểm tra lỗi
        if ($this->userModel->checkEmailExists($data['email'])) {
            $errors['email'] = "Email này đã tồn tại!";
        }
        if ($data['password'] !== $confirmPass) {
            $errors['confirm_password'] = "Mật khẩu xác nhận không khớp!";
        }

        if (empty($errors)) {
            // 2. Lưu vào Database thông qua UserModel
            if ($this->userModel->register($data)) {
                
                // --- PHẦN GỬI MAIL CHÚC MỪNG (CẬP NHẬT DYNAMIC URL) ---
                try {
                    // Lấy thông tin Admin để làm chữ ký mail
                    $stmt = $this->db->prepare("SELECT anh_admin, ho_ten FROM admin WHERE ma_admin = 1 LIMIT 1");
                    $stmt->execute();
                    $admin = $stmt->fetch();

                    // Tự động nhận diện Domain (Không còn bị fix cứng tên thư mục)
                    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https" : "http";
                    $host = $_SERVER['HTTP_HOST'];
                    $path = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'])), '/');
                    $domain = $protocol . "://" . $host . $path . '/';
                    
                    $adminPhoto = $domain . "public/images/admin/" . ($admin['anh_admin'] ?? 'default.png');

                    $subject = "Chào mừng thành viên mới - PhươngSTORE";
                    $content = "
                        <div style='background: #050505; padding: 30px; font-family: Arial, sans-serif; color: white; text-align: center;'>
                            <div style='max-width: 500px; margin: auto; background: #141414; border: 1px solid #D4AF37; border-radius: 15px; padding: 30px;'>
                                <img src='{$adminPhoto}' style='width: 90px; height: 90px; border-radius: 50%; border: 3px solid #D4AF37; object-fit: cover; margin-bottom: 20px;'>
                                <h2 style='color: #D4AF37; margin-bottom: 10px;'>XÁC NHẬN ĐĂNG KÝ THÀNH CÔNG</h2>
                                <p style='font-size: 16px;'>Chào mừng <b>{$data['full_name']}</b>,</p>
                                <p style='color: #ccc; line-height: 1.6;'>Tài khoản của bạn đã được khởi tạo thành công tại <b>PhươngSTORE</b>. Chúc bạn có những trải nghiệm mua sắm công nghệ tuyệt vời nhất!</p>
                                <a href='{$domain}index.php?ctrl=auth&act=login' style='display: inline-block; margin-top: 25px; padding: 12px 30px; background: #D4AF37; color: black; font-weight: bold; text-decoration: none; border-radius: 8px;'>ĐĂNG NHẬP NGAY</a>
                                <hr style='border: 0.5px solid #333; margin: 30px 0;'>
                                <p style='font-size: 13px; color: #777;'>Trân trọng,<br>Admin <b>{$admin['ho_ten']}</b></p>
                            </div>
                        </div>
                    ";

                    sendEmail($data['email'], $subject, $content);
                } catch (Exception $e) {
                    // Nếu lỗi gửi mail thì vẫn cho đăng ký xong, chỉ là không nhận được mail
                    error_log("Lỗi gửi mail đăng ký: " . $e->getMessage());
                }

                $success = "Tạo tài khoản thành công! Vui lòng kiểm tra email chúc mừng.";
                header("refresh:2;url=index.php?ctrl=auth&act=login");
            }
        }
    }
    require_once 'app/views/auth/register.php';
}

    // ==============================================
    // 3. ĐĂNG XUẤT (LOGOUT)
    // ==============================================
    public function logout() {
        session_destroy();
        header("Location: index.php");
        exit;
    }

    // ==============================================
    // 4. QUÊN MẬT KHẨU (FORGOT PASSWORD)
    // ==============================================
    public function forgot_password() {
        $error = '';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email']);
            if ($this->userModel->checkEmailExists($email)) {
                $otp = rand(100000, 999999);
                $expiry = date("Y-m-d H:i:s", strtotime('+15 minutes'));
                $this->userModel->saveResetOTP($email, $otp, $expiry);
                
                $subject = "Mã xác thực OTP - PhươngSTORE";
                $content = "Mã OTP của bạn là: <b style='font-size:24px; color:#D4AF37;'>$otp</b> (Hết hạn sau 15 phút)";
                sendEmail($email, $subject, $content);
                
                header("Location: index.php?ctrl=auth&act=reset_password&email=".urlencode($email));
                exit;
            } else { $error = "Email không tồn tại!"; }
        }
        require_once 'app/views/auth/forgot_password.php';
    }

    // ==============================================
    // 5. ĐẶT LẠI MẬT KHẨU (RESET PASSWORD)
    // ==============================================
    public function reset_password() {
        $email = $_GET['email'] ?? '';
        $error = ''; $success = '';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $otp = trim($_POST['otp']);
            $new_pass = $_POST['new_password'];
            if ($new_pass !== $_POST['confirm_password']) { $error = "Mật khẩu không khớp!"; }
            else {
                if ($this->userModel->verifyOTP($email, $otp)) {
                    $this->userModel->updatePassword($email, $new_pass);
                    $success = "Đổi mật khẩu thành công!";
                    header("refresh:2;url=index.php?ctrl=auth&act=login");
                } else { $error = "Mã OTP sai hoặc đã hết hạn!"; }
            }
        }
        require_once 'app/views/auth/reset_password.php';
    }
}
