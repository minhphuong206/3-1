<?php
// app/controllers/AdminAuthController.php
require_once 'app/config/db.php';
require_once 'app/models/AdminModel.php';

class AdminAuthController {
    private $adminModel;
    private $db;

    public function __construct() {
        $database = new Database();
        $this->db = $database->connect();
        $this->adminModel = new AdminModel($this->db);
    }

    public function login() {
        if (isset($_SESSION['admin_id'])) {
            header("Location: index.php?ctrl=admin&act=dashboard");
            exit;
        }

        $error = '';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = trim($_POST['username']);
            $password = $_POST['password'];
            $result = $this->adminModel->checkAdminLogin($username, $password);

            if ($result === true) {
                header("Location: index.php?ctrl=admin&act=dashboard"); 
                exit;
            } else {
                $error = $result;
            }
        }
        require_once 'app/views/admin/login.php';
    }

    public function logout() {
        unset($_SESSION['admin_id']);
        unset($_SESSION['admin_user']);
        unset($_SESSION['admin_name']);
        unset($_SESSION['admin_role']);
       header("Location: index.php"); 
        exit;
    }
}