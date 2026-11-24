<?php
ob_start();
$customer = $_SESSION['customer_id'] ?? null;

if ($customer) {
    header('Location: /');
    exit;
}
include 'templates/app/header.php';

$username = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $koneksi->prepare("SELECT * FROM tb_customer WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $customer = $result->fetch_assoc();

    if ($customer) {
        if (password_verify($password, $customer['password'])) {
            $_SESSION['customer_login'] = true;
            $_SESSION['customer_id'] = $customer['customer_id'];
            header('Location: index');
            exit;
        } else {
            echo "<script>alert('Username atau password salah!');window.location.href = 'login';</script>";
            exit;
        }
    } else {
        echo "<script>alert('Username tidak terdaftar!');window.location.href = 'login';</script>";
        exit;
    }
}
?>
<div class="container px-lg-5">
    <div class="card bg-white">
        <div class="card-body">
            <h1 class="h2 mt-auto">Welcome back</h1>
            <div class="nav fs-sm mb-4">
                Don't have an account?
                <a class="nav-link text-decoration-underline p-0 ms-2" href="register">Create an account</a>
            </div>

            <!-- Form -->
            <form action="" method="POST">
                <div class="position-relative mb-4">
                    <input type="text" class="form-control form-control-lg" placeholder="Username" required name="username" value="<?= $username ?>">
                </div>
                <div class="mb-4">
                    <div class="password-toggle">
                        <input type="password" class="form-control form-control-lg" placeholder="Password" required name="password">
                    </div>
                </div>
                <div class="d-flex align-items-center justify-content-between mb-4">
                    <div class="form-check me-2">
                        <input type="checkbox" class="form-check-input" id="remember-me" name="remember-me">
                        <label for="remember-me" class="form-check-label">Remember</label>
                    </div>
                </div>
                <button type="submit" class="btn btn-lg btn-primary w-100">Sign In</button>
            </form>

        </div>
    </div>
</div>



<?php

include 'templates/app/footer.php';
?>