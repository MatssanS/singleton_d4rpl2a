<?php
require_once 'koneksi.php';

// Definisikan kelas MenuRestoran sebagai kelas singleton untuk menyimpan menu restoran
class MenuRestoran {
    private static $instance = null;
    private $dbConnection;
    private $table = 'menu';

    // Constructor private untuk mencegah instansiasi langsung dari luar kelas
    private function __construct() {
        // Panggil getInstance dari KoneksiDatabase untuk mendapatkan koneksi
        $koneksiDatabase = KoneksiDatabase::getInstance();
        $this->dbConnection = $koneksiDatabase->getConnection();
    }

    // Metode untuk mendapatkan instance tunggal
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new MenuRestoran();
        }
        return self::$instance;
    }

    // Metode untuk mendapatkan menu restoran dari database
    public function getMenu() {
        $query = "SELECT * FROM {$this->table}";
        $statement = $this->dbConnection->prepare($query);
        $statement->execute();
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    // Metode untuk menambahkan item menu baru ke database
    public function tambahMenu($nama, $harga, $deskripsi, $kategori) {
        $query = "INSERT INTO {$this->table} (nama, harga, deskripsi, kategori) VALUES (?, ?, ?, ?)";
        $statement = $this->dbConnection->prepare($query);
        $statement->execute([$nama, $harga, $deskripsi, $kategori]);
        header("Location: {$_SERVER['REQUEST_URI']}");
        exit();
    }

    // Metode untuk menghapus item menu dari database berdasarkan ID
    public function hapusMenu($id) {
        $query = "DELETE FROM {$this->table} WHERE id = ?";
        $statement = $this->dbConnection->prepare($query);
        $statement->execute([$id]);
    }

}

// Deklarasi objek MenuRestoran untuk penggunaan
$menuRestoran = MenuRestoran::getInstance();

// Proses form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama = $_POST['nama'];
    $harga = $_POST['harga'];
    $deskripsi = $_POST['deskripsi'];
    $kategori = $_POST['kategori'];

    // Memanggil metode tambahMenu untuk menambahkan item menu baru ke database
    $menuRestoran->tambahMenu($nama, $harga, $deskripsi, $kategori);
    echo "Menu berhasil ditambahkan ke dalam database!";
}

// Mendapatkan daftar menu dari database
$dataMenu = $menuRestoran->getMenu();

// Proses penghapusan menu
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['hapus'])) {
    $id_menu = $_GET['hapus'];
    $menuRestoran->hapusMenu($id_menu);
    // Refresh halaman setelah menghapus menu
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="gaya.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <title>Menu Restoran</title>
</head>
<body>
    <div class="container">
        <h2>Menu Restoran</h2>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
            <label for="nama">Nama Menu:</label>
            <input type="text" id="nama" name="nama" required><br>

            <label for="harga">Harga:</label>
            <input type="number" id="harga" name="harga" required><br>

            <label for="deskripsi">Deskripsi:</label>
            <textarea id="deskripsi" name="deskripsi" required></textarea><br>

            <label for="kategori">Kategori:</label>
            <select id="kategori" name="kategori" required>
                <option value="Makanan">Makanan</option>
                <option value="Minuman">Minuman</option>
                <option value="Pencuci Mulut">Pencuci Mulut</option>
            </select><br>

            <input type="submit" value="Tambahkan Menu">
        </form>

        <h2>Daftar Menu Restoran</h2>
        <table>
            <tr>
                <th>Nama Menu</th>
                <th>Harga</th>
                <th>Deskripsi</th>
                <th>Kategori</th>
                <th>Hapus</th>
            </tr>
            <?php foreach ($dataMenu as $menu) : ?>
                <tr>
                    <td><?php echo $menu['nama']; ?></td>
                    <td><?php echo $menu['harga']; ?></td>
                    <td><?php echo $menu['deskripsi']; ?></td>
                    <td><?php echo $menu['kategori']; ?></td>
                    <td><a href="?hapus=<?php echo $menu['id']; ?>"><i class="bi bi-trash3-fill"></i></a></td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>
</body>
</html>
