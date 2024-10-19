<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

require 'db.php';

$error = '';
$success = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama_produk = $_POST['nama_produk'];
    $kategori = $_POST['kategori'];
    $harga = $_POST['harga'];
    $stok = $_POST['stok'];
    $merk = $_POST['merk'];
    $garansi = $_POST['garansi'];
    $tanggal_rilis = $_POST['tanggal_rilis'];

    $sql = "INSERT INTO produk_electronics (nama_produk, kategori, harga, stok, merk, garansi, tanggal_rilis)
            VALUES (?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssiiiss", $nama_produk, $kategori, $harga, $stok, $merk, $garansi, $tanggal_rilis);

    if ($stmt->execute()) {
        $produk_id = $conn->insert_id; 
        if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] == 0) {
            $file_name = basename($_FILES["gambar"]["name"]);
            $target_dir = "uploads/";
            $target_file = $target_dir . $file_name;

         
            $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
            $check = getimagesize($_FILES["gambar"]["tmp_name"]);

            if ($check && $_FILES["gambar"]["size"] <= 50000000 && 
                in_array($imageFileType, ["jpg", "jpeg", "png", "gif"])) {
                if (move_uploaded_file($_FILES["gambar"]["tmp_name"], $target_file)) {
                    $sql_gambar = "UPDATE produk_electronics SET foto_produk = ? WHERE id = ?";
                    $stmt = $conn->prepare($sql_gambar);
                    $stmt->bind_param("si", $file_name, $produk_id);
                    $stmt->execute();
                    $success = "Barang dan gambar berhasil ditambahkan!";
                } else {
                    $error = "Gambar tidak berhasil di-upload.";
                }
            } else {
                $error = "File gambar tidak valid.";
            }
        } else {
            $success = "Barang berhasil ditambahkan! Tidak ada gambar yang di-upload.";
        }
    } else {
        $error = "Error: " . $stmt->error;
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Barang</title>
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
        .btn-back {
            display: inline-block;
            margin-top: 20px;
            color: white;
            background-color: brown;
            padding: 10px 15px;
            text-decoration: none;
            border-radius: 5px;
        }
        .btn-back:hover {
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
    <h1>Tambah Barang</h1>
    <?php if ($error): ?>
        <div class="error"><?php echo $error; ?></div>
    <?php endif; ?>
    <?php if ($success): ?>
        <div class="success"><?php echo $success; ?></div>
    <?php endif; ?>
    
    <form action="add_barang.php" method="POST" enctype="multipart/form-data">
        <label for="nama_produk">Nama Produk:</label>
        <input type="text" name="nama_produk" id="nama_produk" required>

        <label for="kategori">Kategori:</label>
        <input type="text" name="kategori" id="kategori" required>

        <label for="harga">Harga:</label>
        <input type="number" name="harga" id="harga" required>

        <label for="stok">Stok:</label>
        <input type="number" name="stok" id="stok" required>

        <label for="merk">Merk:</label>
        <input type="text" name="merk" id="merk" required>

        <label for="garansi">Garansi (tahun):</label>
        <input type="number" name="garansi" id="garansi" required>

        <label for="tanggal_rilis">Tanggal Rilis:</label>
        <input type="date" name="tanggal_rilis" id="tanggal_rilis" required>

        <label for="gambar">Upload Gambar:</label>
        <input type="file" name="gambar" id="gambar" required>

        <input type="submit" value="Tambah Barang">
    </form>

    <a href="index.php" class="btn-back">Kembali ke Home</a>
</div>

</body>
</html>

<?php
$conn->close();
?>
