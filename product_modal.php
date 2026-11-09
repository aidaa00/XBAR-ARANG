<!-- PRODUCT DETAIL & DESCRIPTION MODAL (POPUP ON CARD CLICK) -->
<div class="modal-overlay product-modal-overlay" id="productDetailModal" aria-hidden="true" role="dialog" aria-modal="true">
    <div class="product-modal-card">
        <button type="button" class="product-modal-close-btn" id="productDetailCloseBtn" aria-label="Close modal">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
                <line x1="18" y1="6" x2="6" y2="18"></line>
                <line x1="6" y1="6" x2="18" y2="18"></line>
            </svg>
        </button>

        <div class="product-modal-grid">
            <!-- Left Column: Image & Quick Stats -->
            <div class="product-modal-media-col">
                <div class="product-modal-img-wrap">
                    <img src="" alt="" id="pmProductImage">
                    <span class="product-modal-cat-pill" id="pmProductCategory">Charcoal</span>
                </div>
                <div class="product-modal-stat-cards">
                    <div class="pm-stat-box">
                        <span class="pm-stat-label" data-en="Recent Price" data-id="Harga Terbaru">Recent Price</span>
                        <strong class="pm-stat-value" id="pmProductPrice">USD 330</strong>
                    </div>
                    <div class="pm-stat-box">
                        <span class="pm-stat-label" data-en="Product Stock" data-id="Stok Produk">Product Stock</span>
                        <strong class="pm-stat-value" id="pmProductStock">50 Tons</strong>
                    </div>
                </div>
            </div>

            <!-- Right Column: Title, Full Description & Technical Specifications -->
            <div class="product-modal-info-col">
                <div class="product-modal-header">
                    <span class="product-modal-subtag" data-en="Product Specification" data-id="Spesifikasi Produk">Product Specification</span>
                    <h2 class="product-modal-title" id="pmProductName">Product Name</h2>
                    
                    <div class="product-modal-rating-row">
                        <div class="product-modal-stars">
                            <span>★</span><span>★</span><span>★</span><span>★</span><span>★</span>
                        </div>
                        <span class="product-modal-score">4.9 / 5.0</span>
                        <span class="product-modal-reviews">• <span data-en="Verified Quality" data-id="Kualitas Terverifikasi">Verified Quality</span></span>
                    </div>
                </div>

                <!-- DESCRIPTION BOX - PROMINENTLY DISPLAYED ON MODAL OPEN -->
                <div class="product-modal-desc-section">
                    <h4 class="product-modal-section-title">
                        <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                            <polyline points="14 2 14 8 20 8"></polyline>
                            <line x1="16" y1="13" x2="8" y2="13"></line>
                            <line x1="16" y1="17" x2="8" y2="17"></line>
                            <polyline points="10 9 9 9 8 9"></polyline>
                        </svg>
                        <span data-en="Product Description" data-id="Deskripsi Produk">Product Description</span>
                    </h4>
                    <div class="product-modal-desc-content" id="pmProductDesc">
                        <!-- Populated dynamically via JS -->
                    </div>
                </div>

                <!-- TECHNICAL SPECS (RAW MATERIAL, ADVANTAGES, APPLICATIONS) -->
                <div class="product-modal-specs-list" id="pmSpecsList">
                    <div class="product-modal-spec-item" id="pmMaterialItem">
                        <span class="pm-spec-name" data-en="Raw Material:" data-id="Bahan Baku:">Raw Material:</span>
                        <span class="pm-spec-text" id="pmProductMaterial">-</span>
                    </div>
                    <div class="product-modal-spec-item" id="pmAdvantagesItem">
                        <span class="pm-spec-name" data-en="Key Advantages:" data-id="Keunggulan:">Key Advantages:</span>
                        <span class="pm-spec-text" id="pmProductAdvantages">-</span>
                    </div>
                    <div class="product-modal-spec-item" id="pmApplicationItem">
                        <span class="pm-spec-name" data-en="Applications:" data-id="Aplikasi:">Applications:</span>
                        <span class="pm-spec-text" id="pmProductApplication">-</span>
                    </div>
                </div>

                <!-- ACTIONS -->
                <div class="product-modal-actions">
                    <button type="button" class="pm-action-btn pm-action-quote" id="pmBtnQuote">
                        <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                            <line x1="22" y1="2" x2="11" y2="13"></line>
                            <polygon points="22 2 15 22 11 13 2 9 22 2"></polygon>
                        </svg>
                        <span data-en="Request Quotation" data-id="Minta Penawaran">Request Quotation</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
