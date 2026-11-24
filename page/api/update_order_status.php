<?php

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $status = $_POST['status'];
        $order_id = $_POST['order_id'];

        $stmt = $koneksi->prepare("UPDATE tb_orders SET order_status = ? WHERE order_id = ?");
        $stmt->bind_param("si", $status, $order_id);
        $stmt->execute();

        echo json_encode(['status' => 'success', 'message' => 'Berhasil mengubah status pesanan!']);
        exit;
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        exit;
    }
}
