<div class="sidebar sidebar-dark sidebar-fixed border-end" id="sidebar">
  <div class="sidebar-header border-bottom">
    <div class="sidebar-brand">
      <h4 class="fw-semibold"><?= APP_NAME ?></h4>
    </div>
    <button class="btn-close d-lg-none" type="button" data-coreui-dismiss="offcanvas" data-coreui-theme="dark" aria-label="Close" onclick="coreui.Sidebar.getInstance(document.querySelector(&quot;#sidebar&quot;)).toggle()"></button>
  </div>
  <ul class="sidebar-nav" data-coreui="navigation" data-simplebar="">
    <li class="nav-item">
      <a class="nav-link" href="/admin">
        <svg class="nav-icon">
          <use xlink:href="<?= BASE_URL ?>assets/vendors/@coreui/icons/svg/free.svg#cil-speedometer"></use>
        </svg> Halaman Utama</a>
    </li>
    <li class="nav-title">Kelola Produk</li>
    <li class="nav-item">
      <a class="nav-link" href="/admin/products">
        <svg class="nav-icon">
          <use xlink:href="<?= BASE_URL ?>assets/vendors/@coreui/icons/svg/free.svg#cil-drop"></use>
        </svg> List Produk</a>
    </li>
    <li class="nav-item">
      <a class="nav-link" href="/admin/category">
        <svg class="nav-icon">
          <use xlink:href="<?= BASE_URL ?>assets/vendors/@coreui/icons/svg/free.svg#cil-pencil"></use>
        </svg> List kategori</a>
    </li>
    <li class="nav-item">
      <a class="nav-link" href="/admin/brand">
        <svg class="nav-icon">
          <use xlink:href="<?= BASE_URL ?>assets/vendors/@coreui/icons/svg/free.svg#cil-star"></use>
        </svg> List Brand</a>
    </li>
    <li class="nav-title">Kelola Order</li>
    <li class="nav-item">
      <a class="nav-link" href="/admin/orders">
        <svg class="nav-icon">
          <use xlink:href="<?= BASE_URL ?>assets/vendors/@coreui/icons/svg/free.svg#cil-money"></use>
        </svg> List Order</a>
    </li>
    <li class="nav-title">Kelola Pengguna</li>
    <li class="nav-item">
      <a class="nav-link" href="/admin/customer">
        <svg class="nav-icon">
          <use xlink:href="<?= BASE_URL ?>assets/vendors/@coreui/icons/svg/free.svg#cil-address-book"></use>
        </svg> List Customer</a>
    </li>
    <li class="nav-item">
      <a class="nav-link" href="/admin/admins">
        <svg class="nav-icon">
          <use xlink:href="<?= BASE_URL ?>assets/vendors/@coreui/icons/svg/free.svg#cil-user"></use>
        </svg> List Admin</a>
    </li>
    <li class="nav-title">Kelola Banner</li>
    <li class="nav-item">
      <a class="nav-link" href="/admin/banner">
        <svg class="nav-icon">
          <use xlink:href="<?= BASE_URL ?>assets/vendors/@coreui/icons/svg/free.svg#cil-image"></use>
        </svg> List Banner</a>
    </li>
  </ul>
  <div class="sidebar-footer border-top d-none d-md-flex">
    <button class="sidebar-toggler" type="button" data-coreui-toggle="unfoldable"></button>
  </div>
</div>