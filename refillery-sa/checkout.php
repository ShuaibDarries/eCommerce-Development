<?php
require_once 'config/db.php';
require_once 'includes/header.php';
require_once 'includes/auth.php';
require_once 'includes/cart_data.php';

// Customer must be logged in before placing an order
require_login();
$customer = current_customer();
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $address = trim($_POST['shipping_address']);
    $city    = trim($_POST['city']);
    $postal  = trim($_POST['postal_code']);

    if ($address === '') $errors[] = 'Street address is required.';
    if ($city === '')    $errors[] = 'City is required.';
    if (!preg_match('/^\d{4}$/', $postal)) $errors[] = 'Postal code must be exactly 4 digits.';

    if (!$errors) {
        $_SESSION['checkout'] = [
            'shipping_address' => "$address, $city, $postal",
            'total' => $total,
        ];
        header('Location: payment.php');
        exit;
    }
}
?>
<h1 class="h3 mb-3">Checkout</h1>
<?php foreach ($errors as $e): ?><div class="alert alert-danger"><?= htmlspecialchars($e) ?></div><?php endforeach; ?>
<div class="row">
    <div class="col-md-7">
        <form method="post" class="card card-body">
            <h2 class="h6">Deliver to</h2>
            <p class="small text-muted mb-3"><?= htmlspecialchars($customer['full_name']) ?> &mdash; <?= htmlspecialchars($customer['email']) ?></p>
            <label class="form-label">Street address</label>
            <input name="shipping_address" class="form-control mb-3" required>
            <label class="form-label">City</label>
            <input name="city" class="form-control mb-3" required>
            <label class="form-label">Postal code</label>
            <input name="postal_code" class="form-control mb-3" maxlength="4" required>
            <button class="btn btn-success">Continue to Payment</button>
        </form>
    </div>
    <div class="col-md-5">
        <div class="card card-body">
            <h2 class="h6">Order summary</h2>
            <?php foreach ($items as $it): ?>
                <div class="d-flex justify-content-between small">
                    <span><?= $it['qty'] ?> × <?= htmlspecialchars($it['p']['name']) ?></span>
                    <span>R<?= number_format($it['line'], 2) ?></span>
                </div>
            <?php endforeach; ?>
            <hr>
            <div class="d-flex justify-content-between fw-bold">
                <span>Total</span><span>R<?= number_format($total, 2) ?></span>
            </div>
        </div>
    </div>
</div>
<?php require_once 'includes/footer.php'; ?>
