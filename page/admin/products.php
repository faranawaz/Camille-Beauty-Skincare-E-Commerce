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
                                <h4 class="card-title mb-0">List Products</h4>
                            </div>
                            <div class="btn-toolbar d-none d-md-block" role="toolbar" aria-label="Toolbar with buttons">
                                <a href="product_add" class="btn btn-primary">Tambah</a>
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
                                        <th class="bg-body-secondary">Product Name</th>
                                        <th class="bg-body-secondary">Price</th>
                                        <th class="bg-body-secondary">Category</th>
                                        <th class="bg-body-secondary">Brand</th>
                                        <th class="bg-body-secondary">Description</th>
                                        <th class="bg-body-secondary">Stock</th>
                                        <th class="bg-body-secondary">Status</th>
                                        <th class="bg-body-secondary">Date Created</th>
                                        <th class="bg-body-secondary"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $stmt = $koneksi->prepare("SELECT * FROM tb_product");
                                    $stmt->execute();
                                    $result = $stmt->get_result();

                                    while ($row = $result->fetch_assoc()) {
                                        $product_image = $row['product_image'] ? BASE_URL . $row['product_image'] : BASE_URL . 'assets/img/default_product.png';
                                    ?>
                                        <tr class="align-middle">
                                            <td class="text-center">
                                                <div class="avatar avatar-md">
                                                    <img class="avatar-img" src="<?= $product_image ?>" alt="avatar">
                                                </div>
                                            </td>
                                            <td>
                                                <div class="text-nowrap"><?= $row['product_name'] ?></div>
                                            </td>
                                            <td>
                                                <div class="text-nowrap"><?= $row['product_price'] ?></div>
                                            </td>
                                            <td>
                                                <div class="text-nowrap">
                                                    <?php
                                                    if ($row['category_id'] == 0) {
                                                        echo 'Uncategorized';
                                                    } else {
                                                        $stmt_category = $koneksi->prepare("SELECT * FROM tb_category WHERE id = ?");
                                                        $stmt_category->bind_param("i", $row['category_id']);
                                                        $stmt_category->execute();
                                                        $result_category = $stmt_category->get_result();
                                                        $category = $result_category->fetch_assoc();
                                                        echo $category['name'];
                                                    }
                                                    ?>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="text-nowrap">
                                                    <?php
                                                    if ($row['brand_id'] == 0) {
                                                        echo 'No Brand';
                                                    } else {
                                                        $stmt_brand = $koneksi->prepare("SELECT * FROM tb_brand WHERE id = ?");
                                                        $stmt_brand->bind_param("i", $row['brand_id']);
                                                        $stmt_brand->execute();
                                                        $result_brand = $stmt_brand->get_result();
                                                        $brand = $result_brand->fetch_assoc();
                                                        echo $brand['name'];
                                                    }
                                                    ?>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="text-nowrap">
                                                    <?= nl2br($row['product_description']) ?>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="text-nowrap">
                                                    <?= number_format($row['stock'], 0, ',', '.') ?> pcs
                                                </div>
                                            </td>
                                            <td>
                                                <div class="text-nowrap">
                                                    <?php
                                                    if ($row['product_status'] == 1) {
                                                        echo '<span class="badge bg-success">Active</span>';
                                                    } else {
                                                        echo '<span class="badge bg-danger">Inactive</span>';
                                                    }
                                                    ?>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="text-nowrap">
                                                    <?= date('d F Y', strtotime($row['date_created'])) ?>
                                                </div>
                                            </td>
                                            <td>
                                                <a class="dropdown-item" href="product_edit?id=<?= $row['product_id'] ?>">Edit</a>
                                                <a class="dropdown-item text-danger" href="product_delete?id=<?= $row['product_id'] ?>" onclick="return confirm('Apakah anda yakin ingin menghapus product ini?')">Delete</a>
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