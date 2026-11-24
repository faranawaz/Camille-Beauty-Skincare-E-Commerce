<?php
$session = $_SESSION['login'] ?? false;
$admin = $_SESSION['is_admin'];
if (!$session || !$admin) {
    header('Location: /login');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $title = $_POST['title'];
        $sub_title = $_POST['sub_title'];
        $image = $_FILES['image'];

        if ($image['error'] === 0) {
            $image_name = $image['name'];
            $image_tmp = $image['tmp_name'];
            $image_size = $image['size'];
            $image_ext = pathinfo($image_name, PATHINFO_EXTENSION);
            $image_target = 'uploads/banners/' . time() . '_' . $image_name;

            if ($image_size > 5242880) {
                echo "<script>alert('Ukuran gambar terlalu besar!');window.location.href = 'banner_add';</script>";
                exit;
            }

            if (!in_array($image_ext, ['jpg', 'jpeg', 'png', 'svg', 'webp'])) {
                echo "<script>alert('Format gambar tidak didukung!');window.location.href = 'banner_add';</script>";
                exit;
            }

            if (!move_uploaded_file($image_tmp, $image_target)) {
                echo "<script>alert('Gagal mengunggah gambar!');window.location.href = 'banner_add';</script>";
                exit;
            }
        } else {
            $image_target = null;
        }

        $stmt = $koneksi->prepare("INSERT INTO tb_banners (title, sub_title, image) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $title, $sub_title, $image_target);
        $stmt->execute();

        echo "<script>alert('Berhasil menambahkan banner!');window.location.href = 'banner';</script>";
        exit;
    } catch (Exception $e) {
        $message = htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
        echo "<script>alert('Terjadi kesalahan: " . json_encode($message) . "');window.location.href = 'banner_add';</script>";
        exit;
    }
}
include_once 'templates/admin/header.php'; ?>
<div class="body flex-grow-1">
    <div class="container-lg px-4">
        <div class="row">
            <div class="col-md-12">
                <div class="card mb-4">
                    <div class="card-header">Add Banner</div>
                    <div class="card-body">
                        <form method="POST" action="" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label for="title" class="form-label fw-bold">Title</label>
                                <input type="text" class="form-control" id="title" name="title">
                            </div>
                            <div class="mb-3">
                                <label for="sub_title" class="form-label fw-bold">Sub Title</label>
                                <input type="text" class="form-control" id="sub_title" name="sub_title">
                            </div>
                            <div class="mb-3">
                                <label for="image" class="form-label fw-bold">Image</label>
                                <input type="file" class="form-control" id="image" name="image" required>
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