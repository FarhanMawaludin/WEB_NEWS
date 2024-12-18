<?php
session_start();
require '../config/db.php';

// Redirect jika user belum login
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit;
}

// Variabel untuk pesan pemberitahuan
$message = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $collection = $db->news;
    $target_dir = "../uploads/"; // Direktori untuk file gambar atau video
    $target_file = $target_dir . basename($_FILES["media"]["name"]);
    $uploadOk = 1;
    $fileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Daftar ekstensi yang diizinkan
    $allowed_image_types = ['jpg', 'jpeg', 'png', 'gif'];
    $allowed_video_types = ['mp4', 'avi', 'mov', 'mkv'];

    // Validasi apakah file termasuk gambar atau video
    if (in_array($fileType, $allowed_image_types)) {
        $is_image = true;
    } elseif (in_array($fileType, $allowed_video_types)) {
        $is_image = false;
    } else {
        $message = "Hanya file gambar (JPG, JPEG, PNG, GIF) atau video (MP4, AVI, MOV, MKV) yang diizinkan.";
        $uploadOk = 0;
    }

    // Validasi ukuran file
    $max_size = $is_image ? 5000000 : 50000000; // 5MB untuk gambar, 50MB untuk video
    if ($_FILES["media"]["size"] > $max_size) {
        $message = $is_image
            ? "Ukuran file gambar terlalu besar. Maksimum 5MB."
            : "Ukuran file video terlalu besar. Maksimum 50MB.";
        $uploadOk = 0;
    }

    // Cek jika file sudah ada
    if (file_exists($target_file)) {
        $message = "File sudah ada. Silakan ganti nama file atau unggah file yang berbeda.";
        $uploadOk = 0;
    }

    // Jika validasi lolos, proses upload
    if ($uploadOk) {
        if (move_uploaded_file($_FILES["media"]["tmp_name"], $target_file)) {
            $result = $collection->insertOne([
                'title' => htmlspecialchars($_POST['title']),
                'content' => $_POST['content'],
                'summary' => htmlspecialchars($_POST['summary']),
                'author' => htmlspecialchars($_POST['author']),
                'category' => htmlspecialchars($_POST['category']),
                'created_at' => new MongoDB\BSON\UTCDateTime(),
                'updated_at' => new MongoDB\BSON\UTCDateTime(),
                'media' => basename($_FILES["media"]["name"]), // Simpan hanya nama file
            ]);

            // Pesan pemberitahuan
            $message = "Berita berhasil ditambahkan!";
            header('Location: manage_news.php');
            exit;
        } else {
            $message = "Terjadi kesalahan saat mengunggah file.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Berita</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Quill.js -->
    <link href="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.snow.css" rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/highlight.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.js"></script>
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/styles/atom-one-dark.min.css" />
    <script src="https://cdn.jsdelivr.net/npm/katex@0.16.9/dist/katex.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/katex@0.16.9/dist/katex.min.css" />

    <script>
        // Fungsi untuk menampilkan alert jika ada pesan
        function showMessage(message) {
            if (message) {
                alert(message);
            }
        }
    </script>
</head>

<body onload="showMessage('<?= $message ?>')">

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light shadow-sm">
        <div class="container-fluid">
            <a class="navbar-brand fw-bold" href="manage_news.php">Admin <span class="text-danger">Polinews</span></a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="manage_news.php">Kelola Berita</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="add_news.php">Tambah Berita</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Form Tambah Berita -->
    <div class="container mt-4">
        <h1>Tambah Berita</h1>
        <form method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="title" class="form-label">Judul</label>
                <input type="text" class="form-control" id="title" name="title" required>
            </div>
            <div class="mb-3">
                <label for="content" class="form-label">Konten</label>
                <div id="toolbar-container"
                    style="border-top-left-radius: var(--bs-border-radius); border-top-right-radius: var(--bs-border-radius); border: 1px solid #dee2e6;">
                    <span class="ql-formats">
                        <select class="ql-size"></select>
                    </span>
                    <span class="ql-formats">
                        <button class="ql-bold"></button>
                        <button class="ql-italic"></button>
                        <button class="ql-underline"></button>
                        <button class="ql-strike"></button>
                    </span>
                    <span class="ql-formats">
                        <select class="ql-color"></select>
                        <select class="ql-background"></select>
                    </span>
                    <span class="ql-formats">
                        <button class="ql-script" value="sub"></button>
                        <button class="ql-script" value="super"></button>
                    </span>
                    <span class="ql-formats">
                        <button class="ql-header" value="1"></button>
                        <button class="ql-header" value="2"></button>
                        <button class="ql-blockquote"></button>
                        <button class="ql-code-block"></button>
                    </span>
                    <span class="ql-formats">
                        <button class="ql-list" value="ordered"></button>
                        <button class="ql-list" value="bullet"></button>
                        <button class="ql-indent" value="-1"></button>
                        <button class="ql-indent" value="+1"></button>
                    </span>
                    <span class="ql-formats">
                        <button class="ql-direction" value="rtl"></button>
                        <select class="ql-align"></select>
                    </span>
                    <span class="ql-formats">
                        <button class="ql-link"></button>
                        <button class="ql-image"></button>
                        <button class="ql-video"></button>
                        <button class="ql-formula"></button>
                    </span>
                    <span class="ql-formats">
                        <button class="ql-clean"></button>
                    </span>
                </div>
                <div id="editor"
                    style="border-bottom-left-radius: var(--bs-border-radius); border-bottom-right-radius: var(--bs-border-radius); border: 1px solid #dee2e6; border-top: 0px solid; min-height: 10rem">
                </div>

                <input type="hidden" id="quill-content" name="content">
                <!-- <textarea class="form-control" id="content" name="content" rows="5" required></textarea> -->
            </div>
            <div class="mb-3">
                <label for="summary" class="form-label">Ringkasan</label>
                <textarea class="form-control" id="summary" name="summary" rows="3" required></textarea>
            </div>
            <div class="mb-3">
                <label for="author" class="form-label">Penulis</label>
                <input type="text" class="form-control" id="author" name="author" required>
            </div>
            <!-- Kategori Dropdown -->
            <div class="mb-3">
                <label for="category" class="form-label">Kategori</label>
                <select class="form-select" id="category" name="category" required>
                    <option value="">Pilih Kategori</option>
                    <option value="politik">Politik</option>
                    <option value="bencana">Bencana</option>
                    <option value="lalu-lintas">Lalu Lintas</option>
                    <option value="pendidikan">Pendidikan</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="image" class="form-label">Gambar/Video Sampul Berita</label>
                <input type="file" class="form-control" id="media" name="media" accept="image/*, video/*">
            </div>
            <br>
            <div class="mb-3">
                <button type="button" class="btn btn-secondary me-2" onclick="window.history.back();">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </form>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Initialize Quill editor -->
    <script>
        const quill = new Quill('#editor', {
            modules: {
                syntax: true,
                toolbar: '#toolbar-container',
            },
            placeholder: 'Add your content here...',
            theme: 'snow',
        });

        quill.on('text-change', (delta, oldDelta, source) => {
            document.getElementById('quill-content').value = quill.root.innerHTML;
        });
    </script>
</body>

</html>