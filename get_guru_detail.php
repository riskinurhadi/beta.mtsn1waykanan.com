<?php
header('Content-Type: application/json');
include 'koneksi.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo json_encode(['error' => 'ID Guru tidak ditemukan.']);
    exit();
}

$guru_id = (int)$_GET['id'];

// PERUBAHAN: Mengambil semua data dari struktur baru
$sql = "SELECT * FROM data_guru WHERE id = ?";
$stmt = $koneksi->prepare($sql);
$stmt->bind_param("i", $guru_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $guru = $result->fetch_assoc();
    echo json_encode($guru);
} else {
    echo json_encode(['error' => 'Data guru tidak ditemukan.']);
}

$stmt->close();
$koneksi->close();
?>
