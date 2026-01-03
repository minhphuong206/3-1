<?php
// debug_momo.php - Đặt trong thư mục gốc
require_once 'app/core/MomoPayment.php';

// Bật hiển thị lỗi
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Debug MoMo Payment</h1>";

// Test 1: Kiểm tra cURL
echo "<h2>Test 1: Kiểm tra cURL</h2>";
if (function_exists('curl_version')) {
    $curl_info = curl_version();
    echo "<p style='color:green;'>✓ cURL enabled: " . $curl_info['version'] . "</p>";
    echo "<p>SSL: " . ($curl_info['features'] & CURL_VERSION_SSL ? 'Có' : 'Không') . "</p>";
} else {
    echo "<p style='color:red;'>✗ cURL không được enable</p>";
}

// Test 2: Kiểm tra kết nối đến MoMo endpoint
echo "<h2>Test 2: Kiểm tra kết nối đến MoMo</h2>";
$test_url = "https://test-payment.momo.vn";
$ch = curl_init($test_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 5);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
$result = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

if ($result !== false) {
    echo "<p style='color:green;'>✓ Có thể kết nối đến MoMo</p>";
    echo "<p>HTTP Code: $http_code</p>";
} else {
    echo "<p style='color:red;'>✗ Không thể kết nối đến MoMo: $error</p>";
}

// Test 3: Kiểm tra base URL
echo "<h2>Test 3: Kiểm tra Base URL</h2>";
$protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? "https://" : "http://";
$host = $_SERVER['HTTP_HOST'];
$scriptDir = dirname($_SERVER['SCRIPT_NAME']);
$basePath = rtrim($scriptDir, '/');
if ($basePath && $basePath !== '/' && $basePath !== '\\') {
    $baseUrl = $protocol . $host . $basePath . '/';
} else {
    $baseUrl = $protocol . $host . '/';
}
echo "<p>Current URL: " . $protocol . $host . $_SERVER['REQUEST_URI'] . "</p>";
echo "<p>Base URL: $baseUrl</p>";

if (strpos($baseUrl, 'localhost') !== false || strpos($baseUrl, '127.0.0.1') !== false) {
    echo "<p style='color:red;'>⚠️ ĐANG CHẠY TRÊN LOCALHOST - MoMo không hỗ trợ!</p>";
    echo "<p>Giải pháp: Dùng ngrok hoặc deploy lên server public</p>";
    echo "<p>Ngrok command: <code>ngrok http 80</code></p>";
}

// Test 4: Kiểm tra tạo payment
echo "<h2>Test 4: Thử tạo payment với MoMo</h2>";
echo "<form method='post' style='background:#f5f5f5; padding:15px; border-radius:5px;'>
    <label>Số tiền: <input type='number' name='amount' value='10000' min='10000'></label><br><br>
    <button type='submit' name='test_payment'>Test Payment</button>
</form>";

if (isset($_POST['test_payment'])) {
    $amount = intval($_POST['amount']);
    if ($amount < 10000) $amount = 10000;
    
    $orderId = 'TEST' . date('YmdHis') . rand(100, 999);
    
    echo "<h3>Kết quả test:</h3>";
    echo "<p>Order ID: $orderId</p>";
    echo "<p>Amount: " . number_format($amount) . " VND</p>";
    
    $momo = new MomoPayment();
    $result = $momo->createPayment($orderId, $amount);
    
    echo "<pre style='background:#000; color:#0f0; padding:10px; border-radius:5px;'>";
    print_r($result);
    echo "</pre>";
    
    if ($result['success']) {
        echo "<p><a href='{$result['payUrl']}' target='_blank' style='background:#a50064; color:white; padding:10px 20px; text-decoration:none; border-radius:5px;'>Mở trang thanh toán</a></p>";
        if ($result['qrCodeUrl']) {
            echo "<p><img src='{$result['qrCodeUrl']}' alt='QR Code' style='border:1px solid #ccc; padding:10px;'></p>";
        }
    } else {
        echo "<p style='color:red;'>Lỗi: {$result['message']}</p>";
    }
}

// Test 5: Hiển thị thông tin server
echo "<h2>Test 5: Thông tin Server</h2>";
echo "<pre style='background:#333; color:#fff; padding:10px;'>";
echo "PHP Version: " . phpversion() . "\n";
echo "Server: " . $_SERVER['SERVER_SOFTWARE'] . "\n";
echo "Allow URL fopen: " . (ini_get('allow_url_fopen') ? 'Yes' : 'No') . "\n";
echo "OpenSSL: " . (extension_loaded('openssl') ? 'Enabled' : 'Disabled') . "\n";
echo "Các extensions: " . implode(', ', get_loaded_extensions()) . "\n";
echo "</pre>";

echo "<hr>";
echo "<h2>Hướng dẫn sử dụng ngrok (nếu đang chạy localhost)</h2>";
echo "<ol>
    <li>Tải ngrok từ <a href='https://ngrok.com/download' target='_blank'>ngrok.com</a></li>
    <li>Đăng ký tài khoản miễn phí</li>
    <li>Lấy auth token từ dashboard ngrok</li>
    <li>Chạy: <code>ngrok authtoken YOUR_TOKEN</code></li>
    <li>Chạy: <code>ngrok http 80</code></li>
    <li>Dùng URL mà ngrok cung cấp (ví dụ: https://abc123.ngrok.io)</li>
    <li>Truy cập: https://abc123.ngrok.io/your-project/index.php</li>
</ol>";
?>