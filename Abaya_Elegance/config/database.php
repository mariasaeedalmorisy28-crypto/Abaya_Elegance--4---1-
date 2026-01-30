<?php
class Database {
    private $host = "localhost";
    private $db_name = "abaya_elegance_db"; // اسم قاعدة البيانات
    private $username = "root";
    private $password = "";
    public $conn;

    // --- إضافة الدومين هنا ---
   
    public $domain = "http://localhost/project_ABAYA4/"; 

    public function getConnection() {
        $this->conn = null;

        try {
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->username, $this->password);
            // دعم اللغة العربية
            $this->conn->exec("set names utf8mb4");
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $exception) {
            echo "خطأ في الاتصال: " . $exception->getMessage();
        }

        return $this->conn;
    }
}
?>