<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/auth_check.php';

// Fetch all products
$search = trim($_GET['q'] ?? '');
if ($search !== '') {
    $searchTerm = "%$search%";
    $stmt = $pdo->prepare("SELECT * FROM products WHERE name LIKE ? OR raw_material LIKE ? ORDER BY id ASC");
    $stmt->execute([$searchTerm, $searchTerm]);
} else {
    $stmt = $pdo->query("SELECT * FROM products ORDER BY id ASC");
}
$products = $stmt->fetchAll();

$totalCountStmt = $pdo->query("SELECT COUNT(*) FROM products");
$totalProducts = $totalCountStmt->fetchColumn();

// Flash messages
$flashSuccess = $_SESSION['flash_success'] ?? null;
$flashError = $_SESSION['flash_error'] ?? null;
unset($_SESSION['flash_success'], $_SESSION['flash_error']);

$adminName = $_SESSION['admin_name'] ?? 'Administrator';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products Management — X BAR Admin</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
    <link rel="shortcut icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>🔥</text></svg>">
</head>
<body>

    <!-- TOP NAVBAR -->
    <header class="admin-navbar">
        <div class="admin-nav-left">
            <a href="index.php" class="admin-brand">
                <span class="auth-logo" style="width:32px;height:32px;font-size:16px;margin:0;">X̄</span>
                <span>X BAR</span>
            </a>
        </div>

        <ul class="admin-nav-links">
            <li><a href="index.php" data-en="Dashboard" data-id="Dashboard">Dashboard</a></li>
            <li><a href="products.php" class="active" data-en="Product" data-id="Produk">Product</a></li>
            <li><a href="website.php" data-en="Website" data-id="Website">Website</a></li>
        </ul>

        <div class="admin-nav-right">
            <div class="admin-lang lang-switch">
                <span class="lang-item" data-lang="id">Indonesia</span>
                <span class="lang-sep">/</span>
                <span class="lang-item active" data-lang="en">English</span>
            </div>
            <a href="logout.php" class="admin-logout-btn" title="Logout">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                    <polyline points="16 17 21 12 16 7"></polyline>
                    <line x1="21" y1="12" x2="9" y2="12"></line>
                </svg>
            </a>
        </div>
    </header>

    <!-- MANAGE PRODUCTS CONTENT (PAGE 5) -->
    <main class="manage-page-layout">
        <!-- Flash notifications -->
        <?php if ($flashSuccess): ?>
            <div class="alert-banner alert-success">
                <?= htmlspecialchars($flashSuccess) ?>
            </div>
        <?php endif; ?>
        <?php if ($flashError): ?>
            <div class="alert-banner alert-error">
                <?= htmlspecialchars($flashError) ?>
            </div>
        <?php endif; ?>

        <!-- Top Header: Title, Search, and Add Product Button -->
        <div class="manage-top-header">
            <h1 class="manage-title" data-en="Products" data-id="Produk">Products</h1>

            <div style="display:flex;align-items:center;gap:16px;">
                <form action="products.php" method="GET" class="manage-search-wrap">
                    <input type="text" id="productSearchInput" name="q" placeholder="Search..." data-en="Search..." data-id="Cari Produk..." value="<?= htmlspecialchars($search) ?>" autocomplete="off">
                    <button type="submit" title="Search">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="11" cy="11" r="8"></circle>
                            <line x1="21" y1="12" x2="16.65" y2="16.65"></line>
                        </svg>
                    </button>
                </form>

                <a href="product_add.php" class="btn-add-product-pill">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <line x1="12" y1="5" x2="12" y2="19"></line>
                        <line x1="5" y1="12" x2="19" y2="12"></line>
                    </svg>
                    <span data-en="Add Product" data-id="Tambah Produk">Add Product</span>
                </a>
            </div>
        </div>

        <!-- Products Management Card -->
        <div class="products-mgmt-card">
            <div class="mgmt-card-header">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect>
                    <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path>
                </svg>
                <div>
                    <h3><?= htmlspecialchars($totalProducts) ?> <span data-en="Total Products" data-id="Total Produk">Total Products</span></h3>
                    <p data-en="Manage all catalog offerings, technical specifications, and inventory updates." data-id="Kelola seluruh katalog penawaran, spesifikasi teknis, dan pembaruan stok.">Manage all catalog offerings, technical specifications, and inventory updates.</p>
                </div>
            </div>

            <div class="mgmt-product-list">
                <?php if (empty($products)): ?>
                    <div style="text-align:center;padding:40px;color:#64748b;">
                        <p style="font-size:16px;font-weight:600;" data-en="No products found." data-id="Tidak ada produk ditemukan.">No products found.</p>
                        <a href="product_add.php" style="display:inline-block;margin-top:12px;color:#000;font-weight:700;" data-en="+ Add your first product" data-id="+ Tambah produk pertama Anda">+ Add your first product</a>
                    </div>
                <?php else: ?>
                    <?php foreach ($products as $p): 
                        $img = $p['image'] ?? $p['image_path'] ?? 'assets/images/mix-charcoal.jpg';
                        if (!file_exists(__DIR__ . '/../' . $img) && !file_exists(__DIR__ . '/../assets/images/' . basename($img))) {
                            $imgSrc = '../assets/images/mix-charcoal.jpg';
                        } else {
                            $imgSrc = '../' . htmlspecialchars($img);
                        }
                        $stockVal = $p['product_stock'] ?? $p['stock'] ?? '10 Tons';
                    ?>
                        <div class="mgmt-product-row">
                            <div class="mgmt-prod-left">
                                <div class="mgmt-prod-thumb">
                                    <img src="<?= $imgSrc ?>" alt="<?= htmlspecialchars($p['name']) ?>">
                                </div>
                                <div>
                                    <h4 class="mgmt-prod-name"><?= strtoupper(htmlspecialchars($p['name'])) ?></h4>
                                    <div style="font-size:13px;color:#64748b;margin-top:4px;">
                                        <span data-en="Stock:" data-id="Stok:">Stock:</span> <strong><?= htmlspecialchars($stockVal) ?></strong> | <span data-en="Price:" data-id="Harga:">Price:</span> <strong><?= htmlspecialchars($p['recent_price'] ?? $p['price'] ?? 'Hubungi Kami') ?></strong>
                                    </div>
                                </div>
                            </div>

                            <div class="mgmt-prod-actions">
                                <a href="product_edit.php?id=<?= $p['id'] ?>" class="btn-prod-edit" data-en="Edit" data-id="Ubah">Edit</a>
                                <a href="product_delete.php?id=<?= $p['id'] ?>" class="btn-prod-delete btn-confirm-delete" data-name="<?= htmlspecialchars($p['name']) ?>" data-en="Delete" data-id="Hapus">Delete</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <script src="../assets/js/admin.js"></script>
</body>
</html>
