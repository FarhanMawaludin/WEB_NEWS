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
                    <li class="nav-item"><a class="nav-link" href="bookmark.php">Bookmark</a></li>

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

    <div class="container custom-container">

        <div class="row">


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
                            <?php
                                        $fileExtension = pathinfo($news['media'], PATHINFO_EXTENSION);
                                        $imageUrl = isset($news['media']) ? 'uploads/' . $news['media'] : 'https://placehold.co/300x200';
                                        ?>

                            <?php if (in_array(strtolower($fileExtension), ['jpg', 'jpeg', 'png', 'gif', 'webp'])): ?>
                            <img src="<?= $imageUrl ?>" class="card-img-top" height="240rem" style="object-fit: cover;"
                                alt="News Image">
                            <?php elseif (in_array(strtolower($fileExtension), ['mp4', 'webm', 'ogg'])): ?>
                            <video class="card-img-top" height="240rem" style="object-fit: cover;" controls muted>
                                <source src="<?= $imageUrl ?>" type="video/<?= $fileExtension ?>">
                                Your browser does not support the video tag.
                            </video>
                            <?php else: ?>
                            <img src="<?= $imageUrl ?>" class="card-img-top" height="240rem" style="object-fit: cover;"
                                alt="Placeholder Image">
                            <?php endif; ?>
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


            <div class="container">

                <?php foreach (array_slice($newsList, 0, 1) as $news): ?>

                <div class="video">
                    <h4 class="mt-3 mb-3 fw-semibold">Semua Berita</h4>
                    <div class="card position-relative" style="height: 30rem;">
                        <!-- Gambar -->
                        <?php
                                $fileExtension = pathinfo($news['media'], PATHINFO_EXTENSION);
                                $imageUrl = isset($news['media']) ? 'uploads/' . $news['media'] : 'https://placehold.co/300x200';
                                ?>

                        <?php if (in_array(strtolower($fileExtension), ['jpg', 'jpeg', 'png', 'gif', 'webp'])): ?>
                        <img src="<?= $imageUrl ?>" class="card-img-top" height="100%" style="object-fit: cover;"
                            alt="News Image">
                        <?php elseif (in_array(strtolower($fileExtension), ['mp4', 'webm', 'ogg'])): ?>
                        <div class="video-wrapper">
                            <video class="card-img-top" controls muted>
                                <source src="<?= $imageUrl ?>" type="video/<?= $fileExtension ?>">
                                Your browser does not support the video tag.
                            </video>
                        </div>
                        <?php else: ?>
                        <img src="<?= $imageUrl ?>" class="card-img-top" height="100%" style="object-fit: cover;"
                            alt="Placeholder Image">
                        <?php endif; ?>

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

                                <h6 class="card-title card-text-custom mb-3 "><?= htmlspecialchars($news['summary']) ?>
                                </h6>
                                <!-- Tanggal -->

                            </a>
                        </div>
                    </div>
                    <?php endforeach; ?>

                </div>

                <div class="row mt-4">
                    <?php foreach (array_slice($newsList, 1) as $news): ?>
                    <div class="col-md-4 mb-4">
                        <div class="card">
                            <a href="detail.php?id=<?= $news['_id'] ?>" class="text-decoration-none text-black">
                                <img src="<?= isset($news['media']) ? 'uploads/' . $news['media'] : 'https://placehold.co/300x200' ?>"
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




                <!-- tutup -->
            </div>

            <?php endif; ?>
        </div>
    </div>

    <footer class="bg-light text-center text-lg-start mt-4">
        <div class="text-center p-3">
            © 2024 <span class="text-danger fw-bold">PoliNews</span>. All rights reserved.
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>