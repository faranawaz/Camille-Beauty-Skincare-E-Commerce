<?php
$id = $_GET['id'] ?? null;
if (!$id) {
    echo "<script>alert('Order tidak ditemukan!');window.location.href = 'order';</script>";
    exit;
}

$stmt = $koneksi->prepare("SELECT * FROM tb_orders JOIN tb_order_items ON tb_orders.order_id = tb_order_items.order_id JOIN tb_product ON tb_order_items.product_id = tb_product.product_id JOIN tb_customer ON tb_orders.customer_id = tb_customer.customer_id WHERE tb_orders.order_id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();

$result = $stmt->get_result();
$order = $result->fetch_assoc();

if (!$order) {
    echo "<script>alert('Order tidak ditemukan!');window.location.href = 'order';</script>";
    exit;
}

function format_rupiah($angka)
{
    $hasil_rupiah = "Rp " . number_format($angka, 2, ',', '.');
    return $hasil_rupiah;
}

include_once 'templates/app/header.php'; ?>
<div class="body flex-grow-1">
    <div class="container-lg px-4">
        <div class="row">
            <div class="col-md-12">
                <div class="card mb-4">
                    <div class="card-header">Order Detail</div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-6 mb-3">
                                <label for="order_id" class="form-label fw-bold">Order ID</label>
                                <input type="text" class="form-control" id="order_id" name="order_id" value="<?= $order['order_id'] ?>" readonly>
                            </div>
                            <div class="col-6 mb-3">
                                <label for="created_at" class="form-label fw-bold">Order Date</label>
                                <input type="text" class="form-control" id="created_at" name="created_at" value="<?= date('d F Y', strtotime($order['created_at'])) ?>" readonly>
                            </div>
                            <div class="col-6 mb-3">
                                <label for="customer_name" class="form-label fw-bold">Customer Name</label>
                                <input type="text" class="form-control" id="customer_name" name="customer_name" value="<?= $order['customer_name'] ?>" readonly>
                            </div>
                            <div class="col-6 mb-3">
                                <label for="order_status" class="form-label fw-bold">Order Status</label>
                                <input type="text" class="form-control" id="order_status" name="order_status" value="<?= $order['order_status'] ?>" readonly>
                            </div>
                            <?php if (!empty($order['shipping_proof'])) : ?>
                                <div class="col-6 mb-3">
                                    <label for="shipping_proof" class="form-label fw-bold">Bukti Pengiriman</label>
                                    <div>
                                        <a href="<?= BASE_URL . $order['shipping_proof'] ?>" target="_blank">Lihat Bukti Pengiriman</a>
                                    </div>
                                </div>
                            <?php endif; ?>
                            <div class="table-responsive">
                                <table class="table border mb-0" id="dataTable">
                                    <thead class="fw-semibold text-nowrap">
                                        <tr class="align-middle">
                                            <th class="bg-body-secondary text-center">
                                                <svg class="icon">
                                                    <use xlink:href="vendors/@coreui/icons/svg/free.svg#cil-people"></use>
                                                </svg>
                                            </th>
                                            <th class="bg-body-secondary">Product Name</th>
                                            <th class="bg-body-secondary text-center">Price</th>
                                            <th class="bg-body-secondary text-center">Quantity</th>
                                            <th class="bg-body-secondary text-center">Subtotal</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $stmt = $koneksi->prepare("SELECT * FROM tb_order_items JOIN tb_product ON tb_order_items.product_id = tb_product.product_id WHERE tb_order_items.order_id = ?");
                                        $stmt->bind_param("i", $id);
                                        $stmt->execute();
                                        $result = $stmt->get_result();

                                        while ($row = $result->fetch_assoc()) {
                                            $product_image = file_exists($row['product_image']) ? BASE_URL . $row['product_image'] : BASE_URL . 'assets/img/default_category.png';
                                        ?>
                                            <tr class="align-middle">
                                                <td class="text-center">
                                                    <div class="avatar avatar-md">
                                                        <img class="avatar-img" src="<?= $product_image ?>" alt="avatar">
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="text-nowrap"><?= $row['product_name'] ?></div>
                                                </td>
                                                <td class="text-center">
                                                    <div class="text-nowrap"><?= format_rupiah($row['product_price']) ?></div>
                                                </td>
                                                <td class="text-center">
                                                    <div class="text-nowrap"><?= $row['quantity'] ?></div>
                                                </td>
                                                <td class="text-center">
                                                    <div class="text-nowrap"><?= format_rupiah($row['product_price'] * $row['quantity']) ?></div>
                                                </td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                            <div class="mb-3 d-flex justify-content-end py-3">
                                <a href="profile" class="btn btn-pink text-white">Back</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php include_once 'templates/app/footer.php'; ?>