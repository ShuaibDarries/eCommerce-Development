<?php
require_once 'config/db.php';
require_once 'includes/header.php';
require_once 'includes/auth.php';
require_login();

$id = (int)($_GET['id'] ?? 0);
$stmt = db()->prepare('SELECT * FROM orders WHERE order_id = ? AND customer_id = ?');
$stmt->execute([$id, $_SESSION['customer_id']]);
$order = $stmt->fetch();
if (!$order) { echo '<div class="alert alert-danger">Order not found.</div>'; require 'includes/footer.php'; exit; }

$stmt = db()->prepare(
    'SELECT oi.*, p.name FROM order_items oi JOIN products p ON p.product_id = oi.product_id WHERE oi.order_id = ?');
$stmt->execute([$id]);
$lines = $stmt->fetchAll();
?>
<div class="text-center mb-4">
    <div class="display-4 text-success">✓</div>
    <h1 class="h3">Thank you for your order!</h1>
    <p class="text-muted">Order #<?= $order['order_id'] ?> &mdash; reference <code><?= htmlspecialchars($order['payment_ref']) ?></code></p>
    <span class="badge bg-success">PAID</span>
</div>
<div class="col-md-7 mx-auto">
    <div class="card card-body">
        <h2 class="h6">Items</h2>
        <?php foreach ($lines as $l): ?>
            <div class="d-flex justify-content-between small">
                <span><?= $l['quantity'] ?> × <?= htmlspecialchars($l['name']) ?></span>
                <span>R<?= number_format($l['unit_price'] * $l['quantity'], 2) ?></span>
            </div>
        <?php endforeach; ?>
        <hr>
        <div class="d-flex justify-content-between fw-bold">
            <span>Total paid</span><span>R<?= number_format($order['total'], 2) ?></span>
        </div>
        <p class="small text-muted mt-3 mb-0">Delivering to: <?= htmlspecialchars($order['shipping_address']) ?></p>
        <p class="small text-muted">Placed on: <?= $order['created_at'] ?></p>
        <a href="index.php" class="btn btn-outline-success">Continue shopping</a>
    </div>
</div>
<?php require_once 'includes/footer.php'; ?>
