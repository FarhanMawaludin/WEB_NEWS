<?php
    session_start();
    require '../config/db.php';

    if (!isset($_SESSION['username'])) {
        header('Location: login.php');
        exit;
    }

    $collection = $db->news;
    $id = new MongoDB\BSON\ObjectId($_GET['id']);
    $news = $collection->findOne(['_id' => $id]);

    if (!empty($news['media'])) {
        unlink('../' . $news['media']);
    }

    $collection->deleteOne(['_id' => $id]);

    header("Location: manage_news.php");
    exit();
?>