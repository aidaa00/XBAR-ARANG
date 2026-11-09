<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/auth_check.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id > 0) {
    // Fetch product to get details
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = :id");
    $stmt->execute([':id' => $id]);
    $product = $stmt->fetch();

    if ($product) {
        $prodName = $product['name'];

        // If uploaded file is in uploads/, delete it to keep storage clean
        $prodImg = $product['image'] ?? $product['image_path'] ?? '';
        if (!empty($prodImg) && str_starts_with($prodImg, 'uploads/')) {
            $filePath = __DIR__ . '/../' . $prodImg;
            if (file_exists($filePath)) {
                @unlink($filePath);
            }
        }

        // Delete from database
        $delStmt = $pdo->prepare("DELETE FROM products WHERE id = :id");
        $delStmt->execute([':id' => $id]);

        // Log activity
        logActivity("Deleted product '$prodName'");

        $_SESSION['flash_success'] = "Product '$prodName' was successfully deleted.";
    } else {
        $_SESSION['flash_error'] = "Product not found.";
    }
} else {
    $_SESSION['flash_error'] = "Invalid product ID.";
}

header("Location: products.php");
exit;
