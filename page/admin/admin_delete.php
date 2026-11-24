<?php
$session = $_SESSION['login'] ?? false;
$admin = $_SESSION['is_admin'];
if (!$session || !$admin) {
    header('Location: admin/login');
    exit;
}

try {
    $admin_id = $_GET['id'] ?? null;

    if (!$admin_id) {
        echo "<script>alert('Admin tidak ditemukan!');window.location.href = 'admins';</script>";
        exit;
    }

    $stmt = $koneksi->prepare("SELECT * FROM tb_admin WHERE admin_id = ?");
    $stmt->bind_param("i", $admin_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $admin = $result->fetch_assoc();
    $profile_picture = $admin['profile_picture'];

    if (!$admin) {
        echo "<script>alert('Admin tidak ditemukan!');window.location.href = 'admins';</script>";
        exit;
    }

    if ($profile_picture) {
        unlink($profile_picture);
    }

    $stmt = $koneksi->prepare("DELETE FROM tb_admin WHERE admin_id = ?");
    $stmt->bind_param("i", $admin_id);
    $stmt->execute();

    echo "<script>alert('Berhasil menghapus admin!');window.location.href = 'admins';</script>";
    exit;
} catch (Exception $e) {
    $message = htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
    echo "<script>alert('Terjadi kesalahan: " . json_encode($message) . "');window.location.href = 'admins';</script>";
    exit;
}
