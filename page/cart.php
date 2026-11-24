<?php
$session = $_SESSION['customer_login'] ?? false;
$customer = $_SESSION['customer_id'];

if (!$session || !$customer) {
    header('Location: login');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $address = $_POST['address'];
        $payment_proof = $_FILES['payment_proof'];

        if ($payment_proof['error'] === 0) {
            $payment_proof_name = $payment_proof['name'];
            $payment_proof_tmp = $payment_proof['tmp_name'];
            $payment_proof_size = $payment_proof['size'];
            $payment_proof_ext = pathinfo($payment_proof_name, PATHINFO_EXTENSION);
            $payment_proof_target = 'uploads/payment/' . time() . '_' . $payment_proof_name;

            if ($payment_proof_size > 9000000) {
                echo "<script>alert('Ukuran gambar terlalu besar!');window.location.href = 'cart';</script>";
                exit;
            }

            if (!in_array($payment_proof_ext, ['jpg', 'jpeg', 'png'])) {
                echo "<script>alert('Format gambar tidak didukung!');window.location.href = 'cart';</script>";
                exit;
            }

            if (!move_uploaded_file($payment_proof_tmp, $payment_proof_target)) {
                echo "<script>alert('Gagal mengunggah gambar!');window.location.href = 'cart';</script>";
                exit;
            }
        }

        $orderNo = 'ORD' . date('Ymd') . rand(1000, 9999);
        $total = 0;

        $stmt = $koneksi->prepare("SELECT SUM(tb_product.product_price * tb_cart.quantity) AS total FROM tb_cart JOIN tb_product ON tb_cart.product_id = tb_product.product_id WHERE customer_id = ?");
        $stmt->bind_param("i", $_SESSION['customer_id']);
        $stmt->execute();
        $result = $stmt->get_result();
        $total = $result->fetch_assoc()['total'];

        $stmtOrder = $koneksi->prepare("INSERT INTO tb_orders (order_no, customer_id, order_total, order_status, shipping_address, payment_proof) VALUES (?, ?, ?, 'pending', ?, ?)");
        $stmtOrder->bind_param("siiss", $orderNo, $customer, $total, $address, $payment_proof_target);
        $stmtOrder->execute();

        $stmtCart = $koneksi->prepare("SELECT * FROM tb_cart JOIN tb_product ON tb_cart.product_id = tb_product.product_id WHERE customer_id = ?");
        $stmtCart->bind_param("i", $customer);
        $stmtCart->execute();
        $result = $stmtCart->get_result();
        $orderId = $stmtOrder->insert_id;

        while ($cart = $result->fetch_assoc()) {
            $stmtOrderItem = $koneksi->prepare("INSERT INTO tb_order_items (order_id, product_id, quantity, product_price) VALUES (?, ?, ?, ?)");
            $stmtOrderItem->bind_param("iiid", $orderId, $cart['product_id'], $cart['quantity'], $cart['product_price']);
            $stmtOrderItem->execute();

            $stmtProduct = $koneksi->prepare("UPDATE tb_product SET stock = stock - ? WHERE product_id = ?");
            $stmtProduct->bind_param("ii", $cart['quantity'], $cart['product_id']);
            $stmtProduct->execute();
        }

        $stmt = $koneksi->prepare("DELETE FROM tb_cart WHERE customer_id = ?");
        $stmt->bind_param("i", $customer);
        $stmt->execute();

        echo "<script>alert('Berhasil checkout!');window.location.href = 'transaction';</script>";
        exit;
    } catch (Exception $e) {
        $message = htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
        echo "<script>alert('Terjadi kesalahan: " . json_encode($message) . "');window.location.href = 'cart';</script>";
        exit;
    }
}

include 'templates/app/header.php';
?>
<div class="container-fluid">
    <div class="row d-flex justify-content-center align-items-center h-100">
        <div class="col-10">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="fw-normal mb-0">Keranjang Belanja</h3>
                <a href="cara-pembayaran" class="text-link">Tata Cara Pengiriman</a>
            </div>

            <div class="row">
                <div class="col-md-8">
                    <?php
                    $stmt = $koneksi->prepare("SELECT * FROM tb_cart JOIN tb_product ON tb_cart.product_id = tb_product.product_id JOIN tb_brand ON tb_product.brand_id = tb_brand.id WHERE customer_id = ? ORDER BY cart_id DESC");
                    $stmt->bind_param("i", $_SESSION['customer_id']);
                    $stmt->execute();
                    $result = $stmt->get_result();

                    while ($cart = $result->fetch_assoc()) {
                        $product_image = file_exists($cart['product_image']) ? BASE_URL . $cart['product_image'] : BASE_URL . 'assets/img/default_category.png';
                    ?>
                        <div class="card rounded-3 mb-4">
                            <div class="card-body p-4">
                                <div class="row d-flex justify-content-between align-items-center">
                                    <div class="col-md-2 col-lg-2 col-xl-2">
                                        <img src="<?= $product_image ?>" class="img-fluid rounded-3" alt="Cotton T-shirt">
                                    </div>
                                    <div class="col-md-3 col-lg-3 col-xl-3">
                                        <p class="mb-2"><?= $cart['product_name'] ?></p>
                                        <p><span class="text-muted">Brand:</span> <?= $cart['name'] ?></p>
                                    </div>
                                    <div class="col-md-3 d-flex">
                                        <form action="updateQuantity?id=<?= $cart['cart_id'] ?>&action=min" method="POST">
                                            <button data-mdb-button-init data-mdb-ripple-init class="btn btn-link btn-sm px-2" onclick="this.parentNode.querySelector('input[type=number]').stepDown()">
                                                <i class="fas fa-minus"></i>
                                            </button>
                                        </form>

                                        <?php
                                        $max = $cart['stock'];
                                        $quantity = intval($cart['quantity']);
                                        ?>
                                        <input id="form1" min="1" name="quantity" max="<?= $max ?>" value="<?= $quantity ?>" type="number" class="form-control text-center form-control-sm" readonly />

                                        <form action="updateQuantity?id=<?= $cart['cart_id'] ?>&action=add" method="POST">
                                            <button data-mdb-button-init data-mdb-ripple-init class="btn btn-link btn-sm px-2" onclick="this.parentNode.querySelector('input[type=number]').stepUp()">
                                                <i class="fas fa-plus"></i>
                                            </button>
                                        </form>
                                    </div>
                                    <div class="col-md-3 col-lg-2 col-xl-2 offset-lg-1">
                                        <h6 class="mb-0">
                                            <?php
                                            $price = $cart['product_price'] * $quantity;
                                            echo 'Rp. ' . number_format($price, 0, ',', '.');
                                            ?>
                                        </h6>
                                    </div>
                                    <div class="col-md-1 col-lg-1 col-xl-1 text-end">
                                        <a href="deleteCart?id=<?= $cart['cart_id'] ?>" class="text-danger btn-sm btn" onclick="return confirm('Apakah anda yakin ingin menghapus item ini?')">
                                            <i class="fas fa-trash fa-lg"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php
                    }
                    if ($result->num_rows == 0) {
                    ?>
                        <div class="card rounded-3 mb-4">
                            <div class="card-body p-4">
                                <div class="text-center">
                                    <h5 class="fw-normal mb-3">Keranjang belanja masih kosong</h5>
                                    <a href="index" class="btn btn-pink">Belanja Sekarang</a>
                                </div>
                            </div>
                        </div>
                    <?php } ?>

                </div>

                <div class="col-md-4 card mb-4">
                    <div class="card-body">
                        <h5 class="fw-normal mb-3">Alamat Pengiriman</h5>
                        <form action="" method="POST" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label for="payment_method" class="form-label">Metode Pembayaran</label>
                                <div class="alert alert-primary" role="alert">
                                    <h6 class="alert-heading">Silahkan transfer ke rekening berikut</h6>
                                    <p>BCA 1234567890 a/n PT. Toko Online</p>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="address" class="form-label">Alamat</label>
                                <textarea class="form-control" id="address" name="address" rows="3" required></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="payment_proof" class="form-label fw-bold">Bukti Transfer</label>
                                <input type="file" class="form-control" id="payment_proof" name="payment_proof" required>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="fw-bold mb-0">Total</h5>
                                <h5 class="fw-bold mb-0">
                                    <?php
                                    $stmt = $koneksi->prepare("SELECT SUM(tb_product.product_price * tb_cart.quantity) AS total FROM tb_cart JOIN tb_product ON tb_cart.product_id = tb_product.product_id WHERE customer_id = ?");
                                    $stmt->bind_param("i", $_SESSION['customer_id']);
                                    $stmt->execute();
                                    $result = $stmt->get_result();
                                    $total = $result->fetch_assoc()['total'] ?? 0;
                                    echo 'Rp. ' . number_format($total, 0, ',', '.');
                                    ?>
                                </h5>
                            </div>
                            <div class="mb-3 d-flex justify-content-end pt-3">
                                <button type="submit" class="btn btn-pink text-white">Checkout</button>
                            </div>
                        </form>

                    </div>
                </div>
            </div>



        </div>
    </div>
</div>

<?php

include 'templates/app/footer.php';
?>