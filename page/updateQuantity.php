<?php

$id = $_GET['id'] ?? null;
if (!$id) {
    echo "<script>alert('Product tidak ditemukan!');window.location.href = 'product';</script>";
    exit;
}

try {
    $id = $_GET['id'];
    $action = $_GET['action'] ?? 'add';

    $stmt = $koneksi->prepare("
    SELECT * 
    FROM tb_cart c
    JOIN tb_product p ON c.product_id = p.product_id 
    WHERE c.cart_id = ?
");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $cart = $result->fetch_assoc();

    if (!$cart) {
        echo "<script>alert('Product tidak ditemukan!');window.location.href = 'cart';</script>";
        exit;
    }

    if ($cart['quantity'] >= $cart['stock']) {
        echo "<script>alert('Stok tidak mencukupi!');window.location.href = 'cart';</script>";
        exit;
    }

    $quantity = $action === 'add' ? $cart['quantity'] + 1 : $cart['quantity'] - 1;

    $stmtUpdate = $koneksi->prepare("UPDATE tb_cart SET quantity = ? WHERE cart_id = ?");
    $stmtUpdate->bind_param("ii", $quantity, $id);
    $stmtUpdate->execute();

    echo "<script type='text/javascript'>alert('Berhasil memperbaharui quantity!');window.location.href = 'cart';</script>";
    exit;
} catch (Exception $e) {
    $message = htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
    echo "<script type='text/javascript'>alert('Terjadi kesalahan: " . json_encode($message) . "');window.location.href = 'cart';</script>";
    exit;
}
