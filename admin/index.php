<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/auth_check.php';

// Product total count
$stmtCount = $pdo->query("SELECT COUNT(*) FROM products");
$totalProducts = $stmtCount->fetchColumn();

// Recent activities
$stmtLogs = $pdo->query("SELECT * FROM activity_logs ORDER BY id DESC LIMIT 8");
$activities = $stmtLogs->fetchAll();

// Last admin activity (e.g. managing website or products)
$stmtLastAct = $pdo->query("SELECT created_at FROM activity_logs WHERE LOWER(badge) = 'admin' ORDER BY id DESC LIMIT 1");
$lastActRow = $stmtLastAct->fetch();
if (!$lastActRow) {
    $stmtLastAct = $pdo->query("SELECT created_at FROM activity_logs ORDER BY id DESC LIMIT 1");
    $lastActRow = $stmtLastAct->fetch();
}

$lastActTimestamp = ($lastActRow && !empty($lastActRow['created_at'])) ? strtotime($lastActRow['created_at']) : 0;
$initialDiff = $lastActTimestamp > 0 ? max(0, time() - $lastActTimestamp) : -1;
$lastActivity = $lastActTimestamp > 0 ? calculateTimeAgo($lastActRow['created_at']) : [
    'en' => 'No activity',
    'id' => 'Belum ada'
];
$lastActivityEn = ucwords($lastActivity['en']);
$lastActivityId = $lastActivity['id'];

// Product Inventory Data & Analysis
$stmtInv = $pdo->query("SELECT * FROM products ORDER BY id ASC");
$inventoryProducts = $stmtInv->fetchAll();

foreach ($inventoryProducts as &$p) {
    $p['tons'] = parseStockInTons($p['product_stock'] ?? '');
}
unset($p);

// Sorted for Treemap inventory blocks (highest stock first)
$sortedInvProducts = $inventoryProducts;
usort($sortedInvProducts, function($a, $b) {
    if ($b['tons'] == $a['tons']) return $a['id'] - $b['id'];
    return ($b['tons'] > $a['tons']) ? 1 : -1;
});

// Low Stock Analysis (Threshold < 30 Tons)
$lowStockThreshold = 30.0;
$lowStockItems = array_values(array_filter($inventoryProducts, function($p) use ($lowStockThreshold) {
    return $p['tons'] < $lowStockThreshold;
}));
// Sort low stock by stock ascending (most critical first)
usort($lowStockItems, function($a, $b) {
    if ($a['tons'] == $b['tons']) return $a['id'] - $b['id'];
    return ($a['tons'] > $b['tons']) ? 1 : -1;
});

// Inventory Health Score Calculation
$totalInventoryCount = count($inventoryProducts);
$lowStockCount = count($lowStockItems);
$healthyInventoryCount = $totalInventoryCount - $lowStockCount;

if ($totalInventoryCount > 0) {
    $inventoryHealthScore = round(($healthyInventoryCount / $totalInventoryCount) * 100);
} else {
    $inventoryHealthScore = 100;
}

if ($inventoryHealthScore >= 80) {
    $healthStatusEn = 'Healthy';
    $healthStatusId = 'Sehat';
    $healthBorderColor = '#22c55e'; // Green
} elseif ($inventoryHealthScore >= 50) {
    $healthStatusEn = 'Moderate';
    $healthStatusId = 'Perhatian';
    $healthBorderColor = '#f59e0b'; // Amber
} else {
    $healthStatusEn = 'Critical';
    $healthStatusId = 'Kritis';
    $healthBorderColor = '#ef4444'; // Red
}

// Current admin info
$adminName = $_SESSION['admin_name'] ?? 'Administrator';
$adminAvatar = $_SESSION['admin_avatar'] ?? 'assets/images/admin-avatar.jpg';
if (!file_exists(__DIR__ . '/../' . $adminAvatar)) {
    $adminAvatar = 'assets/images/admin-avatar.jpg';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard — X BAR</title>
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
            <li><a href="index.php" class="active" data-en="Dashboard" data-id="Dashboard">Dashboard</a></li>
            <li><a href="products.php" data-en="Product" data-id="Produk">Product</a></li>
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

    <!-- DASHBOARD MAIN -->
    <main class="dashboard-layout">
        <!-- Welcome Row -->
        <div class="dashboard-header">
            <h1 class="dashboard-welcome"><span data-en="Welcome" data-id="Selamat Datang">Welcome</span>, <?= htmlspecialchars($adminName) ?>!</h1>
            <a href="products.php" class="btn-manage-products-top" data-en="Manage Products" data-id="Kelola Produk">Manage Products</a>
        </div>

        <div class="dashboard-grid">
            <!-- LEFT SIDEBAR -->
            <aside class="dash-sidebar">
                <div class="dash-avatar-wrap">
                    <img src="../<?= htmlspecialchars($adminAvatar) ?>" alt="<?= htmlspecialchars($adminName) ?>">
                </div>

                <a href="profile.php" class="btn-sidebar-pill" data-en="Edit Profiles" data-id="Edit Profil">Edit Profiles</a>
                <a href="website.php" class="btn-sidebar-pill" data-en="Manage Website" data-id="Kelola Website">Manage Website</a>

                <div class="dash-stat-pill">
                    <div class="dash-stat-label" data-en="Products Total" data-id="Total Produk">Products Total</div>
                    <div class="dash-stat-val"><?= $totalProducts ?> <span data-en="Items" data-id="Item">Items</span></div>
                </div>

                <div class="dash-stat-pill">
                    <div class="dash-stat-label" data-en="Last Activity" data-id="Aktivitas Terakhir">Last Activity</div>
                    <div class="dash-stat-val last-activity-time" 
                         data-initial-diff="<?= $initialDiff ?>"
                         data-en="<?= htmlspecialchars($lastActivityEn) ?>" 
                         data-id="<?= htmlspecialchars($lastActivityId) ?>"><?= htmlspecialchars($lastActivityEn) ?></div>
                </div>
            </aside>

            <!-- MAIN CARDS -->
            <section class="dash-main-cards">
                <!-- Col 1: Recent Activity & Inventory -->
                <div style="display:flex;flex-direction:column;gap:24px;">
                    <!-- Recent Activity Card -->
                    <div class="dash-card-white">
                        <h2 class="dash-card-title" data-en="Recent Activity" data-id="Aktivitas Terkini">Recent Activity</h2>
                        <div class="activity-list">
                            <?php if (empty($activities)): ?>
                                <div class="activity-item">
                                    <div class="activity-actor">
                                        <div class="actor-avatar admin">A</div>
                                        <div class="activity-info">
                                            <h4>Administrator</h4>
                                            <p>Updated the product's catalogue</p>
                                        </div>
                                    </div>
                                    <div class="activity-time">24m ago</div>
                                </div>
                                <div class="activity-item">
                                    <div class="activity-actor">
                                        <div class="actor-avatar">V</div>
                                        <div class="activity-info">
                                            <h4>Visitor</h4>
                                            <p>Clicked Request Quotation</p>
                                        </div>
                                    </div>
                                    <div class="activity-time">1h ago</div>
                                </div>
                            <?php else: ?>
                                <?php foreach ($activities as $idx => $act): 
                                    $badgeClass = (strtolower($act['badge']) === 'admin') ? 'admin' : '';
                                    $initials = strtoupper(substr($act['actor_name'], 0, 2));
                                    $isExtra = ($idx >= 2);
                                ?>
                                <div class="activity-item<?= $isExtra ? ' activity-item-extra' : '' ?>"<?= $isExtra ? ' style="display: none;"' : '' ?>>
                                    <div class="activity-actor">
                                        <div class="actor-avatar <?= $badgeClass ?>"><?= htmlspecialchars($initials) ?></div>
                                        <div class="activity-info">
                                            <h4><?= htmlspecialchars($act['actor_name']) ?></h4>
                                            <p><?= htmlspecialchars($act['action']) ?></p>
                                        </div>
                                    </div>
                                    <?php 
                                        $actTime = !empty($act['created_at']) ? calculateTimeAgo($act['created_at']) : ['en' => $act['time_ago'], 'id' => $act['time_ago']];
                                    ?>
                                    <div class="activity-time" data-en="<?= htmlspecialchars($actTime['en']) ?>" data-id="<?= htmlspecialchars($actTime['id']) ?>"><?= htmlspecialchars($actTime['en']) ?></div>
                                </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                        <?php if (count($activities) > 2): ?>
                        <div style="margin-top:16px;text-align:center;">
                            <a href="#" id="toggleRecentActivity" class="btn-toggle-activity" data-en="View All ∨" data-id="Lihat Semua ∨">View All ∨</a>
                        </div>
                        <?php endif; ?>
                    </div>

                    <!-- Product Inventory Card -->
                    <div class="dash-card-white">
                        <h2 class="dash-card-title" data-en="Product Inventory" data-id="Inventaris Produk">Product Inventory</h2>
                        <div style="font-size:13px;font-weight:700;color:#334155;" data-en="Product Stock Overview" data-id="Ringkasan Stok Produk">Product Stock Overview</div>
                        
                        <div class="inventory-blocks">
                            <?php if (empty($sortedInvProducts)): ?>
                                <div style="flex:1;display:flex;align-items:center;justify-content:center;background:#f8fafc;border-radius:8px;color:#64748b;font-size:13px;" data-en="No products in catalog yet." data-id="Belum ada produk di katalog.">
                                    No products in catalog yet.
                                </div>
                            <?php else: 
                                $countInv = count($sortedInvProducts);
                                $p1 = $sortedInvProducts[0] ?? null;
                                $p2 = $sortedInvProducts[1] ?? null;
                                $colPalette = ['#64748b', '#94a3b8', '#475569', '#6b7280', '#52525b'];
                            ?>
                                <!-- Box 1: Largest Stock -->
                                <?php if ($p1): ?>
                                <div class="inv-box b1" style="<?= ($countInv === 1 ? 'flex:1;' : '') ?>">
                                    <h5><?= htmlspecialchars($p1['name']) ?></h5>
                                    <span><?= formatStockDisplay($p1['tons']) ?></span>
                                </div>
                                <?php endif; ?>

                                <!-- Box 2: Second Largest Stock -->
                                <?php if ($p2): ?>
                                <div class="inv-box b2" style="<?= ($countInv === 2 ? 'flex:1;' : '') ?>">
                                    <h5><?= htmlspecialchars($p2['name']) ?></h5>
                                    <span><?= formatStockDisplay($p2['tons']) ?></span>
                                </div>
                                <?php endif; ?>

                                <!-- Box Column: Remaining Products -->
                                <?php if ($countInv > 2): ?>
                                <div class="inv-box-col">
                                    <?php for ($i = 2; $i < $countInv; $i++): 
                                        $pi = $sortedInvProducts[$i];
                                        $colBg = $colPalette[($i - 2) % count($colPalette)];
                                        $isSingleInCol = ($countInv === 3);
                                    ?>
                                    <div class="inv-box" style="background:<?= $colBg ?>;<?= ($isSingleInCol ? 'flex:1;' : 'min-height:86px;flex-shrink:0;') ?>">
                                        <h5><?= htmlspecialchars($pi['name']) ?></h5>
                                        <span><?= formatStockDisplay($pi['tons']) ?></span>
                                    </div>
                                    <?php endfor; ?>
                                </div>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>

                        <div class="inventory-footer-row">
                            <!-- Low Stock Alerts (< 30 Tons) -->
                            <div>
                                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:8px;">
                                    <div style="font-size:12px;font-weight:700;" data-en="Low Stock Alerts (< 30 Tons)" data-id="Peringatan Stok Menipis (< 30 Ton)">Low Stock Alerts (< 30 Tons)</div>
                                    <?php if ($lowStockCount > 0): ?>
                                        <span style="font-size:10px;font-weight:700;background:#fee2e2;color:#ef4444;padding:1px 6px;border-radius:10px;">
                                            <?= $lowStockCount ?> <span data-en="Items" data-id="Item">Items</span>
                                        </span>
                                    <?php endif; ?>
                                </div>
                                <div class="alert-table">
                                    <?php if (empty($lowStockItems)): ?>
                                        <div style="font-size:12px;color:#16a34a;padding:12px 10px;background:#f0fdf4;border-radius:6px;font-weight:600;display:flex;align-items:center;gap:6px;">
                                            <span>✓</span>
                                            <span data-en="All products have sufficient stock (≥ 30 Tons)." data-id="Semua stok produk aman (≥ 30 Ton).">All products have sufficient stock (≥ 30 Tons).</span>
                                        </div>
                                    <?php else: ?>
                                        <?php foreach ($lowStockItems as $idx => $p): 
                                            $itemNum = sprintf('%02d', $idx + 1);
                                            $tons = $p['tons'];
                                            if ($tons < 0.01 && $tons > 0) {
                                                $leftEn = '< 0.01 Tons Left';
                                                $leftId = '< 0.01 Ton Tersisa';
                                            } elseif ($tons == floor($tons)) {
                                                $leftEn = (int)$tons . ' Tons Left';
                                                $leftId = (int)$tons . ' Ton Tersisa';
                                            } else {
                                                $leftEn = round($tons, 2) . ' Tons Left';
                                                $leftId = round($tons, 2) . ' Ton Tersisa';
                                            }
                                            $isCritical = ($tons < 10.0);
                                        ?>
                                        <div class="alert-row">
                                            <div style="overflow:hidden;text-overflow:ellipsis;white-space:nowrap;margin-right:8px;">
                                                <strong style="color:#0f172a;">ITEM <?= $itemNum ?> - <?= htmlspecialchars($p['name']) ?></strong>
                                                <span style="color:#64748b;font-size:11px;margin-left:4px;" data-en="(<?= $leftEn ?>)" data-id="(<?= $leftId ?>)">(<?= $leftEn ?>)</span>
                                            </div>
                                            <a href="product_edit.php?id=<?= $p['id'] ?>" class="btn-restock" style="<?= $isCritical ? 'background:#fca5a5;color:#b91c1c;' : 'background:#fed7aa;color:#c2410c;' ?>text-decoration:none;" title="Restock <?= htmlspecialchars($p['name']) ?>" data-en="Restock" data-id="Isi Ulang">Restock</a>
                                        </div>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- Inventory Health Score -->
                            <div class="health-score-box">
                                <div style="font-size:12px;font-weight:700;" data-en="Inventory Health Score" data-id="Skor Kesehatan Stok">Inventory Health Score</div>
                                <div class="health-circle" style="border-color: <?= $healthBorderColor ?>; color: <?= ($inventoryHealthScore >= 80 ? '#1e293b' : $healthBorderColor) ?>;">
                                    <?= $inventoryHealthScore ?>%
                                    <span style="color: <?= ($inventoryHealthScore >= 80 ? '#64748b' : $healthBorderColor) ?>;" data-en="<?= $healthStatusEn ?>" data-id="<?= $healthStatusId ?>"><?= $healthStatusEn ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Col 2: Sales & Insights -->
                <div style="display:flex;flex-direction:column;gap:24px;">
                    <!-- Sales & Customer Insight Card -->
                    <div class="dash-card-white">
                        <h2 class="dash-card-title" data-en="Sales & Customer Insight" data-id="Wawasan Penjualan & Pelanggan">Sales & Customer Insight</h2>
                        <div style="font-size:15px;font-weight:700;margin-bottom:14px;" data-en="Monthly Sales Trend (Tons)" data-id="Tren Penjualan Bulanan (Ton)">Monthly Sales Trend (Tons)</div>

                        <!-- Bar Chart -->
                        <div class="chart-container">
                            <div class="chart-bars-wrap">
                                <!-- Jan -->
                                <div class="chart-bar-col">
                                    <span class="bar-val-label">80</span>
                                    <div class="bar-pill" style="height: 120px;"></div>
                                    <span class="bar-month-label">Jan</span>
                                </div>
                                <!-- Feb -->
                                <div class="chart-bar-col">
                                    <span class="bar-val-label">90</span>
                                    <div class="bar-pill" style="height: 135px;"></div>
                                    <span class="bar-month-label">Feb</span>
                                </div>
                                <!-- Mar -->
                                <div class="chart-bar-col">
                                    <span class="bar-val-label">60</span>
                                    <div class="bar-pill" style="height: 90px;"></div>
                                    <span class="bar-month-label">Mar</span>
                                </div>
                                <!-- Apr -->
                                <div class="chart-bar-col">
                                    <span class="bar-val-label">100</span>
                                    <div class="bar-pill" style="height: 150px;"></div>
                                    <span class="bar-month-label">Apr</span>
                                </div>
                                <!-- May -->
                                <div class="chart-bar-col">
                                    <span class="bar-val-label">110</span>
                                    <div class="bar-pill" style="height: 165px;"></div>
                                    <span class="bar-month-label" data-en="May" data-id="Mei">May</span>
                                </div>
                                <!-- Jun -->
                                <div class="chart-bar-col">
                                    <span class="bar-val-label">90</span>
                                    <div class="bar-pill" style="height: 135px;"></div>
                                    <span class="bar-month-label">Jun</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Bottom Split: Top Performing & Customer Growth -->
                    <div class="dash-bottom-row">
                        <!-- Top Performing Products -->
                        <div class="dash-card-white">
                            <h3 style="font-size:16px;font-weight:800;margin-bottom:18px;" data-en="Top Performing Products" data-id="Produk Terlaris">Top Performing Products</h3>

                            <div class="prog-item">
                                <div class="prog-header">
                                    <span>Tamarind Charcoal</span>
                                    <span data-en="80 Tons sold" data-id="80 Ton terjual">80 Tons sold</span>
                                </div>
                                <div class="prog-bar-wrap">
                                    <div class="prog-fill" style="width: 80%;"></div>
                                </div>
                            </div>

                            <div class="prog-item">
                                <div class="prog-header">
                                    <span>Mix Charcoal</span>
                                    <span data-en="70 Tons sold" data-id="70 Ton terjual">70 Tons sold</span>
                                </div>
                                <div class="prog-bar-wrap">
                                    <div class="prog-fill" style="width: 70%;"></div>
                                </div>
                            </div>

                            <div class="prog-item">
                                <div class="prog-header">
                                    <span>Charcoal Powder</span>
                                    <span data-en="65 Tons sold" data-id="65 Ton terjual">65 Tons sold</span>
                                </div>
                                <div class="prog-bar-wrap">
                                    <div class="prog-fill" style="width: 65%;"></div>
                                </div>
                            </div>
                        </div>

                        <!-- New Customer Growth -->
                        <div class="dash-card-white growth-box">
                            <h3 style="font-size:15px;font-weight:800;margin-bottom:12px;" data-en="New Customer Growth" data-id="Pertumbuhan Pelanggan Baru">New Customer Growth</h3>
                            <div class="growth-number">+22</div>
                            <div style="font-size:14px;font-weight:700;" data-en="New Customer" data-id="Pelanggan Baru">New Customer</div>
                            <div style="font-size:11px;color:#64748b;margin-top:2px;" data-en="Month-on-month Growth: +15%" data-id="Pertumbuhan MoM: +15%">Month-on-month Growth: +15%</div>
                            <div class="growth-pill">+15% ↑</div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </main>

    <script src="../assets/js/admin.js"></script>
</body>
</html>
