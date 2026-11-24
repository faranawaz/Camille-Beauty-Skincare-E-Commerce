<?php
$session = $_SESSION['login'] ?? false;
$admin = $_SESSION['is_admin'];
if (!$session || !$admin) {
    header('Location: admin/login');
    exit;
}

try {
    $product_id = $_GET['id'] ?? null;

    if (!$product_id) {
        echo "<script>alert('Product tidak ditemukan!');window.location.href = 'products';</script>";
        exit;
    }

    $stmt = $koneksi->prepare("SELECT product_image FROM tb_product WHERE product_id = ?");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = $result->fetch_assoc();
    $product_image = $data['product_image'];

    if ($product_image) {
        unlink($product_image);
    }

    $stmt = $koneksi->prepare("DELETE FROM tb_product WHERE product_id = ?");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();

    echo "<script>alert('Berhasil menghapus product!');window.location.href = 'products';</script>";
    exit;
} catch (Exception $e) {
    $message = htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
    echo "<script>alert('Terjadi kesalahan: " . json_encode($message) . "');window.location.href = 'products';</script>";
    exit;
}
