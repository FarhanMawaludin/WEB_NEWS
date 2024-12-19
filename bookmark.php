<?php
    require 'config/db.php';

    $searchQuery = isset($_GET['search']) ? trim($_GET['search']) : "";
    $categoryFilter = isset($_GET['category']) ? $_GET['category'] : "";
    $newsCollection = $db->news;
    $categoriesCollection = $db->categories;
    
    // Ambil kategori unik dari database
    $categories = $categoriesCollection->find([], ['sort' => ['name' => 1]]);

    // Query berita
    if ($categoryFilter) {
        $cursor = $newsCollection->find(
            ['category' => $categoryFilter],
            ['sort' => ['created_at' => -1]]
        );
    } elseif ($searchQuery) {
        $cursor = $newsCollection->find(
            [
                '$or' => [
                    ['title' => new MongoDB\BSON\Regex($searchQuery, 'i')],
                    ['content' => new MongoDB\BSON\Regex($searchQuery, 'i')]
                ]
            ],
            ['sort' => ['created_at' => -1]]
        );
    } else {
        $cursor = $newsCollection->find([], ['sort' => ['created_at' => -1]]);
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
                            <?php foreach ($categories as $category): ?>
                            <a class="dropdown-item" href="index.php?category=<?= $category->name ?>"><?= ucwords($category->name) ?></a>
                            <?php endforeach; ?>
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
        <div class="jumbotron jumbotron-fluid text-center py-4 mt-4 "
            style="background: rgba(234, 234, 234, 0.5); border-radius: 10px;">
            <div class="judul fw-bold" style="font-size: 32px;"><span class="text-danger">PoliNews,
                </span> Sumber Berita Terpercaya, Aktual, dan Berimbang
            </div>
            <div class="judul text-danger fw-semibold text-danger">Tetap terhubung dengan informasi terkini, inspirasi,
                dan analisis mendalam di POLINEMA</div>
        </div>
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
                            <img src="<?= isset($news['media']) ? 'uploads/' . $news['media'] : 'https://placehold.co/300x200' ?>"
                                class="card-img-top" height="240rem" style="object-fit: cover;" alt="News Image">
                            <div class="card-body">
                                <h5 class="card-title card-text-custom fw-semibold"><?= $news['title'] ?></h5>
                                <p class="card-text card-text-custom"><?= $news['summary'] ?></p>
                                <a href="detail.php?id=${id}" class="btn btn-danger">Selengkapnya</a>
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
            <h4 class="mt-3 mb-3 fw-semibold">Your Bookmark</h4>
            <div id="bookmarks" class="container"></div>
        </div>

    </div>
    </div>

    <footer class="bg-light text-center text-lg-start mt-4">
        <div class="text-center p-3">
            © 2024 <span class="text-danger fw-bold">PoliNews</span>. All rights reserved.
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <script src="bookmark.js"></script>
    <script>
        function bookmarkHtmlBuilder(
            id,
            title,
            summary,
            author,
            mediaUrl,
            mediaExt,
            date
        ){
            const IMG_EXTENSIONS = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            const VIDEO_EXTENSIONS = ['mp4', 'webm', 'ogg'];

            let media = '';
            if (IMG_EXTENSIONS.includes(mediaExt)) {
                media = `<img src="uploads/${mediaUrl}"
                            class="card-img-top img-fluid float-end"
                            style=" object-fit: cover; border-radius: 10px; float: right; height: 13rem;"
                            alt="News Image">`;
            } else if (VIDEO_EXTENSIONS.includes(mediaExt)) {
                media = `<video src="uploads/${mediaUrl}"
                            class="card-img-top img-fluid float-end"
                            style=" object-fit: cover; border-radius: 10px; float: right; height: 13rem;"
                            alt="News Image" controls></video>`;
            } else {
                media = `<img src="https://placehold.co/300x200"
                            class="card-img-top img-fluid float-end"
                            style=" object-fit: cover; border-radius: 10px; float: right; height: 13rem;"
                            alt="News Image">`;
            }

            return `<div id="bookmark-${id}" class="row">
                    <div class="col-md-4 text-end">
                        ${media}
                    </div>
                    <div class="col-md-8">
                        <div class="d-flex align-items-center mt-2 mb-2">
                            <i class="fas fa-user-circle me-2" style=" font-size: 40px; color: #6c757d;"></i>
                            <div>
                                <span class="fw-semibold">${author}</span>
                                <p class="mb-0 text-muted" style="font-size: 12px;">${date}</p>
                            </div>
                        </div>


                        <a href="detail.php?id=${id}"
                            class="card-title card-text-custom fw-semibold mb-2 fs-5 text-decoration-none">${title}</a>
                        <p class="card-text card-text-custom ">${summary}</p>
                        <div class="d-flex justify-content-between align-items-center mt-3">

                            <!-- Ikon di kanan -->
                            <div class="d-flex align-items-center me-3">
                                <!-- Bookmark -->
                                <i 
                                    id="bookmark-${id}"
                                    class="bi bi-bookmark-check-fill text-primary"
                                    onclick="removeBookmark('${id}')"
                                    style="cursor: pointer"
                                ></i>
                            </div>
                        </div>

                    </div>

                    <hr class="mt-4 mb-4">
                </div>`;
        }

        const bookmarksElement = document.getElementById('bookmarks');
        
        const bookmark = new Bookmark();

        function loadBookmarks(){
            bookmarksElement.innerHTML = `
                <div class="text-center mt-5">
                    <i class="bi bi-bookmark text-muted" style="font-size: 4rem;"></i>
                    <p class="text-muted mt-3 fs-5">Your bookmark list is empty</p>
                    <p class="text-muted">Start adding your favorite news to keep them here!</p>
                </div>
            `;

            if (bookmark.length !== 0) {
                bookmarksElement.innerHTML = '';
            }
            bookmark.getListId().forEach(id => {
                const news = bookmark.bookmarks[id];
                const bookmarkHtml = bookmarkHtmlBuilder(
                    id,
                    news['title'],
                    news['summary'],
                    news['author'],
                    news['mediaUrl'],
                    news['mediaExt'],
                    news['date']
                );
                bookmarksElement.innerHTML += bookmarkHtml;
            });
        }
        loadBookmarks();

        function removeBookmark(
            id
        ){
            if (!bookmark.isBookmarked(id)) {
                return;
            }

            if (!confirm("Apakah Anda yakin ingin menghapus bookmark ini?")) {
                return;
            }

            bookmark.remove(id);
            document.getElementById('bookmark-' + id).classList.remove('bi-bookmark-check-fill');
            document.getElementById('bookmark-' + id).classList.remove('text-primary');
            document.getElementById('bookmark-' + id).classList.add('bi-bookmark');
            alert("Bookmark telah di hapus");

            loadBookmarks();
        }
    </script>
</body>

</html>