<?php
// File ini hanya untuk menangani upload gambar dari editor Summernote

if (isset($_FILES['file']['name'])) {
    if (!$_FILES['file']['error']) {
        // Path fisik untuk menyimpan file (relatif terhadap file ini)
        $upload_dir = 'uploads/'; 
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        
        $file_name = uniqid() . '_' . preg_replace("/[^a-zA-Z0-9\._-]/", "", $_FILES['file']['name']);
        $destination = $upload_dir . $file_name;

        // Path URL yang akan dikembalikan ke editor (selalu dimulai dari root website)
        $url_path = '/admin/uploads/' . $file_name;

        // Cek tipe file (opsional tapi disarankan)
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        if (in_array($_FILES['file']['type'], $allowed_types)) {
            if (move_uploaded_file($_FILES['file']['tmp_name'], $destination)) {
                // Berhasil: kembalikan URL root-relative
                echo json_encode(['url' => $url_path]);
            } else {
                echo json_encode(['error' => 'Gagal menyimpan file. Cek permissions folder.']);
            }
        } else {
            echo json_encode(['error' => 'Tipe file tidak diizinkan.']);
        }
    } else {
        echo json_encode(['error' => 'Error: ' . $_FILES['file']['error']]);
    }
} else {
    echo json_encode(['error' => 'Tidak ada file yang diterima.']);
}
?>

