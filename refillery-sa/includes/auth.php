<?php
/**
 * Session & authentication helpers.
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/** Is a customer currently logged in? */
function is_logged_in(): bool {
    return isset($_SESSION['customer_id']);
}

/** Redirect to login if not authenticated (used by checkout/account). */
function require_login(): void {
    if (!is_logged_in()) {
        $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
        header('Location: login.php');
        exit;
    }
}

/** Currently logged-in customer's display name. */
function current_customer(): ?array {
    if (!is_logged_in()) return null;
    $stmt = db()->prepare('SELECT customer_id, full_name, email FROM customers WHERE customer_id = ?');
    $stmt->execute([$_SESSION['customer_id']]);
    return $stmt->fetch() ?: null;
}
