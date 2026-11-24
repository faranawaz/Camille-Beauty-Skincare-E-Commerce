<?php
$session = $_SESSION['login'] ?? false;
$admin = $_SESSION['is_admin'];
if (!$session || !$admin) {
    header('Location: admin/login');
    exit;
}

try {
    $customer_id = $_GET['id'] ?? null;

    if (!$customer_id) {
        echo "<script>alert('Customer tidak ditemukan!');window.location.href = 'customer';</script>";
        exit;
    }

    $stmt = $koneksi->prepare("SELECT profile_picture FROM tb_customer WHERE customer_id = ?");
    $stmt->bind_param("i", $customer_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = $result->fetch_assoc();
    $profile_picture = $data['profile_picture'];

    if ($profile_picture) {
        unlink($profile_picture);
    }

    $stmt = $koneksi->prepare("DELETE FROM tb_customer WHERE customer_id = ?");
    $stmt->bind_param("i", $customer_id);
    $stmt->execute();

    echo "<script>alert('Berhasil menghapus customer!');window.location.href = 'customer';</script>";
    exit;
} catch (Exception $e) {
    $message = htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
    echo "<script>alert('Terjadi kesalahan: " . json_encode($message) . "');window.location.href = 'customer';</script>";
    exit;
}
