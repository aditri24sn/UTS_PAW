<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

require 'db.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "SELECT * FROM produk_electronics WHERE id=$id";
    $result = $conn->query($sql);
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
    } else {
        echo "<div class='error'>Barang tidak ditemukan.</div>";
    }
} else {
    echo "<div class='error'>ID Barang tidak valid.</div>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Barang</title>
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
            max-width: 800px;
            margin: 0 auto;
            background-color: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        .product-detail {
            margin-bottom: 20px;
        }
        .product-detail p {
            margin: 10px 0;
            color: #555;
        }
        .product-image {
            display: block;
            max-width: 100%;
            height: auto;
            margin: 20px auto;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .btn-detail {
            display: inline-block;
            margin: 20px 0;
            color: white;
            background-color: brown;
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            text-decoration: none;
            transition: background-color 0.3s;
        }
        .btn-detail:hover {
            background-color: darkred;
        }
        .error {
            text-align: center;
            background-color: #f8d7da;
            color: #721c24;
            padding: 10px;
            border-radius: 5px;
            margin: 10px 0;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>Detail Barang</h1>
    <?php if (isset($row)): ?>
        <div class="product-detail">
            <p><strong>Nama Produk:</strong> <?php echo htmlspecialchars($row['nama_produk']); ?></p>
            <p><strong>Kategori:</strong> <?php echo htmlspecialchars($row['kategori']); ?></p>
            <p><strong>Harga:</strong> Rp <?php echo number_format($row['harga'], 2, ',', '.'); ?></p> 
            <p><strong>Stok:</strong> <?php echo htmlspecialchars($row['stok']); ?></p>
            <p><strong>Merk:</strong> <?php echo htmlspecialchars($row['merk']); ?></p>
            <p><strong>Garansi:</strong> <?php echo htmlspecialchars($row['garansi']); ?> tahun</p>
            <p><strong>Tanggal Rilis:</strong> <?php echo htmlspecialchars($row['tanggal_rilis']); ?></p>

            <?php if (!empty($row['foto_produk'])): ?>
                <h2>Gambar Produk:</h2>
                <img src='uploads/<?php echo htmlspecialchars($row['foto_produk']); ?>' alt='<?php echo htmlspecialchars($row['nama_produk']); ?>' class='product-image'>
            <?php else: ?>
                <p>Tidak ada gambar untuk produk ini.</p>
            <?php endif; ?>
        </div>
    <?php endif; ?>
    
    <a href="index.php" class='btn-detail'>Kembali Home</a>
</div>

</body>
</html>

<?php
$conn->close();
?>
