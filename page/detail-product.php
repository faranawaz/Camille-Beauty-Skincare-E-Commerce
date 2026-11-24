<?php

include_once 'templates/app/header.php';

$stmt = $koneksi->prepare("SELECT 
    tb_product.product_id,
    tb_product.product_name,
    tb_product.product_image,
    tb_product.product_description,
    tb_product.product_price,
    tb_product.stock,
    tb_category.name AS product_category,
    tb_brand.name AS product_brand
 FROM tb_product JOIN tb_category ON tb_product.category_id = tb_category.id JOIN tb_brand ON tb_product.brand_id = tb_brand.id WHERE product_id = ?");
$stmt->bind_param("i", $_GET['id']);
$stmt->execute();

$result = $stmt->get_result();

$product = $result->fetch_assoc();

if (!$product) {
    echo "<script>alert('Product tidak ditemukan!');window.location.href = 'product';</script>";
    exit;
}

$productImage = file_exists($product['product_image']) ? BASE_URL . $product['product_image'] : BASE_URL . 'assets/img/default_category.png';
?>
<div class="body flex-grow-1">
    <div class="container-lg px-4">
        <div class="row">
            <div class="col-md-12">
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h4 class="card-title mb-0">Detail Product</h4>
                            </div>
                            <div class="btn-toolbar d-none d-md-block" role="toolbar" aria-label="Toolbar with buttons">

                                <h4 class="fw-bold text-primary">Rp <?= number_format($product['product_price'], 0, ',', '.') ?></h4>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-4">
                                <img src="<?= $productImage ?>" alt="Product Image" class="img-fluid rounded">
                            </div>
                            <div class="col-md-8">
                                <h4 class="fw-bold"><?= $product['product_name'] ?></h4>
                                <div class="row">
                                    <div class="col-md-6">
                                        <p class="fw-bold">Category:</p>
                                        <p class="mb-2"><?= $product['product_category'] ?></p>
                                    </div>
                                    <div class="col-md-6">
                                        <p class="fw-bold">Brand:</p>
                                        <p class="mb-2"><?= $product['product_brand'] ?></p>
                                    </div>
                                </div>
                                <p class="fw-bold">Description:</p>
                                <p class="text-muted mb-2"><?= nl2br($product['product_description']) ?></p>
                                <div class="d-flex justify-content-between">
                                    <p class="fw-bold">Stock:</p>
                                    <p class="mb-2">
                                        <?php if ($product['stock'] > 0) { ?>
                                            <?= $product['stock'] ?> pcs
                                        <?php } else { ?>
                                            Out of Stock
                                        <?php } ?>
                                    </p>
                                    <a href="addToCart?id=<?= $product['product_id'] ?>" class="btn btn-pink text-white">Add to Cart</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php

include_once 'templates/app/footer.php';

?>