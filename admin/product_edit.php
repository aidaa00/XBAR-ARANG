<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/auth_check.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id <= 0) {
    header("Location: products.php");
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM products WHERE id = :id");
$stmt->execute([':id' => $id]);
$product = $stmt->fetch();

if (!$product) {
    $_SESSION['flash_error'] = "Product not found.";
    header("Location: products.php");
    exit;
}

$error = '';
$success = '';

// Handle Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $stock = trim($_POST['stock'] ?? '');
    $recentPrice = trim($_POST['recent_price'] ?? '');
    $rawMaterial = trim($_POST['raw_material'] ?? '');
    $description = trim($_POST['description'] ?? '');

    if (empty($name)) {
        $error = "Product name cannot be empty.";
    } else {
        $imagePath = $product['image'] ?? $product['image_path'] ?? 'assets/images/mix-charcoal.jpg';

        // Handle File Upload if provided
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $allowedTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
            $fileTmp = $_FILES['image']['tmp_name'];
            $fileType = mime_content_type($fileTmp);

            if (in_array($fileType, $allowedTypes)) {
                $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
                $newFilename = 'prod_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
                $targetDir = __DIR__ . '/../uploads/';
                if (!is_dir($targetDir)) {
                    mkdir($targetDir, 0755, true);
                }
                $targetFile = $targetDir . $newFilename;

                if (move_uploaded_file($fileTmp, $targetFile)) {
                    $imagePath = 'uploads/' . $newFilename;
                } else {
                    $error = "Failed to upload image.";
                }
            } else {
                $error = "Invalid file type. Only JPG, PNG, WEBP, and GIF are allowed.";
            }
        }

        if (empty($error)) {
            $updateStmt = $pdo->prepare("
                UPDATE products 
                SET name = :name, 
                    product_stock = :stock, 
                    recent_price = :recent_price, 
                    raw_material = :raw_material, 
                    description = :description, 
                    image = :image 
                WHERE id = :id
            ");

            $updateStmt->execute([
                ':name' => $name,
                ':stock' => $stock,
                ':recent_price' => $recentPrice,
                ':raw_material' => $rawMaterial,
                ':description' => $description,
                ':image' => $imagePath,
                ':id' => $id
            ]);

            // Log activity
            logActivity("Updated product '$name'");

            $_SESSION['flash_success'] = "Product '$name' updated successfully!";
            header("Location: products.php");
            exit;
        }
    }
}

// Prepare image display
$currentImg = $product['image'] ?? $product['image_path'] ?? 'assets/images/mix-charcoal.jpg';
if (!file_exists(__DIR__ . '/../' . $currentImg) && !file_exists(__DIR__ . '/../assets/images/' . basename($currentImg))) {
    $imgSrc = '../assets/images/mix-charcoal.jpg';
} else {
    $imgSrc = '../' . htmlspecialchars($currentImg);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product — <?= htmlspecialchars($product['name']) ?> — X BAR Admin</title>
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

    <!-- EDIT PRODUCT CONTAINER (PAGE 7) -->
    <main class="form-card-container">
        <h1 class="form-page-title" data-en="Edit Product" data-id="Edit Produk">Edit Product</h1>

        <?php if ($error): ?>
            <div class="alert-banner alert-error" style="margin-bottom:24px;">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form action="product_edit.php?id=<?= $id ?>" method="POST" enctype="multipart/form-data">
            <div class="prod-form-grid">
                <!-- Left Column: Image Preview & Upload Button -->
                <div class="prod-upload-col">
                    <div class="prod-img-preview-box">
                        <img src="<?= $imgSrc ?>" alt="<?= htmlspecialchars($product['name']) ?>" id="imagePreviewImg">
                    </div>

                    <input type="file" id="imageFileInput" name="image" accept="image/*" style="display:none;">
                    <button type="button" id="btnTriggerUpload" class="btn-change-image" data-en="Change Image" data-id="Ganti Gambar">Change Image</button>
                </div>

                <!-- Right Column: Product Fields -->
                <div class="prod-fields-col">
                    <!-- Product Name -->
                    <div class="admin-input-card">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                            <circle cx="8.5" cy="8.5" r="1.5"></circle>
                            <polyline points="21 15 16 10 5 21"></polyline>
                        </svg>
                        <div class="input-card-inner">
                            <label class="input-card-label" data-en="Product Name" data-id="Nama Produk">Product Name</label>
                            <input type="text" name="name" value="<?= htmlspecialchars($product['name']) ?>" required>
                        </div>
                    </div>

                    <!-- Stock and Recent Price Row -->
                    <div class="admin-inputs-row-2">
                        <!-- Stock -->
                        <div class="admin-input-card">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path>
                            </svg>
                            <div class="input-card-inner">
                                <label class="input-card-label" data-en="Stock" data-id="Stok">Stock</label>
                                <input type="text" name="stock" value="<?= htmlspecialchars($product['product_stock'] ?? $product['stock'] ?? '') ?>" placeholder="e.g. 5,000 kg" data-en="e.g. 5,000 kg" data-id="Contoh: 5.000 kg" required>
                            </div>
                        </div>

                        <!-- Recent Price -->
                        <div class="admin-input-card">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <line x1="12" y1="1" x2="12" y2="23"></line>
                                <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
                            </svg>
                            <div class="input-card-inner">
                                <label class="input-card-label" data-en="Recent Price" data-id="Harga Terbaru">Recent Price</label>
                                <input type="text" name="recent_price" value="<?= htmlspecialchars($product['recent_price'] ?? $product['price']) ?>" placeholder="e.g. Rp 12.000 / kg" data-en="e.g. Rp 12.000 / kg" data-id="Contoh: Rp 12.000 / kg" required>
                            </div>
                        </div>
                    </div>

                    <!-- Raw Material -->
                    <div class="admin-input-card">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M12 2L2 7l10 5 10-5-10-5z"></path>
                            <path d="M2 17l10 5 10-5"></path>
                            <path d="M2 12l10 5 10-5"></path>
                        </svg>
                        <div class="input-card-inner">
                            <label class="input-card-label" data-en="Raw Material" data-id="Bahan Baku">Raw Material</label>
                            <input type="text" name="raw_material" value="<?= htmlspecialchars($product['raw_material']) ?>" placeholder="e.g. 100% Kayu Asam Jawa Murni" data-en="e.g. 100% Kayu Asam Jawa Murni" data-id="Contoh: 100% Kayu Asam Jawa Murni">
                        </div>
                    </div>

                    <!-- Description -->
                    <div class="admin-textarea-card">
                        <label class="input-card-label" style="display:block;margin-bottom:6px;" data-en="Description" data-id="Deskripsi Produk">Description</label>
                        <textarea name="description" placeholder="Enter detailed product description and specifications..." data-en="Enter detailed product description and specifications..." data-id="Masukkan deskripsi detail produk dan spesifikasi..."><?= htmlspecialchars($product['description']) ?></textarea>
                    </div>
                </div>
            </div>

            <!-- Bottom Actions -->
            <div class="form-bottom-actions" style="gap:16px;">
                <a href="products.php" class="btn-change-image" style="background:#e2e8f0;color:#0f172a;text-decoration:none;display:inline-block;width:auto;padding:14px 28px;" data-en="Cancel" data-id="Batal">Cancel</a>
                <button type="submit" class="btn-save-changes" data-en="Save Changes" data-id="Simpan Perubahan">Save Changes</button>
            </div>
        </form>
    </main>

    <script src="../assets/js/admin.js"></script>
</body>
</html>
