/**
 * X BAR - Admin Panel JavaScript
 * Password toggles, live image previews, website preview, delete modals
 */

document.addEventListener('DOMContentLoaded', () => {
    // ----------------------------------------------------
    // 1. Password Visibility Toggle (Show / Hide)
    // ----------------------------------------------------
    document.querySelectorAll('.toggle-password').forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            const targetId = btn.dataset.target;
            const input = document.getElementById(targetId) || btn.closest('.auth-input-group, .admin-input-card')?.querySelector('input');
            if (!input) return;

            if (input.type === 'password') {
                input.type = 'text';
                btn.innerHTML = `<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>`;
            } else {
                input.type = 'password';
                btn.innerHTML = `<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line></svg>`;
            }
        });
    });

    // ----------------------------------------------------
    // 2. Image Upload Preview
    // ----------------------------------------------------
    const imageInput = document.getElementById('imageFileInput');
    const imagePreview = document.getElementById('imagePreviewImg');
    const uploadPlaceholder = document.getElementById('uploadPlaceholder');

    if (imageInput) {
        imageInput.addEventListener('change', function() {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    if (imagePreview) {
                        imagePreview.src = e.target.result;
                        imagePreview.style.display = 'block';
                    }
                    if (uploadPlaceholder) {
                        uploadPlaceholder.style.display = 'none';
                    }
                };
                reader.readAsDataURL(file);
            }
        });
    }

    // Trigger file input via button
    const triggerUploadBtn = document.getElementById('btnTriggerUpload');
    if (triggerUploadBtn && imageInput) {
        triggerUploadBtn.addEventListener('click', (e) => {
            e.preventDefault();
            imageInput.click();
        });
    }

    // ----------------------------------------------------
    // 3. Live Header Preview in Manage Website (Page 12)
    // ----------------------------------------------------
    const inputWebTitle = document.getElementById('inputWebTitle');
    const inputWebDesc = document.getElementById('inputWebDesc');
    const liveTitle = document.getElementById('liveTitle');
    const liveDesc = document.getElementById('liveDesc');

    if (inputWebTitle && liveTitle) {
        inputWebTitle.addEventListener('input', function() {
            liveTitle.textContent = this.value || 'Natural Energy, Global Quality';
        });
    }

    if (inputWebDesc && liveDesc) {
        inputWebDesc.addEventListener('input', function() {
            liveDesc.textContent = this.value || 'And Firewood For Global Culinary (BBQ/Grill), Industrial, And Agricultural Needs.';
        });
    }

    // ----------------------------------------------------
    // 4. Products Search Filter (Page 5)
    // ----------------------------------------------------
    const productSearchInput = document.getElementById('productSearchInput');
    if (productSearchInput) {
        productSearchInput.addEventListener('input', function() {
            const term = this.value.toLowerCase().trim();
            document.querySelectorAll('.mgmt-product-row').forEach(row => {
                const name = row.querySelector('.mgmt-prod-name')?.textContent.toLowerCase() || '';
                if (name.includes(term)) {
                    row.style.display = 'flex';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    }

    // ----------------------------------------------------
    // 5. Delete Confirmation
    // ----------------------------------------------------
    document.querySelectorAll('.btn-confirm-delete').forEach(btn => {
        btn.addEventListener('click', function(e) {
            const name = this.dataset.name || 'this item';
            if (!confirm(`Are you sure you want to delete ${name}? This action cannot be undone.`)) {
                e.preventDefault();
            }
        });
    });

    // ----------------------------------------------------
    // 6. Language Switcher (Indonesia / English)
    // ----------------------------------------------------
    let currentLang = localStorage.getItem('xbar_lang') || 'en';

    function setLanguage(lang) {
        currentLang = lang;
        localStorage.setItem('xbar_lang', lang);

        document.querySelectorAll('.lang-item').forEach(el => {
            if (el.dataset.lang === lang) {
                el.classList.add('active');
            } else {
                el.classList.remove('active');
            }
        });

        // Switch all elements with data-en and data-id
        document.querySelectorAll('[data-en]').forEach(el => {
            if (lang === 'id' && el.dataset.id) {
                if (el.tagName === 'INPUT' || el.tagName === 'TEXTAREA') {
                    el.placeholder = el.dataset.id;
                } else {
                    el.innerHTML = el.dataset.id;
                }
            } else if (lang === 'en' && el.dataset.en) {
                if (el.tagName === 'INPUT' || el.tagName === 'TEXTAREA') {
                    el.placeholder = el.dataset.en;
                } else {
                    el.innerHTML = el.dataset.en;
                }
            }
        });
    }

    document.addEventListener('click', (e) => {
        const item = e.target.closest('.lang-item');
        if (item && item.dataset.lang) {
            e.preventDefault();
            setLanguage(item.dataset.lang);
        }
    });

    // Initialize saved language
    setLanguage(currentLang);

    // ----------------------------------------------------
    // 7. Dynamic History Milestones (Add / Remove)
    // ----------------------------------------------------
    const milestonesContainer = document.getElementById('milestonesContainer');

    window.reindexMilestoneCards = function() {
        if (!milestonesContainer) return;
        const rows = milestonesContainer.querySelectorAll('.milestone-row-card');
        rows.forEach((row, idx) => {
            const idxSpan = row.querySelector('.m-index');
            if (idxSpan) idxSpan.textContent = (idx + 1);
        });
    };

    window.addMilestonePeriod = function(e) {
        if (e && e.preventDefault) e.preventDefault();
        const container = document.getElementById('milestonesContainer');
        if (!container) return;

        const count = container.querySelectorAll('.milestone-row-card').length + 1;
        const newCard = document.createElement('div');
        newCard.className = 'milestone-row-card';
        newCard.style.cssText = 'background:#f8fafc;padding:12px;border-radius:12px;border:1.5px solid #fed7aa;position:relative;margin-bottom:4px;box-shadow:0 4px 12px rgba(234,88,12,0.1);animation:fadeIn 0.25s ease;';
        newCard.innerHTML = `
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
        container.appendChild(newCard);
        window.reindexMilestoneCards();
        container.scrollTop = container.scrollHeight;
        const input = newCard.querySelector('input[name="milestone_year[]"]');
        if (input) input.focus();
    };

    window.removeMilestonePeriod = function(btn, e) {
        if (e && e.preventDefault) e.preventDefault();
        const container = document.getElementById('milestonesContainer');
        if (!container) return;

        const total = container.querySelectorAll('.milestone-row-card').length;
        if (total <= 1) {
            alert('Minimal harus ada 1 periode.');
            return;
        }

        const card = btn.closest('.milestone-row-card');
        if (card) {
            card.remove();
            window.reindexMilestoneCards();
        }
    };

    // ----------------------------------------------------
    // 8. Recent Activity Toggle (Show 2 or 8)
    // ----------------------------------------------------
    const toggleActivityBtn = document.getElementById('toggleRecentActivity');
    if (toggleActivityBtn) {
        let isExpanded = false;
        toggleActivityBtn.addEventListener('click', function(e) {
            e.preventDefault();
            isExpanded = !isExpanded;
            const extraItems = document.querySelectorAll('.activity-item-extra');

            if (isExpanded) {
                extraItems.forEach(item => {
                    item.style.display = 'flex';
                });
                this.dataset.en = 'Hide ∧';
                this.dataset.id = 'Sembunyikan ∧';
            } else {
                extraItems.forEach(item => {
                    item.style.display = 'none';
                });
                this.dataset.en = 'View All ∨';
                this.dataset.id = 'Lihat Semua ∨';
            }

            const activeLang = localStorage.getItem('xbar_lang') || 'en';
            this.textContent = (activeLang === 'id') ? this.dataset.id : this.dataset.en;
        });
    }

    // ----------------------------------------------------
    // 9. Live Last Activity Counter (Sidebar)
    // ----------------------------------------------------
    const lastActEl = document.querySelector('.last-activity-time');
    if (lastActEl && lastActEl.dataset.initialDiff !== undefined) {
        const initialDiff = parseInt(lastActEl.dataset.initialDiff, 10);
        if (initialDiff >= 0) {
            const pageStartTime = performance.now();

            function formatDiffTime(diff) {
                let en = 'Just Now';
                let id = 'Baru saja';

                if (diff > 10 && diff < 60) {
                    en = diff + 's Ago';
                    id = diff + 'd yang lalu';
                } else if (diff >= 60 && diff < 3600) {
                    const mins = Math.floor(diff / 60);
                    en = mins + 'm Ago';
                    id = mins + 'm yang lalu';
                } else if (diff >= 3600 && diff < 86400) {
                    const hours = Math.floor(diff / 3600);
                    en = hours + 'h Ago';
                    id = hours + ' jam lalu';
                } else if (diff >= 86400 && diff < 2592000) {
                    const days = Math.floor(diff / 86400);
                    en = days + 'd Ago';
                    id = days + ' hari lalu';
                } else if (diff >= 2592000 && diff < 31536000) {
                    const months = Math.floor(diff / 2592000);
                    en = months + 'mo Ago';
                    id = months + ' bln lalu';
                } else if (diff >= 31536000) {
                    const years = Math.floor(diff / 31536000);
                    en = years + 'y Ago';
                    id = years + ' thn lalu';
                }

                return { en, id };
            }

            function tickLastActivity() {
                const elapsedSec = Math.floor((performance.now() - pageStartTime) / 1000);
                const currentDiff = initialDiff + elapsedSec;
                const formatted = formatDiffTime(currentDiff);

                lastActEl.dataset.en = formatted.en;
                lastActEl.dataset.id = formatted.id;

                const activeLang = localStorage.getItem('xbar_lang') || 'en';
                lastActEl.textContent = (activeLang === 'id') ? formatted.id : formatted.en;
            }

            setInterval(tickLastActivity, initialDiff < 60 ? 5000 : 30000);
        }
    }
});
