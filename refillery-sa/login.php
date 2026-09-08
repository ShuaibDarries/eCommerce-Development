<?php
require_once 'config/db.php';
require_once 'includes/header.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $stmt = db()->prepare('SELECT * FROM customers WHERE email = ?');
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    // password_verify() checks the bcrypt hash — plain-text passwords are never compared
    if ($user && password_verify($_POST['password'], $user['password_hash'])) {
        $_SESSION['customer_id'] = $user['customer_id'];
        $dest = $_SESSION['redirect_after_login'] ?? 'index.php';
        unset($_SESSION['redirect_after_login']);
        header('Location: ' . $dest);
        exit;
    }
    $error = 'Invalid email or password.';
}
?>
<h1 class="h3 mb-3">Log in</h1>
<?php if ($error): ?><div class="alert alert-danger"><?= htmlspecialchars($error) ?></div><?php endif; ?>
<div class="col-md-5">
    <form method="post" class="card card-body">
        <label class="form-label">Email address</label>
        <input name="email" type="email" class="form-control mb-3" required>
        <label class="form-label">Password</label>
        <input name="password" type="password" class="form-control mb-3" required>
        <button class="btn btn-success">Log in</button>
        <p class="mt-3 small mb-0">New here? <a href="register.php">Create an account</a></p>
    </form>
</div>
<?php require_once 'includes/footer.php'; ?>
