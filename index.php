<?php
require 'config/db.php';

$searchQuery = isset($_GET['search']) ? trim($_GET['search']) : "";
$categoryFilter = isset($_GET['category']) ? $_GET['category'] : "";
$collection = $db->news;

// Ambil kategori unik dari database
$categories = $collection->distinct('category');

// Query berita
if ($categoryFilter) {
    $cursor = $collection->find(
        ['category' => $categoryFilter],
        ['sort' => ['created_at' => -1]]
    );
} elseif ($searchQuery) {
    $cursor = $collection->find(
        [
            '$or' => [
                ['title' => new MongoDB\BSON\Regex($searchQuery, 'i')],
                ['content' => new MongoDB\BSON\Regex($searchQuery, 'i')]
            ]
        ],
        ['sort' => ['created_at' => -1]]
    );
} else {
    $cursor = $collection->find([], ['sort' => ['created_at' => -1]]);
}

$newsList = iterator_to_array($cursor);

?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PoliNews</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css"
        integrity="sha384-tViUnnbYAV00FLIhhi3v/dWt3Jxw4gZQcNoSCxCIFNJVCx7/D55/wXsrNIRANwdD" crossorigin="anonymous">
    <style>
    html,
    body {
        height: 100%;
        margin: 0;
        padding: 0;
    }

    body {
        display: flex;
        flex-direction: column;
        min-height: 100vh;
    }

    .footer {
        margin-top: auto;
    }

    .container {
        flex-grow: 1;
    }


    .custom-container {
        max-width: 100%;
        margin: 0 auto;
        padding: 0 200px;
    }

    .card-text-custom {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .featured-card {
        position: relative;
        overflow: hidden;
        border-radius: 10px;
    }

    .featured-card img {
        filter: brightness(70%);
        width: 100%;
        height: auto;
    }

    .featured-card .card-img-overlay {
        background: rgba(0, 0, 0, 0.6);
        color: white;
    }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-md navbar-light bg-light shadow-sm sticky-top">
        <div class="container custom-container">
            <a class="navbar-brand fw-bold text-danger" href="index.php" style="font-size: 36px;">PoliNews</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNavDropdown">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item"><a class="nav-link" href="index.php">Beranda</a></li>
                    <!-- <li class="nav-item"><a class="nav-link" href="#">All</a></li> -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">Kategori</a>
                        <div class="dropdown-menu">
                            <a class="dropdown-item" href="index.php?category=politik">Politik</a>
                            <a class="dropdown-item" href="index.php?category=bencana">Bencana</a>
                            <a class="dropdown-item" href="index.php?category=lalu-lintas">Lalu Lintas</a>
                            <a class="dropdown-item" href="index.php?category=pendidikan">Pendidikan</a>
                        </div>
                    </li>
                </ul>
                <form class="d-flex" method="get" action="index.php">
                    <input class="form-control me-2" type="search" name="search" placeholder="Search"
                        value="<?= htmlspecialchars($searchQuery) ?>">
                    <button class="btn btn-outline-danger me-2" type="submit">Search</button>
                </form>
                <a href="admin/login.php">
                    <button class="btn btn-danger me-2" type="button">Login</button>
                </a>
            </div>
        </div>
    </nav>

    <div class="container custom-container mt-3">
        <div class="jumbotron jumbotron-fluid text-center py-4"
            style="background: rgba(234, 234, 234, 0.5); border-radius: 10px;">
            <div class="judul fw-bold" style="font-size: 20px;">Selamat Datang, di <span class="text-danger">PoliNews
            </div>
            <div class="judul fw-bold" style="font-size: 32px;">Sumber Berita Terpercaya, Aktual, dan Berimbang</div>
            <div class="judul text-danger fw-semibold text-danger">Tetap terhubung dengan informasi terkini, inspirasi,
                dan analisis mendalam di POLINEMA</div>
        </div>

        <div class="row mt-4">

            <?php if (count($newsList) > 0 && !$searchQuery && !$categoryFilter): ?>
            <!-- Kiri: Berita Utama -->
            <div class="col-md-6">
                <div class=" text-white">
                    <img src="<?= isset($newsList[0]['image']) ? 'images/' . $newsList[0]['image'] : '' ?>"
                        class="card-img" alt="Featured News Image" style="border-radius: 8px;">
                </div>
            </div>

            <!-- Kanan: Daftar Berita Lainnya -->
            <div class="col-md-6 pt-2 d-flex flex-column align-items-start">
                <div class="d-flex align-items-center mb-3">
                    <i class="fas fa-user-circle me-2 fs-2" style="font-size: 40px; color: #6c757d;"></i>
                    <span class="fw-bold"><?= $newsList[0]['author'] ?? 'Unknown Author' ?></span>
                    <span class="mx-3">·</span>
                    <span><?= $newsList[0]['created_at']->toDateTime()->format('Y-m-d H:i:s') ?></span>
                </div>
                <h2 class="card-title fw-bold my-3"><?= $newsList[0]['title'] ?? '' ?> </h2>
                <p class="card-text" style="text-align: justify;">
                    <?= $newsList[0]['summary'] ?? '' ?>
                </p>
                <a href="detail.php?id=<?= $newsList[0]['_id'] ?>" class="btn btn-danger mt-3">Selengkapnya</a>
                <?php endif; ?>
            </div>



            <div class=" mt-4">
                <?php if ($searchQuery || $categoryFilter): ?>
                <h5>Hasil pencarian untuk: <strong><?= htmlspecialchars($searchQuery) ?></strong></h5>
                <?php if (empty($newsList)): ?>
                <p class="text-muted">Tidak ada hasil yang ditemukan untuk pencarian Anda.</p>
                <?php else: ?>
                <div class="row mt-4">
                    <?php foreach ($newsList as $news): ?>
                    <div class="col-md-3 mb-4">
                        <div class="card">
                            <img src="<?= isset($news['image']) ? 'images/' . $news['image'] : 'https://placehold.co/300x200' ?>"
                                class="card-img-top" height="240rem" style="object-fit: cover;" alt="News Image">
                            <div class="card-body">
                                <h5 class="card-title card-text-custom fw-semibold"><?= $news['title'] ?></h5>
                                <p class="card-text card-text-custom"><?= $news['summary'] ?></p>
                                <a href="detail.php?id=<?= $news['_id'] ?>" class="btn btn-danger">Selengkapnya</a>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
            <?php endif; ?>

        </div>


        <div class="row">
            <?php if (count($newsList) > 0 && !$searchQuery && !$categoryFilter): ?>
            <!-- Cek apakah tidak ada query pencarian -->
            <div class="row mb-3 justify-content-between">
                <div class="col-6">
                    <h4 class="fw-semibold">Berita Lainnya</h4>
                </div>
                <div class="col-6 text-end">
                    <a class="text-decoration-none text-danger" href="see_all.php">
                        See all
                        <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>

            <?php foreach (array_slice($newsList, 1, 4) as $news): ?>
            <div class="col-md-3 mb-4">
                <div class="card ">
                    <a href="detail.php?id=<?= $news['_id'] ?>" class="text-decoration-none text-black">
                        <img src="<?= isset($news['image']) ? 'images/' . $news['image'] : 'https://placehold.co/300x200' ?>"
                            class="card-img-top" height="200rem" style="object-fit: cover;" alt=" News Image">
                        <div class="card-body">
                            <span class="badge bg-danger mb-2"><?= htmlspecialchars($news['category']) ?></span>
                            <h5 class="card-title card-text-custom fw-semibold"><?= $news['title'] ?></h5>
                            <p class="card-text card-text-custom"><?= $news['summary'] ?></p>
                            <!-- <a href="detail.php?id=<?= $news['_id'] ?>" class="btn btn-danger">Selengkapnya</a> -->
                        </div>
                    </a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>


        <div class="container  my-4">
            <div class="row mb-3">
                <div class="col-6">
                    <h5 class="fw-semibold">Author of the Day</h5>
                </div>
                <div class="col-6 text-end">
                    <a class="text-decoration-none text-danger" href="#">
                        See all
                        <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>

            <div class="row text-center">
                <?php foreach (array_slice($newsList, 0, 12) as $news): ?>
                <div class="col-1">
                    <img src="https://storage.googleapis.com/a1aa/image/Du44GVELJFbnBBf39md0pYwfECFK4G6tF4fhaalxvH33fNsPB.jpg"
                        class="rounded-circle border" alt="BBC News" width="60" height="60">
                    <p class="small mt-2 mb-0"><?= $news['author'] ?></p>
                </div>
                <?php endforeach; ?>
            </div>


            <div class="video mt-4">
                <h4 class="mt-3 mb-3 fw-semibold">News Terbaru</h4>
                <div class="card position-relative" style="height: 30rem;">
                    <!-- Gambar -->
                    <img src="<?= isset($news['image']) ? 'images/' . $news['image'] : 'https://placehold.co/600x400' ?>"
                        class="card-img-top img-fluid" style="object-fit: cover; height: 100%; border-radius: 4px;"
                        alt="News Image">

                    <!-- Konten Overlay -->
                    <div class="card-img-overlay d-flex flex-column justify-content-end"
                        style="background: rgba(0, 0, 0, 0.2); color: white; border-radius: 4px;">
                        <!-- Kategori -->
                        <!-- <span class="badge bg-danger mb-2"><?= htmlspecialchars($news['category']) ?></span> -->

                        <span class="fw-semibold mb-1">
                            <p class="badge bg-danger mb-2 fs-6"><?= htmlspecialchars($news['category']) ?></p>
                            <i class="bi bi-dot"></i>
                            <?= date('d M Y', strtotime($news['date'] ?? 'now')) ?>

                        </span>
                        <a href="detail.php?id=<?= $news['_id'] ?>" class="text-decoration-none text-white">
                            <!-- Judul Berita -->
                            <h2 class="card-title fw-bold mb-1"><?= htmlspecialchars($news['title']) ?></h2>

                            <h6 class="card-title card-text-custom mb-3 "><?= htmlspecialchars($news['summary']) ?></h6>
                            <!-- Tanggal -->

                        </a>
                    </div>
                </div>
            </div>

            <div class="row">
                <h4 class="mt-4 mb-3 fw-semibold">Berita Lainnya</h4>
                <?php foreach (array_slice($newsList, 1, 3) as $news): ?>
                <div class="col-md-4 mb-4">
                    <div class="card">
                        <a href="detail.php?id=<?= $news['_id'] ?>" class="text-decoration-none text-black">
                            <img src="<?= isset($news['image']) ? 'images/' . $news['image'] : 'https://placehold.co/300x200' ?>"
                                class="card-img-top" height="200rem" style="object-fit: cover;" alt=" News Image">
                            <div class="card-body">
                                <span class="badge bg-danger mb-2"><?= htmlspecialchars($news['category']) ?></span>
                                <h5 class="card-title card-text-custom fw-semibold"><?= $news['title'] ?></h5>
                                <p class="card-text card-text-custom"><?= $news['summary'] ?></p>
                                <!-- <a href="detail.php?id=<?= $news['_id'] ?>" class="btn btn-danger">Selengkapnya</a> -->
                            </div>
                        </a>
                    </div>
                </div>
                <?php endforeach; ?>

            </div>

            <div class="row">
                <!-- Kolom Kiri -->

                <div class="col-md-8 mt-4">
                    <h3 class="fw-semibold mb-4">Latest News</h3>

                    <?php foreach(array_slice($newsList, 0, 5) as $news): ?>
                    <div class="row">
                        <div class="col-md-8">
                            <div class="d-flex align-items-center mt-2 mb-2">
                                <i class="fas fa-user-circle me-2 fs-2" style="font-size: 20px; color: #6c757d;"></i>
                                <span class="fw-semibold"><?= htmlspecialchars($news['author']) ?></span>
                            </div>
                            <a href="detail.php?id=<?= $news['_id'] ?>"
                                class="card-title card-text-custom fw-semibold mb-2 fs-5 text-decoration-none"><?= $news['title'] ?></a>
                            <p class="card-text card-text-custom "><?= $news['summary'] ?></p>
                            <div class="d-flex justify-content-between align-items-center mt-3">
                                <!-- Tanggal di kiri -->
                                <p class="mb-0"><?= $news['created_at']->toDateTime()->format('l, d M Y') ?><i
                                        class="bi bi-dot"></i>
                                    <i class="bi bi-eye me-2"> 23 Views</i>
                                </p>


                                <!-- Ikon di kanan -->
                                <div class="d-flex align-items-center me-3">
                                    <!-- Bookmark -->
                                    <!-- <button class="" style="margin-right: 0px;"> -->
                                    <i class=" bi bi-bookmark me-2"></i>
                                    <!-- </button> -->
                                    <!-- Like/Love -->
                                    <!-- <button class="me-2">
                                    <i class="bi bi-heart"></i>
                                </button> -->
                                    <!-- Share -->
                                    <!-- <button class=""> -->
                                    <i class="bi bi-share"></i>
                                    <!-- </button> -->
                                </div>
                            </div>

                        </div>
                        <div class="col-md-4 text-end">
                            <img src="<?= isset($news['image']) ? 'images/' . $news['image'] : 'https://placehold.co/60x60' ?>"
                                class="card-img-top img-fluid float-end"
                                style=" object-fit: cover; border-radius: 10px; float: right; height: 13rem;"
                                alt="News Image">
                        </div>
                        <hr class="mt-4 mb-4">
                    </div>
                    <?php endforeach; ?>
                </div>

                <!-- Kolom Kanan -->
                <div class="col-md-4 mt-4">
                    <h4 class="mt-3 mb-3">Search</h4>
                    <form class="d-flex" method="get" action="index.php">
                        <input class="form-control me-2" type="search" name="search" placeholder="Search"
                            value="<?= htmlspecialchars($searchQuery) ?>">
                        <button class="btn btn-danger " type="submit"><i class="bi bi-search"></i></button>
                    </form>

                    <div class="video mt-4">
                        <h4 class="mt-3 mb-3">Berita Terlama</h4>
                        <div class="card position-relative" style="height: 18rem;">
                            <!-- Gambar -->
                            <img src="<?= isset($news['image']) ? 'images/' . $news['image'] : 'https://placehold.co/600x400' ?>"
                                class="card-img-top img-fluid"
                                style="object-fit: cover; height: 100%; border-radius: 4px;" alt="News Image">

                            <!-- Konten Overlay -->
                            <div class="card-img-overlay d-flex flex-column justify-content-end"
                                style="background: rgba(0, 0, 0, 0.2); color: white; border-radius: 4px;">
                                <!-- Kategori -->
                                <!-- <span class="badge bg-danger mb-2"><?= htmlspecialchars($news['category']) ?></span> -->

                                <!-- Judul Berita -->
                                <h5 class="card-title fw-bold mb-1"><?= htmlspecialchars($news['title']) ?></h5>

                                <!-- Tanggal -->
                                <small class="text-light mb-2">
                                    <?= date('d M Y', strtotime($news['date'] ?? 'now')) ?>
                                </small>

                            </div>
                        </div>
                    </div>

                    <div class="listKategori">
                        <div class="card mt-4">
                            <h4 class="p-2">Categories</h4>
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item">
                                    <h6 class="fw-semibold">Bencana</h6>
                                </li>
                                <li class="list-group-item">
                                    <h6 class="fw-semibold">Bencana</h6>
                                </li>
                                <li class="list-group-item">
                                    <h6 class="fw-semibold">Bencana</h6>
                                </li>
                                <li class="list-group-item">
                                    <h6 class="fw-semibold">Bencana</h6>
                                </li>
                            </ul>
                        </div>

                    </div>

                    <div class="topPost mt-4">
                        <div class="card">
                            <h4 class="p-2">Top Posts</h4>
                            <ul class="list-group list-group-flush">
                                <?php 
            // Contoh array untuk berita top
            $topPosts = array_slice($newsList, 0, 5); // Mengambil 4 berita teratas
            $counter = 1; // Inisialisasi angka
            foreach ($topPosts as $post): ?>
                                <li class=" list-group-item d-flex align-items-start">
                                    <span class="me-3 fw-bold"><?= $counter++; ?>.</span> <!-- Angka -->
                                    <div>
                                        <h6 class="fw-semibold mb-1">
                                            <a href="detail.php?id=<?= $post['_id'] ?>"
                                                class="text-decoration-none text-dark">
                                                <?= htmlspecialchars($post['title']) ?>
                                            </a>
                                        </h6>
                                        <p class="mb-0 text-muted">
                                            <?= $news['created_at']->toDateTime()->format('d M Y') ?>
                                        </p>
                                    </div>
                                </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>



                </div>



                <!-- tutup -->
            </div>

            <?php endif; ?>
        </div>
    </div>

    <footer class="bg-light text-center text-lg-start mt-4 ">
        <div class="text-center p-3">
            © 2024 <span class="text-danger fw-bold">PoliNews</span>. All rights reserved.
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>