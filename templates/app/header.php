<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Beauty Store</title>

  <link rel="shortcut icon" href="<?php BASE_URL ?>assets/images/logo/favicon.ico" type="image/x-icon">

  <link rel="stylesheet" href="<?php BASE_URL ?>assets/css/style-prefix.css">
  <link rel="stylesheet" href="https://pro.fontawesome.com/releases/v5.10.0/css/all.css" integrity="sha384-AYmEC3Yw5cVb3ZcuHtOA93w35dYTsvhLPVnYs9eStHfGJvOvKxVfELGroGkvsg+p" crossorigin="anonymous" />

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
  <link href="<?= BASE_URL ?>assets/css/style_admin.css" rel="stylesheet">

  <style>
    .marquee {
      width: 100%;
      overflow: hidden;
      white-space: nowrap;
    }

    .marquee span {
      width: 100%;
      padding-left: 100%;
      animation: marquee 10s linear infinite;
    }

    @keyframes marquee {
      0% {
        transform: translate(0, 0);
      }

      100% {
        transform: translate(-100%, 0);
      }
    }
  </style>
</head>

<body>
  <div class="overlay" data-overlay></div>
  <header>
    <div class="header-top">
      <div class="container text-center d-flex justify-content-center">
        <div class="header-alert-news marquee">
          <span class="alert-news">Website yang menjual produk Camille Beauty khusus daerah Depok, Jawa Barat</span>
        </div>
      </div>
    </div>

    <div class="header-main" style="background-color: #FFC0CB;">
      <div class="container">
        <a href="/" class="header-logo btn text-white">
          <h2><?= APP_NAME ?></h2>
        </a>
        <div class="header-search-container">

          <form action="">
            <input type="search" name="search" class="search-field form-control" placeholder="Enter your product name...">
          </form>

          <button class="search-btn">
            <ion-icon name="search-outline"></ion-icon>
          </button>

        </div>

        <div class="header-user-actions">

          <a class="action-btn" href="profile">
            <ion-icon name="person-outline"></ion-icon>
          </a>

          <a class="action-btn" href="cart">
            <ion-icon name="bag-handle-outline"></ion-icon>
            <span class="count">
              <?php
              if (isset($_SESSION['customer_id'])) {
                $stmt = $koneksi->prepare("SELECT COUNT(*) AS total FROM tb_cart WHERE customer_id = ?");
                $stmt->bind_param("i", $_SESSION['customer_id']);
                $stmt->execute();
                $result = $stmt->get_result();
                $cart = $result->fetch_assoc();
                echo $cart['total'];
              } else {
                echo 0;
              }
              ?>
            </span>
          </a>

        </div>

      </div>

    </div>

    <nav class="desktop-navigation-menu" style="background-color: #F8E1E7;">

      <div class="container">

        <ul class="desktop-menu-category-list">

          <li class="menu-category">
            <a href="/" class="menu-title text-decoration-none">Home</a>
          </li>

          <?php
          $stmt = $koneksi->prepare("SELECT * FROM tb_category ORDER BY RAND() LIMIT 5");
          $stmt->execute();
          $result = $stmt->get_result();
          while ($category = $result->fetch_assoc()) {
          ?>

            <li class="menu-category">
              <a href="?category=<?= $category['id'] ?>" class="menu-title text-decoration-none"><?= $category['name'] ?></a>
            </li>
          <?php } ?>

        </ul>

      </div>
    </nav>

    <div class="mobile-bottom-navigation">

      <button class="action-btn" data-mobile-menu-open-btn>
        <ion-icon name="menu-outline"></ion-icon>
      </button>

      <button class="action-btn">
        <a class="action-btn" href="cart">
          <ion-icon name="bag-handle-outline"></ion-icon>
          <span class="count">
              <?php
              if (isset($_SESSION['customer_id'])) {
                $stmt = $koneksi->prepare("SELECT COUNT(*) AS total FROM tb_cart WHERE customer_id = ?");
                $stmt->bind_param("i", $_SESSION['customer_id']);
                $stmt->execute();
                $result = $stmt->get_result();
                $cart = $result->fetch_assoc();
                echo $cart['total'];
              } else {
                echo 0;
              }
              ?>
            </span>
        </a>
      </button>

      <button class="action-btn">
        <a href="/" class="action-btn">
          <ion-icon name="home-outline"></ion-icon>
          </a>
      </button>

      <button class="action-btn">
        <a class="action-btn" href="profile">
          <ion-icon name="person-outline"></ion-icon>
        </a>
      </button>

    </div>

    <nav class="mobile-navigation-menu  has-scrollbar" data-mobile-menu>

      <div class="menu-top">
        <h2 class="menu-title">Menu</h2>

        <button class="menu-close-btn" data-mobile-menu-close-btn>
          <ion-icon name="close-outline"></ion-icon>
        </button>
      </div>

      <ul class="mobile-menu-category-list">

        <li class="menu-category">
          <a href="/" class="menu-title text-decoration-none">Home</a>
        </li>

        <?php
          $stmt = $koneksi->prepare("SELECT * FROM tb_category ORDER BY RAND() LIMIT 5");
          $stmt->execute();
          $result = $stmt->get_result();
          while ($category = $result->fetch_assoc()) {
          ?>

            <li class="menu-category">
              <a href="?category=<?= $category['id'] ?>" class="menu-title text-decoration-none"><?= $category['name'] ?></a>
            </li>
          <?php } ?>
      </ul>

    </nav>


  </header>
  <main>