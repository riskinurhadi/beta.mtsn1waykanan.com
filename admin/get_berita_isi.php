<?php
// Endpoint untuk mengambil isi berita via AJAX (lazy loading)
require_once 'config.php';

// Security check
if (!isset($_SESSION['operator_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'ID tidak valid']);
    exit();
}

$id = intval($_GET['id']);

// Ambil isi berita
$sql = "SELECT isi FROM berita WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    header('Content-Type: application/json');
    echo json_encode(['isi' => $row['isi']]);
} else {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Berita tidak ditemukan']);
}

$stmt->close();
$conn->close();
?>

