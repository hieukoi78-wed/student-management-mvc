<?php
// app/models/UserModel.php
require_once __DIR__ . '/../../config/DataBase.php';
class UserModel {
    public $username;
    public $password;
    public $role;
    private $db;
    public function __construct(DataBase $db) {
        $this->db = $db;
    }
    public function authenticate($username, $password) {
        $pdo = $this->db->getConnection();
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username LIMIT 1");
        $stmt->bindValue(':username', $username, PDO::PARAM_STR);
        $stmt->execute();
        $user = $stmt->fetch();
        // So sánh trực tiếp chuỗi mật khẩu dạng chữ thuần
        if ($user && $password === $user['password']) {
            return $user;
        }
        return false;
    }
}