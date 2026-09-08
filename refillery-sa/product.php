<?php
require_once 'config/db.php';
require_once 'includes/header.php';

$id = (int)($_GET['id'] ?? 0);
$stmt = db()->prepare('SELECT * FROM products WHERE product_id = ? AND stock > 0');
$stmt->execute([$id]);
$p = $stmt->fetch();
if (!$p) { echo '<div class="alert alert-danger">Product not found.</div>'; require 'includes/footer.php'; exit; }

// Handle add-to-cart
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $qty = max(1, (int)$_POST['quantity']);
    $_SESSION['cart'][$id] = ($_SESSION['cart'][$id] ?? 0) + $qty;
    header('Location: cart.php');
    exit;
}
?>
<div class="row">
    <div class="col-md-5">
        <div class="product-img product-img-lg d-flex align-items-center justify-content-center fs-1" style="background:#2e7d32">🌱</div>
    </div>
    <div class="col-md-7">
        <span class="badge bg-success-subtle text-success"><?= htmlspecialchars($p['category']) ?></span>
        <h1 class="h3 mt-1"><?= htmlspecialchars($p['name']) ?></h1>
        <p class="lead fw-bold">R<?= number_format($p['price'], 2) ?></p>
        <p><?= nl2br(htmlspecialchars($p['description'])) ?></p>
        <p class="<?= $p['stock'] < 10 ? 'text-danger' : 'text-muted' ?> small">
            <?= $p['stock'] < 10 ? 'Only ' . $p['stock'] . ' left in stock!' : $p['stock'] . ' in stock' ?>
        </p>
        <form method="post" class="d-flex gap-2 align-items-center">
            <input type="number" name="quantity" value="1" min="1" max="<?= $p['stock'] ?>" class="form-control" style="width:90px">
            <button class="btn btn-success">Add to Cart</button>
        </form>
    </div>
</div>
<?php require_once 'includes/footer.php'; ?>
