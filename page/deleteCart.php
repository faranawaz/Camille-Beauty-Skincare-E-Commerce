<?php
$session = $_SESSION['customer_login'] ?? false;
$customer = $_SESSION['customer_id'];

if (!$session) {
    header('Location: login');
    exit;
}

if ($_GET['id'] == null) {
    echo "<script>alert('Product tidak ditemukan!');window.location.href = 'product';</script>";
    exit;
}

try {
    $id = $_GET['id'];

    $stmt = $koneksi->prepare("DELETE FROM tb_cart WHERE cart_id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();

    echo "<script>alert('Berhasil menghapus dari keranjang!');window.location.href = 'cart';</script>";
    exit;
} catch (Exception $e) {
    $message = htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
    echo "<script>alert('Terjadi kesalahan: " . json_encode($message) . "');window.location.href = 'cart';</script>";
    exit;
}
