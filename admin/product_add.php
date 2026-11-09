<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/auth_check.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $stock = trim($_POST['stock'] ?? '');
    $recentPrice = trim($_POST['recent_price'] ?? '');
    $rawMaterial = trim($_POST['raw_material'] ?? '');
    $description = trim($_POST['description'] ?? '');

    if (empty($name)) {
        $error = "Product name is required.";
    } else {
        $imagePath = 'assets/images/mix-charcoal.jpg'; // default fallback

        // Handle image upload
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
            $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name))) . '-' . time();
            $insertStmt = $pdo->prepare("
                INSERT INTO products (name, slug, category, product_stock, recent_price, raw_material, description, image, created_at)
                VALUES (:name, :slug, :category, :stock, :recent_price, :raw_material, :description, :image, NOW())
            ");

            $insertStmt->execute([
                ':name' => $name,
                ':slug' => $slug,
                ':category' => 'Charcoal',
                ':stock' => $stock ?: '10 Tons',
                ':recent_price' => $recentPrice ?: 'Hubungi Kami',
                ':raw_material' => $rawMaterial ?: '-',
                ':description' => $description ?: '',
                ':image' => $imagePath
            ]);

            // Log activity
            logActivity("Created product '$name'");

            $_SESSION['flash_success'] = "Product '$name' added successfully!";
            header("Location: products.php");
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Product — X BAR Admin</title>
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

    <!-- ADD PRODUCT CONTAINER (PAGE 9) -->
    <main class="form-card-container">
        <h1 class="form-page-title" data-en="Add Product" data-id="Tambah Produk">Add Product</h1>

        <?php if ($error): ?>
            <div class="alert-banner alert-error" style="margin-bottom:24px;">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form action="product_add.php" method="POST" enctype="multipart/form-data">
            <div class="prod-form-grid">
                <!-- Left Column: Upload Placeholder / Image Preview Box -->
                <div class="prod-upload-col">
                    <div class="prod-img-preview-box" id="uploadDropArea" style="cursor:pointer;">
                        <!-- Hidden Image Preview -->
                        <img src="" alt="Preview" id="imagePreviewImg" style="display:none;width:100%;height:100%;object-fit:cover;">

                        <!-- Default Placeholder content -->
                        <div id="uploadPlaceholder">
                            <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="1.5" style="margin-bottom:12px;">
                                <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                                <circle cx="8.5" cy="8.5" r="1.5"></circle>
                                <polyline points="21 15 16 10 5 21"></polyline>
                            </svg>
                            <div class="upload-placeholder-text" data-en="Click or Drop Image" data-id="Klik atau Tarik Gambar">Click or Drop Image</div>
                            <div class="upload-placeholder-sub" data-en="JPG, PNG, WEBP max 5MB" data-id="JPG, PNG, WEBP maks 5MB">JPG, PNG, WEBP max 5MB</div>
                        </div>
                    </div>

                    <input type="file" id="imageFileInput" name="image" accept="image/*" style="display:none;">
                    <button type="button" id="btnTriggerUpload" class="btn-change-image" data-en="Choose Product Image" data-id="Pilih Gambar Produk">Choose Product Image</button>
                </div>

                <!-- Right Column: Product Form Fields -->
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
                            <input type="text" name="name" placeholder="e.g. Tamarind Charcoal" data-en="e.g. Tamarind Charcoal" data-id="Contoh: Arang Asam Jawa" required>
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
                                <input type="text" name="stock" placeholder="e.g. 5,000 kg" data-en="e.g. 5,000 kg" data-id="Contoh: 5.000 kg" required>
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
                                <input type="text" name="recent_price" placeholder="e.g. Rp 12.000 / kg" data-en="e.g. Rp 12.000 / kg" data-id="Contoh: Rp 12.000 / kg" required>
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
                            <input type="text" name="raw_material" placeholder="e.g. 100% Kayu Asam Jawa Pilihan" data-en="e.g. 100% Kayu Asam Jawa Pilihan" data-id="Contoh: 100% Kayu Asam Jawa Pilihan">
                        </div>
                    </div>

                    <!-- Description -->
                    <div class="admin-textarea-card">
                        <label class="input-card-label" style="display:block;margin-bottom:6px;" data-en="Description" data-id="Deskripsi Produk">Description</label>
                        <textarea name="description" placeholder="Enter detailed product description, specifications, calorific value, ash content, and export standards..." data-en="Enter detailed product description, specifications, calorific value, ash content, and export standards..." data-id="Masukkan deskripsi detail produk, spesifikasi, nilai kalori, kadar abu, dan standar ekspor..."></textarea>
                    </div>
                </div>
            </div>

            <!-- Bottom Actions -->
            <div class="form-bottom-actions" style="gap:16px;">
                <a href="products.php" class="btn-change-image" style="background:#e2e8f0;color:#0f172a;text-decoration:none;display:inline-block;width:auto;padding:14px 28px;" data-en="Cancel" data-id="Batal">Cancel</a>
                <button type="submit" class="btn-save-changes" data-en="Save Product" data-id="Simpan Produk">Save Product</button>
            </div>
        </form>
    </main>

    <script src="../assets/js/admin.js"></script>
    <script>
        // Make the drop area trigger file upload click as well
        document.getElementById('uploadDropArea')?.addEventListener('click', () => {
            document.getElementById('imageFileInput')?.click();
        });
    </script>
</body>
</html>
