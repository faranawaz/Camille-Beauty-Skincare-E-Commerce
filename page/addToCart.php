<?php

if (!isset($_SESSION['customer_id'])) {
    echo "<script>alert('Silahkan login terlebih dahulu!');window.location.href = 'login';</script>";
    exit;
}
$id = $_GET['id'] ?? null;
if (!$id) {
    echo "<script>alert('Product tidak ditemukan!');window.location.href = 'product';</script>";
    exit;
}

try {
    $product_id = $_GET['id'];
    $customer_id = $_SESSION['customer_id'];
    $quantity = $_GET['quantity'] ?? 1;

    $stmt = $koneksi->prepare("SELECT * FROM tb_cart WHERE product_id = ? AND customer_id = ?");
    $stmt->bind_param("ii", $product_id, $customer_id);
    $stmt->execute();

    $result = $stmt->get_result();
    $cart = $result->fetch_assoc();

    $stmt = $koneksi->prepare("SELECT * FROM tb_product WHERE product_id = ?");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $product = $result->fetch_assoc();

    if (!$product) {
        echo "<script>alert('Product tidak ditemukan!');window.location.href = 'product';</script>";
        exit;
    }

    if ($quantity > $product['stock']) {
        echo "<script>alert('Stok tidak mencukupi!');window.location.href = 'product';</script>";
        exit;
    }

    if ($cart) {
        $quantity = $cart['quantity'] + $quantity;

        $stmtUpdate = $koneksi->prepare("UPDATE tb_cart SET quantity = ? WHERE product_id = ? AND customer_id = ?");
        $stmtUpdate->bind_param("iii", $quantity, $product_id, $customer_id);
        $stmtUpdate->execute();

        echo "<script type='text/javascript'>alert('Berhasil memperbaharui quantity!');window.location.href = 'product';</script>";
        exit;
    } else {
        $stmtInsert = $koneksi->prepare("INSERT INTO tb_cart (product_id, customer_id, quantity) VALUES (?, ?, ?)");
        $stmtInsert->bind_param("iii", $product_id, $customer_id, $quantity);
        $stmtInsert->execute();

        echo "<script type='text/javascript'>alert('Berhasil menambahkan ke keranjang!');window.location.href = 'product';</script>";
        exit;
    }

    echo "<script type='text/javascript'>alert('Berhasil menambahkan ke keranjang!');window.location.href = 'product';</script>";
    exit;
} catch (Exception $e) {
    $message = htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
    echo "<script type='text/javascript'>alert('Terjadi kesalahan: " . json_encode($message) . "');window.location.href = 'product';</script>";
    exit;
}
