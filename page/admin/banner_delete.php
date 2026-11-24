<?php
$session = $_SESSION['login'] ?? false;
$admin = $_SESSION['is_admin'];
if (!$session || !$admin) {
    header('Location: admin/login');
    exit;
}

try {
    $banner_id = $_GET['id'] ?? null;

    if (!$banner_id) {
        echo "<script>alert('Banner tidak ditemukan!');window.location.href = 'banner';</script>";
        exit;
    }

    $stmt = $koneksi->prepare("SELECT image FROM tb_banners WHERE id = ?");
    $stmt->bind_param("i", $banner_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = $result->fetch_assoc();
    $icon = $data['icon'];

    if ($icon) {
        unlink($icon);
    }

    $stmt = $koneksi->prepare("DELETE FROM tb_banners WHERE id = ?");
    $stmt->bind_param("i", $banner_id);
    $stmt->execute();

    echo "<script>alert('Berhasil menghapus banner!');window.location.href = 'banner';</script>";
    exit;
} catch (Exception $e) {
    $message = htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
    echo "<script>alert('Terjadi kesalahan: " . json_encode($message) . "');window.location.href = 'banner';</script>";
    exit;
}
