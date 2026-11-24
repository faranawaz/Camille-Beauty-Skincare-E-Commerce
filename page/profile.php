<?php
$session = $_SESSION['customer_login'] ?? false;
$customerId = $_SESSION['customer_id'];
if (!$session || !$customerId) {
    header('Location: login');
    exit;
}

$stmt = $koneksi->prepare("SELECT * FROM tb_customer WHERE customer_id = ?");
$stmt->bind_param("i", $customerId);
$stmt->execute();
$result = $stmt->get_result();
$customer = $result->fetch_assoc();


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $customer_name = $_POST['customer_name'];
        $username = $_POST['username'];
        $email = $_POST['email'];
        $password = $customer['password'];
        $telp = $_POST['customer_telp'];
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

            if ($profile_picture_size > 5242880) {
                echo "<script>alert('Ukuran gambar terlalu besar!');window.location.href = 'profile';</script>";
                exit;
            }

            if (!in_array($profile_picture_ext, ['jpg', 'jpeg', 'png'])) {
                echo "<script>alert('Format gambar tidak didukung!');window.location.href = 'profile';</script>";
                exit;
            }

            if (!move_uploaded_file($profile_picture_tmp, $profile_picture_target)) {
                echo "<script>alert('Gagal mengunggah gambar!');window.location.href = 'profile';</script>";
                exit;
            }
        } else {
            $profile_picture_target = $customer['profile_picture'];
        }

        $stmt = $koneksi->prepare("UPDATE tb_customer SET telp = ?, customer_name = ?, username = ?, email = ?, password = ?, profile_picture = ? WHERE customer_id = ?");
        $stmt->bind_param("ssssssi", $telp, $customer_name, $username, $email, $password, $profile_picture_target, $customerId);
        $stmt->execute();

        echo "<script>alert('Berhasil mengubah profile!');window.location.href = 'profile';</script>";
        exit;
    } catch (Exception $e) {
        $message = htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
        echo "<script>alert('Terjadi kesalahan: " . json_encode($message) . "');window.location.href = 'profile';</script>";
        exit;
    }
}

include_once 'templates/app/header.php'; ?>

<div class="body flex-grow-1">
    <div class="container-lg px-4">

        <div class="row">
            <div class="col-md-12">
                <div class="card mb-4">
                    <div class="card-header">
                        Profile
                        <a href="logout" class="btn btn-danger text-white float-end" onclick="return confirm('Are you sure?')">Logout</a>
                    </div>
                    <div class="card-body">
                        <form action="" method="post" enctype="multipart/form-data">
                            <div class="mb-3">
                                <?php if ($customer['profile_picture']) { ?>
                                    <img src="<?= BASE_URL . $customer['profile_picture'] ?>" alt="Profile Picture" class="img-thumbnail mt-2 rounded-circle w-25 text-center">
                                <?php } ?>
                                <label for="profile_picture" class="form-label fw-bold">Profile Picture</label>
                                <input type="file" class="form-control" id="profile_picture" name="profile_picture" accept="image/*">

                            </div>
                            <div class="row">
                                <div class="mb-3 col-md-6">
                                    <label for="customer_name" class="form-label fw-bold">Customer Name</label>
                                    <input type="text" class="form-control" id="customer_name" name="customer_name" value="<?= $customer['customer_name'] ?>">
                                </div>
                                <div class="mb-3 col-md-6">
                                    <label for="customer_telp" class="form-label fw-bold">Nomor Telp</label>
                                    <input type="text" class="form-control" id="customer_telp" name="customer_telp" value="<?= $customer['telp'] ?>">
                                </div>
                                <div class="mb-3 col-md-6">
                                    <label for="username" class="form-label fw-bold">Username</label>
                                    <input type="text" class="form-control" id="username" name="username" value="<?= $customer['username'] ?>">
                                </div>
                                <div class="mb-3 col-md-6">
                                    <label for="email" class="form-label fw-bold">Email</label>
                                    <input type="email" class="form-control" id="email" name="email" value="<?= $customer['email'] ?>">
                                </div>
                                <div class="mb-3 col-md-6">
                                    <label for="password" class="form-label fw-bold">Password</label>
                                    <input type="password" class="form-control" id="password" name="password">
                                </div>
                            </div>

                            <div class="mb-3 d-flex justify-content-end">
                                <button type="submit" class="btn btn-pink text-white">Save</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card mb-4">
                    <div class="card-header">Order History</div>
                    <div class="card-body">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Order ID</th>
                                    <th>Order Date</th>
                                    <th>Order Status</th>
                                    <th>Bukti Pengiriman</th>
                                    <th>Total</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $stmt = $koneksi->prepare("SELECT * FROM tb_orders WHERE customer_id = ?");
                                $stmt->bind_param("i", $customerId);
                                $stmt->execute();
                                $result = $stmt->get_result();

                                while ($row = $result->fetch_assoc()) {
                                ?>
                                    <tr>
                                        <td><?= $row['order_no'] ?></td>
                                        <td><?= date('d F Y', strtotime($row['created_at'])) ?></td>
                                        <td>
                                            <?php
                                            if ($row['order_status'] == 'pending') {
                                                echo '<span class="badge bg-warning">Menunggu</span>';
                                            } else if ($row['order_status'] == 'shipping') {
                                                echo '<span class="badge bg-info">Dikirim</span>';
                                            } else if ($row['order_status'] == 'completed') {
                                                echo '<span class="badge bg-success">Telah Sampai</span>';
                                            } else {
                                                echo '<span class="badge bg-danger">DiBatalkan</span>';
                                            }
                                            ?>
                                        </td>
                                        <td>
                                            <?php if ($row['shipping_proof']) { ?>
                                                <a href="<?= BASE_URL . $row['shipping_proof'] ?>" target="_blank" class="btn btn-sm btn-pink text-white">Lihat</a>
                                            <?php } else {
                                                echo '<span class="badge bg-danger">Belum ada</span>';
                                            } ?>
                                        </td>
                                        <td><?= 'Rp ' . number_format($row['order_total'], 0, ',', '.') ?></td>
                                        <td>
                                            <a href="order_detail?id=<?= $row['order_id'] ?>" class="btn btn-sm btn-pink text-white">Detail</a>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<?php include_once 'templates/app/footer.php'; ?>