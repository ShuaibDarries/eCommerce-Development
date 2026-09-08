<?php
require_once 'config/db.php';
require_once 'includes/header.php';
require_once 'includes/auth.php';
require_login();

$stmt = db()->prepare('SELECT * FROM orders WHERE customer_id = ? ORDER BY created_at DESC');
$stmt->execute([$_SESSION['customer_id']]);
$orders = $stmt->fetchAll();
?>
<h1 class="h3 mb-3">My orders</h1>
<?php if (!$orders): ?>
    <div class="alert alert-info">You haven't placed any orders yet. <a href="index.php">Shop now</a>.</div>
<?php else: ?>
    <table class="table">
        <thead><tr><th>Order #</th><th>Date</th><th>Status</th><th class="text-end">Total</th></tr></thead>
        <tbody>
        <?php foreach ($orders as $o): ?>
            <tr>
                <td>#<?= $o['order_id'] ?></td>
                <td><?= $o['created_at'] ?></td>
                <td><span class="badge bg-<?= $o['status'] === 'paid' ? 'success' : 'warning' ?>"><?= ucfirst($o['status']) ?></span></td>
                <td class="text-end">R<?= number_format($o['total'], 2) ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>
<?php require_once 'includes/footer.php'; ?>
