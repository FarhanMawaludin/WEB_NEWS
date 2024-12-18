<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit;
}

$collection = $db->categories;

// Tambah Kategori
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_category'])) {
    $name = trim($_POST['name']);
    if (!empty($name)) {
        $collection->insertOne(['name' => $name]);
        header('Location: manage_categories.php');
        exit;
    }
}

// Hapus Kategori
if (isset($_GET['delete_id'])) {
    $deleteId = new MongoDB\BSON\ObjectId($_GET['delete_id']);
    $collection->deleteOne(['_id' => $deleteId]);
    header('Location: manage_categories.php');
    exit;
}

// menampilkan semua kategori
$categoriesCursor = $collection->find([], ['sort' => ['name' => 1]]);
$categories = iterator_to_array($categoriesCursor);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Kategori Berita</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<nav class="navbar navbar-expand-lg navbar-light bg-light shadow-sm">
    <div class="container-fluid">
        <a class="navbar-brand fw-bold" href="manage_news.php">Admin <span class="text-danger">Polinews</span></a>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link" href="manage_news.php">Kelola Berita</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" href="manage_category.php">Kelola Kategori</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="logout.php">
                        <button class="btn btn-danger btn-sm">Logout</button>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="container mt-4">
    <h1>Kelola Kategori Berita</h1>

    <!-- Form Tambah Kategori -->
    <form method="POST" class="mb-4">
        <div class="input-group">
            <input type="text" name="name" class="form-control" placeholder="Nama Kategori" required>
            <button class="btn btn-primary" type="submit" name="add_category">Tambah</button>
        </div>
    </form>

    <!-- Tabel Kategori -->
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Kategori</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php $no = 1; ?>
            <?php foreach ($categories as $category): ?>
            <tr>
                <td><?= $no++ ?></td>
                <td><?= $category['name'] ?></td>
                <td>
                    <a href="edit_category.php?id=<?= $category['_id'] ?>" class="btn btn-warning btn-sm">Edit</a>
                    <a href="?delete_id=<?= $category['_id'] ?>" class="btn btn-danger btn-sm"
                        onclick="return confirm('Yakin ingin menghapus kategori ini?');">Hapus</a>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php if (count($categories) === 0): ?>
            <tr>
                <td colspan="3" class="text-center">Tidak ada kategori tersedia.</td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>