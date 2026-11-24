<!DOCTYPE html>
<html lang="en" data-coreui-theme="light">

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
  <title>Admin - <?php echo APP_NAME; ?></title>

  <link rel="icon" type="image/png" sizes="16x16" href="<?= BASE_URL ?>assets/favicon/favicon-16x16.png">
  <link rel="manifest" href="<?= BASE_URL ?>assets/favicon/manifest.json">
  <meta name="msapplication-TileColor" content="#ffffff">
  <meta name="msapplication-TileImage" content="<?= BASE_URL ?>assets/favicon/ms-icon-144x144.png">
  <meta name="theme-color" content="#ffffff">
  <!-- Vendors styles-->
  <link rel="stylesheet" href="<?= BASE_URL ?>assets/vendors/simplebar/css/simplebar.css">
  <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/vendors/simplebar.css">
  <!-- Main styles for this application-->
  <link href="<?= BASE_URL ?>assets/css/style_admin.css" rel="stylesheet">
  <script src="<?= BASE_URL ?>assets/js/config.js"></script>
  <script src="<?= BASE_URL ?>assets/js/color-modes.js"></script>
  <link href="<?= BASE_URL ?>assets/vendors/@coreui/chartjs/css/coreui-chartjs.css" rel="stylesheet">
  <link href="<?= BASE_URL ?>assets/css/datatables.min.css" rel="stylesheet">

  <script src="<?= BASE_URL ?>assets/js/datatables.min.js"></script>

</head>

<body>
  <?php include_once 'templates/admin/sidebar.php'; ?>
  <div class="wrapper d-flex flex-column min-vh-100">
    <header class="header header-sticky p-0 mb-4">
      <div class="container-fluid border-bottom px-4">
        <button class="header-toggler" type="button" onclick="coreui.Sidebar.getInstance(document.querySelector('#sidebar')).toggle()" style="margin-inline-start: -14px;">
          <svg class="icon icon-lg">
            <use xlink:href="<?= BASE_URL ?>assets/vendors/@coreui/icons/svg/free.svg#cil-menu"></use>
          </svg>
        </button>
        <ul class="header-nav">
          <li class="nav-item py-1">
            <div class="vr h-100 mx-2 text-body text-opacity-75"></div>
          </li>
          <li class="nav-item dropdown">
            <button class="btn btn-link nav-link py-2 px-2 d-flex align-items-center" type="button" aria-expanded="false" data-coreui-toggle="dropdown">
              <svg class="icon icon-lg theme-icon-active">
                <use xlink:href="<?= BASE_URL ?>assets/vendors/@coreui/icons/svg/free.svg#cil-contrast"></use>
              </svg>
            </button>
            <ul class="dropdown-menu dropdown-menu-end" style="--cui-dropdown-min-width: 8rem;">
              <li>
                <button class="dropdown-item d-flex align-items-center" type="button" data-coreui-theme-value="light">
                  <svg class="icon icon-lg me-3">
                    <use xlink:href="<?= BASE_URL ?>assets/vendors/@coreui/icons/svg/free.svg#cil-sun"></use>
                  </svg>Light
                </button>
              </li>
              <li>
                <button class="dropdown-item d-flex align-items-center" type="button" data-coreui-theme-value="dark">
                  <svg class="icon icon-lg me-3">
                    <use xlink:href="<?= BASE_URL ?>assets/vendors/@coreui/icons/svg/free.svg#cil-moon"></use>
                  </svg>Dark
                </button>
              </li>
              <li>
                <button class="dropdown-item d-flex align-items-center active" type="button" data-coreui-theme-value="auto">
                  <svg class="icon icon-lg me-3">
                    <use xlink:href="<?= BASE_URL ?>assets/vendors/@coreui/icons/svg/free.svg#cil-contrast"></use>
                  </svg>Auto
                </button>
              </li>
            </ul>
          </li>
          <li class="nav-item py-1">
            <div class="vr h-100 mx-2 text-body text-opacity-75"></div>
          </li>
          <li class="nav-item dropdown">
            <a class="nav-link py-0 pe-0" data-coreui-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">
              <div class="avatar avatar-md">
                <?php
                $stmt = $koneksi->prepare("SELECT * FROM tb_admin WHERE admin_id = ?");
                $stmt->bind_param("i", $_SESSION['id']);
                $stmt->execute();
                $result = $stmt->get_result();
                $admin = $result->fetch_assoc();

                $avatar = $admin['profile_picture'] ? BASE_URL . $admin['profile_picture'] : BASE_URL . 'assets/img/default_avatar.png';
                ?>
                <img class="avatar-img" src="<?= $avatar ?>" alt="<?= $admin['admin_name'] ?>">
              </div>
            </a>
            <div class="dropdown-menu dropdown-menu-end pt-0">
              <div class="dropdown-header bg-body-tertiary text-body-secondary fw-semibold my-2">
                <div class="fw-semibold">
                  <?php echo ucwords($admin['admin_name']); ?>
                </div>
              </div><a class="dropdown-item" href="#">
                <svg class="icon me-2">
                  <use xlink:href="<?= BASE_URL ?>assets/vendors/@coreui/icons/svg/free.svg#cil-user"></use>
                </svg> Profile</a><a class="dropdown-item" href="#">
              </a>
              <div class="dropdown-divider"></div>
              <a class="dropdown-item" href="logout" onclick="return confirm('Apakah Anda yakin ingin keluar?')">
                <svg class="icon me-2">
                  <use xlink:href="<?= BASE_URL ?>assets/vendors/@coreui/icons/svg/free.svg#cil-account-logout"></use>
                </svg>
                Logout</a>
            </div>
          </li>
        </ul>
      </div>
    </header>