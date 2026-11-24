<?php
$session = $_SESSION['login'] ?? false;
$admin = $_SESSION['is_admin'];
if (!$session || !$admin) {
    header('Location: admin/login');
    exit;
}

include_once 'templates/admin/header.php'; ?>
<div class="body flex-grow-1">
    <div class="container-lg px-4">
        <div class="row">
            <div class="col-md-12">
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h4 class="card-title mb-0">List Customer</h4>
                            </div>
                            <div class="btn-toolbar d-none d-md-block" role="toolbar" aria-label="Toolbar with buttons">
                                <a href="customer_add" class="btn btn-primary">Tambah</a>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table border mb-0" id="dataTable">
                                <thead class="fw-semibold text-nowrap">
                                    <tr class="align-middle">
                                        <th class="bg-body-secondary text-center">
                                            <svg class="icon">
                                                <use xlink:href="vendors/@coreui/icons/svg/free.svg#cil-people"></use>
                                            </svg>
                                        </th>
                                        <th class="bg-body-secondary">Customer Name</th>
                                        <th class="bg-body-secondary text-center">Username</th>
                                        <th class="bg-body-secondary">Email</th>
                                        <th class="bg-body-secondary text-center">Nomor Telp</th>
                                        <th class="bg-body-secondary"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $stmt = $koneksi->prepare("SELECT * FROM tb_customer");
                                    $stmt->execute();
                                    $result = $stmt->get_result();

                                    while ($row = $result->fetch_assoc()) {
                                        $avatar = $row['profile_picture'] ? BASE_URL . $row['profile_picture'] : BASE_URL . 'assets/img/default_avatar.png';
                                    ?>
                                        <tr class="align-middle">
                                            <td class="text-center">
                                                <div class="avatar avatar-md">
                                                    <img class="avatar-img" src="<?= $avatar ?>" alt="avatar">
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <div class="text-nowrap"><?= $row['customer_name'] ?></div>
                                            </td>
                                            <td class="text-center">
                                                <div class="text-nowrap"><?= $row['username'] ?></div>
                                            </td>
                                            <td class="text-center">
                                                <div class="text-nowrap"><?= $row['email'] ?></div>
                                            </td>
                                            <td class="text-center">
                                                <div class="text-nowrap"><?= $row['telp'] ?></div>
                                            </td>
                                            <td>
                                                <a class="dropdown-item" href="customer_edit?id=<?= $row['customer_id'] ?>">Edit</a>
                                                <a class="dropdown-item text-danger" href="customer_delete?id=<?= $row['customer_id'] ?>" onclick="return confirm('Apakah anda yakin ingin menghapus customer ini?')">Delete</a>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /.col-->
        </div>
    </div>
</div>
<?php include_once 'templates/admin/footer.php'; ?>