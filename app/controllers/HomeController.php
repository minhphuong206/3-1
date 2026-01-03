<?php
// app/controllers/HomeController.php

require_once __DIR__ . '/../models/ProductModel.php';

class HomeController {
    private $productModel;
    private $db;

    public function __construct() {
        $database = new Database();
        $this->db = $database->connect();
        $this->productModel = new ProductModel($this->db);
    }

    public function index() {
        // 1. Lấy dữ liệu từ Database thông qua Model
        
        // a. Lấy 4 sản phẩm HOT (Nổi bật)
        $featuredProducts = $this->productModel->getProductsByTag('hot', 10);
        
        // b. Lấy 4 Laptop mới nhất (Giả sử id danh mục Laptop là 1)
        $laptopProducts = $this->productModel->getProductsByCategory(2,20);
        $phoneProducts = $this->productModel->getProductsByCategory(1, 8);
        
        // c. Lấy 4 Phụ kiện mới nhất (Giả sử id danh mục Phụ kiện là 3)
        $accessoryProducts = $this->productModel->getProductsByCategory(3, 4);
        //moi
        $allCategoryData = $this->productModel->getProductsBySortedCategories();

        // 2. Gửi dữ liệu sang View
        // Đã sửa lỗi đường dẫn: đi ra 1 cấp (..) rồi vào views/home/
       require_once 'app/views/home/index.php';
    }
}
?>