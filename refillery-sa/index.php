<?php
require_once 'config/db.php';
require_once 'includes/header.php';

// Fetch categories for the filter dropdown
$categories = db()->query('SELECT DISTINCT category FROM products ORDER BY category')->fetchAll(PDO::FETCH_COLUMN);
$search = trim($_GET['q'] ?? '');
$category = trim($_GET['category'] ?? '');

// Build a safe, dynamic query — search + category filter from user input
$sql = 'SELECT * FROM products WHERE stock > 0';
$params = [];
if ($search !== '')   { $sql .= ' AND (name LIKE ? OR description LIKE ?)'; $params[] = "%$search%"; $params[] = "%$search%"; }
if ($category !== '') { $sql .= ' AND category = ?'; $params[] = $category; }
$sql .= ' ORDER BY name';
$stmt = db()->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll();

function img_for(string $key): string {
    // Colourful placeholder tiles (no external image dependencies)
    $colours = ['#2e7d32','#00695c','#558b2f','#4e342e','#00695c','#33691e','#004d40','#5d4037','#455a64','#1b5e20'];
    $c = $colours[crc32($key) % count($colours)];
    return "<div class='product-img' style='background:$c'>🌱</div>";
}
?>
<h1 class="h3 mb-1">Shop zero-waste products</h1>
<p class="text-muted">Every product plastic-free. Every delivery carbon-aware.</p>

<form class="row g-2 mb-4" method="get" action="index.php">
    <div class="col-md-6">
        <input type="text" name="q" class="form-control" placeholder="Search products…" value="<?= htmlspecialchars($search) ?>">
    </div>
    <div class="col-md-4">
        <select name="category" class="form-select">
            <option value="">All categories</option>
            <?php foreach ($categories as $c): ?>
                <option value="<?= htmlspecialchars($c) ?>" <?= $category === $c ? 'selected' : '' ?>><?= htmlspecialchars($c) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-md-2 d-grid"><button class="btn btn-success">Filter</button></div>
</form>

<?php if (!$products): ?>
    <div class="alert alert-warning">No products matched your search. <a href="index.php">Clear filters</a></div>
<?php endif; ?>

<div class="row g-3">
    <?php foreach ($products as $p): ?>
        <div class="col-6 col-md-3">
            <div class="card h-100 product-card">
                <?= img_for($p['image']) ?>
                <div class="card-body d-flex flex-column">
                    <span class="badge bg-success-subtle text-success mb-1 align-self-start"><?= htmlspecialchars($p['category']) ?></span>
                    <h2 class="h6 card-title"><?= htmlspecialchars($p['name']) ?></h2>
                    <p class="card-text small text-muted flex-grow-1"><?= htmlspecialchars(mb_strimwidth($p['description'], 0, 80, '…')) ?></p>
                    <p class="fw-bold mb-2">R<?= number_format($p['price'], 2) ?></p>
                    <a href="product.php?id=<?= $p['product_id'] ?>" class="btn btn-outline-success btn-sm mt-auto">View &amp; Add to Cart</a>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>
<?php require_once 'includes/footer.php'; ?>
