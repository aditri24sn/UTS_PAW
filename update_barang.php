<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

require 'db.php';
$error = '';
$product = [];

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "SELECT * FROM produk_electronics WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $product = $result->fetch_assoc();
    $stmt->close();
    
    if (!$product) {
        echo "<div class='error'>ID tidak valid atau barang tidak ditemukan.</div>";
        exit();
    }
} else {
    echo "<div class='error'>ID tidak valid.</div>";
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama_produk = $_POST['nama_produk'];
    $kategori = $_POST['kategori'];
    $harga = $_POST['harga'];
    $stok = $_POST['stok'];
    $merk = $_POST['merk'];
    $garansi = $_POST['garansi'];
    $tanggal_rilis = $_POST['tanggal_rilis'];

    $target_dir = __DIR__ . "/uploads/";
    $image_updated = false;

    if (!empty($_FILES["gambar"]["name"])) {
        $target_file = $target_dir . basename($_FILES["gambar"]["name"]);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        $check = getimagesize($_FILES["gambar"]["tmp_name"]);

        if ($check && $_FILES["gambar"]["size"] <= 50000000 && 
            in_array($imageFileType, ["jpg", "jpeg", "png", "gif","webp"])) {
            if (move_uploaded_file($_FILES["gambar"]["tmp_name"], $target_file)) {
                $image_updated = true; 
            } else {
                $error = "Error uploading file.";
            }
        } else {
            $error = "File tidak valid.";
        }
    }

    if ($image_updated) {
        $nama_file_gambar = basename($_FILES["gambar"]["name"]);
        $sql = "UPDATE produk_electronics SET 
                nama_produk = ?, kategori = ?, harga = ?, stok = ?, merk = ?, garansi = ?, 
                tanggal_rilis = ?, foto_produk = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssiisissi", $nama_produk, $kategori, $harga, $stok, $merk, $garansi, $tanggal_rilis, $nama_file_gambar, $id);
    } else {
        $sql = "UPDATE produk_electronics SET 
                nama_produk = ?, kategori = ?, harga = ?, stok = ?, merk = ?, garansi = ?, 
                tanggal_rilis = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssissssi", $nama_produk, $kategori, $harga, $stok, $merk, $garansi, $tanggal_rilis, $id);
    }

    if ($stmt->execute()) {
        echo "<div class='success'>Barang berhasil diperbarui!</div>";
    } else {
        echo "<div class='error'>Error: " . $stmt->error . "</div>";
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Barang</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 20px;
        }
        h1 {
            text-align: center;
            color: #333;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        label {
            display: block;
            margin: 10px 0 5px;
        }
        input[type="text"],
        input[type="number"],
        input[type="date"],
        input[type="file"] {
            width: 100%;
            padding: 8px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        input[type="submit"] {
            background-color: #007bff;
            color: white;
            padding: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        input[type="submit"]:hover {
            background-color: #0056b3;
        }
        .btn-detail {
            display: inline-block;
            margin-top: 20px;
            color: white;
            background-color: brown;
            padding: 10px 15px;
            text-decoration: none;
            border-radius: 5px;
        }
        .btn-detail:hover {
            background-color: darkred;
        }
        .error {
            background-color: #f8d7da;
            color: #721c24;
            padding: 10px;
            margin: 10px 0;
            border-radius: 5px;
            text-align: center;
        }
        .success {
            background-color: #d4edda;
            color: #155724;
            padding: 10px;
            margin: 10px 0;
            border-radius: 5px;
            text-align: center;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>Update Barang</h1>
    <form action="update_barang.php?id=<?php echo $id; ?>" method="POST" enctype="multipart/form-data">
        <label for="nama_produk">Nama Produk:</label>
        <input type="text" name="nama_produk" id="nama_produk" value="<?php echo htmlspecialchars($product['nama_produk']); ?>" required>

        <label for="kategori">Kategori:</label>
        <input type="text" name="kategori" id="kategori" value="<?php echo htmlspecialchars($product['kategori']); ?>" required>

        <label for="harga">Harga:</label>
        <input type="number" name="harga" id="harga" value="<?php echo htmlspecialchars($product['harga']); ?>" required>

        <label for="stok">Stok:</label>
        <input type="number" name="stok" id="stok" value="<?php echo htmlspecialchars($product['stok']); ?>" required>

        <label for="merk">Merk:</label>
        <input type="text" name="merk" id="merk" value="<?php echo htmlspecialchars($product['merk']); ?>" required>

        <label for="garansi">Garansi (tahun):</label>
        <input type="number" name="garansi" id="garansi" value="<?php echo htmlspecialchars($product['garansi']); ?>" required>

        <label for="tanggal_rilis">Tanggal Rilis:</label>
        <input type="date" name="tanggal_rilis" id="tanggal_rilis" value="<?php echo htmlspecialchars($product['tanggal_rilis']); ?>" required>

        <label for="gambar">Upload Gambar Baru:</label>
        <input type="file" name="gambar" id="gambar"><br> 
        <div class="product-image">
            <h3>Gambar Saat Ini:</h3>
            <?php if (!empty($product['foto_produk'])): ?>
                <img src="uploads/<?php echo $product['foto_produk']; ?>" alt="<?php echo $product['nama_produk']; ?>">
            <?php else: ?>
                <p>Tidak ada gambar untuk produk ini.</p>
            <?php endif; ?>
        </div>
        <input type="submit" value="Update Barang">
    </form>

    <a href="index.php" class='btn-detail'>Kembali ke Home</a>
</div>

</body>
</html>

<?php
$conn->close();
?>
