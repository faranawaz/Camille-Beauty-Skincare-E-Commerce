<?php
$session = $_SESSION['login'] ?? false;
$admin = $_SESSION['is_admin'];
if (!$session || !$admin) {
    header('Location: admin/login');
    exit;
}

$id = $_GET['id'] ?? null;
if (!$id) {
    echo "<script>alert('Admin tidak ditemukan!');window.location.href = 'admins';</script>";
    exit;
}

$stmt = $koneksi->prepare("SELECT * FROM tb_admin WHERE admin_id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$admin = $result->fetch_assoc();

if (!$admin) {
    echo "<script>alert('Admin tidak ditemukan!');window.location.href = 'admins';</script>";
    exit;
}

$admin_name = $admin['admin_name'];
$username = $admin['username'];
$admin_telp = $admin['admin_telp'];
$admin_email = $admin['admin_email'];
$admin_address = $admin['admin_address'];
$profile_picture = $admin['profile_picture'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $admin_name = $_POST['admin_name'];
        $username = $_POST['username'];
        $admin_telp = $_POST['admin_telp'];
        $admin_email = $_POST['admin_email'];
        $admin_address = $_POST['admin_address'];
        $password = $admin['password'];
        if ($_POST['password']) {
            $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        }
        $profile_picture = $_FILES['profile_picture'];

        if ($profile_picture['error'] === 0) {
            $profile_picture_name = $profile_picture['name'];
            $profile_picture_tmp = $profile_picture['tmp_name'];
            $profile_picture_size = $profile_picture['size'];
            $profile_picture_ext = pathinfo($profile_picture_name, PATHINFO_EXTENSION);
            $profile_picture_target = 'uploads/profile/' . time() . '_' . $profile_picture_name;

            if ($profile_picture_size > 2097152) {
                echo "<script>alert('Ukuran gambar terlalu besar!');window.location.href = 'admin_edit?id=$id';</script>";
                exit;
            }

            if (!in_array($profile_picture_ext, ['jpg', 'jpeg', 'png'])) {
                echo "<script>alert('Format gambar tidak didukung!');window.location.href = 'admin_edit?id=$id';</script>";
                exit;
            }

            if (!move_uploaded_file($profile_picture_tmp, $profile_picture_target)) {
                echo "<script>alert('Gagal mengunggah gambar!');window.location.href = 'admin_edit?id=$id';</script>";
                exit;
            }
        } else {
            $profile_picture_target = $admin['profile_picture'];
        }

        $stmt = $koneksi->prepare("UPDATE tb_admin SET admin_name = ?, username = ?, admin_telp = ?, admin_email = ?, admin_address = ?, password = ?, profile_picture = ? WHERE admin_id = ?");
        $stmt->bind_param("sssssssi", $admin_name, $username, $admin_telp, $admin_email, $admin_address, $password, $profile_picture_target, $id);
        $stmt->execute();

        echo "<script>alert('Berhasil mengubah admin!');window.location.href = 'admins';</script>";
        exit;
    } catch (Exception $e) {
        $message = htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
        echo "<script>alert('Terjadi kesalahan: " . json_encode($message) . "');window.location.href = 'customer_edit?id=$id';</script>";
        exit;
    }
}
include_once 'templates/admin/header.php'; ?>
<div class="body flex-grow-1">
    <div class="container-lg px-4">
        <div class="row">
            <div class="col-md-12">
                <div class="card mb-4">
                    <div class="card-header">Edit Admin <?= $admin_name ?></div>
                    <div class="card-body">
                        <form method="POST" action="" enctype="multipart/form-data">
                            <div class="row">
                                <div class="mb-3 col-md-6">
                                    <label for="admin_name" class="form-label fw-bold">Admin Name</label>
                                    <input type="text" class="form-control" id="admin_name" name="admin_name" value="<?= $admin_name ?>" required>
                                </div>
                                <div class="mb-3 col-md-6">
                                    <label for="username" class="form-label fw-bold">Username</label>
                                    <input type="text" class="form-control" id="username" name="username" value="<?= $username ?>" required>
                                </div>
                                <div class="mb-3 col-md-6">
                                    <label for="admin_telp" class="form-label fw-bold">Admin Telp</label>
                                    <input type="text" class="form-control" id="admin_telp" name="admin_telp" value="<?= $admin_telp ?>" required>
                                </div>
                                <div class="mb-3 col-md-6">
                                    <label for="admin_email" class="form-label fw-bold">Admin Email</label>
                                    <input type="email" class="form-control" id="admin_email" name="admin_email" value="<?= $admin_email ?>" required>
                                </div>

                                <div class="mb-3 col-md-6">
                                    <label for="password" class="form-label fw-bold">Password</label>
                                    <input type="password" class="form-control" id="password" name="password">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="admin_address" class="form-label fw-bold">Admin Address</label>
                                <textarea class="form-control" id="admin_address" name="admin_address" rows="3"><?= $admin_address ?></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="profile_picture" class="form-label fw-bold">Profile Picture</label>
                                <input type="file" class="form-control" id="profile_picture" name="profile_picture">
                                <?php if ($profile_picture) { ?>
                                    <img src="<?= BASE_URL . $profile_picture ?>" alt="Profile Picture" class="img-fluid mb-3 rounded pt-3" style="max-height: 200px;">
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