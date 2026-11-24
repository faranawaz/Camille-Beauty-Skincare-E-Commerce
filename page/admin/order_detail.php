<?php
$session = $_SESSION['login'] ?? false;
$admin = $_SESSION['is_admin'];
if (!$session || !$admin) {
    header('Location: admin/login');
    exit;
}

$id = $_GET['id'] ?? null;
if (!$id) {
    echo "<script>alert('Order tidak ditemukan!');window.location.href = 'orders';</script>";
    exit;
}

$stmt = $koneksi->prepare("SELECT * FROM tb_orders JOIN tb_customer ON tb_orders.customer_id = tb_customer.customer_id WHERE tb_orders.order_id = ?");

$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$order = $result->fetch_assoc();

if (!$order) {
    echo "<script>alert('Order tidak ditemukan!');window.location.href = 'orders';</script>";
    exit;
}

include_once 'templates/admin/header.php';

?>

<div class="body flex-grow-1">
    <div class="container-lg px-4" id="printArea">
        <div class="row">
            <div class="col-md-12">
                <div class="card mb-4">
                    <div class="card-header">Order Detail</div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h4 class="card-title mb-0">Order No: <?= $order['order_no'] ?></h4>
                            </div>
                            <div class="btn-toolbar d-none d-md-block" role="toolbar" aria-label="Toolbar with buttons">
                                <a href="javascript:;" class="btn btn-primary" id="print">Print</a>
                            </div>
                        </div>
                        <div class="d-flex justify-content-between mt-4">
                            <div>
                                <h5 class="card-title mb-0">Customer</h5>
                                <p><?= $order['customer_name'] ?></p>
                            </div>
                            <div>
                                <h5 class="card-title mb-0">Date</h5>
                                <p><?= date('d F Y H:i', strtotime($order['created_at'])) ?></p>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table border mb-0" id="dataTable">
                                <thead class="fw-semibold text-nowrap">
                                    <tr class="align-middle">
                                        <th class="bg-body-secondary">Product</th>
                                        <th class="bg-body-secondary">Category</th>
                                        <th class="bg-body-secondary">Brand</th>
                                        <th class="bg-body-secondary">Price</th>
                                        <th class="bg-body-secondary">Qty</th>
                                        <th class="bg-body-secondary">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $stmt = $koneksi->prepare("SELECT 
                                    tb_order_items.quantity as qty,
                                    tb_product.product_name as product_name,
                                    tb_product.product_price as product_price,
                                    tb_brand.name as brand_name,
                                    tb_category.name as category_name
                                     FROM tb_order_items JOIN tb_product ON tb_order_items.product_id = tb_product.product_id JOIN tb_category ON tb_product.category_id = tb_category.id JOIN tb_brand ON tb_product.brand_id = tb_brand.id WHERE tb_order_items.order_id = ?");
                                    $stmt->bind_param("i", $id);
                                    $stmt->execute();
                                    $result = $stmt->get_result();

                                    while ($row = $result->fetch_assoc()) {
                                    ?>
                                        <tr class="align-middle">
                                            <td>
                                                <div class="text-nowrap"><?= $row['product_name'] ?></div>
                                            </td>
                                            <td>
                                                <div class="text-nowrap"><?= $row['category_name'] ?></div>
                                            </td>
                                            <td>
                                                <div class="text-nowrap"><?= $row['brand_name'] ?></div>
                                            </td>
                                            <td>
                                                <div class="text-nowrap"><?= 'Rp ' . number_format($row['product_price'], 0, ',', '.') ?></div>
                                            </td>
                                            <td>
                                                <div class="text-nowrap"><?= $row['qty'] ?></div>
                                            </td>
                                            <td>
                                                <div class="text-nowrap">
                                                    <?php
                                                    $subtotal = $row['product_price'] * $row['qty'];
                                                    echo 'Rp ' . number_format($subtotal, 0, ',', '.');
                                                    ?>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="d-flex justify-content-between mt-4">
                            <div>
                                <h4 class="card-title mb-0">Total: <?= 'Rp ' . number_format($order['order_total'], 0, ',', '.') ?></h4>
                            </div>
                            <div>
                                <h4 class="card-title  mb-0">Status: <div class="badge bg-primary"><?= ucwords($order['order_status']) ?></div>
                                </h4>
                            </div>
                        </div>
                        <div class="d-flex justify-content-between mt-4">
                            <div>
                                <h4 class="card-title mb-0">Shipping Address</h4>
                                <p><?= nl2br($order['shipping_address']) ?></p>
                            </div>
                            <div class="d-flex justify-content-between">
                                <div>
                                    <?php
                                    if ($order['payment_proof'] != null) {

                                    ?>
                                        <h6 class="card-title mb-0">Bukti Transfer <br /> <a href="<?= BASE_URL . $order['payment_proof'] ?>" download>Download</a>
                                        </h6>
                                        <img src="<?= BASE_URL . $order['payment_proof'] ?>" alt="Bukti Transfer" style="max-width: 200px;" class="img-fluid mb-3">
                                    <?php } ?>
                                </div>
                                <div>
                                    <?php
                                    if ($order['shipping_proof'] != null) {

                                    ?>
                                        <h6 class="card-title mb-0">Bukti Pengiriman<br /> <a href="<?= BASE_URL . $order['shipping_proof'] ?>" download>Download</a>
                                        </h6>
                                        <img src="<?= BASE_URL . $order['shipping_proof'] ?>" alt="Bukti Transfer" style="max-width: 200px;" class="img-fluid mb-3">
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    @media print {
        #printArea {
            margin: 0;
            padding: 0;
        }

        #printArea .card {
            border: none;
            box-shadow: none;
        }

        #printArea .dt-search,
        #printArea .dt-info,
        #printArea .dt-paging,
        #printArea .dt-length {
            display: none;
        }

        #datatable {
            width: 100%;
        }

        #print {
            display: none;
        }
    }
</style>

<script>
    document.getElementById('print').addEventListener('click', function() {
        window.print();
    });
</script>
<?php include_once 'templates/admin/footer.php'; ?>