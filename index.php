<?php
require_once __DIR__ . '/config/database.php';
trackVisitor('index.php');

// Fetch dynamic content from DB with defaults
$web_title = getContent('website_title', 'Natural Energy, Global Quality');
$hero_desc = getContent('hero_short_desc', getContent('hero_tagline', 'A trusted supplier of premium-grade charcoal and firewood for global culinary (BBQ/Grill), industrial, and agricultural needs. Ready to consistently supply both local and international markets.'));
$hero_bg = getContent('hero_image', 'assets/images/hero-bg.jpg');

$about_year = getContent('about_year', '2017');
$about_desc = getContent('about_desc', 'X BAR is an Indonesia-based supplier of quality charcoal and wood-based products, serving both local and international markets. We specialize in Tamarind Charcoal, Mixed Charcoal, Firewood, and Charcoal Powder, carefully selected and processed to deliver consistent quality and reliable performance. Our commitment is simple: Quality Products, Reliable Service, and Long-Term Partnerships. From Indonesia to the world, X BAR is committed to bringing trusted Indonesian products to global markets while continuously improving our quality, service, and responsible use of natural resources.');

// Fetch history milestones from database
$stmtMilestones = $pdo->query("SELECT * FROM history_milestones ORDER BY sort_order ASC, id ASC");
$milestones = $stmtMilestones ? $stmtMilestones->fetchAll() : [];
if (empty($milestones)) {
    $milestones = [
        ['year_range' => '2017 - 2018', 'description' => 'As the manufacturer, we built three kilns at a cost of 9 million per unit, with specifications of 9 meters in length and 2 meters in width. We supply our output to local middlemen in Bondowoso.'],
        ['year_range' => '2019 - 2022', 'description' => 'We partnered with an external investor to construct 25 kilns at a production site in Sukowono Village, Pujer District. With these 25 kilns, we are able to ship an output of 4-5 containers per month. We export the charcoal to buyers in the Middle East.'],
        ['year_range' => '2022 - Now', 'description' => 'We have transformed into charcoal aggregators, partnering with farmers and kiln owners in Bondowoso to help market their charcoal production.']
    ];
}

$vm_year        = getContent('vision_mission_year', '2026 - 2030');
$vision_desc    = getContent('vision', 'To become a trusted, world-class Indonesian company providing high-quality wood- and charcoal-based energy products.');
$mission_desc   = getContent('mission', "1. Provide consistently high-quality charcoal and wood products for the international market.\n2. Build long-term relationships based on trust, quality, and excellent service.\n3. Develop products sustainably through the responsible use of natural resources.\n4. Bring the quality and excellence of Indonesian products to the global market.");
$map_url        = getContent('map_url', 'https://www.google.com/maps/search/?api=1&query=3RQG%2BCCV+CV+SUDUT%2C+Jl.+P.b.+Sudirman%2C+Patemon%2C+Blindungan%2C+Bondowoso%2C+Bondowoso+Regency%2C+East+Java+68212');

// Fetch products from database
$stmt = $pdo->query("SELECT * FROM products ORDER BY sort_order ASC, id ASC");
$products = $stmt->fetchAll();

// Fetch galleries from database
$stmtGal = $pdo->query("SELECT * FROM galleries ORDER BY sort_order ASC, id ASC");
$galleries = $stmtGal->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>X BAR — <?= htmlspecialchars($web_title) ?></title>
    <link rel="stylesheet" href="assets/css/style.css?v=<?= time() ?>">
    <link rel="shortcut icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>🔥</text></svg>">
</head>
<body>

    <!-- NAVBAR -->
    <nav class="navbar">
        <div class="navbar-inner">
            <a href="index.php" class="brand-logo">
                <span class="brand-badge">X̄</span>
                <span>X BAR</span>
            </a>

            <ul class="nav-links">
                <li><a href="#about" data-en="About Us" data-id="Tentang Kami">About Us</a></li>
                <li><a href="#products" data-en="Product" data-id="Produk">Product</a></li>
                <li><a href="#contact" data-en="Our Contact" data-id="Kontak Kami">Our Contact</a></li>
            </ul>

            <div class="nav-right">
                <div class="lang-switch">
                    <span class="lang-item" data-lang="id">Indonesia</span>
                    <span style="color:#64748b;">/</span>
                    <span class="lang-item active" data-lang="en">English</span>
                </div>
                <a href="admin/login.php" class="admin-icon-btn" title="Admin Login">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                        <circle cx="12" cy="7" r="4"></circle>
                    </svg>
                </a>
                <button class="mobile-menu-toggle" aria-label="Toggle menu">☰</button>
            </div>
        </div>
    </nav>

    <!-- HERO SECTION -->
    <header class="hero-section">
        <div class="hero-bg-overlay" style="background-image: linear-gradient(135deg, rgba(17, 24, 39, 0.85) 0%, rgba(0, 0, 0, 0.75) 100%), url('<?= htmlspecialchars($hero_bg) ?>'); background-size: cover; background-position: center;"></div>
        <div class="hero-content">
            <h1 class="hero-title">X BAR</h1>
            <h2 class="hero-subtitle"><?= htmlspecialchars($web_title) ?></h2>
            <p class="hero-desc">
                <?= htmlspecialchars($hero_desc) ?>
            </p>
            <div class="hero-actions">
                <a href="#contact" class="btn-glass btn-open-quote" data-en="CONTACT US" data-id="HUBUNGI KAMI">CONTACT US</a>
                <a href="#products" class="btn-glass" data-en="PRODUCTS" data-id="PRODUK KAMI">PRODUCTS</a>
            </div>
        </div>
    </header>

    <!-- ABOUT US SECTION -->
    <section id="about" class="section-light about-section">
        <div class="container">
            <h2 class="section-heading-dark" data-en="About Us" data-id="Tentang Kami">About Us</h2>
            <div class="about-body">
                <p class="about-text">
                    <?= nl2br(htmlspecialchars($about_desc)) ?>
                </p>
                <h3 class="about-tagline" data-en="X BAR — From Indonesia, Trusted Worldwide." data-id="X BAR — Dari Indonesia, Terpercaya di Seluruh Dunia.">
                    X BAR — From Indonesia, Trusted Worldwide.
                </h3>
            </div>
        </div>
    </section>

    <!-- OUR HISTORY SECTION -->
    <section class="section-dark history-section">
        <div class="container">
            <h2 class="section-heading-light" data-en="Our History" data-id="Sejarah Kami">Our History</h2>
            <div class="history-timeline">
                <?php foreach ($milestones as $idx => $m): 
                    $side = ($idx % 2 === 0) ? 'right' : 'left';
                ?>
                <div class="history-item <?= $side ?>">
                    <div class="history-year"><?= htmlspecialchars($m['year_range']) ?></div>
                    <div class="history-text">
                        <?= nl2br(htmlspecialchars($m['description'])) ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- WHY CHOOSE US SECTION -->
    <section class="section-light">
        <div class="container">
            <h2 class="section-heading-dark" data-en="Why Choose Us" data-id="Mengapa Memilih Kami">Why Choose Us</h2>
            <div class="features-grid">
                <!-- 1. Consistent Quality -->
                <div class="feature-card">
                    <div class="feature-icon-wrapper">
                        <img src="assets/images/icons/why-quality.svg" alt="Consistent Quality" class="feature-icon-img">
                    </div>
                    <div class="feature-title" data-en="Consistent<br>Quality" data-id="Kualitas<br>Konsisten">Consistent<br>Quality</div>
                </div>

                <!-- 2. Reliable Supply -->
                <div class="feature-card">
                    <div class="feature-icon-wrapper">
                        <img src="assets/images/icons/why-supply.svg" alt="Reliable Supply" class="feature-icon-img">
                    </div>
                    <div class="feature-title" data-en="Reliable<br>Supply" data-id="Pasokan<br>Andal">Reliable<br>Supply</div>
                </div>

                <!-- 3. Competitive Pricing -->
                <div class="feature-card">
                    <div class="feature-icon-wrapper">
                        <img src="assets/images/icons/why-pricing.svg" alt="Competitive Pricing" class="feature-icon-img">
                    </div>
                    <div class="feature-title" data-en="Competitive<br>Pricing" data-id="Harga<br>Kompetitif">Competitive<br>Pricing</div>
                </div>

                <!-- 4. Export Ready -->
                <div class="feature-card">
                    <div class="feature-icon-wrapper">
                        <img src="assets/images/icons/why-export.svg" alt="Export Ready" class="feature-icon-img">
                    </div>
                    <div class="feature-title" data-en="Export<br>Ready" data-id="Siap<br>Ekspor">Export<br>Ready</div>
                </div>

                <!-- 5. Long-Term Partnership -->
                <div class="feature-card">
                    <div class="feature-icon-wrapper">
                        <img src="assets/images/icons/why-partnership.svg" alt="Long-Term Partnership" class="feature-icon-img">
                    </div>
                    <div class="feature-title" data-en="Long-Term<br>Partnership" data-id="Kemitraan<br>Jangka Panjang">Long-Term<br>Partnership</div>
                </div>
            </div>
        </div>
    </section>

    <!-- VISION & MISSION SECTION -->
    <section class="vision-mission-section">
        <div class="container">

            <div class="vm-grid">
                <div class="vm-col-divider"></div>
                <!-- Vision -->
                <div class="vm-box">
                    <h2 class="vm-title">
                        <span data-en="VISION" data-id="VISI">VISION</span>
                        <?php if (!empty($vm_year)): ?>
                            <small style="font-size:0.55em;opacity:0.85;font-weight:600;margin-left:8px;background:rgba(255,255,255,0.15);padding:2px 8px;border-radius:10px;vertical-align:middle;"><?= htmlspecialchars($vm_year) ?></small>
                        <?php endif; ?>
                    </h2>
                    <div class="vm-content">
                        <?= nl2br(htmlspecialchars($vision_desc)) ?>
                    </div>
                </div>

                <!-- Mission -->
                <div class="vm-box">
                    <h2 class="vm-title">
                        <span data-en="MISSION" data-id="MISI">MISSION</span>
                        <?php if (!empty($vm_year)): ?>
                            <small style="font-size:0.55em;opacity:0.85;font-weight:600;margin-left:8px;background:rgba(255,255,255,0.15);padding:2px 8px;border-radius:10px;vertical-align:middle;"><?= htmlspecialchars($vm_year) ?></small>
                        <?php endif; ?>
                    </h2>
                    <div class="vm-content">
                        <?php 
                        $mission_lines = array_filter(array_map('trim', explode("\n", $mission_desc)));
                        if (!empty($mission_lines)): ?>
                            <ol>
                                <?php foreach ($mission_lines as $mLine): 
                                    $cleanLine = preg_replace('/^\d+\.\s*/', '', $mLine);
                                ?>
                                    <li><?= htmlspecialchars($cleanLine) ?></li>
                                <?php endforeach; ?>
                            </ol>
                        <?php else: ?>
                            <?= nl2br(htmlspecialchars($mission_desc)) ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- PRODUCTS SECTION -->
    <section id="products" class="section-light">
        <div class="container">
            <h2 class="section-heading-dark" data-en="Products" data-id="Produk">Products</h2>

            <!-- LANDING PAGE PRODUCT SEARCH BAR -->
            <div class="landing-product-search-wrap">
                <div class="landing-search-inner">
                    <svg class="search-input-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3">
                        <circle cx="11" cy="11" r="8"></circle>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                    </svg>
                    <input type="text" id="landingProductSearch" placeholder="Search Product..." data-en="Search Product..." data-id="Cari Produk..." autocomplete="off">
                    <button type="button" id="landingSearchClear" class="landing-search-clear-btn" aria-label="Clear search" style="display:none;">✕</button>
                </div>
            </div>

            <div class="products-travel-grid">
                <?php foreach ($products as $idx => $p): 
                    $categoryTag = !empty($p['category']) ? $p['category'] : 'Premium';
                    $stockText = !empty($p['product_stock']) ? $p['product_stock'] : 'Ready Stock';
                    $priceText = !empty($p['recent_price']) ? $p['recent_price'] : 'Contact for Price';

                    // Truncate product description to 10-20 words followed by ellipsis
                    $rawDesc = strip_tags($p['description'] ?? '');
                    $cleanDesc = trim(preg_replace('/\s+/', ' ', str_replace(['???', '??'], ' — ', $rawDesc)));
                    $descWords = !empty($cleanDesc) ? preg_split('/\s+/', $cleanDesc) : [];
                    $wordLimit = 15; // 10 to 20 words
                    if (count($descWords) > $wordLimit) {
                        $slicedWords = array_slice($descWords, 0, $wordLimit);
                        $lastIdx = count($slicedWords) - 1;
                        $slicedWords[$lastIdx] = rtrim($slicedWords[$lastIdx], " \t\n\r\0\x0B.,;:!?") . '...';
                        $shortDesc = implode(' ', $slicedWords);
                    } else {
                        $shortDesc = !empty($cleanDesc) ? $cleanDesc : '';
                    }
                ?>
                <div class="product-travel-card" 
                     id="prod-home-<?= $p['id'] ?>"
                     data-id="<?= $p['id'] ?>"
                     data-name="<?= htmlspecialchars($p['name'], ENT_QUOTES) ?>"
                     data-category="<?= htmlspecialchars($categoryTag, ENT_QUOTES) ?>"
                     data-price="<?= htmlspecialchars($priceText, ENT_QUOTES) ?>"
                     data-stock="<?= htmlspecialchars($stockText, ENT_QUOTES) ?>"
                     data-image="<?= htmlspecialchars($p['image'], ENT_QUOTES) ?>"
                     data-material="<?= htmlspecialchars($p['raw_material'] ?? '', ENT_QUOTES) ?>"
                     data-advantages="<?= htmlspecialchars($p['advantages'] ?? '', ENT_QUOTES) ?>"
                     data-application="<?= htmlspecialchars($p['application'] ?? '', ENT_QUOTES) ?>"
                     tabindex="0"
                     role="button"
                     aria-label="<?= htmlspecialchars($p['name']) ?>">
                    
                    <div class="travel-card-thumb-wrap">
                        <img src="<?= htmlspecialchars($p['image']) ?>" alt="<?= htmlspecialchars($p['name']) ?>" loading="lazy">
                        <span class="travel-tag-pill"><?= htmlspecialchars($categoryTag) ?></span>
                        <button type="button" class="travel-bookmark-btn" title="Save / View" aria-label="Save / View">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3">
                                <path d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z"></path>
                            </svg>
                        </button>
                        <span class="travel-promo-badge"><?= htmlspecialchars($stockText) ?></span>
                    </div>

                    <div class="travel-card-body">
                        <h3 class="travel-card-title"><?= htmlspecialchars($p['name']) ?></h3>
                        
                        <div class="travel-price-val"><?= htmlspecialchars($priceText) ?></div>

                        <?php if (!empty($shortDesc)): ?>
                        <p class="travel-card-desc"><?= htmlspecialchars($shortDesc) ?></p>
                        <?php endif; ?>

                        <div class="travel-card-hint">
                            <span data-en="more details &gt;" data-id="lihat selengkapnya &gt;">more details &gt;</span>
                        </div>
                    </div>

                    <div class="travel-card-desc-data" style="display:none;">
                        <?= nl2br(htmlspecialchars($p['description'])) ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <!-- GET E-CATALOGUE BUTTON (MATCHING MOCKUP) -->
            <div class="landing-catalogue-wrap">
                <a href="https://wa.me/6281333775663?text=Halo%20X%20BAR,%20saya%20ingin%20meminta%20E-Catalogue%20resmi%20produk%20arang%20X%20BAR." target="_blank" class="btn-e-catalogue">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                        <polyline points="7 10 12 15 17 10"></polyline>
                        <line x1="12" y1="15" x2="12" y2="3"></line>
                    </svg>
                    <span data-en="Get E-Catalogue" data-id="Dapatkan E-Katalog">Get E-Catalogue</span>
                </a>
            </div>

            <div id="productSearchEmptyMsg" style="display:none;text-align:center;padding:50px 20px;">
                <div style="font-size:36px;margin-bottom:12px;">🔍</div>
                <h3 data-en="No products match your search." data-id="Tidak ada produk yang cocok dengan pencarian Anda.">No products match your search.</h3>
                <p style="color:#64748b;font-size:14px;margin-top:6px;" data-en="Please try another keyword." data-id="Silakan coba dengan kata kunci lain.">Please try another keyword.</p>
            </div>
        </div>
    </section>

    <!-- GALLERY SECTION -->
    <section class="section-light gallery-section" id="gallery">
        <div class="container">
            <div class="gallery-header-row">
                <div class="gallery-header-text">
                    <h2 class="section-heading-dark" style="margin-bottom:6px;text-align:left;" data-en="Gallery" data-id="Galeri">Gallery</h2>
                    <p style="font-size:14px;color:#64748b;margin:0;" data-en="A glimpse into our production, sorting, and packaging facilities" data-id="Sekilas fasilitas produksi, penyortiran, dan pengemasan kami">A glimpse into our production, sorting, and packaging facilities</p>
                </div>
                <div class="gallery-nav-controls">
                    <button type="button" class="gallery-nav-btn" id="galleryPrevBtn" aria-label="Previous photo">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"></polyline></svg>
                    </button>
                    <button type="button" class="gallery-nav-btn" id="galleryNextBtn" aria-label="Next photo">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"></polyline></svg>
                    </button>
                </div>
            </div>

            <div class="gallery-slider-wrapper">
                <div class="gallery-slider-track" id="galleryTrack">
                    <?php foreach ($galleries as $idx => $g): ?>
                    <div class="gallery-slide-card" data-index="<?= $idx ?>">
                        <div class="gallery-card-inner">
                            <img src="<?= htmlspecialchars($g['image']) ?>" alt="<?= htmlspecialchars($g['title']) ?>" loading="lazy">
                            <div class="gallery-card-overlay">
                                <span class="gallery-card-num">#<?= $idx + 1 ?></span>
                                <h4 class="gallery-card-title"><?= htmlspecialchars($g['title']) ?></h4>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

                <div class="gallery-scrollbar-info">
                    <span class="gallery-scroll-hint" data-en="← Scroll or drag horizontally to explore photos →" data-id="← Geser atau gulir ke samping untuk melihat foto lainnya →">← Scroll or drag horizontally to explore photos →</span>
                </div>
            </div>
        </div>
    </section>

    <!-- QUALITY YOU CAN TRUST & LOCATION SECTION -->
    <section class="trust-section">
        <div class="container">
            <div class="trust-grid">
                <a href="<?= htmlspecialchars($map_url) ?>" target="_blank" rel="noopener noreferrer" class="location-box" title="Open in Google Maps">
                    <div class="location-img-wrap">
                        <img src="assets/images/map-location.jpg" alt="Our Location in Bondowoso">
                    </div>
                    <div class="location-title" data-en="Our Location" data-id="Lokasi Kami">Our Location</div>
                    <div class="location-badge">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path><polyline points="15 3 21 3 21 9"></polyline><line x1="10" y1="14" x2="21" y2="3"></line></svg>
                        <span data-en="View on Google Maps" data-id="Buka di Google Maps">View on Google Maps</span>
                    </div>
                </a>

                <div class="trust-content">
                    <h2 class="trust-title" data-en="Quality You Can Trust" data-id="Kualitas Yang Dapat Anda Percaya">Quality You Can Trust</h2>

                    <div class="trust-flow">
                        <span class="trust-flow-step" data-en="Raw Material" data-id="Bahan Baku">Raw Material</span>
                        <span class="trust-arrow">→</span>
                        <span class="trust-flow-step" data-en="Processing" data-id="Pengolahan">Processing</span>
                        <span class="trust-arrow">→</span>
                        <span class="trust-flow-step" data-en="Quality Check" data-id="Cek Kualitas">Quality Check</span>
                    </div>

                    <div class="trust-arrow-down">↓</div>
                    <div class="trust-step-down" data-en="Packing & Delivery" data-id="Pengemasan & Pengiriman">Packing & Delivery</div>

                    <div class="trust-arrow-down">↓</div>
                    <div class="trust-step-down" data-en="From Indonesia to the World" data-id="Dari Indonesia untuk Dunia">From Indonesia to the World</div>

                    <div class="trust-arrow-down">↓</div>
                    <div class="trust-step-down" data-en="Let's Build a Long-Term Partnership" data-id="Mari Bangun Kemitraan Jangka Panjang">Let's Build a Long-Term Partnership</div>

                    <div class="trust-btn-wrap">
                        <button class="btn-quotation btn-open-quote" data-en="REQUEST QUOTATION" data-id="MINTA PENAWARAN HARGA">REQUEST QUOTATION</button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- FOOTER -->
    <footer id="contact" class="site-footer">
        <div class="container">
            <div class="footer-grid">
                <!-- Col 1 -->
                <div class="footer-col-desc" data-en="A trusted supplier of premium-grade charcoal and firewood for global culinary (BBQ/Grill), industrial, and agricultural needs. Ready to consistently supply both local and international markets." data-id="Pemasok arang dan kayu bakar berkualitas premium terpercaya untuk kebutuhan kuliner global (BBQ/Grill), industri, dan pertanian. Siap memasok secara konsisten untuk pasar lokal maupun internasional.">
                    A trusted supplier of premium-grade charcoal and firewood for global culinary (BBQ/Grill), industrial, and agricultural needs. Ready to consistently supply both local and international markets.
                </div>

                <!-- Col 2: Address -->
                <a href="<?= htmlspecialchars($map_url) ?>" target="_blank" rel="noopener noreferrer" class="footer-col-address" title="Open in Google Maps">
                    <svg viewBox="0 0 24 24" fill="none" stroke-width="2">
                        <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                        <circle cx="12" cy="10" r="3"></circle>
                    </svg>
                    <p>JL. PB. Sudirman II/26<br>Bondowoso, 68213</p>
                </a>

                <!-- Col 3: Contacts -->
                <div class="footer-col-contacts">
                    <div class="footer-contact-item">
                        <svg viewBox="0 0 24 24" fill="none" stroke-width="2">
                            <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path>
                        </svg>
                        <span>081333775663</span>
                    </div>
                    <div class="footer-contact-item">
                        <svg viewBox="0 0 24 24" fill="none" stroke-width="2">
                            <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                            <polyline points="22,6 12,13 2,6"></polyline>
                        </svg>
                        <span>cvsudut@gmail.com</span>
                    </div>
                </div>
            </div>

            <div class="footer-copyright">
                X Bar. Copyright ©2026 All Right Reserved.
            </div>
        </div>
    </footer>

    <!-- FLOATING CHATBOT TRIGGER & PANEL (PAGE 10 & 13) -->
    <div class="chatbot-trigger" id="chatTrigger" title="Chat with Us">
        <svg viewBox="0 0 24 24">
            <path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"></path>
        </svg>
    </div>

    <div class="chatbot-panel" id="chatPanel">
        <div class="chatbot-header">
            <div class="chatbot-header-title">
                <span class="brand-badge" style="width:26px;height:26px;font-size:12px;">X̄</span>
                <span>X BAR Assistant</span>
            </div>
            <button class="chatbot-close-btn" id="chatCloseBtn">✕</button>
        </div>

        <div class="chatbot-body" id="chatMessages">
            <div class="chat-bubble bot">
                Hi, I'm your ChatBot. Is there anything I can help you with? I'm here to help.
            </div>
        </div>

        <div class="chatbot-footer">
            <form class="chat-input-form" id="chatForm">
                <input type="text" id="chatInput" placeholder="Type your message ..." autocomplete="off">
                <button type="submit" aria-label="Send">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="22" y1="2" x2="11" y2="13"></line>
                        <polygon points="22 2 15 22 11 13 2 9 22 2"></polygon>
                    </svg>
                </button>
            </form>
        </div>
    </div>

    <!-- REQUEST QUOTATION MODAL -->
    <div class="modal-overlay" id="quotationModal">
        <div class="modal-card">
            <div class="modal-header">
                <h3 class="modal-title" data-en="Request Quotation" data-id="Permintaan Penawaran">Request Quotation</h3>
                <button class="modal-close" id="quotationCloseBtn" type="button" aria-label="Close">&times;</button>
            </div>
            <form action="quotation_submit.php" method="POST" class="modal-form">
                <div class="modal-form-grid">
                    <div class="form-group">
                        <label data-en="Full Name *" data-id="Nama Lengkap *">Full Name *</label>
                        <input type="text" name="full_name" required placeholder="Your name">
                    </div>
                    <div class="form-group">
                        <label data-en="Email Address *" data-id="Alamat Email *">Email Address *</label>
                        <input type="email" name="email" required placeholder="name@company.com">
                    </div>
                </div>
                <div class="modal-form-grid">
                    <div class="form-group">
                        <label data-en="Phone / WhatsApp" data-id="Nomor Telepon / WA">Phone / WhatsApp</label>
                        <input type="text" name="phone" placeholder="+62 812 3456 7890">
                    </div>
                    <div class="form-group">
                        <label data-en="Product of Interest" data-id="Produk Yang Diminati">Product of Interest</label>
                        <select name="product_name" id="quoteProduct">
                            <option value="">Select Product</option>
                            <?php foreach ($products as $p): ?>
                            <option value="<?= htmlspecialchars($p['name']) ?>"><?= htmlspecialchars($p['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label data-en="Estimated Quantity (e.g. 1 Container / 20 Tons)" data-id="Perkiraan Jumlah">Estimated Quantity</label>
                    <input type="text" name="quantity" placeholder="e.g. 20 Tons / 1 Container">
                </div>
                <div class="form-group">
                    <label data-en="Message / Destination Port" data-id="Pesan / Pelabuhan Tujuan">Message / Destination Port</label>
                    <textarea name="message" rows="2" required placeholder="Specify delivery port and requirements..."></textarea>
                </div>
                <button type="submit" class="btn-modal-submit">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3"><line x1="22" y1="2" x2="11" y2="13"></line><polygon points="22 2 15 22 11 13 2 9 22 2"></polygon></svg>
                    <span data-en="Send Request" data-id="Kirim Permintaan">Send Request</span>
                </button>
            </form>
        </div>
    </div>

    <?php require_once __DIR__ . '/product_modal.php'; ?>

    <script src="assets/js/main.js"></script>
</body>
</html>
