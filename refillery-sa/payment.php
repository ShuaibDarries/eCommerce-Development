<?php
require_once 'config/db.php';
require_once 'includes/header.php';
require_once 'includes/auth.php';
require_once 'includes/cart_data.php';
require_once 'includes/auth.php';
require_login();

// No checkout details yet? Go back to checkout
if (!isset($_SESSION['checkout'])) { header('Location: checkout.php'); exit; }
$checkout = $_SESSION['checkout'];
$errors = [];

/**
 * Simulated PayFast gateway.
 * Mirrors the real gateway flow: customer enters mock card details, the bank
 * "responds", and success/failure is clearly communicated. A declined payment
 * returns the customer to checkout with the cart preserved.
 */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $card = preg_replace('/\s+/', '', $_POST['card_number']);
    $exp  = $_POST['expiry'];
    $cvv  = $_POST['cvv'];

    if (!preg_match('/^\d{16}$/', $card))          $errors[] = 'Card number must be 16 digits.';
    if (!preg_match('/^\d{2}\/\d{2}$/', $exp))   $errors[] = 'Expiry must be in MM/YY format.';
    if (!preg_match('/^\d{3,4}$/', $cvv))          $errors[] = 'CVV must be 3 or 4 digits.';

    if (!$errors) {
        // Simulated bank response (80% approval rate for demo realism)
        $approved = random_int(1, 100) <= 80;

        if ($approved) {
            $pdo = db();
            $pdo->beginTransaction();
            try {
                // 1. Create the order (paid)
                $stmt = $pdo->prepare('INSERT INTO orders (customer_id, total, status, shipping_address, payment_ref) VALUES (?,?,?,?,?)');
                $ref = 'PF-SIM-' . strtoupper(bin2hex(random_bytes(4)));
                $stmt->execute([$_SESSION['customer_id'], $checkout['total'], 'paid', $checkout['shipping_address'], $ref]);
                $orderId = $pdo->lastInsertId();

                // 2. Create order line items + decrement stock
                $itemStmt = $pdo->prepare('INSERT INTO order_items (order_id, product_id, quantity, unit_price) VALUES (?,?,?,?)');
                $stockStmt = $pdo->prepare('UPDATE products SET stock = stock - ? WHERE product_id = ? AND stock >= ?');
                foreach ($items as $it) {
                    $itemStmt->execute([$orderId, $it['p']['product_id'], $it['qty'], $it['p']['price']]);
                    $stockStmt->execute([$it['qty'], $it['p']['product_id'], $it['qty']]);
                }
                $pdo->commit();
            } catch (Exception $ex) {
                $pdo->rollBack();
                $errors[] = 'Payment could not be recorded. Please try again.';
                require 'includes/footer.php'; exit;
            }

            unset($_SESSION['cart'], $_SESSION['checkout']);
            header('Location: order_confirmation.php?id=' . $orderId);
            exit;
        } else {
            // Declined — keep cart, send back to checkout with clear message
            $_SESSION['payment_error'] = 'Transaction declined by the (simulated) bank. Please try again or use different card details. Your cart was kept.';
            header('Location: checkout.php');
            exit;
        }
    }
}
?>
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-success text-white fw-bold">PayFast &mdash; Secure Checkout (Simulated)</div>
            <div class="card-body">
                <div class="d-flex justify-content-between mb-3">
                    <span>Payment to: <strong>The Refillery SA</strong></span>
                    <span class="fw-bold">R<?= number_format($checkout['total'], 2) ?></span>
                </div>
                <?php foreach ($errors as $e): ?><div class="alert alert-danger py-2"><?= htmlspecialchars($e) ?></div><?php endforeach; ?>
                <form method="post">
                    <label class="form-label">Card number (16 digits)</label>
                    <input name="card_number" class="form-control mb-3" placeholder="4242 4242 4242 4242" required>
                    <div class="row">
                        <div class="col-6">
                            <label class="form-label">Expiry (MM/YY)</label>
                            <input name="expiry" class="form-control mb-3" placeholder="09/28" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label">CVV</label>
                            <input name="cvv" type="password" class="form-control mb-3" placeholder="123" required>
                        </div>
                    </div>
                    <button class="btn btn-success w-100">Pay R<?= number_format($checkout['total'], 2) ?></button>
                    <p class="text-muted small mt-2 mb-0 text-center">🔒 This is a simulated gateway for the capstone project — no real charge occurs.</p>
                </form>
            </div>
        </div>
    </div>
</div>
<?php require_once 'includes/footer.php'; ?>
