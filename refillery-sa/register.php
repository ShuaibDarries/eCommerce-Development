<?php
require_once 'config/db.php';
require_once 'includes/header.php';

$errors = [];
$old = ['full_name' => '', 'email' => '', 'phone' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $old['full_name'] = trim($_POST['full_name']);
    $old['email']     = trim($_POST['email']);
    $old['phone']     = trim($_POST['phone']);
    $password         = $_POST['password'];
    $confirm          = $_POST['confirm'];

    // --- Validation with clear error messaging ---
    if ($old['full_name'] === '')                      $errors[] = 'Full name is required.';
    if (!filter_var($old['email'], FILTER_VALIDATE_EMAIL)) $errors[] = 'Please enter a valid email address.';
    if (strlen($password) < 8)                         $errors[] = 'Password must be at least 8 characters.';
    if ($password !== $confirm)                        $errors[] = 'Passwords do not match.';

    // Email must be unique
    if (!$errors) {
        $stmt = db()->prepare('SELECT customer_id FROM customers WHERE email = ?');
        $stmt->execute([$old['email']]);
        if ($stmt->fetch()) $errors[] = 'An account with this email already exists.';
    }

    if (!$errors) {
        // Passwords are hashed with bcrypt — never stored in plain text
        $stmt = db()->prepare('INSERT INTO customers (full_name, email, phone, password_hash) VALUES (?,?,?,?)');
        $stmt->execute([$old['full_name'], $old['email'], $old['phone'], password_hash($password, PASSWORD_DEFAULT)]);
        $_SESSION['customer_id'] = db()->lastInsertId();
        header('Location: index.php?welcome=1');
        exit;
    }
}
?>
<h1 class="h3 mb-3">Create your account</h1>
<?php foreach ($errors as $e): ?><div class="alert alert-danger"><?= htmlspecialchars($e) ?></div><?php endforeach; ?>
<div class="col-md-6">
    <form method="post" class="card card-body">
        <label class="form-label">Full name</label>
        <input name="full_name" class="form-control mb-3" value="<?= htmlspecialchars($old['full_name']) ?>" required>
        <label class="form-label">Email address</label>
        <input name="email" type="email" class="form-control mb-3" value="<?= htmlspecialchars($old['email']) ?>" required>
        <label class="form-label">Phone (optional)</label>
        <input name="phone" class="form-control mb-3" value="<?= htmlspecialchars($old['phone']) ?>">
        <label class="form-label">Password (min 8 characters)</label>
        <input name="password" type="password" class="form-control mb-3" required>
        <label class="form-label">Confirm password</label>
        <input name="confirm" type="password" class="form-control mb-3" required>
        <button class="btn btn-success">Register</button>
        <p class="mt-3 small mb-0">Already registered? <a href="login.php">Log in</a></p>
    </form>
</div>
<?php require_once 'includes/footer.php'; ?>
