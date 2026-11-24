<?php
$session = $_SESSION['login'] ?? false;
$admin = $_SESSION['is_admin'];
if (!$session || !$admin) {
    header('Location: /login');
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
                                <h4 class="card-title mb-0">List Banners</h4>
                            </div>
                            <div class="btn-toolbar d-none d-md-block" role="toolbar" aria-label="Toolbar with buttons">
                                <a href="banner_add" class="btn btn-primary">Tambah</a>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table border mb-0" id="dataTable">
                                <thead class="fw-semibold text-nowrap">
                                    <tr class="align-middle">
                                        <th class="bg-body-secondary">
                                            <svg class="icon">
                                                <use xlink:href="vendors/@coreui/icons/svg/free.svg#cil-people"></use>
                                            </svg>
                                        </th>
                                        <th class="bg-body-secondary">Title Banner</th>
                                        <th class="bg-body-secondary">Sub Title</th>
                                        <th class="bg-body-secondary"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $stmt = $koneksi->prepare("SELECT * FROM tb_banners");
                                    $stmt->execute();
                                    $result = $stmt->get_result();

                                    while ($row = $result->fetch_assoc()) {
                                        $image = $row['image'] ? BASE_URL . $row['image'] : BASE_URL . 'assets/img/default_banner.jpg';
                                    ?>
                                        <tr class="align-middle">
                                            <td class="text-center">
                                                <img src="<?= $image ?>" alt="image" class="img-fluid mb-3 rounded pt-3" style="max-width: 200px;">
                                            </td>
                                            <td>
                                                <div class="text-nowrap"><?= $row['title'] ?></div>
                                            </td>
                                            <td>
                                                <div class="text-nowrap"><?= $row['sub_title'] ?></div>
                                            </td>
                                            <td>
                                                <a class="dropdown-item" href="banner_edit?id=<?= $row['id'] ?>">Edit</a>
                                                <a class="dropdown-item text-danger" href="banner_delete?id=<?= $row['id'] ?>" onclick="return confirm('Apakah anda yakin ingin menghapus banner ini?')">Delete</a>
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