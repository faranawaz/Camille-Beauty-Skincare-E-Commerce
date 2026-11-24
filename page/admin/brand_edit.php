<?php
$session = $_SESSION['login'] ?? false;
$admin = $_SESSION['is_admin'];
if (!$session || !$admin) {
    header('Location: admin/login');
    exit;
}

$id = $_GET['id'] ?? null;
if (!$id) {
    echo "<script>alert('Brand tidak ditemukan!');window.location.href = 'brand';</script>";
    exit;
}

$stmt = $koneksi->prepare("SELECT * FROM tb_brand WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$brand = $result->fetch_assoc();

if (!$brand) {
    echo "<script>alert('brand tidak ditemukan!');window.location.href = 'brand';</script>";
    exit;
}
$name = $brand['name'];
$image = $brand['image'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $name = $_POST['name'];
        $image = $_FILES['image'];

        if ($image['error'] === 0) {
            $image_name = $image['name'];
            $image_tmp = $image['tmp_name'];
            $image_size = $image['size'];
            $image_ext = pathinfo($image_name, PATHINFO_EXTENSION);
            $image_target = 'uploads/brand/' . time() . '_' . $image_name;

            if ($image_size > 2097152) {
                echo "<script>alert('Ukuran gambar terlalu besar!');window.location.href = 'brand_edit?id=$id';</script>";
                exit;
            }

            if (!in_array($icon_ext, ['jpg', 'jpeg', 'png', 'svg', 'webp'])) {
                echo "<script>alert('Format gambar tidak didukung!');window.location.href = 'brand_edit?id=$id';</script>";
                exit;
            }

            if (!move_uploaded_file($icon_tmp, $image_target)) {
                echo "<script>alert('Gagal mengunggah gambar!');window.location.href = 'brand_edit?id=$id';</script>";
                exit;
            }
        } else {
            $image_target = $brand['image'];
        }

        $stmt = $koneksi->prepare("UPDATE tb_brand SET name = ?, image = ? WHERE id = ?");
        $stmt->bind_param("ssi", $name, $image_target, $id);
        $stmt->execute();

        echo "<script>alert('Berhasil mengubah brand!');window.location.href = 'brand';</script>";
        exit;
    } catch (Exception $e) {
        $message = htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
        echo "<script>alert('Terjadi kesalahan: " . json_encode($message) . "');window.location.href = 'brand_edit?id=$id';</script>";
        exit;
    }
}
include_once 'templates/admin/header.php'; ?>
<div class="body flex-grow-1">
    <div class="container-lg px-4">
        <div class="row">
            <div class="col-md-12">
                <div class="card mb-4">
                    <div class="card-header">Edit Brand <?= $name ?></div>
                    <div class="card-body">
                        <form method="POST" action="" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label for="name" class="form-label fw-bold">Brand Name</label>
                                <input type="text" class="form-control" id="name" name="name" value="<?= $name ?>">
                            </div>
                            <div class="mb-3">
                                <label for="image" class="form-label fw-bold">Image</label>
                                <input type="file" class="form-control" id="image" name="image">
                                <?php if ($image) { ?>
                                    <img src="<?= BASE_URL . $image ?>" alt="image" class="img-fluid mb-3 rounded pt-3" style="max-height: 200px;">
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