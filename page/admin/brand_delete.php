<?php
$session = $_SESSION['login'] ?? false;
$admin = $_SESSION['is_admin'];
if (!$session || !$admin) {
    header('Location: admin/login');
    exit;
}

try {
    $brand_id = $_GET['id'] ?? null;

    if (!$brand_id) {
        echo "<script>alert('Brand tidak ditemukan!');window.location.href = 'brand';</script>";
        exit;
    }

    $stmt = $koneksi->prepare("SELECT image FROM tb_brand WHERE id = ?");
    $stmt->bind_param("i", $brand_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = $result->fetch_assoc();
    $image = $data['image'];

    if ($image) {
        unlink($image);
    }

    $stmt = $koneksi->prepare("DELETE FROM tb_brand WHERE id = ?");
    $stmt->bind_param("i", $brand_id);
    $stmt->execute();

    echo "<script>alert('Berhasil menghapus brand!');window.location.href = 'brand';</script>";
    exit;
} catch (Exception $e) {
    $message = htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
    echo "<script>alert('Terjadi kesalahan: " . json_encode($message) . "');window.location.href = 'brand';</script>";
    exit;
}
