<?php
$session = $_SESSION['login'] ?? false;
$admin = $_SESSION['is_admin'];
if (!$session || !$admin) {
    header('Location: admin/login');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $customer_name = $_POST['customer_name'];
        $username = $_POST['username'];
        $email = $_POST['email'];
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $profile_picture = $_FILES['profile_picture'];

        if (strlen($_POST['password']) < 8) {
            echo "<script>alert('Password harus 8 karakter!');window.location.href = 'customer_add';</script>";
            exit;
        }

        if ($profile_picture['error'] === 0) {
            $profile_picture_name = $profile_picture['name'];
            $profile_picture_tmp = $profile_picture['tmp_name'];
            $profile_picture_size = $profile_picture['size'];
            $profile_picture_ext = pathinfo($profile_picture_name, PATHINFO_EXTENSION);
            $profile_picture_target = 'uploads/profile/' . time() . '_' . $profile_picture_name;

            if ($profile_picture_size > 2097152) {
                echo "<script>alert('Ukuran gambar terlalu besar!');window.location.href = 'customer_add';</script>";
                exit;
            }

            if (!in_array($profile_picture_ext, ['jpg', 'jpeg', 'png'])) {
                echo "<script>alert('Format gambar tidak didukung!');window.location.href = 'customer_add';</script>";
                exit;
            }

            if (!move_uploaded_file($profile_picture_tmp, $profile_picture_target)) {
                echo "<script>alert('Gagal mengunggah gambar!');window.location.href = 'customer_add';</script>";
                exit;
            }
        } else {
            $profile_picture_target = null;
        }

        $stmt = $koneksi->prepare("INSERT INTO tb_customer (customer_name, username, email, password, profile_picture) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $customer_name, $username, $email, $password, $profile_picture_target);
        $stmt->execute();

        echo "<script>alert('Berhasil menambahkan customer!');window.location.href = 'customer';</script>";
        exit;
    } catch (Exception $e) {
        $message = htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
        echo "<script>alert('Terjadi kesalahan: " . json_encode($message) . "');window.location.href = 'customer_add';</script>";
        exit;
    }
}
include_once 'templates/admin/header.php'; ?>
<div class="body flex-grow-1">
    <div class="container-lg px-4">
        <div class="row">
            <div class="col-md-12">
                <div class="card mb-4">
                    <div class="card-header">Add Customer</div>
                    <div class="card-body">
                        <form method="POST" action="" enctype="multipart/form-data">
                            <div class="row">
                                <div class="col-6 mb-3">
                                    <label for="customer_name" class="form-label fw-bold">Customer Name</label>
                                    <input type="text" class="form-control" id="customer_name" name="customer_name" required>
                                </div>
                                <div class="col-6 mb-3">
                                    <label for="username" class="form-label fw-bold">Username</label>
                                    <input type="text" class="form-control" id="username" name="username" required>
                                </div>
                                <div class="col-6 mb-3">
                                    <label for="email" class="form-label fw-bold">Email</label>
                                    <input type="email" class="form-control" id="email" name="email" required>
                                </div>
                                <div class="col-6 mb-3">
                                    <label for="password" class="form-label fw-bold">Password</label>
                                    <input type="password" class="form-control" id="password" name="password" required>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="profile_picture" class="form-label fw-bold">Profile Picture</label>
                                <input type="file" class="form-control" id="profile_picture" name="profile_picture">
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