<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

require 'db.php';

if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    $sql = "DELETE FROM produk_electronics WHERE id = $delete_id";
    
    if ($conn->query($sql) === TRUE) {
        echo "<div class='success'>Item berhasil dihapus.</div>";
    } else {
        echo "<div class='error'>Error: " . $conn->error . "</div>";
    }
}

$sql = "SELECT * FROM produk_electronics";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Halaman Utama</title>
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
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: left;
        }
        th {
            background-color: #007bff;
            color: white;
        }
        td {
            background-color: white;
        }
        a {
            text-decoration: none;
        }
        .btn-update {
            color: white;
            background-color: green;
            padding: 5px 10px;
            border: none;
            border-radius: 3px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .btn-update:hover {
            background-color: darkgreen;
        }
        .btn-delete {
            color: white;
            background-color: red;
            padding: 5px 10px;
            border: none;
            border-radius: 3px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .btn-delete:hover {
            background-color: darkred;
        }
        .btn-detail {
            color: white;
            background-color: blue;
            padding: 5px 10px;
            border: none;
            border-radius: 3px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .btn-detail:hover {
            background-color: darkblue;
        }
        img {
            width: 100px; 
            height: 100px; 
            object-fit: cover; 
        }
        .success, .error {
            text-align: center;
            margin: 10px 0;
            padding: 10px;
            border-radius: 3px;
        }
        .success {
            background-color: #d4edda;
            color: #155724;
        }
        .error {
            background-color: #f8d7da;
            color: #721c24;
        }
        .logout-link, .add-item-link {
            display: inline-block;
            margin: 10px;
            color: white;
            background-color: #007bff;
            padding: 10px 15px;
            border-radius: 5px;
            text-align: center;
            transition: background-color 0.3s;
        }
        .logout-link:hover, .add-item-link:hover {
            background-color: darkblue;
        }
    </style>
</head>
<body>

<h1>Home</h1>

<table>
    <thead>
        <tr>
            <th>Nama Produk</th>
            <th>Kategori</th>
            <th>Gambar</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        <?php
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($row['nama_produk']) . "</td>";
                echo "<td>" . htmlspecialchars($row['kategori']) . "</td>";
                echo "<td>";
                if (!empty($row['foto_produk'])) {
                    echo "<img src='uploads/" . htmlspecialchars($row['foto_produk']) . "' alt='Gambar Produk'>";
                } else {
                    echo "Tidak ada gambar";
                }
                echo "</td>";
                echo "<td>";
                echo "<a href='detail_barang.php?id=" . $row['id'] . "' class='btn-detail'>Detail</a> | ";
                echo "<a href='update_barang.php?id=" . $row['id'] . "' class='btn-update'>Update</a> | ";
                echo "<a href='index.php?delete_id=" . $row['id'] . "' onclick=\"return confirm('Apakah Anda yakin ingin menghapus item ini?')\" class='btn-delete'>Delete</a>";
                echo "</td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='4'>Tidak ada barang.</td></tr>";
        }
        ?>
    </tbody>
</table>

<a href="logout.php" class='logout-link'>Logout</a>
<a href="add_barang.php" class='add-item-link'>Tambah Barang Baru</a>

</body>
</html>

<?php
$conn->close();
?>
