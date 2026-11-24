<?php
include 'templates/app/header.php';

$errors = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $customer_name = $_POST['customer_name'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $profile_picture = $_FILES['profile_picture']['name'];

    // Validate inputs
    if (empty($customer_name) || empty($username)  ||empty($email) || empty($password)) {
        $errors[] = 'All fields are required';
    }

    // Check if username or email already exists
    $stmt = $koneksi->prepare("SELECT * FROM tb_customer WHERE username = ? OR email = ?");
    $stmt->bind_param("ss", $username, $email);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $errors[] = 'Username or Email already exists';
    }

    // Validate password length
    if (strlen($password) < 8) {
        $errors[] = 'Password must be at least 8 characters long';
    }

    if (empty($errors)) {
        // Hash the password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Handle file upload
        if (!empty($profile_picture)) {
            $target_dir = "uploads/";
            $target_file = $target_dir . basename($profile_picture);
            move_uploaded_file($_FILES['profile_picture']['tmp_name'], $target_file);
        }

        // Insert into database
        $stmt = $koneksi->prepare("INSERT INTO tb_customer (customer_name, username, email, password, profile_picture) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $customer_name, $username, $email, $hashed_password, $profile_picture);
        if ($stmt->execute()) {
            echo "<script>alert('Registration successful!');window.location.href = 'login';</script>";
        } else {
            $errors[] = 'Registration failed. Please try again.';
        }
    }
}
?>

<div class="container px-lg-5">
    <div class="card bg-white">
        <div class="card-body">
            <h1 class="h2 mt-auto">Create an Account</h1>
            <div class="nav fs-sm mb-4">
                Already have an account?
                <a class="nav-link text-decoration-underline p-0 ms-2" href="login">Sign In</a>
            </div>

            <?php if (!empty($errors)) : ?>
                <div class="alert alert-danger">
                    <ul>
                        <?php foreach ($errors as $error) : ?>
                            <li><?= $error ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <!-- Form -->
            <form action="" method="POST" enctype="multipart/form-data">
                <div class="position-relative mb-4">
                    <input type="text" class="form-control form-control-lg" placeholder="Full Name" required name="customer_name">
                </div>
                <div class="position-relative mb-4">
                    <input type="text" class="form-control form-control-lg" placeholder="Username" required name="username">
                </div>
                <div class="position-relative mb-4">
                    <input type="email" class="form-control form-control-lg" placeholder="Email" required name="email">
                </div>
                <div class="mb-4">
                    <div class="password-toggle">
                        <input type="password" class="form-control form-control-lg" placeholder="Password" required name="password">
                    </div>
                </div>
                <div class="mb-4">
                    <input type="file" class="form-control form-control-lg" name="profile_picture">
                </div>
                <button type="submit" class="btn btn-lg btn-primary w-100">Sign Up</button>
            </form>
        </div>
    </div>
</div>

<?php
include 'templates/app/footer.php';
?>