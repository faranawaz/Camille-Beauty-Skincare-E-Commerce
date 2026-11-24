<?php
if (!isset($_SESSION['login']) || !$_SESSION['is_admin']) {
    echo json_encode(['status' => 'error', 'message' => 'Anda tidak memiliki izin untuk melakukan tindakan ini.']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $order_id = $_POST['order_id'];

    if (isset($_FILES['shipping_proof']) && $_FILES['shipping_proof']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['shipping_proof']['tmp_name'];
        $fileName = $_FILES['shipping_proof']['name'];
        $fileSize = $_FILES['shipping_proof']['size'];
        $fileType = $_FILES['shipping_proof']['type'];
        $fileNameCmps = explode(".", $fileName);
        $fileExtension = strtolower(end($fileNameCmps));

        $newFileName = md5(time() . $fileName) . '.' . $fileExtension;

        $uploadFileDir = 'uploads/shipping_proofs/';
        $dest_path = $uploadFileDir . $newFileName;

        if (!is_dir($uploadFileDir)) {
            mkdir($uploadFileDir, 0777, true);
        }

        if (move_uploaded_file($fileTmpPath, $dest_path)) {
            $relative_path = 'uploads/shipping_proofs/' . $newFileName;

            $stmt = $koneksi->prepare("UPDATE tb_orders SET shipping_proof = ? WHERE order_id = ?");
            $stmt->bind_param('si', $relative_path, $order_id);

            if ($stmt->execute()) {
                echo json_encode(['status' => 'success', 'message' => 'Bukti pengiriman berhasil diunggah.']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Gagal memperbarui basis data.']);
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Gagal mengunggah berkas.']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Tidak ada berkas yang diunggah atau terjadi kesalahan saat mengunggah.']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Permintaan tidak valid.']);
}
