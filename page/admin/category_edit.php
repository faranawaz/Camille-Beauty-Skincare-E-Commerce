<?php
$session = $_SESSION['login'] ?? false;
$admin = $_SESSION['is_admin'];
if (!$session || !$admin) {
    header('Location: admin/login');
    exit;
}

$id = $_GET['id'] ?? null;
if (!$id) {
    echo "<script>alert('Category tidak ditemukan!');window.location.href = 'category';</script>";
    exit;
}

$stmt = $koneksi->prepare("SELECT * FROM tb_category WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$category = $result->fetch_assoc();

if (!$category) {
    echo "<script>alert('Category tidak ditemukan!');window.location.href = 'category';</script>";
    exit;
}
$name = $category['name'];
$icon = $category['icon'];

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
                echo "<script>alert('Ukuran gambar terlalu besar!');window.location.href = 'category_edit?id=$id';</script>";
                exit;
            }

            if (!in_array($icon_ext, ['jpg', 'jpeg', 'png', 'svg', 'webp'])) {
                echo "<script>alert('Format gambar tidak didukung!');window.location.href = 'category_edit?id=$id';</script>";
                exit;
            }

            if (!move_uploaded_file($icon_tmp, $icon_target)) {
                echo "<script>alert('Gagal mengunggah gambar!');window.location.href = 'category_edit?id=$id';</script>";
                exit;
            }
        } else {
            $icon_target = $category['icon'];
        }

        $stmt = $koneksi->prepare("UPDATE tb_category SET name = ?, icon = ? WHERE id = ?");
        $stmt->bind_param("ssi", $name, $icon_target, $id);
        $stmt->execute();

        echo "<script>alert('Berhasil mengubah category!');window.location.href = 'category';</script>";
        exit;
    } catch (Exception $e) {
        $message = htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
        echo "<script>alert('Terjadi kesalahan: " . json_encode($message) . "');window.location.href = 'category_edit?id=$id';</script>";
        exit;
    }
}
include_once 'templates/admin/header.php'; ?>
<div class="body flex-grow-1">
    <div class="container-lg px-4">
        <div class="row">
            <div class="col-md-12">
                <div class="card mb-4">
                    <div class="card-header">Edit Category <?= $name ?></div>
                    <div class="card-body">
                        <form method="POST" action="" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label for="name" class="form-label fw-bold">Category Name</label>
                                <input type="text" class="form-control" id="name" name="name" value="<?= $name ?>">
                            </div>
                            <div class="mb-3">
                                <label for="icon" class="form-label fw-bold">Icon</label>
                                <input type="file" class="form-control" id="icon" name="icon">
                                <?php if ($icon) { ?>
                                    <img src="<?= BASE_URL . $icon ?>" alt="Icon" class="img-fluid mb-3 rounded pt-3" style="max-height: 200px;">
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