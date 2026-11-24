<?php
$session = $_SESSION['login'] ?? false;
$admin = $_SESSION['is_admin'];
if (!$session || !$admin) {
    header('Location: admin/login');
    exit;
}

$id = $_GET['id'] ?? null;
if (!$id) {
    echo "<script>alert('Product tidak ditemukan!');window.location.href = 'products';</script>";
    exit;
}

$stmt = $koneksi->prepare("SELECT * FROM tb_product WHERE product_id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$product = $result->fetch_assoc();

if (!$product) {
    echo "<script>alert('Product tidak ditemukan!');window.location.href = 'product';</script>";
    exit;
}
$category_id = $product['category_id'];
$brand_id = $product['brand_id'];
$product_name = $product['product_name'];
$product_price = $product['product_price'];
$product_description = $product['product_description'];
$product_status = $product['product_status'];
$product_image = $product['product_image'];
$product_stock = $product['stock'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $category_id = $_POST['category_id'];
        $brand_id = $_POST['brand_id'];
        $product_name = $_POST['product_name'];
        $product_price = $_POST['product_price'];
        $product_description = $_POST['product_description'];
        $product_status = $_POST['product_status'];
        $product_image = $_FILES['product_image'];
        $product_stock = $_POST['product_stock'];

        if ($product_image['error'] === 0) {
            $product_image_name = $product_image['name'];
            $product_image_tmp = $product_image['tmp_name'];
            $product_image_size = $product_image['size'];
            $product_image_ext = pathinfo($product_image_name, PATHINFO_EXTENSION);
            $product_image_target = 'uploads/products/' . time() . '_' . $product_image_name;

            if ($product_image_size > 5242880) {
                echo "<script>alert('Ukuran gambar terlalu besar!');window.location.href = 'product_edit?id=$id';</script>";
                exit;
            }

            if (!in_array($product_image_ext, ['jpg', 'jpeg', 'png', 'svg', 'webp'])) {
                echo "<script>alert('Format gambar tidak didukung!');window.location.href = 'product_edit?id=$id';</script>";
                exit;
            }

            if (!move_uploaded_file($product_image_tmp, $product_image_target)) {
                echo "<script>alert('Gagal mengunggah gambar!');window.location.href = 'product_edit?id=$id';</script>";
                exit;
            }
        } else {
            $product_image_target = $product['product_image'];
        }

        $stmt = $koneksi->prepare("UPDATE tb_product SET category_id = ?, brand_id = ?, product_name = ?, product_price = ?, product_description = ?, product_status = ?, product_image = ?, stock = ? WHERE product_id = ?");
        $stmt->bind_param("iisdsissi", $category_id, $brand_id, $product_name, $product_price, $product_description, $product_status, $product_image_target, $product_stock, $id);
        $stmt->execute();

        echo "<script>alert('Berhasil mengubah product!');window.location.href = 'products';</script>";
        exit;
    } catch (Exception $e) {
        $message = htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
        echo "<script>alert('Terjadi kesalahan: " . json_encode($message) . "');window.location.href = 'product_edit?id=$id';</script>";
        exit;
    }
}
include_once 'templates/admin/header.php'; ?>
<div class="body flex-grow-1">
    <div class="container-lg px-4">
        <div class="row">
            <div class="col-md-12">
                <div class="card mb-4">
                    <div class="card-header">Edit Product <?= $product_name ?></div>
                    <div class="card-body">
                        <form method="POST" action="" enctype="multipart/form-data">
                            <div class="row">
                                <div class="mb-3 col-md-6">
                                    <label for="category_id" class="form-label required">Category</label>
                                    <select class="form-select" id="category_id" name="category_id" required>
                                        <option value="">Pilih Category</option>
                                        <?php
                                        $stmt = $koneksi->prepare("SELECT * FROM tb_category");
                                        $stmt->execute();
                                        $result = $stmt->get_result();
                                        while ($category = $result->fetch_assoc()) {
                                            $selected = $category_id == $category['id'] ? 'selected' : '';
                                            echo "<option value='$category[id]' $selected>$category[name]</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="mb-3 col-md-6">
                                    <label for="brand_id" class="form-label required">Brand</label>
                                    <select class="form-select" id="brand_id" name="brand_id" required>
                                        <option value="">Pilih Brand</option>
                                        <?php
                                        $stmt = $koneksi->prepare("SELECT * FROM tb_brand");
                                        $stmt->execute();
                                        $result = $stmt->get_result();
                                        while ($brand = $result->fetch_assoc()) {
                                            $selected = $brand_id == $brand['id'] ? 'selected' : '';
                                            echo "<option value='$brand[id]' $selected>$brand[name]</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="mb-3 col-md-6">
                                    <label for="product_name" class="form-label required">Product Name</label>
                                    <input type="text" class="form-control" id="product_name" name="product_name" value="<?= $product_name ?>" required>
                                </div>
                                <div class="mb-3 col-md-6">
                                    <label for="product_price" class="form-label required">Product Price</label>
                                    <input type="number" class="form-control" id="product_price" name="product_price" value="<?= $product_price ?>" required>
                                </div>
                                <div class="mb-3 col-md-6">
                                    <label for="product_status" class="form-label required">Product Status</label>
                                    <select class="form-select" id="product_status" name="product_status" required>
                                        <option value="1" <?= $product_status === 1 ? 'selected' : '' ?>>Aktif</option>
                                        <option value="0" <?= $product_status === 0 ? 'selected' : '' ?>>Tidak Aktif</option>
                                    </select>
                                </div>
                                <div class="mb-3 col-md-6">
                                    <label for="product_stock" class="form-label required">Product Stock</label>
                                    <input type="number" class="form-control" id="product_stock" name="product_stock" value="<?= $product_stock ?>" required min="0">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="product_description" class="form-label required">Product Description</label>
                                <textarea class="form-control" id="product_description" name="product_description" required><?= $product_description ?></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="product_image" class="form-label required">Product Image</label>
                                <input type="file" class="form-control" id="product_image" name="product_image">
                                <?php if ($product_image) { ?>
                                    <img src="<?= BASE_URL . $product_image ?>" class="img-fluid mt-3 rounded" alt="Product Image" style="max-height: 200px;">
                                <?php } ?>
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