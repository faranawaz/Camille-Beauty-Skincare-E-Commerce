<?php
$session = $_SESSION['login'] ?? false;
$admin = $_SESSION['is_admin'];
if (!$session || !$admin) {
    header('Location: admin/login');
    exit;
}

include_once 'templates/admin/header.php'; ?>
<div class="body flex-grow-1">
    <div class="container-lg px-4">
        <div class="row">
            <div class="col-md-12">
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h4 class="card-title mb-0">List Orders</h4>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table border mb-0" id="dataTable">
                                <thead class="fw-semibold text-nowrap">
                                    <tr class="align-middle">
                                        <th class="bg-body-secondary">No Ref</th>
                                        <th class="bg-body-secondary text-center">Customer</th>
                                        <th class="bg-body-secondary">Total</th>
                                        <th class="bg-body-secondary">Status</th>
                                        <th class="bg-body-secondary">Shipping Address</th>
                                        <th class="bg-body-secondary">Date</th>
                                        <th class="bg-body-secondary">Bukti Transfer</th>
                                        <th class="bg-body-secondary">Bukti Pengiriman</th>
                                        <th class="bg-body-secondary"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $stmt = $koneksi->prepare("SELECT * FROM tb_orders JOIN tb_customer ON tb_orders.customer_id = tb_customer.customer_id ORDER BY tb_orders.order_id DESC");

                                    $stmt->execute();
                                    $result = $stmt->get_result();

                                    while ($row = $result->fetch_assoc()) {
                                    ?>
                                        <tr class="align-middle">
                                            <td>
                                                <a href="order_detail?id=<?= $row['order_id'] ?>" class="text-nowrap"><?= $row['order_no'] ?></a>
                                            </td>
                                            <td>
                                                <div class="text-nowrap"><?= $row['customer_name'] ?></div>
                                            </td>
                                            <td>
                                                <div class="text-nowrap"><?= 'Rp ' . number_format($row['order_total'], 0, ',', '.') ?></div>
                                            </td>
                                            <td>
                                                <div class="text-nowrap">
                                                    <select class="form-select" name="status" id="status-<?= $row['order_id'] ?>" onchange="updateOrderStatus(<?= $row['order_id'] ?>, this.value)">
                                                        <option value="pending" <?= $row['order_status'] == 'pending' ? 'selected' : '' ?>>Menunggu</option>
                                                        <option value="process" <?= $row['order_status'] == 'process' ? 'selected' : '' ?>>DiProses</option>
                                                        <option value="shipping" <?= $row['order_status'] == 'shipping' ? 'selected' : '' ?>>Dikirim</option>
                                                        <option value="completed" <?= $row['order_status'] == 'completed' ? 'selected' : '' ?>>Selesai</option>
                                                        <option value="canceled" <?= $row['order_status'] == 'canceled' ? 'selected' : '' ?>>Dibatalkan</option>
                                                    </select>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="text-nowrap"><?= nl2br($row['shipping_address']) ?></div>
                                            </td>
                                            <td>
                                                <div class="text-nowrap"><?= date('d F Y H:i', strtotime($row['created_at'])) ?></div>
                                            </td>
                                            <td>
                                                <div class="text-nowrap">
                                                    <?php if ($row['payment_proof']) { ?>
                                                        <a href="<?= BASE_URL . $row['payment_proof'] ?>" target="_blank">Lihat Bukti Transfer</a>
                                                    <?php } else { ?>
                                                        Belum ada
                                                    <?php } ?>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="text-nowrap">
                                                    <?php if ($row['shipping_proof']) { ?>
                                                        <a href="<?= BASE_URL . $row['shipping_proof'] ?>" target="_blank">Lihat Bukti Pengiriman</a>
                                                    <?php } else { ?>
                                                        Belum ada
                                                    <?php } ?>
                                                </div>
                                            </td>
                                            <td>
                                                <a class="btn btn-pink text-white" href="order_detail?id=<?= $row['order_id'] ?>">Detail</a>
                                                <?php if ($row['order_status'] !== 'completed' && $row['order_status'] !== 'canceled') { ?>
                                                    <button type="button" class="btn btn-primary" onclick="updateOrderStatus(<?= $row['order_id'] ?>, 'completed')">Selesai</button>
                                                <?php } ?>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /.col-->
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="shippingProofModal" tabindex="-1" aria-labelledby="shippingProofModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="shippingProofModalLabel">Tambah Bukti Pengiriman</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="shippingProofForm" enctype="multipart/form-data">
                    <input type="hidden" class="form-control" id="order_id" name="order_id" readonly>
                    <div class="mb-3">
                        <label for="shipping_proof" class="form-label">Bukti Pengiriman</label>
                        <input type="file" class="form-control" id="shipping_proof" name="shipping_proof">
                    </div>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function updateOrderStatus(order_id, status) {
        $.ajax({
            url: '/api/update_order_status',
            method: 'POST',
            data: {
                order_id: order_id,
                status: status
            },
            success: function(response) {
                var data = JSON.parse(response);
                if (data.status === 'success') {
                    alert(data.message);
                    if (status === 'completed') {
                        $('#order_id').val(order_id);
                        $('#shippingProofModal').modal({
                            backdrop: 'static',
                            keyboard: false
                        }).modal('show');
                    } else {
                        window.location.reload();
                    }
                } else {
                    alert('Terjadi kesalahan: ' + data.message);
                }
            },
            error: function() {
                alert('Terjadi kesalahan saat menghubungi server!');
            }
        });
    }

    $(document).ready(function() {
        $('#shippingProofForm').on('submit', function(e) {
            e.preventDefault();
            var formData = new FormData(this);
            $.ajax({
                url: '/api/upload_shipping_proof',
                method: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                success: function(response) {
                    var data = JSON.parse(response);
                    if (data.status === 'success') {
                        alert(data.message);
                        $('#shippingProofModal').modal('hide');
                        window.location.reload();
                    } else {
                        alert('Terjadi kesalahan: ' + data.message);
                    }
                },
                error: function() {
                    alert('Terjadi kesalahan saat menghubungi server!');
                }
            });
        });
    });
</script>
<?php include_once 'templates/admin/footer.php'; ?>