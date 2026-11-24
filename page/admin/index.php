<?php
$session = $_SESSION['login'] ?? false;
$admin = $_SESSION['is_admin'];
if (!$session || !$admin) {
  header('Location: /admin/login');
  exit;
}

$productTerjual = 0;
$pendapatan = 0;
$jumlahProduk = 0;
$jumlahCustomer = 0;

$stmt = $koneksi->prepare("SELECT SUM(quantity) as qty FROM tb_order_items");
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$productTerjual = $row['qty'];

$stmt = $koneksi->prepare("SELECT SUM(order_total) as total FROM tb_orders");
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$pendapatan = $row['total'];

$stmt = $koneksi->prepare("SELECT COUNT(*) as total FROM tb_product");
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$jumlahProduk = $row['total'];

$stmt = $koneksi->prepare("SELECT COUNT(*) as total FROM tb_customer");
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$jumlahCustomer = $row['total'];

$penjualanChart = [];
for ($i = 1; $i <= 12; $i++) {
  $stmt = $koneksi->prepare("SELECT SUM(order_total) as total FROM tb_orders WHERE MONTH(created_at) = ?");
  $stmt->bind_param("i", $i);
  $stmt->execute();
  $result = $stmt->get_result();
  $row = $result->fetch_assoc();
  $penjualanChart[] = $row['total'] ?? 0;
}


include_once 'templates/admin/header.php'; ?>


<script>
  var alertList = document.querySelectorAll(".alert");
  alertList.forEach(function(alert) {
    new bootstrap.Alert(alert);
  });
</script>

<div class="body flex-grow-1">
  <div class="container-lg px-4">
    <div class="row g-4 mb-4">
      <div class="col-sm-6 col-xl-3">
        <div class="card text-white bg-primary">
          <div class="card-body pb-0 d-flex justify-content-between align-items-start">
            <div>
              <div class="fs-4 fw-semibold"><?= number_format($productTerjual, 0, ',', '.') . ' items' ?>
                <span class="fs-6 fw-normal">( <?= $productTerjual / ($jumlahProduk * 100) ?>%)</span>
              </div>
              <div>
                Product Terjual
              </div>
            </div>
          </div>
          <div class="c-chart-wrapper mt-3 mx-3" style="height:70px;">
          </div>
        </div>
      </div>
      <!-- /.col-->
      <div class="col-sm-6 col-xl-3">
        <div class="card text-white bg-info">
          <div class="card-body pb-0 d-flex justify-content-between align-items-start">
            <div>
              <div class="fs-4 fw-semibold"> <?= 'Rp. ' . number_format($pendapatan, 0, ',', '.') ?>
                <span class="fs-6 fw-normal">( <?= $pendapatan / ($jumlahProduk * 100) ?>%)</span>
              </div>
              <div>Pendapatan</div>
            </div>
          </div>
          <div class="c-chart-wrapper mt-3 mx-3" style="height:70px;">
          </div>
        </div>
      </div>
      <!-- /.col-->
      <div class="col-sm-6 col-xl-3">
        <div class="card text-white bg-warning">
          <div class="card-body pb-0 d-flex justify-content-between align-items-start">
            <div>
              <div class="fs-4 fw-semibold"> <?= number_format($jumlahProduk, 0, ',', '.') . ' items' ?>
                <span class="fs-6 fw-normal">( <?= $jumlahProduk / ($jumlahProduk * 100) ?>%)</span>
              </div>
              <div>Jumlah Produk</div>
            </div>
          </div>
          <div class="c-chart-wrapper mt-3" style="height:70px;">
          </div>
        </div>
      </div>
      <!-- /.col-->
      <div class="col-sm-6 col-xl-3">
        <div class="card text-white bg-danger">
          <div class="card-body pb-0 d-flex justify-content-between align-items-start">
            <div>
              <div class="fs-4 fw-semibold"> <?= number_format($jumlahCustomer, 0, ',', '.')  ?>
                <span class="fs-6 fw-normal">( <?= $jumlahCustomer / ($jumlahProduk * 100) ?>%)</span>
              </div>
              <div>
                Jumlah Customer
              </div>
            </div>
          </div>
          <div class="c-chart-wrapper mt-3 mx-3" style="height:70px;">
          </div>
        </div>
      </div>
      <!-- /.col-->
    </div>
    <!-- /.row-->
    <div class="card mb-4">
      <div class="card-body">
        <div class="d-flex justify-content-between">
          <div>
            <h4 class="card-title mb-0">Penjualan</h4>
            <div class="small text-body-secondary"> <span class="fw-semibold">Penjualan</span> per bulan</div>
          </div>
          <div class="btn-toolbar d-none d-md-block" role="toolbar" aria-label="Toolbar with buttons">
            <button class="btn btn-primary" type="button" onclick="downloadChart('penjualan-chart')">Download
              <svg class="icon">
                <use xlink:href="<?= BASE_URL ?>assets/vendors/@coreui/icons/svg/free.svg#cil-cloud-download"></use>
              </svg>
            </button>
          </div>
        </div>
        <div class="c-chart-wrapper">
          <canvas id="penjualan-chart"></canvas>
        </div>
      </div>
      <div class="card-footer">
        <div class="row row-cols-3 row-cols-sm-3 row-cols-lg-4 row-cols-xl-5 g-4 mb-2 text-center">
          <div class="col">
            <div class="text-body-secondary">Pendapatan</div>
            <div class="fw-semibold text-truncate"> <?= 'Rp. ' . number_format($pendapatan, 0, ',', '.') ?></div>
          </div>
          <div class="col">
            <div class="text-body-secondary">Customer Terdaftar</div>
            <div class="fw-semibold text-truncate"> <?= number_format($jumlahCustomer, 0, ',', '.') ?></div>
          </div>
          <div class="col">
            <div class="text-body-secondary">Jumlah Produk</div>
            <div class="fw-semibold text-truncate"> <?= number_format($jumlahProduk, 0, ',', '.') ?></div>
          </div>
          <div class="col">
            <div class="text-body-secondary">Produk Terjual</div>
            <div class="fw-semibold text-truncate"> <?= number_format($productTerjual, 0, ',', '.') ?></div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
  const ctx = document.getElementById('penjualan-chart').getContext('2d');

  new Chart(ctx, {
    type: 'line',
    data: {
      labels: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
      datasets: [{
        label: 'Penjualan',
        data: <?= json_encode($penjualanChart) ?>,
        borderWidth: 1
      }]
    },
    options: {
      scales: {
        y: {
          beginAtZero: true
        }
      }
    }
  });

  function downloadChart(id) {
    const canvas = document.getElementById(id);
    const image = canvas.toDataURL('image/png');

    const link = document.createElement('a');
    link.href = image;
    link.download = 'chart.png';
    link.click();
  }
</script>
<?php include_once 'templates/admin/footer.php'; ?>