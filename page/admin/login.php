<?php
$session = $_SESSION['login'] ?? false;

if ($session) {
  header("Location: admin");
  exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  try {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $koneksi->prepare("SELECT * FROM tb_admin WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
      $data = $result->fetch_assoc();

      if (password_verify($password, $data['password'])) {
        $_SESSION['login'] = true;
        $_SESSION['id'] = $data['admin_id'];
        $_SESSION['username'] = $data['username'];
        $_SESSION['name'] = $data['admin_name'];
        $_SESSION['avatar'] = $data['profile_picture'];
        $_SESSION['is_admin'] = true;

        header("Location: /admin");
        exit;
      } else {
        echo "<script>alert('Username atau password salah!');window.location.href = 'login';</script>";
        exit;
      }
    } else {
      echo "<script>alert('Username atau password salah!');window.location.href = 'login';</script>";
      exit;
    }
  } catch (Exception $e) {
    $message = htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
    echo "<script>alert('Terjadi kesalahan: " . json_encode($message) . "');window.location.href = 'login';</script>";
    exit;
  }
}

include_once 'templates/admin/guest.php';
?>

<div class="bg-body-tertiary min-vh-100 d-flex flex-row align-items-center">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-lg-5">
        <div class="card-group d-block d-md-flex row">
          <div class="card col-md-5 p-4 mb-0">
            <div class="card-body">
              <h1>Login</h1>
              <p class="text-body-secondary">Masukkan username dan password untuk login.</p>
              <form method="POST" action="">
                <div class="input-group mb-3 pt-3">
                  <span class="input-group-text">
                    <svg class="icon">
                      <use xlink:href="<?= BASE_URL ?>assets/vendors/@coreui/icons/svg/free.svg#cil-user"></use>
                    </svg>
                  </span>
                  <input class="form-control" type="text" placeholder="Username" name="username" required value="<?= htmlspecialchars($_POST['username'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" autocomplete="username">
                </div>
                <div class="input-group mb-4">
                  <span class="input-group-text">
                    <svg class="icon">
                      <use xlink:href="<?= BASE_URL ?>assets/vendors/@coreui/icons/svg/free.svg#cil-lock-locked"></use>
                    </svg>
                  </span>
                  <input class="form-control" type="password" placeholder="Password" name="password" required autocomplete="current-password">
                </div>
                <div class="row p-3">
                  <button class="btn btn-primary px-4" type="submit">Login</button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<?php include_once 'templates/admin/guest_footer.php'; ?>