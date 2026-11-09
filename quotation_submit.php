<?php
require_once __DIR__ . '/config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $product_name = trim($_POST['product_name'] ?? '');
    $quantity = trim($_POST['quantity'] ?? '');
    $message = trim($_POST['message'] ?? '');

    if (!empty($full_name) && !empty($email)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO quotations (full_name, email, phone, product_name, quantity, message) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$full_name, $email, $phone, $product_name, $quantity, $message]);

            // Track visitor action
            trackVisitor('quotation_submit');

            // Log activity in activity_logs
            $logStmt = $pdo->prepare("INSERT INTO activity_logs (user_id, actor_name, badge, action, time_ago, created_at) VALUES (NULL, ?, 'Visitor', ?, 'Just now', NOW())");
            $logStmt->execute([$full_name, "Requested quotation for " . ($product_name ? $product_name : 'products')]);

            // Owner WhatsApp number (the same number previously used for direct WhatsApp: 6281333775663)
            $owner_wa = '6281333775663';

            // Construct professional pre-filled WhatsApp message
            $wa_text = "Halo X BAR, saya telah mengirim Permintaan Penawaran (Request Quotation) melalui website:\n\n"
                     . "• Nama Lengkap: " . $full_name . "\n"
                     . "• Email: " . $email . "\n"
                     . "• No. Telepon / WA: " . (!empty($phone) ? $phone : '-') . "\n"
                     . "• Produk: " . (!empty($product_name) ? $product_name : 'Semua Produk') . "\n"
                     . "• Perkiraan Jumlah: " . (!empty($quantity) ? $quantity : '-') . "\n"
                     . "• Catatan / Pelabuhan Tujuan: " . (!empty($message) ? $message : '-') . "\n\n"
                     . "Mohon informasi penawaran harga terbaik (FOB/CIF) dan ketersediaan stok. Terima kasih.";

            $wa_url = "https://wa.me/" . $owner_wa . "?text=" . rawurlencode($wa_text);

            ?>
            <!DOCTYPE html>
            <html lang="id">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>Mengalihkan ke WhatsApp...</title>
                <meta http-equiv="refresh" content="2;url=<?= htmlspecialchars($wa_url, ENT_QUOTES) ?>">
                <style>
                    body {
                        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
                        background: #000000;
                        color: #ffffff;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        min-height: 100vh;
                        margin: 0;
                        padding: 20px;
                        box-sizing: border-box;
                    }
                    .redirect-box {
                        background: #111827;
                        border: 1px solid rgba(255, 255, 255, 0.15);
                        border-radius: 20px;
                        padding: 32px 24px;
                        max-width: 440px;
                        text-align: center;
                        box-shadow: 0 20px 50px rgba(0, 0, 0, 0.6);
                    }
                    .icon-circle {
                        width: 58px;
                        height: 58px;
                        background: #22c55e;
                        border-radius: 50%;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        margin: 0 auto 16px;
                        color: #ffffff;
                    }
                    h2 {
                        font-size: 20px;
                        margin: 0 0 8px;
                        font-weight: 800;
                    }
                    p {
                        font-size: 14px;
                        color: #94a3b8;
                        line-height: 1.6;
                        margin: 0 0 22px;
                    }
                    .btn-wa-direct {
                        display: inline-flex;
                        align-items: center;
                        justify-content: center;
                        gap: 8px;
                        background: #22c55e;
                        color: #ffffff;
                        text-decoration: none;
                        font-weight: 800;
                        padding: 12px 28px;
                        border-radius: 999px;
                        font-size: 14px;
                        transition: background 0.2s ease;
                    }
                    .btn-wa-direct:hover {
                        background: #16a34a;
                    }
                </style>
            </head>
            <body>
                <div class="redirect-box">
                    <div class="icon-circle">
                        <svg width="28" height="28" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.711 2.598 2.664-.698c.974.532 1.769.813 2.796.813h.001c3.182 0 5.768-2.586 5.768-5.766 0-3.18-2.586-5.766-5.769-5.766zm3.364 8.163c-.144.405-.837.774-1.17.822-.312.043-.683.076-1.99-.466-1.579-.654-2.583-2.27-2.661-2.374-.078-.105-.638-.85-0.638-1.621 0-.771.403-1.15.546-1.306.144-.157.313-.196.417-.196.104 0 .209.002.3.007.096.005.226-.036.353.27.13.314.444 1.085.483 1.164.039.078.065.17.013.274-.052.105-.078.17-.156.262-.078.092-.164.205-.235.275-.078.078-.16.163-.069.32.091.156.406.669.871 1.083.598.533 1.102.698 1.258.776.156.078.247.065.339-.039.091-.105.391-.456.495-.613.104-.157.208-.131.348-.078.14.052.887.418 1.04.496.152.078.253.117.291.183.039.065.039.379-.105.784z"/>
                        </svg>
                    </div>
                    <h2>Permintaan Terkirim!</h2>
                    <p>Permintaan penawaran Anda berhasil disimpan. Anda sedang dialihkan langsung ke WhatsApp pemilik...</p>
                    <a href="<?= htmlspecialchars($wa_url, ENT_QUOTES) ?>" class="btn-wa-direct">Buka WhatsApp Sekarang</a>
                </div>
                <script>
                    setTimeout(function() {
                        window.location.href = <?= json_encode($wa_url) ?>;
                    }, 400);
                </script>
            </body>
            </html>
            <?php
            exit;
        } catch (Exception $e) {
            echo "<script>
                alert('Terjadi kendala saat memproses permintaan. Anda akan dialihkan ke WhatsApp.');
                window.location.href = 'https://wa.me/6281333775663';
            </script>";
            exit;
        }
    }
}

header('Location: index.php');
exit;
