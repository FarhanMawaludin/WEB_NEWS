<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit;
}

$collection = $db->categories;

// Data Kategori
if (!isset($_GET['id'])) {
    header('Location: manage_category.php');
    exit;
}

$id = new MongoDB\BSON\ObjectId($_GET['id']);
$category = $collection->findOne(['_id' => $id]);

if (!$category) {
    header('Location: manage_category.php');
    exit;
}

// Update Kategori
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_category'])) {
    $name = trim($_POST['name']);
    if (!empty($name)) {
        $collection->updateOne(['_id' => $id], ['$set' => ['name' => $name]]);
        header('Location: manage_category.php');
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Kategori</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-4">
        <h1>Edit Kategori</h1>
        <form method="POST">
            <div class="mb-3">
                <label for="name" class="form-label">Nama Kategori</label>
                <input type="text" name="name" id="name" class="form-control" value="<?= $category['name'] ?>" required>
            </div>
            <button type="submit" name="update_category" class="btn btn-success">Update</button>
            <a href="manage_category.php" class="btn btn-secondary">Kembali</a>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
    <script> </script>
</body>

</html>
