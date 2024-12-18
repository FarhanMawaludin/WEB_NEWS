<?php
// Mengimpor MongoDB
require 'vendor/autoload.php'; // Jika menggunakan Composer

// Membuat koneksi ke MongoDB
$mongoClient = new MongoDB\Client("mongodb://localhost:27017");

// Menentukan basis data dan koleksi
$db = $mongoClient->basisdata_news;
$collection = $db->news;

try {
    // Melakukan update untuk mengganti nama field 'image' menjadi 'media'
    $result = $collection->updateMany(
        [], // Menargetkan seluruh dokumen dalam koleksi
        ['$rename' => ['image' => 'media']] // Operator $rename untuk mengganti nama field
    );

    // Menampilkan jumlah dokumen yang terupdate
    echo "Dokumen yang diperbarui: " . $result->getModifiedCount() . " dokumen\n";
} catch (Exception $e) {
    echo "Terjadi kesalahan: " . $e->getMessage() . "\n";
}
?>
