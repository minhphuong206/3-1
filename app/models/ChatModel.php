<?php
// app/models/ChatModel.php
class ChatModel {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    // 1. Lưu chat (ĐÃ SỬA: Dùng đúng tên cột 'tin_nhan_khach', 'phan_hoi_bot')
    public function saveChat($userId, $question, $reply) {
        $sql = "INSERT INTO lichsuchat (ma_khach_hang, tin_nhan_khach, phan_hoi_bot) VALUES (?, ?, ?)";
        return $this->db->prepare($sql)->execute([$userId, $question, $reply]);
    }

    // 2. Lấy tất cả cho Admin (ĐÃ SỬA: Đổi tên hiển thị cho khớp với code cũ của Admin)
    public function getAllChats() {
        $sql = "SELECT ls.ma_chat, ls.ma_khach_hang, ls.thoi_gian,
                       ls.tin_nhan_khach AS cau_hoi, 
                       ls.phan_hoi_bot AS tra_loi,
                       kh.ho_ten, kh.email 
                FROM lichsuchat ls
                LEFT JOIN khachhang kh ON ls.ma_khach_hang = kh.ma_khach_hang
                ORDER BY ls.thoi_gian DESC
                LIMIT 5"; // <--- CHỈ LẤY 10 DÒNG
        return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    // 3. Lấy chi tiết
    public function getChatById($id) {
        $sql = "SELECT ma_chat, ma_khach_hang, thoi_gian,
                       tin_nhan_khach AS cau_hoi, 
                       phan_hoi_bot AS tra_loi 
                FROM lichsuchat WHERE ma_chat = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // 4. Update (ĐÃ SỬA: Cập nhật đúng cột 'phan_hoi_bot')
    public function updateChatResponse($id, $newResponse) {
        $sql = "UPDATE lichsuchat SET phan_hoi_bot = ? WHERE ma_chat = ?";
        return $this->db->prepare($sql)->execute([$newResponse, $id]);
    }

    // 5. Xóa
    public function deleteChat($id) {
        return $this->db->prepare("DELETE FROM lichsuchat WHERE ma_chat = ?")->execute([$id]);
    }

    // 6. Lấy lịch sử (ĐÃ SỬA)
    public function getHistory($userId) {
        $stmt = $this->db->prepare("SELECT ma_chat, tin_nhan_khach AS cau_hoi, phan_hoi_bot AS tra_loi, thoi_gian 
                                    FROM lichsuchat 
                                    WHERE ma_khach_hang = ? 
                                    ORDER BY thoi_gian ASC");
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    // ... (Các hàm cũ giữ nguyên) ...

    // ====================================================
    // PHẦN QUẢN LÝ HUẤN LUYỆN BOT (dulieuhuanluyen)
    // ====================================================

    // 7. Lấy tất cả dữ liệu huấn luyện
    public function getAllTrainingData() {
        return $this->db->query("SELECT * FROM dulieuhuanluyen ORDER BY ma_du_lieu DESC")->fetchAll(PDO::FETCH_ASSOC);
    }

    // 8. Thêm câu huấn luyện mới
    public function addTrainingData($question, $reply) {
        $sql = "INSERT INTO dulieuhuanluyen (cau_hoi, tra_loi) VALUES (?, ?)";
        return $this->db->prepare($sql)->execute([$question, $reply]);
    }

    // 9. Cập nhật câu huấn luyện
    public function updateTrainingData($id, $question, $reply) {
        $sql = "UPDATE dulieuhuanluyen SET cau_hoi = ?, tra_loi = ? WHERE ma_du_lieu = ?";
        return $this->db->prepare($sql)->execute([$question, $reply, $id]);
    }

    // 10. Xóa câu huấn luyện
    public function deleteTrainingData($id) {
        return $this->db->prepare("DELETE FROM dulieuhuanluyen WHERE ma_du_lieu = ?")->execute([$id]);
    }
} // Kết thúc class

?>