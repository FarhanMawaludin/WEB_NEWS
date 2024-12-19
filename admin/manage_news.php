<?php
    session_start();
    require '../config/db.php';

    if (!isset($_SESSION['username'])) {
        header('Location: login.php');
        exit;
    }

    $collection = $db->news;

    // Ambil semua berita dari database
    $newsCursor = $collection->find([], ['sort' => ['created_at' => -1]]);

    // Ubah cursor menjadi array
    $newsList = iterator_to_array($newsCursor);
?>

<!DOCTYPE html>
<html>

<head>
    <title>Kelola Berita</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light shadow-sm">
        <div class="container-fluid">
            <a class="navbar-brand fw-bold" href="manage_news.php">Admin <span class="text-danger">Polinews</span></a>
            <!-- Tombol Menu Burger -->
            <button class="navbar-toggler ms-auto" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <!-- Menu -->
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="manage_news.php">Kelola Berita</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="add_news.php">Tambah Berita</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="manage_category.php">Kelola Kategori</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">
                            <button class="btn btn-danger btn-sm">
                                Logout
                            </button>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>


    <!-- Daftar Berita -->
    <div class="container mt-4">
        <h1 class="mb-4">Daftar Berita</h1>
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Judul</th>
                    <th>Kategori</th>
                    <th>Penulis</th>
                    <th>Tanggal</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php $no = 1; ?>
                <?php foreach ($newsList as $news): ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= $news['title'] ?></td>
                    <td><?= $news['category'] ?></td>
                    <td><?= $news['author'] ?></td>
                    <td><?= $news['created_at']->toDateTime()->format('Y-m-d H:i:s') ?></td>
                    <td>
                        <a href="edit_news.php?id=<?= $news['_id'] ?>" class="btn btn-warning btn-sm">Edit</a>
                        <a href="delete_news.php?id=<?= $news['_id'] ?>" class="btn btn-danger btn-sm"
                            onclick="return confirm('Apakah Anda yakin ingin menghapus berita ini?');">Hapus</a>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if (count($newsList) == 0): ?>
                <tr>
                    <td colspan="6" class="text-center">Tidak ada berita yang tersedia.</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>