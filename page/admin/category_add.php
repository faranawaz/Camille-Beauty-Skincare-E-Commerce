<?php
$session = $_SESSION['login'] ?? false;
$admin = $_SESSION['is_admin'];
if (!$session || !$admin) {
    header('Location: admin/login');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $name = $_POST['name'];
        $icon = $_FILES['icon'];

        if ($icon['error'] === 0) {
            $icon_name = $icon['name'];
            $icon_tmp = $icon['tmp_name'];
            $icon_size = $icon['size'];
            $icon_ext = pathinfo($icon_name, PATHINFO_EXTENSION);
            $icon_target = 'uploads/category/' . time() . '_' . $icon_name;

            if ($icon_size > 2097152) {
                echo "<script>alert('Ukuran gambar terlalu besar!');window.location.href = 'category_add';</script>";
                exit;
            }

            if (!in_array($icon_ext, ['jpg', 'jpeg', 'png', 'svg', 'webp'])) {
                echo "<script>alert('Format gambar tidak didukung!');window.location.href = 'category_add';</script>";
                exit;
            }

            if (!move_uploaded_file($icon_tmp, $icon_target)) {
                echo "<script>alert('Gagal mengunggah gambar!');window.location.href = 'category_add';</script>";
                exit;
            }
        } else {
            $icon_target = null;
        }

        $stmt = $koneksi->prepare("INSERT INTO tb_category (name, icon) VALUES (?, ?)");
        $stmt->bind_param("ss", $name, $icon_target);
        $stmt->execute();

        echo "<script>alert('Berhasil menambahkan category!');window.location.href = 'category';</script>";
        exit;
    } catch (Exception $e) {
        $message = htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
        echo "<script>alert('Terjadi kesalahan: " . json_encode($message) . "');window.location.href = 'category_add';</script>";
        exit;
    }
}
include_once 'templates/admin/header.php'; ?>
<div class="body flex-grow-1">
    <div class="container-lg px-4">
        <div class="row">
            <div class="col-md-12">
                <div class="card mb-4">
                    <div class="card-header">Add Category</div>
                    <div class="card-body">
                        <form method="POST" action="" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label for="name" class="form-label fw-bold">Kategory</label>
                                <input type="text" class="form-control" id="name" name="name" required>
                            </div>
                            <div class="mb-3">
                                <label for="icon" class="form-label fw-bold">Icon</label>
                                <input type="file" class="form-control" id="icon" name="icon">
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