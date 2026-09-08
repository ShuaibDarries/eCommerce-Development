<?php
/** Loads cart line items + total from the database (live data, no hardcoding). */
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
if (!$items) { header('Location: cart.php'); exit; } // nothing to check out
