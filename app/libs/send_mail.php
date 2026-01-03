<?php
// app/libs/send_mail.php

// 1. IMPORT CÁC CLASS CỦA PHPMAILER
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// 2. CHỈNH SỬA ĐƯỜNG DẪN (Dùng __DIR__ để định vị chính xác)
// __DIR__ đại diện cho thư mục chứa file này (app/libs)
require __DIR__ . '/phpmailer/Exception.php';
require __DIR__ . '/phpmailer/PHPMailer.php';
require __DIR__ . '/phpmailer/SMTP.php';

// 3. HÀM GỬI MAIL
function sendEmail($to, $subject, $content) {
    $mail = new PHPMailer(true);

    try {
        // --- CẤU HÌNH SERVER ---
        // $mail->SMTPDebug = SMTP::DEBUG_SERVER;   // Bật dòng này nếu muốn xem lỗi chi tiết (Dev mode)
        $mail->SMTPDebug = 0;                       // Tắt debug để giao diện web sạch sẽ (Production mode)
        $mail->isSMTP();                            
        $mail->Host       = 'smtp.gmail.com';       
        $mail->SMTPAuth   = true;                   
        
        // ⚠️ THAY THÔNG TIN CỦA BẠN VÀO ĐÂY
        $mail->Username   = 'pyd6863@gmail.com';  // Email Gmail của bạn
        $mail->Password   = 'lonb tfys auyw ekey';        // Mật khẩu ứng dụng (App Password), KHÔNG PHẢI mật khẩu đăng nhập Gmail
        
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS; // Dùng SSL/TLS
        $mail->Port       = 465;                         // Port chuẩn của Gmail SSL

        // --- NGƯỜI GỬI & NGƯỜI NHẬN ---
        $mail->setFrom('pyd6863@gmail.com', 'PhuongStore'); // Tên hiển thị khi gửi
        $mail->addAddress($to);                                   // Gửi tới email khách hàng

        // --- NỘI DUNG ---
        $mail->isHTML(true);                                  
        $mail->Subject = $subject;
        $mail->Body    = $content;
        $mail->CharSet = 'UTF-8'; // Quan trọng: Để không bị lỗi font tiếng Việt

        $mail->send();
        return true; // Gửi thành công

    } catch (Exception $e) {
        // Ghi log lỗi vào file error_log của server (nếu cần)
        error_log("Mail Error: {$mail->ErrorInfo}");
        return false; // Gửi thất bại
    }
}
?>