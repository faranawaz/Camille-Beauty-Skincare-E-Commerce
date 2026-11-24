<?php
$session = $_SESSION['login'] ?? false;
$admin = $_SESSION['is_admin'];
if (!$session || !$admin) {
    header('Location: admin/login');
    exit;
}

try {
    $category_id = $_GET['id'] ?? null;

    if (!$category_id) {
        echo "<script>alert('Category tidak ditemukan!');window.location.href = 'category';</script>";
        exit;
    }

    $stmt = $koneksi->prepare("SELECT icon FROM tb_category WHERE id = ?");
    $stmt->bind_param("i", $category_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = $result->fetch_assoc();
    $icon = $data['icon'];

    if ($icon) {
        unlink($icon);
    }

    $stmt = $koneksi->prepare("DELETE FROM tb_category WHERE id = ?");
    $stmt->bind_param("i", $category_id);
    $stmt->execute();

    echo "<script>alert('Berhasil menghapus category!');window.location.href = 'category';</script>";
    exit;
} catch (Exception $e) {
    $message = htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
    echo "<script>alert('Terjadi kesalahan: " . json_encode($message) . "');window.location.href = 'category';</script>";
    exit;
}
