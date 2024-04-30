<?php 
class KoneksiDatabase {
    private static $instance = null;
    private $dbConnection;

    // Constructor private untuk mencegah instansiasi langsung dari luar kelas
    private function __construct() {
        $host = 'localhost';
        $dbname = 'db_resto';
        $username = 'root';
        $password = '';

        try {
            $this->dbConnection = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
            $this->dbConnection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $e) {
            echo "Koneksi database gagal: " . $e->getMessage();
        }
    }

    // Metode untuk mendapatkan instance tunggal
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new KoneksiDatabase();
        }
        return self::$instance;
    }

    // Metode untuk mendapatkan objek koneksi database
    public function getConnection() {
        return $this->dbConnection;
    }
}
