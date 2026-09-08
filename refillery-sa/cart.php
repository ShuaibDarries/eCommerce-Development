<?php
require_once 'config/db.php';
require_once 'includes/header.php';

// Update quantities / remove items
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach (($_POST['qty'] ?? []) as $id => $qty) {
        $qty = (int)$qty;
        if ($qty <= 0) unset($_SESSION['cart'][$id]);
        else $_SESSION['cart'][$id] = $qty;
    }
    if (isset($_POST['remove'])) unset($_SESSION['cart'][(int)$_POST['remove']]);
    header('Location: cart.php');
    exit;
}

$cart = $_SESSION['cart'] ?? [];
$items = [];
$total = 0;
if ($cart) {
    $ids = implode(',', array_map('intval', array_keys($cart)));
    foreach (db()->query("SELECT * FROM products WHERE product_id IN ($ids)") as $p) {
        $qty = $cart[$p['product_id']];
        $line = $p['price'] * $qty;
        $total += $line;
        $items[] = ['p' => $p, 'qty' => $qty, 'line' => $line];
    }
}
?>
<h1 class="h3 mb-3">Your cart</h1>
<?php if (!$items): ?>
    <div class="alert alert-info">Your cart is empty. <a href="index.php">Start shopping</a>.</div>
<?php else: ?>
<form method="post">
    <table class="table align-middle">
        <thead><tr><th>Product</th><th>Price</th><th style="width:110px">Quantity</th><th>Subtotal</th><th></th></tr></thead>
        <tbody>
        <?php foreach ($items as $it): ?>
            <tr>
                <td><?= htmlspecialchars($it['p']['name']) ?></td>
                <td>R<?= number_format($it['p']['price'], 2) ?></td>
                <td><input type="number" name="qty[<?= $it['p']['product_id'] ?>]" value="<?= $it['qty'] ?>" min="0" class="form-control form-control-sm"></td>
                <td>R<?= number_format($it['line'], 2) ?></td>
                <td><button name="remove" value="<?= $it['p']['product_id'] ?>" class="btn btn-sm btn-outline-danger">&times;</button></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
        <tfoot><tr><th colspan="3" class="text-end">Total</th><th>R<?= number_format($total, 2) ?></th><th></th></tr></tfoot>
    </table>
    <div class="d-flex gap-2">
        <button class="btn btn-outline-success">Update cart</button>
        <a href="checkout.php" class="btn btn-success">Proceed to Checkout</a>
    </div>
</form>
<?php endif; ?>
<?php require_once 'includes/footer.php'; ?>
