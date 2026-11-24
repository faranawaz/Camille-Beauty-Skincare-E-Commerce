<?php
include 'templates/app/header.php';

$search = isset($_GET['search']) ? $_GET['search'] : '';
$filter_category = isset($_GET['category']) ? $_GET['category'] : '';
$filter_brand = isset($_GET['brand']) ? $_GET['brand'] : '';

if (empty($search) && empty($filter_category) && empty($filter_brand)) {

?>
    <div class="banner">
        <div class="container">
            <div class="slider-container has-scrollbar">
                <?php
                $stmt = $koneksi->prepare("SELECT * FROM tb_banners");
                $stmt->execute();
                $result = $stmt->get_result();
                while ($row = $result->fetch_assoc()) {
                    $image = $row['image'] ? BASE_URL . $row['image'] : BASE_URL . 'assets/img/default_banner.jpg';
                ?>
                    <div class="slider-item">
                        <img src="<?= $image ?>" alt="<?= $row['title'] ?>" class="banner-img">
                        <div class="banner-content">
                            <p class="banner-subtitle"><?= $row['sub_title'] ?></p>
                            <h2 class="banner-title"><?= $row['title'] ?></h2>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
    <div class="category">
        <div class="container">
            <div class="category-item-container has-scrollbar">
                <?php
                $stmt = $koneksi->prepare("SELECT icon, name, total_product FROM tb_category JOIN (SELECT category_id, COUNT(*) AS total_product FROM tb_product GROUP BY category_id) AS tb_product ON tb_category.id = tb_product.category_id");
                $stmt->execute();
                $result = $stmt->get_result();
                while ($row = $result->fetch_assoc()) {
                    $image = $row['icon'] ? BASE_URL . $row['icon'] : BASE_URL . 'assets/img/default_category.png';
                ?>
                    <div class="category-item">
                        <div class="category-img-box">
                            <img src="<?= $image ?>" alt="<?= $row['name'] ?>" width="30" />
                        </div>
                        <div class="category-content-box">
                            <div class="category-content-flex">
                                <h3 class="category-item-title"><?= $row['name'] ?></h3>

                                <p class="category-item-amount"><?= $row['total_product'] ?></p>
                            </div>
                            <a href="#" class="category-btn">Show all</a>
                        </div>
                    </div>
                <?php } ?>

            </div>

        </div>

    </div>
<?php } ?>



<div class="product-container bg-white py-4">

    <div class="container ">
        <div class="sidebar  has-scrollbar ">
            <div class="sidebar-category">
                <div class="sidebar-top">
                    <h2 class="sidebar-title">Brand</h2>
                    <button class="sidebar-close-btn" data-mobile-menu-close-btn>
                        <ion-icon name="close-outline"></ion-icon>
                    </button>
                </div>
                <ul class="sidebar-menu-category-list">
                    <?php
                    $stmt = $koneksi->prepare("SELECT * FROM tb_brand");
                    $stmt->execute();
                    $result = $stmt->get_result();
                    while ($row = $result->fetch_assoc()) {
                        $image = $row['image'] ? BASE_URL . $row['image'] : BASE_URL . 'assets/img/default_category.png';
                    ?>
                        <li class="sidebar-menu-category">
                            <a class="sidebar-accordion-menu" href="?brand=<?= $row['id'] ?>">
                                <div class="menu-title-flex">
                                    <img src="<?= $image ?>" alt="<?= $row['name'] ?>" width="20" height="20" class="menu-title-img" />
                                    <p class="menu-title pt-3"><?= $row['name'] ?></p>
                                </div>

                            </a>
                        </li>
                    <?php } ?>

                </ul>

            </div>

            <div class="product-showcase">

                <h3 class="showcase-heading">best sellers</h3>

                <div class="showcase-wrapper">

                    <div class="showcase-container">
                        <?php
                        $stmt = $koneksi->prepare("
    SELECT 
        p.product_id, 
        p.category_id, 
        p.product_name, 
        p.product_price, 
        p.product_description, 
        p.product_image, 
        p.product_status, 
        p.date_created,
        IFNULL(o.total_order, 0) AS total_order
    FROM 
        tb_product p
    LEFT JOIN (
        SELECT 
            product_id, 
            COUNT(*) AS total_order 
        FROM 
            tb_order_items 
        GROUP BY 
            product_id
    ) o ON p.product_id = o.product_id 
    ORDER BY 
        o.total_order DESC 
    LIMIT 4
");

                        $stmt->execute();
                        $result = $stmt->get_result();
                        while ($row = $result->fetch_assoc()) {
                            $image = file_exists($row['product_image']) ? BASE_URL . $row['product_image'] : BASE_URL . 'assets/img/default_category.png';
                        ?>
                            <div class="showcase">
                                <a href="detail-product?id=<?= $row['product_id'] ?>" class="showcase-img-box">
                                    <img src="<?= $image ?>" alt="<?= $row['product_name'] ?>" width="75" height="75" class="showcase-img">
                                </a>

                                <div class="showcase-content">

                                    <a href="detail-product?id=<?= $row['product_id'] ?>">
                                        <h4 class="showcase-title"><?= $row['product_name'] ?></h4>
                                    </a>

                                    <div class="showcase-rating">
                                        <ion-icon name="star"></ion-icon>
                                        <ion-icon name="star"></ion-icon>
                                        <ion-icon name="star"></ion-icon>
                                        <ion-icon name="star"></ion-icon>
                                        <ion-icon name="star"></ion-icon>
                                    </div>

                                    <div class="price-box">
                                        <p class="price">Rp. <?= number_format($row['product_price'], '0', ',', '.') ?></p>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>

        </div>
        <div class="product-box">
            <div class="product-main">
                <h2 class="title">Products</h2>

                <div class="product-grid">
                    <?php
                    if ($search) {
                        $stmt = $koneksi->prepare("SELECT * FROM tb_product JOIN tb_category ON tb_product.category_id = tb_category.id WHERE product_name LIKE ? ORDER BY tb_product.date_created DESC");
                        $search = '%' . $search . '%';
                        $stmt->bind_param("s", $search);
                    } else {
                        $stmt = $koneksi->prepare("SELECT * FROM tb_product JOIN tb_category ON tb_product.category_id = tb_category.id ORDER BY tb_product.date_created DESC");
                    }
                    $stmt->execute();
                    $result = $stmt->get_result();
                    while ($row = $result->fetch_assoc()) {
                        $image = file_exists($row['product_image']) ? BASE_URL . $row['product_image'] : BASE_URL . 'assets/img/default_category.png';
                    ?>
                        <div class="showcase card">
                            <div class="showcase-banner">
                                <img src="<?= $image ?>" alt="<?= $row['product_name'] ?>" width="300" class="product-img default">
                                <img src="<?= $image ?>" alt="<?= $row['product_name'] ?>" width="300" class="product-img hover">

                                <div class="showcase-actions">
                                    <?php
                                    if (isset($_SESSION['customer_id'])) {

                                    ?>
                                        <a class="btn-action" href="addToCart?id=<?= $row['product_id'] ?>">
                                            <ion-icon name="cart-outline"></ion-icon>
                                        </a>
                                    <?php } ?>
                                </div>
                            </div>

                            <div class="showcase-content">

                                <a href="detail-product?id=<?= $row['product_id'] ?>" class="showcase-category"><?= $row['name'] ?></a>

                                <a href="detail-product?id=<?= $row['product_id'] ?>">
                                    <h3 class="showcase-title"><?= $row['product_name'] ?></h3>
                                </a>

                                <div class="price-box">
                                    <p class="price">Rp. <?= number_format($row['product_price'], '0', ',', '.') ?></p>
                                </div>

                            </div>

                        </div>
                    <?php } ?>

                </div>

            </div>

        </div>
    </div>
</div>
<div>

</div>
<footer>

    <div class="footer-nav">

        <div class="container">

            <ul class="footer-nav-list">

                <li class="footer-nav-item">
                    <h2 class="nav-title">Popular Categories</h2>
                </li>

                <?php
                $stmt = $koneksi->prepare("SELECT * FROM tb_category JOIN (SELECT category_id, COUNT(*) AS total_product FROM tb_product GROUP BY category_id) AS tb_product ON tb_category.id = tb_product.category_id ORDER BY tb_product.total_product DESC LIMIT 5");
                $stmt->execute();
                $result = $stmt->get_result();
                while ($row = $result->fetch_assoc()) {
                ?>
                    <li class="footer-nav-item">
                        <a href="#" class="footer-nav-link"><?= $row['name'] ?></a>
                    </li>
                <?php } ?>
            </ul>

            <ul class="footer-nav-list">

                <li class="footer-nav-item">
                    <h2 class="nav-title">Products</h2>
                </li>

                <?php
                $stmt = $koneksi->prepare("SELECT * FROM tb_product ORDER BY date_created DESC LIMIT 5");
                $stmt->execute();
                $result = $stmt->get_result();
                while ($row = $result->fetch_assoc()) {
                ?>
                    <li class="footer-nav-item">
                        <a href="#" class="footer-nav-link"><?= $row['product_name'] ?></a>
                    </li>
                <?php } ?>

            </ul>


            <ul class="footer-nav-list">

                <li class="footer-nav-item">
                    <h2 class="nav-title">Contact</h2>
                </li>

                <li class="footer-nav-item flex">
                    <div class="icon-box">
                        <ion-icon name="location-outline"></ion-icon>
                    </div>

                    <address class="content">
                        Jl. Talas No.140 Beji, Depok Utara
                    </address>
                </li>

                <li class="footer-nav-item flex">
                    <div class="icon-box">
                        <ion-icon name="call-outline"></ion-icon>
                    </div>

                    <a href="tel:+607936-8058" class="footer-nav-link">+6283879356069</a>
                </li>

                <li class="footer-nav-item flex">
                    <div class="icon-box">
                        <ion-icon name="mail-outline"></ion-icon>
                    </div>

                    <a href="mailto:example@gmail.com" class="footer-nav-link">nawalazaliaf@gmail.com</a>
                </li>

            </ul>

            <ul class="footer-nav-list">

                <li class="footer-nav-item">
                    <h2 class="nav-title">Follow Us</h2>
                </li>

                <li>
                    <ul class="social-link">

                        <li class="footer-nav-item">
                            <a href="#" class="footer-nav-link">
                                <ion-icon name="logo-facebook"></ion-icon>
                            </a>
                        </li>

                        <li class="footer-nav-item">
                            <a href="#" class="footer-nav-link">
                                <ion-icon name="logo-twitter"></ion-icon>
                            </a>
                        </li>

                        <li class="footer-nav-item">
                            <a href="#" class="footer-nav-link">
                                <ion-icon name="logo-linkedin"></ion-icon>
                            </a>
                        </li>

                        <li class="footer-nav-item">
                            <a href="#" class="footer-nav-link">
                                <ion-icon name="logo-instagram"></ion-icon>
                            </a>
                        </li>

                    </ul>
                </li>

            </ul>

        </div>

    </div>

    <div class="footer-bottom">

        <div class="container">

            <img src="./assets/images/payment.png" alt="payment method" class="payment-img">

            <p class="copyright">
                Copyright &copy; <a href="#"><?= APP_NAME ?></a> all rights reserved.
            </p>

        </div>

    </div>

</footer>
<?php

include 'templates/app/footer.php';
?>