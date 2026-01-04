<?php
// app/controllers/ChatController.php
require_once 'app/models/ProductModel.php';
require_once 'app/models/ChatModel.php';

class ChatController {
    private $productModel;
    private $chatModel;
    private $db;

    public function __construct() {
        $database = new Database();
        $this->db = $database->connect();
        $this->productModel = new ProductModel($this->db);
        $this->chatModel = new ChatModel($this->db);
    }

    public function handle_chat() {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $input = json_decode(file_get_contents('php://input'), true);
            $message = trim($input['message'] ?? '');

            if (empty($message)) {
                echo json_encode(['reply' => 'Bạn cần tìm sản phẩm nào ạ?']);
                exit;
            }

            // =================================================================
            // 1. KỸ THUẬT "BẮT KEY" (RULE-BASED)
            // =================================================================
            $brandKeywords = ['Samsung', 'iPhone', 'Apple', 'Dell', 'HP', 'Asus', 'Acer', 'Lenovo', 'MSI', 'MacBook', 'iPad', 'Oppo', 'Xiaomi'];
            $stopWords = ['tôi', 'muốn', 'mua', 'cần', 'tìm', 'máy', 'điện thoại', 'laptop', 'giá', 'bao nhiêu', 'shop', 'ơi', 'với', 'có', 'không', 'nào', 'cấu hình', 'thông số', 'về', 'các', 'hãng', 'thương hiệu'];

            $searchKey = $message; 
            $foundBrand = false;

            // A. Ưu tiên 1: Quét xem có tên Hãng trong câu không?
            foreach ($brandKeywords as $brand) {
                if (stripos($message, $brand) !== false) {
                    $searchKey = $brand; 
                    $foundBrand = true;
                    break; 
                }
            }

            // B. Ưu tiên 2: Nếu không có tên hãng, kiểm tra hỏi danh sách
            if (!$foundBrand) {
                $genericKeywords = ['điện thoại', 'laptop', 'hãng', 'thương hiệu', 'mua máy', 'sản phẩm'];
                foreach ($genericKeywords as $geo) {
                    if (stripos($message, $geo) !== false) {
                        $brands = $this->productModel->getAllBrands();
                        if (!empty($brands)) {
                            $brandHtml = "<div style='display:flex; flex-wrap:wrap; gap:5px; margin-top:5px;'>";
                            foreach ($brands as $b) {
                                $brandHtml .= "<span onclick=\"document.getElementById('chat-input').value='{$b['ten_thuong_hieu']}'; sendMessage();\" style='background:#f1f1f1; border:1px solid #ccc; padding:5px 10px; border-radius:15px; cursor:pointer; font-size:12px; color:#333; transition:0.2s;'>{$b['ten_thuong_hieu']}</span>";
                            }
                            $brandHtml .= "</div>";

                            // Lưu tin nhắn này vào lịch sử nếu đã đăng nhập
                            if (isset($_SESSION['user_id'])) {
                                $this->chatModel->saveChat($_SESSION['user_id'], $message, "Danh sách thương hiệu");
                            }

                            echo json_encode(['reply' => "Dạ, PhươngStore hiện đang có các hãng sau:<br>" . $brandHtml, 'type' => 'text']);
                            exit;
                        }
                    }
                }
                $searchKey = str_ireplace($stopWords, '', $message);
                $searchKey = trim($searchKey);
            }

            // =================================================================
            // 2. TRUY VẤN DATABASE
            // =================================================================
            $products = $this->productModel->searchProducts($searchKey);
            $reply = "";
            $imgUrl = null;

            if (!empty($products)) {
                $p = $products[0];
                $giaHienTai = $p['gia_ban'];
                $mucGiam = isset($p['muc_giam_gia']) ? intval($p['muc_giam_gia']) : 0;
                $priceHtml = ($mucGiam > 0) ? number_format($giaHienTai) . "đ <span style='color:red;font-size:10px'>-$mucGiam%</span>" : number_format($giaHienTai) . "đ";
                
                $imgUrl = (strpos($p['url_anh'], 'http') === 0) ? $p['url_anh'] : "public/images/" . $p['url_anh'];
                
                $reply = "Dạ có <b>{$p['ten_san_pham']}</b> nè.<br>Giá: <b>{$priceHtml}</b>.<br><a href='index.php?ctrl=product&act=detail&id={$p['ma_san_pham']}' style='color:#d70018'>Xem chi tiết</a>";
            } else {
                // Kiểm tra câu hỏi xã giao
                $stmt = $this->db->prepare("SELECT tra_loi FROM dulieuhuanluyen WHERE cau_hoi LIKE ? LIMIT 1");
                $stmt->execute(["%" . $message . "%"]);
                $trainingData = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($trainingData) {
                    $reply = $trainingData['tra_loi'];
                } else {
                    $reply = "Huhu, em tìm không thấy sản phẩm <b>'$message'</b>. Anh/chị thử tên hãng (Samsung, Dell...) xem sao ạ!";
                }
            }

            // =================================================================
            // 3. LƯU VÀO DATABASE (ĐÃ SỬA SESSION USER_ID)
            // =================================================================
            if (isset($_SESSION['user_id'])) {
                $userId = $_SESSION['user_id']; 
                $this->chatModel->saveChat($userId, $message, $reply);
            }

            echo json_encode([
                'reply' => $reply,
                'image' => $imgUrl,
                'type'  => 'text'
            ]);
        }
    }

    public function load_history() {
        header('Content-Type: application/json');
        if (isset($_SESSION['user_id'])) { 
            $userId = $_SESSION['user_id'];
            $history = $this->chatModel->getHistory($userId);
            echo json_encode(['status' => 'success', 'data' => $history]);
        } else {
            echo json_encode(['status' => 'guest', 'data' => []]);
        }
        exit;
    }
}
?>