<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/auth_check.php';

// Handle Gallery Deletion
if (isset($_GET['del_gallery'])) {
    $delId = intval($_GET['del_gallery']);
    if ($delId > 0) {
        $stmtG = $pdo->prepare("SELECT * FROM galleries WHERE id = :id");
        $stmtG->execute([':id' => $delId]);
        $gItem = $stmtG->fetch();

        if ($gItem) {
            // Delete image file if in uploads/
            if (str_starts_with($gItem['image'], 'uploads/')) {
                @unlink(__DIR__ . '/../' . $gItem['image']);
            }
            $pdo->prepare("DELETE FROM galleries WHERE id = :id")->execute([':id' => $delId]);

            logActivity("Deleted gallery photo '{$gItem['title']}'");

            $_SESSION['flash_success'] = "Gallery photo deleted successfully.";
        }
    }
    header("Location: website.php");
    exit;
}

$error = '';
$success = '';

// Handle Form Submissions (Content update & Gallery Add)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1. Update text content in website_content
    $textFields = [
        'website_title'       => $_POST['website_title'] ?? '',
        'hero_short_desc'     => $_POST['hero_short_desc'] ?? '',
        'hero_tagline'        => $_POST['hero_short_desc'] ?? '',
        'about_year'          => $_POST['about_year'] ?? '',
        'about_desc'          => $_POST['about_desc'] ?? '',
        'history_year_1'      => $_POST['history_year_1'] ?? '',
        'history_text_1'      => $_POST['history_text_1'] ?? '',
        'history_year_2'      => $_POST['history_year_2'] ?? '',
        'history_text_2'      => $_POST['history_text_2'] ?? '',
        'history_year_3'      => $_POST['history_year_3'] ?? '',
        'history_desc'        => $_POST['history_desc'] ?? '',
        'vision_mission_year' => $_POST['vision_mission_year'] ?? '',
        'vision'              => $_POST['vision'] ?? '',
        'mission'             => $_POST['mission'] ?? '',
        'map_url'             => $_POST['map_url'] ?? '',
    ];

    $updateContentStmt = $pdo->prepare("
        INSERT INTO website_content (content_key, content_value) 
        VALUES (?, ?) 
        ON DUPLICATE KEY UPDATE content_value = ?
    ");

    foreach ($textFields as $key => $val) {
        $valTrimmed = trim($val);
        $updateContentStmt->execute([$key, $valTrimmed, $valTrimmed]);
    }

    // 1b. Save dynamic history milestones
    if (isset($_POST['milestone_year']) && is_array($_POST['milestone_year'])) {
        try {
            $pdo->beginTransaction();
            $pdo->exec("DELETE FROM history_milestones");
            $stmtM = $pdo->prepare("INSERT INTO history_milestones (year_range, description, sort_order) VALUES (?, ?, ?)");
            $order = 1;
            $allMilestones = [];
            foreach ($_POST['milestone_year'] as $idx => $yearVal) {
                $yearTrim = trim($yearVal);
                $descTrim = trim($_POST['milestone_desc'][$idx] ?? '');
                if ($yearTrim !== '' || $descTrim !== '') {
                    $stmtM->execute([$yearTrim, $descTrim, $order++]);
                    $allMilestones[] = ['year' => $yearTrim, 'desc' => $descTrim];
                }
            }
            $pdo->commit();

            // Also keep legacy website_content keys in sync
            if (isset($allMilestones[0])) {
                $updateContentStmt->execute(['history_year_1', $allMilestones[0]['year'], $allMilestones[0]['year']]);
                $updateContentStmt->execute(['history_text_1', $allMilestones[0]['desc'], $allMilestones[0]['desc']]);
            }
            if (isset($allMilestones[1])) {
                $updateContentStmt->execute(['history_year_2', $allMilestones[1]['year'], $allMilestones[1]['year']]);
                $updateContentStmt->execute(['history_text_2', $allMilestones[1]['desc'], $allMilestones[1]['desc']]);
            }
            if (isset($allMilestones[2])) {
                $updateContentStmt->execute(['history_year_3', $allMilestones[2]['year'], $allMilestones[2]['year']]);
                $updateContentStmt->execute(['history_desc',   $allMilestones[2]['desc'], $allMilestones[2]['desc']]);
            }
        } catch (Exception $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
        }
    }

    // 2. Handle Header Background Image Upload
    if (isset($_FILES['hero_image']) && $_FILES['hero_image']['error'] === UPLOAD_ERR_OK) {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
        $fileTmp = $_FILES['hero_image']['tmp_name'];
        $ext = strtolower(pathinfo($_FILES['hero_image']['name'], PATHINFO_EXTENSION));
        $fileType = function_exists('mime_content_type') ? mime_content_type($fileTmp) : '';

        if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp']) && (empty($fileType) || in_array($fileType, $allowedTypes))) {
            $newFilename = 'hero_' . time() . '.' . $ext;
            $targetDir = __DIR__ . '/../uploads/';
            if (!is_dir($targetDir)) mkdir($targetDir, 0755, true);
            if (move_uploaded_file($fileTmp, $targetDir . $newFilename)) {
                $heroPath = 'uploads/' . $newFilename;
                $updateContentStmt->execute(['hero_image', $heroPath, $heroPath]);
            }
        }
    }

    // 3. Handle New Gallery Item Upload
    if (isset($_FILES['gallery_file']) && $_FILES['gallery_file']['error'] === UPLOAD_ERR_OK) {
        $galleryTitle = trim($_POST['gallery_title'] ?? '');
        if (empty($galleryTitle)) {
            $galleryTitle = 'Warehouse Operation';
        }
        $allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
        $fileTmp = $_FILES['gallery_file']['tmp_name'];
        $ext = strtolower(pathinfo($_FILES['gallery_file']['name'], PATHINFO_EXTENSION));
        $fileType = function_exists('mime_content_type') ? mime_content_type($fileTmp) : '';

        if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp']) && (empty($fileType) || in_array($fileType, $allowedTypes))) {
            $newFilename = 'gallery_' . time() . '_' . bin2hex(random_bytes(3)) . '.' . $ext;
            $targetDir = __DIR__ . '/../uploads/';
            if (!is_dir($targetDir)) mkdir($targetDir, 0755, true);

            if (move_uploaded_file($fileTmp, $targetDir . $newFilename)) {
                $gOrder = $pdo->query("SELECT COALESCE(MAX(sort_order), 0) + 1 FROM galleries")->fetchColumn();
                $insertG = $pdo->prepare("INSERT INTO galleries (title, file_name, image, sort_order) VALUES (?, ?, ?, ?)");
                $insertG->execute([$galleryTitle, $newFilename, 'uploads/' . $newFilename, $gOrder]);
            }
        }
    }

    logActivity("Updated website presentation and content");

    $_SESSION['flash_success'] = "Website content updated successfully!";
    header("Location: website.php");
    exit;
}

// Fetch current website content
$stmtContent = $pdo->query("SELECT content_key, content_value FROM website_content");
$content = [];
while ($row = $stmtContent->fetch()) {
    $content[$row['content_key']] = $row['content_value'];
}

// Fetch all galleries
$stmtGalleries = $pdo->query("SELECT * FROM galleries ORDER BY sort_order ASC, id ASC");
$galleries = $stmtGalleries->fetchAll();

// Fetch all history milestones
$stmtMilestones = $pdo->query("SELECT * FROM history_milestones ORDER BY sort_order ASC, id ASC");
$milestones = $stmtMilestones ? $stmtMilestones->fetchAll() : [];
if (empty($milestones)) {
    $milestones = [
        ['year_range' => '2017 - 2018', 'description' => 'As the manufacturer, we built three kilns at a cost of 9 million per unit, with specifications of 9 meters in length and 2 meters in width. We supply our output to local middlemen in Bondowoso.'],
        ['year_range' => '2019 - 2022', 'description' => 'We partnered with an external investor to construct 25 kilns at a production site in Sukowono Village, Pujer District. With these 25 kilns, we are able to ship an output of 4-5 containers per month. We export the charcoal to buyers in the Middle East.'],
        ['year_range' => '2022 - Now', 'description' => 'We have transformed into charcoal aggregators, partnering with farmers and kiln owners in Bondowoso to help market their charcoal production.'],
    ];
}

// Flash messages
$flashSuccess = $_SESSION['flash_success'] ?? null;
unset($_SESSION['flash_success']);

$heroBg = $content['hero_image'] ?? 'assets/images/hero-bg.jpg';
if (!file_exists(__DIR__ . '/../' . $heroBg) && !file_exists(__DIR__ . '/../assets/images/' . basename($heroBg))) {
    $heroBgSrc = '../assets/images/hero-bg.jpg';
} else {
    $heroBgSrc = '../' . htmlspecialchars($heroBg);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Website — X BAR Admin</title>
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
            <li><a href="products.php" data-en="Product" data-id="Produk">Product</a></li>
            <li><a href="website.php" class="active" data-en="Website" data-id="Website">Website</a></li>
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

    <!-- MANAGE WEBSITE CONTAINER (PAGE 12) -->
    <main class="website-mgmt-container">
        <h1 class="form-page-title" style="margin-bottom:30px;text-align:left;" data-en="Manage Website" data-id="Kelola Website">Manage Website</h1>

        <?php if ($flashSuccess): ?>
            <div class="alert-banner alert-success" style="margin-bottom:24px;">
                <?= htmlspecialchars($flashSuccess) ?>
            </div>
        <?php endif; ?>

        <form action="website.php" method="POST" enctype="multipart/form-data">
            
            <!-- SECTION 1: EDIT HEADER WITH LIVE PREVIEW -->
            <div class="mgmt-section-box">
                <h2 class="mgmt-section-title" data-en="Edit Header" data-id="Edit Header">Edit Header</h2>

                <div class="header-split-grid">
                    <!-- Left: Header Inputs -->
                    <div style="display:flex;flex-direction:column;gap:16px;">
                        <div class="admin-input-card">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <polyline points="4 7 4 4 20 4 20 7"></polyline>
                                <line x1="9" y1="20" x2="15" y2="20"></line>
                                <line x1="12" y1="4" x2="12" y2="20"></line>
                            </svg>
                            <div class="input-card-inner">
                                <label class="input-card-label" data-en="Header Headline" data-id="Judul Utama Header">Header Headline</label>
                                <input type="text" name="website_title" id="inputWebTitle" value="<?= htmlspecialchars($content['website_title'] ?? 'Natural Energy, Global Quality') ?>" required>
                            </div>
                        </div>

                        <div class="admin-textarea-card" style="height:120px;">
                            <label class="input-card-label" style="display:block;margin-bottom:6px;" data-en="Header Subtitle / Short Description" data-id="Subjudul / Deskripsi Singkat Header">Header Subtitle / Short Description</label>
                            <textarea name="hero_short_desc" id="inputWebDesc"><?= htmlspecialchars($content['hero_short_desc'] ?? 'And Firewood For Global Culinary (BBQ/Grill), Industrial, And Agricultural Needs. Ready To Consistently Supply Both Local And International Markets.') ?></textarea>
                        </div>

                        <div class="admin-input-card">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                                <circle cx="8.5" cy="8.5" r="1.5"></circle>
                                <polyline points="21 15 16 10 5 21"></polyline>
                            </svg>
                            <div class="input-card-inner">
                                <label class="input-card-label" data-en="Change Hero Background Image" data-id="Ganti Gambar Latar Hero">Change Hero Background Image</label>
                                <input type="file" name="hero_image" accept="image/*" style="font-size:13px;padding-top:4px;">
                            </div>
                        </div>
                    </div>

                    <!-- Right: Live Preview Box -->
                    <div class="header-live-preview">
                        <img src="<?= $heroBgSrc ?>" alt="Hero Preview">
                        <div class="header-live-content">
                            <div class="preview-title" id="liveTitle"><?= htmlspecialchars($content['website_title'] ?? 'Natural Energy, Global Quality') ?></div>
                            <div class="preview-desc" id="liveDesc"><?= htmlspecialchars($content['hero_short_desc'] ?? 'And Firewood For Global Culinary (BBQ/Grill), Industrial, And Agricultural Needs. Ready To Consistently Supply Both Local And International Markets.') ?></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- SECTION 2: THREE COLUMNS (ABOUT US, HISTORY, VISION & MISSION) -->
            <div class="mgmt-three-cols">
                <!-- Column 1: Edit About Us -->
                <div class="mgmt-section-box" style="margin-bottom:0;display:flex;flex-direction:column;gap:14px;">
                    <h3 class="mgmt-section-title" style="font-size:16px;" data-en="Edit About Us" data-id="Edit Tentang Kami">Edit About Us</h3>

                    <div class="admin-textarea-card" style="height:270px;">
                        <label class="input-card-label" style="display:block;margin-bottom:6px;" data-en="Company Overview" data-id="Ringkasan Perusahaan">Company Overview</label>
                        <textarea name="about_desc" style="height:220px;"><?= htmlspecialchars($content['about_desc'] ?? '') ?></textarea>
                    </div>
                </div>

                <!-- Column 2: Edit History & Milestones -->
                <div class="mgmt-section-box" style="margin-bottom:0;display:flex;flex-direction:column;gap:12px;">
                    <div style="display:flex;align-items:center;justify-content:space-between;gap:8px;">
                        <h3 class="mgmt-section-title" style="font-size:16px;margin:0;" data-en="Edit History & Milestones" data-id="Edit Sejarah & Linimasa">Edit History</h3>
                        <button type="button" id="btnAddMilestone" onclick="addMilestonePeriod(event)" style="background:#ea580c;color:#ffffff;border:none;padding:6px 14px;border-radius:8px;font-size:12px;font-weight:700;cursor:pointer;display:inline-flex;align-items:center;gap:6px;transition:background 0.2s;box-shadow:0 2px 8px rgba(234,88,12,0.25);">
                            <span style="font-size:14px;line-height:1;">+</span> <span data-en="Add Period" data-id="Tambah Periode">Add Period</span>
                        </button>
                    </div>
                    
                    <div id="milestonesContainer" style="display:flex;flex-direction:column;gap:10px;max-height:360px;overflow-y:auto;padding-right:4px;">
                        <?php foreach ($milestones as $idx => $m): ?>
                        <div class="milestone-row-card" style="background:#f8fafc;padding:12px;border-radius:12px;border:1px solid #e2e8f0;position:relative;">
                            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:8px;">
                                <span class="milestone-badge-num" style="font-size:11px;font-weight:700;color:#64748b;">
                                    #<span class="m-index"><?= $idx + 1 ?></span> <span data-en="Period" data-id="Periode">Period</span>
                                </span>
                                <button type="button" class="btn-del-milestone" onclick="removeMilestonePeriod(this, event)" title="Delete Period" style="background:transparent;border:none;color:#ef4444;cursor:pointer;padding:2px 6px;font-size:14px;font-weight:700;border-radius:4px;">✕</button>
                            </div>
                            <div class="admin-input-card" style="margin-bottom:8px;padding:6px 10px;">
                                <div class="input-card-inner">
                                    <label class="input-card-label" style="font-size:10px;" data-en="Period / Year Range" data-id="Periode / Rentang Tahun">Period / Year Range (Tahun)</label>
                                    <input type="text" name="milestone_year[]" value="<?= htmlspecialchars($m['year_range']) ?>" placeholder="e.g. 2026 - 2028" style="font-weight:700;font-size:12px;" required>
                                </div>
                            </div>
                            <div style="background:#ffffff;border:1px solid #cbd5e1;border-radius:8px;padding:6px 8px;">
                                <label class="input-card-label" style="font-size:10px;color:#64748b;display:block;margin-bottom:4px;" data-en="Period Content / Story" data-id="Isi Periode / Deskripsi">Period Content / Story (Isi Periode)</label>
                                <textarea name="milestone_desc[]" style="width:100%;height:52px;border:none;outline:none;font-size:11px;font-family:inherit;resize:vertical;" placeholder="Tuliskan peristiwa atau pencapaian pada periode ini..."><?= htmlspecialchars($m['description']) ?></textarea>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Column 3: Edit Vision & Mission -->
                <div class="mgmt-section-box" style="margin-bottom:0;display:flex;flex-direction:column;gap:12px;">
                    <h3 class="mgmt-section-title" style="font-size:16px;" data-en="Edit Vision & Mission" data-id="Edit Visi & Misi">Edit Vision & Mission</h3>
                    
                    <div class="admin-input-card">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                            <line x1="16" y1="2" x2="16" y2="6"></line>
                            <line x1="8" y1="2" x2="8" y2="6"></line>
                            <line x1="3" y1="10" x2="21" y2="10"></line>
                        </svg>
                        <div class="input-card-inner">
                            <label class="input-card-label" data-en="Target Period / Year" data-id="Periode / Tahun Target">Target Period / Year</label>
                            <input type="text" name="vision_mission_year" value="<?= htmlspecialchars($content['vision_mission_year'] ?? '2026 - 2030') ?>" placeholder="e.g. 2026 - 2030">
                        </div>
                    </div>

                    <div class="admin-textarea-card" style="height:95px;">
                        <label class="input-card-label" style="display:block;margin-bottom:4px;" data-en="Vision" data-id="Visi">Vision</label>
                        <textarea name="vision"><?= htmlspecialchars($content['vision'] ?? '') ?></textarea>
                    </div>
                    <div class="admin-textarea-card" style="height:115px;">
                        <label class="input-card-label" style="display:block;margin-bottom:4px;" data-en="Mission" data-id="Misi">Mission</label>
                        <textarea name="mission"><?= htmlspecialchars($content['mission'] ?? '') ?></textarea>
                    </div>

                    <div class="admin-input-card">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                            <circle cx="12" cy="10" r="3"></circle>
                        </svg>
                        <div class="input-card-inner">
                            <label class="input-card-label" data-en="Google Maps Link" data-id="Tautan Google Maps">Google Maps Link</label>
                            <input type="text" name="map_url" value="<?= htmlspecialchars($content['map_url'] ?? 'https://www.google.com/maps/search/?api=1&query=3RQG%2BCCV+CV+SUDUT%2C+Jl.+P.b.+Sudirman%2C+Patemon%2C+Blindungan%2C+Bondowoso%2C+Bondowoso+Regency%2C+East+Java+68212') ?>" placeholder="https://www.google.com/maps/search/?api=1&query=...">
                        </div>
                    </div>
                </div>
            </div>

            <!-- SECTION 3: MANAGE GALLERY -->
            <div class="mgmt-section-box" style="margin-top:24px;">
                <h2 class="mgmt-section-title" data-en="Manage Gallery" data-id="Kelola Galeri">Manage Gallery</h2>

                <div class="galleries-mgmt-grid">
                    <!-- Left: Upload New Photo -->
                    <div style="background:#f8fafc;padding:20px;border-radius:18px;border:1px solid #e2e8f0;">
                        <h4 style="font-size:15px;font-weight:700;margin-bottom:12px;" data-en="+ Add Gallery Photo" data-id="+ Tambah Foto Galeri">+ Add Gallery Photo</h4>
                        <div style="margin-bottom:14px;">
                            <label class="input-card-label" style="display:block;margin-bottom:4px;" data-en="Caption / Title" data-id="Keterangan / Judul Foto">Caption / Title</label>
                            <input type="text" name="gallery_title" placeholder="e.g. Quality Inspection" data-en="e.g. Quality Inspection" data-id="Contoh: Inspeksi Kualitas" style="width:100%;padding:10px;border-radius:10px;border:1px solid #cbd5e1;font-size:13px;">
                        </div>
                        <div style="margin-bottom:16px;">
                            <label class="input-card-label" style="display:block;margin-bottom:4px;" data-en="Select Image File" data-id="Pilih File Gambar">Select Image File</label>
                            <input type="file" name="gallery_file" accept="image/*" style="font-size:12px;width:100%;">
                        </div>
                        <p style="font-size:11px;color:#64748b;" data-en="Save Changes below will upload your new gallery photo directly." data-id="Simpan Perubahan di bawah akan langsung mengunggah foto galeri baru.">Save Changes below will upload your new gallery photo directly.</p>
                    </div>

                    <!-- Right: Existing Gallery Photos Grid -->
                    <div>
                        <div style="font-size:13px;font-weight:700;color:#64748b;margin-bottom:12px;">
                            <?= count($galleries) ?> <span data-en="Total Gallery Items" data-id="Total Item Galeri">Total Gallery Items</span>
                        </div>
                        <div class="gallery-thumbs-list">
                            <?php foreach ($galleries as $idx => $g): 
                                $gImg = $g['image'] ?? 'assets/images/galeri1.jpg';
                                if (!file_exists(__DIR__ . '/../' . $gImg) && !file_exists(__DIR__ . '/../assets/images/' . basename($gImg))) {
                                    $gSrc = '../assets/images/galeri1.jpg';
                                } else {
                                    $gSrc = '../' . htmlspecialchars($gImg);
                                }
                            ?>
                                <div class="thumb-card-item">
                                    <div class="thumb-card-header">
                                        <span>#<?= $idx + 1 ?></span>
                                        <div class="thumb-card-actions">
                                            <a href="website.php?del_gallery=<?= $g['id'] ?>" class="btn-confirm-delete" data-name="gallery item '<?= htmlspecialchars($g['title']) ?>'" title="Delete" style="color:#ef4444;">
                                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <polyline points="3 6 5 6 21 6"></polyline>
                                                    <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                                </svg>
                                            </a>
                                        </div>
                                    </div>
                                    <div class="thumb-img-box">
                                        <img src="<?= $gSrc ?>" alt="<?= htmlspecialchars($g['title']) ?>">
                                    </div>
                                    <div style="font-size:11px;font-weight:600;margin-top:6px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                                        <?= htmlspecialchars($g['title']) ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bottom Save Action -->
            <div class="form-bottom-actions">
                <button type="submit" class="btn-save-changes" data-en="Save Website Changes" data-id="Simpan Perubahan Website">Save Website Changes</button>
            </div>
        </form>
    </main>

    <script>
    function addMilestonePeriod(e) {
        if (e) e.preventDefault();
        var container = document.getElementById('milestonesContainer');
        if (!container) return;

        var count = container.querySelectorAll('.milestone-row-card').length + 1;
        var card = document.createElement('div');
        card.className = 'milestone-row-card';
        card.style.cssText = 'background:#f8fafc;padding:12px;border-radius:12px;border:1.5px solid #fed7aa;position:relative;margin-bottom:4px;box-shadow:0 4px 12px rgba(234,88,12,0.1);';

        card.innerHTML = `
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:8px;">
                <span class="milestone-badge-num" style="font-size:11px;font-weight:700;color:#ea580c;background:#ffedd5;padding:2px 8px;border-radius:6px;">
                    #<span class="m-index">${count}</span> <span data-en="Period" data-id="Periode">Period</span>
                </span>
                <button type="button" class="btn-del-milestone" onclick="removeMilestonePeriod(this, event)" title="Hapus Periode" style="background:#fee2e2;border:none;color:#ef4444;cursor:pointer;padding:3px 8px;font-size:12px;font-weight:700;border-radius:6px;">✕ Hapus</button>
            </div>
            <div class="admin-input-card" style="margin-bottom:8px;padding:8px 10px;background:#ffffff;border:1px solid #cbd5e1;">
                <div class="input-card-inner">
                    <label class="input-card-label" style="font-size:10px;color:#64748b;font-weight:700;" data-en="Period / Year Range" data-id="Periode / Rentang Tahun">Period / Year Range (Tahun)</label>
                    <input type="text" name="milestone_year[]" value="" placeholder="e.g. 2026 - 2028" style="font-weight:700;font-size:13px;width:100%;border:none;outline:none;" required>
                </div>
            </div>
            <div style="background:#ffffff;border:1px solid #cbd5e1;border-radius:8px;padding:6px 8px;">
                <label class="input-card-label" style="font-size:10px;color:#64748b;font-weight:700;display:block;margin-bottom:4px;" data-en="Period Content / Story" data-id="Isi Periode / Deskripsi">Period Content / Story (Isi Periode)</label>
                <textarea name="milestone_desc[]" style="width:100%;height:56px;border:none;outline:none;font-size:12px;font-family:inherit;resize:vertical;" placeholder="Tuliskan peristiwa atau pencapaian pada periode ini..."></textarea>
            </div>
        `;

        container.appendChild(card);
        reindexMilestoneCards();
        container.scrollTop = container.scrollHeight;

        var input = card.querySelector('input[name="milestone_year[]"]');
        if (input) input.focus();
    }

    function removeMilestonePeriod(btn, e) {
        if (e) e.preventDefault();
        var container = document.getElementById('milestonesContainer');
        if (!container) return;

        var total = container.querySelectorAll('.milestone-row-card').length;
        if (total <= 1) {
            alert('Minimal harus ada 1 periode.');
            return;
        }

        var card = btn.closest('.milestone-row-card');
        if (card) {
            card.remove();
            reindexMilestoneCards();
        }
    }

    function reindexMilestoneCards() {
        var container = document.getElementById('milestonesContainer');
        if (!container) return;
        var rows = container.querySelectorAll('.milestone-row-card');
        rows.forEach(function(row, idx) {
            var span = row.querySelector('.m-index');
            if (span) span.textContent = (idx + 1);
        });
    }
    </script>
    <script src="../assets/js/admin.js?v=<?= time() ?>"></script>
</body>
</html>
