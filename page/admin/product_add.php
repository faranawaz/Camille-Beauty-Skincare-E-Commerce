<?php
$session = $_SESSION['login'] ?? false;
$admin = $_SESSION['is_admin'];
if (!$session || !$admin) {
    header('Location: admin/login');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $category_id = $_POST['category_id'];
        $brand_id = $_POST['brand_id'];
        $product_name = $_POST['product_name'];
        $product_price = $_POST['product_price'];
        $product_description = $_POST['product_description'];
        $product_status = $_POST['product_status'];
        $date_created = date('Y-m-d H:i:s');
        $product_image = $_FILES['product_image'];
        $stock = $_POST['product_stock'];

        if ($product_image['error'] === 0) {
            // $product_image_name = $product_image['name'];
            $product_image_name = "products";
            $product_image_tmp = $product_image['tmp_name'];
            $product_image_size = $product_image['size'];
            $product_image_ext = pathinfo($product_image['name'], PATHINFO_EXTENSION);
            $product_image_target = 'uploads/products/' . time() . '_' . $product_image_name;

            if ($product_image_size > 5242880) {
                echo "<script>alert('Ukuran gambar terlalu besar!');window.location.href = 'product_add';</script>";
                exit;
            }

            if (!in_array($product_image_ext, ['jpg', 'jpeg', 'png', 'svg', 'webp'])) {
                echo "<script>alert('Format gambar tidak didukung!');window.location.href = 'product_add';</script>";
                exit;
            }

            if (!move_uploaded_file($product_image_tmp, $product_image_target)) {
                echo "<script>alert('Gagal mengunggah gambar!');window.location.href = 'product_add';</script>";
                exit;
            }
        } else {
            $product_image_target = null;
        }

        $stmt = $koneksi->prepare("INSERT INTO tb_product (category_id, brand_id, product_name, product_price, product_description, product_status, stock, date_created, product_image) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("iisdssiss", $category_id, $brand_id, $product_name, $product_price, $product_description, $product_status, $stock, $date_created, $product_image_target);
        $stmt->execute();

        echo "<script>alert('Berhasil menambahkan produk!');window.location.href = 'products';</script>";
        exit;
    } catch (Exception $e) {
        $message = htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
        echo "<script>alert('Terjadi kesalahan: " . json_encode($message) . "');window.location.href = 'product_add';</script>";
        exit;
    }
}
include_once 'templates/admin/header.php'; ?>
<div class="body flex-grow-1">
    <div class="container-lg px-4">
        <div class="row">
            <div class="col-md-12">
                <div class="card mb-4">
                    <div class="card-header">Add Product</div>
                    <div class="card-body">
                        <form method="POST" action="" enctype="multipart/form-data">
                            <div class="row">
                                <div class="mb-3 col-md-6">
                                    <label for="category_id" class="form-label fw-bold">Category</label>
                                    <select class="form-select" id="category_id" name="category_id" required>
                                        <option value="" selected disabled>Pilih Category</option>
                                        <?php
                                        $stmt = $koneksi->prepare("SELECT * FROM tb_category");
                                        $stmt->execute();
                                        $result = $stmt->get_result();
                                        while ($category = $result->fetch_assoc()) { ?>
                                            <option value="<?= $category['id'] ?>"><?= $category['name'] ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <div class="mb-3 col-md-6">
                                    <label for="brand_id" class="form-label fw-bold">Brand</label>
                                    <select class="form-select" id="brand_id" name="brand_id" required>
                                        <option value="" selected disabled>Pilih Brand</option>
                                        <?php
                                        $stmt = $koneksi->prepare("SELECT * FROM tb_brand");
                                        $stmt->execute();
                                        $result = $stmt->get_result();
                                        while ($brand = $result->fetch_assoc()) { ?>
                                            <option value="<?= $brand['id'] ?>"><?= $brand['name'] ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <div class="mb-3 col-md-6">
                                    <label for="product_name" class="form-label fw-bold">Product Name</label>
                                    <input type="text" class="form-control" id="product_name" name="product_name" required>
                                </div>
                                <div class="mb-3 col-md-6">
                                    <label for="product_price" class="form-label fw-bold">Product Price</label>
                                    <input type="number" class="form-control" id="product_price" name="product_price" required>
                                </div>
                                <div class="mb-3 col-md-6">
                                    <label for="product_status" class="form-label fw-bold">Product Status</label>
                                    <select class="form-select" id="product_status" name="product_status" required>
                                        <option value="">Pilih Status</option>
                                        <option value="1">Aktif</option>
                                        <option value="0">Tidak Aktif</option>
                                    </select>
                                </div>
                                <div class="mb-3 col-md-6">
                                    <label for="product_stock" class="form-label fw-bold">Product Stock</label>
                                    <input type="number" class="form-control" id="product_stock" name="product_stock" required min="0">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="product_description" class="form-label fw-bold">Product Description</label>
                                <textarea class="form-control" id="description" name="product_description" required></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="product_image" class="form-label fw-bold">Product Image</label>
                                <input type="file" class="form-control" id="product_image" name="product_image">
                            </div>

                            <div class="mb-3 d-flex justify-content-end">
                                <button type="submit" class="btn btn-primary">Simpan</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include_once 'templates/admin/footer.php'; ?>