/**
 * X BAR - Main Frontend JavaScript
 * Language Switcher, Interactive Chatbot Widget, Modals & Navigation
 */

document.addEventListener('DOMContentLoaded', () => {
    // ----------------------------------------------------
    // 1. Language Switcher (Indonesia / English)
    // ----------------------------------------------------
    const langSwitchers = document.querySelectorAll('.lang-switch');
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

    langSwitchers.forEach(sw => {
        sw.addEventListener('click', (e) => {
            const item = e.target.closest('.lang-item');
            if (item && item.dataset.lang) {
                setLanguage(item.dataset.lang);
            }
        });
    });

    // Initialize saved language
    setLanguage(currentLang);

    // ----------------------------------------------------
    // 2. Mobile Menu Toggle
    // ----------------------------------------------------
    const mobileBtn = document.querySelector('.mobile-menu-toggle');
    const navLinks = document.querySelector('.nav-links');
    if (mobileBtn && navLinks) {
        mobileBtn.addEventListener('click', () => {
            navLinks.classList.toggle('show');
        });
    }

    // ----------------------------------------------------
    // 3. Floating Chatbot Widget (Page 10 & 13)
    // ----------------------------------------------------
    const chatTrigger = document.getElementById('chatTrigger');
    const chatPanel = document.getElementById('chatPanel');
    const chatCloseBtn = document.getElementById('chatCloseBtn');
    const chatForm = document.getElementById('chatForm');
    const chatInput = document.getElementById('chatInput');
    const chatMessages = document.getElementById('chatMessages');

    if (chatTrigger && chatPanel) {
        chatTrigger.addEventListener('click', () => {
            chatPanel.classList.toggle('open');
            if (chatPanel.classList.contains('open')) {
                chatInput.focus();
            }
        });

        if (chatCloseBtn) {
            chatCloseBtn.addEventListener('click', () => {
                chatPanel.classList.remove('open');
            });
        }

        // Close on outside click
        document.addEventListener('click', (e) => {
            if (!chatPanel.contains(e.target) && !chatTrigger.contains(e.target) && chatPanel.classList.contains('open')) {
                chatPanel.classList.remove('open');
            }
        });

        // Chat form submit
        if (chatForm && chatInput && chatMessages) {
            chatForm.addEventListener('submit', (e) => {
                e.preventDefault();
                const userMsg = chatInput.value.trim();
                if (!userMsg) return;

                // Append user bubble
                appendChatMessage(userMsg, 'user');
                chatInput.value = '';

                // Simulate bot typing / response
                setTimeout(() => {
                    const botReply = generateBotResponse(userMsg);
                    appendChatMessage(botReply, 'bot');
                }, 500);
            });
        }
    }

    function appendChatMessage(text, sender) {
        const bubble = document.createElement('div');
        bubble.className = `chat-bubble ${sender}`;
        bubble.innerHTML = text;
        chatMessages.appendChild(bubble);
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }

    function generateBotResponse(msg) {
        const text = msg.toLowerCase();
        if (text.includes('harga') || text.includes('price')) {
            return "Our current prices are: Tamarind Charcoal: USD 340/ton, Mix Charcoal: USD 330/ton, Firewood: USD 205/ton, Charcoal Powder: USD 33.57/kg, Industrial: USD 255/ton. For FOB/CIF quotes, please click <b>REQUEST QUOTATION</b>!";
        } else if (text.includes('tamarind') || text.includes('asam')) {
            return "Tamarind Charcoal is made 100% from premium tamarind wood. Burns 6-7 hours with neutral scent, low ash, and zero sparks. Ideal for BBQ & industrial kitchens!";
        } else if (text.includes('export') || text.includes('shipping') || text.includes('kirim')) {
            return "We regularly export 4-5 containers per month to the Middle East, Asia, Europe, and America via Tanjung Perak Port, Surabaya.";
        } else if (text.includes('lokasi') || text.includes('location') || text.includes('alamat') || text.includes('address')) {
            return "Our headquarters & kiln facilities are located at <b>JL. PB. Sudirman II/26, Bondowoso, 68213, East Java, Indonesia</b>.";
        } else if (text.includes('kontak') || text.includes('contact') || text.includes('wa') || text.includes('whatsapp') || text.includes('phone')) {
            return "You can contact our sales team directly via WhatsApp/Phone at <b>+62 813-3377-5663</b> or email: <b>cvsudut@gmail.com</b>.";
        } else if (text.includes('halo') || text.includes('hello') || text.includes('hi')) {
            return "Hello! How can I assist you today? You can ask about our Charcoal products, specifications, export terms, or prices.";
        } else {
            return "Thank you for reaching out! For detailed orders or custom inquiries, please submit a <b>Request Quotation</b> or reach out via WhatsApp at <b>+62 813-3377-5663</b>.";
        }
    }

    // ----------------------------------------------------
    // 4. Quotation Modal
    // ----------------------------------------------------
    const quotationModal = document.getElementById('quotationModal');
    const quotationCloseBtn = document.getElementById('quotationCloseBtn');
    const openQuoteBtns = document.querySelectorAll('.btn-open-quote');

    openQuoteBtns.forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            const product = btn.dataset.product || '';
            const prodInput = document.getElementById('quoteProduct');
            if (prodInput && product) {
                prodInput.value = product;
            }
            if (quotationModal) {
                quotationModal.classList.add('active');
            }
        });
    });

    if (quotationCloseBtn && quotationModal) {
        quotationCloseBtn.addEventListener('click', () => {
            quotationModal.classList.remove('active');
        });

        quotationModal.addEventListener('click', (e) => {
            if (e.target === quotationModal) {
                quotationModal.classList.remove('active');
            }
        });

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && quotationModal.classList.contains('active')) {
                quotationModal.classList.remove('active');
            }
        });
    }

    // ----------------------------------------------------
    // 5. WhatsApp Direct Action for "BUY NOW" buttons
    // ----------------------------------------------------
    document.querySelectorAll('.btn-buy-now').forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            const productName = btn.dataset.product || 'Charcoal';
            const price = btn.dataset.price || '';
            const phone = '6281333775663';
            const message = encodeURIComponent(`Hello X BAR, I am interested in ordering ${productName} (${price}). Please provide further details on shipment and minimum order quantity.`);
            window.open(`https://wa.me/${phone}?text=${message}`, '_blank');
        });
    });

    // ----------------------------------------------------
    // 6. Horizontal Gallery Slider with Dynamic Blur & Darken
    // ----------------------------------------------------
    const galleryTrack = document.getElementById('galleryTrack');
    const galleryPrevBtn = document.getElementById('galleryPrevBtn');
    const galleryNextBtn = document.getElementById('galleryNextBtn');

    if (galleryTrack) {
        const cards = galleryTrack.querySelectorAll('.gallery-slide-card');

        function updateGalleryEffects() {
            const trackRect = galleryTrack.getBoundingClientRect();
            const trackCenter = trackRect.left + (trackRect.width / 2);

            cards.forEach(card => {
                const cardRect = card.getBoundingClientRect();
                const cardCenter = cardRect.left + (cardRect.width / 2);
                const distance = Math.abs(trackCenter - cardCenter);
                const maxDistance = trackRect.width * 0.55;
                const ratio = Math.min(distance / Math.max(maxDistance, 1), 1);

                // Closer to center = ratio 0: sharp, bright, scaled
                // Farther along = ratio 1: blurred, darker, stacked down
                const blurPx = (ratio * 4.5).toFixed(1);
                const brightnessVal = (1 - (ratio * 0.52)).toFixed(2);
                const scaleVal = (1.03 - (ratio * 0.11)).toFixed(2);
                const opacityVal = (1 - (ratio * 0.32)).toFixed(2);
                const zIndexVal = Math.round((1 - ratio) * 10);

                card.style.filter = `blur(${blurPx}px) brightness(${brightnessVal})`;
                card.style.transform = `scale(${scaleVal})`;
                card.style.opacity = opacityVal;
                card.style.zIndex = zIndexVal;

                if (ratio < 0.25) {
                    card.classList.add('is-active');
                } else {
                    card.classList.remove('is-active');
                }
            });
        }

        let isTicking = false;
        galleryTrack.addEventListener('scroll', () => {
            if (!isTicking) {
                window.requestAnimationFrame(() => {
                    updateGalleryEffects();
                    isTicking = false;
                });
                isTicking = true;
            }
        });

        window.addEventListener('resize', updateGalleryEffects);

        // Click card to center it smoothly
        cards.forEach(card => {
            card.addEventListener('click', () => {
                card.scrollIntoView({ behavior: 'smooth', inline: 'center', block: 'nearest' });
            });
        });

        // Prev & Next Buttons
        if (galleryPrevBtn) {
            galleryPrevBtn.addEventListener('click', () => {
                galleryTrack.scrollBy({ left: -350, behavior: 'smooth' });
            });
        }
        if (galleryNextBtn) {
            galleryNextBtn.addEventListener('click', () => {
                galleryTrack.scrollBy({ left: 350, behavior: 'smooth' });
            });
        }

        // Mouse drag scrolling support
        let isDown = false;
        let startX, scrollLeft;

        galleryTrack.addEventListener('mousedown', (e) => {
            isDown = true;
            galleryTrack.style.cursor = 'grabbing';
            startX = e.pageX - galleryTrack.offsetLeft;
            scrollLeft = galleryTrack.scrollLeft;
        });

        galleryTrack.addEventListener('mouseleave', () => {
            isDown = false;
            galleryTrack.style.cursor = '';
        });

        galleryTrack.addEventListener('mouseup', () => {
            isDown = false;
            galleryTrack.style.cursor = '';
        });

        galleryTrack.addEventListener('mousemove', (e) => {
            if (!isDown) return;
            e.preventDefault();
            const x = e.pageX - galleryTrack.offsetLeft;
            const walk = (x - startX) * 1.5;
            galleryTrack.scrollLeft = scrollLeft - walk;
        });

        // Initial setup
        setTimeout(updateGalleryEffects, 100);
    }

    // ----------------------------------------------------
    // 7. Interactive Product Travel Cards & Detail Modal
    // ----------------------------------------------------
    const productDetailModal = document.getElementById('productDetailModal');
    const productDetailCloseBtn = document.getElementById('productDetailCloseBtn');
    const productCards = document.querySelectorAll('.product-travel-card');

    function openProductDetailModal(card) {
        if (!productDetailModal || !card) return;

        const name = card.dataset.name || '';
        const category = card.dataset.category || 'Charcoal';
        const price = card.dataset.price || '';
        const stock = card.dataset.stock || '';
        const image = card.dataset.image || '';
        const material = card.dataset.material || '';
        const advantages = card.dataset.advantages || '';
        const application = card.dataset.application || '';

        // Get description from hidden container
        const descEl = card.querySelector('.travel-card-desc-data');
        const descHtml = descEl ? descEl.innerHTML.trim() : '';

        // Modal Elements
        const pmName = document.getElementById('pmProductName');
        const pmCat = document.getElementById('pmProductCategory');
        const pmPrice = document.getElementById('pmProductPrice');
        const pmStock = document.getElementById('pmProductStock');
        const pmImg = document.getElementById('pmProductImage');
        const pmDesc = document.getElementById('pmProductDesc');

        if (pmName) pmName.textContent = name;
        if (pmCat) pmCat.textContent = category;
        if (pmPrice) pmPrice.textContent = price;
        if (pmStock) pmStock.textContent = stock;
        if (pmImg) {
            pmImg.src = image;
            pmImg.alt = name;
        }
        if (pmDesc) {
            pmDesc.innerHTML = descHtml || '<p style="color:#94a3b8;font-style:italic;">No description available.</p>';
        }

        // Specs
        const pmMat = document.getElementById('pmProductMaterial');
        const pmMatBox = document.getElementById('pmMaterialItem');
        if (pmMat && pmMatBox) {
            if (material) {
                pmMat.textContent = material;
                pmMatBox.style.display = 'flex';
            } else {
                pmMatBox.style.display = 'none';
            }
        }

        const pmAdv = document.getElementById('pmProductAdvantages');
        const pmAdvBox = document.getElementById('pmAdvantagesItem');
        if (pmAdv && pmAdvBox) {
            if (advantages) {
                pmAdv.textContent = advantages;
                pmAdvBox.style.display = 'flex';
            } else {
                pmAdvBox.style.display = 'none';
            }
        }

        const pmApp = document.getElementById('pmProductApplication');
        const pmAppBox = document.getElementById('pmApplicationItem');
        if (pmApp && pmAppBox) {
            if (application) {
                pmApp.textContent = application;
                pmAppBox.style.display = 'flex';
            } else {
                pmAppBox.style.display = 'none';
            }
        }

        // WhatsApp direct button
        const pmBtnWa = document.getElementById('pmBtnWa');
        if (pmBtnWa) {
            const phone = '6281333775663';
            const msg = encodeURIComponent(`Hello X BAR, I am interested in ordering ${name} (${price}). Please provide more details on product specifications and shipping terms.`);
            pmBtnWa.href = `https://wa.me/${phone}?text=${msg}`;
        }

        // Quotation button inside modal
        const pmBtnQuote = document.getElementById('pmBtnQuote');
        if (pmBtnQuote) {
            pmBtnQuote.onclick = () => {
                closeProductDetailModal();
                const quoteProduct = document.getElementById('quoteProduct');
                if (quoteProduct && name) {
                    quoteProduct.value = name;
                }
                const quoteModal = document.getElementById('quotationModal');
                if (quoteModal) {
                    quoteModal.classList.add('active');
                }
            };
        }

        // Show Modal
        productDetailModal.classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    function closeProductDetailModal() {
        if (!productDetailModal) return;
        productDetailModal.classList.remove('active');
        document.body.style.overflow = '';
    }

    // Attach card click handlers
    productCards.forEach(card => {
        card.addEventListener('click', (e) => {
            if (e.target.closest('.travel-bookmark-btn')) {
                e.stopPropagation();
                const btn = e.target.closest('.travel-bookmark-btn');
                btn.classList.toggle('is-bookmarked');
                return;
            }
            openProductDetailModal(card);
        });

        card.addEventListener('keydown', (e) => {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                openProductDetailModal(card);
            }
        });
    });

    if (productDetailCloseBtn) {
        productDetailCloseBtn.addEventListener('click', closeProductDetailModal);
    }

    if (productDetailModal) {
        productDetailModal.addEventListener('click', (e) => {
            if (e.target === productDetailModal) {
                closeProductDetailModal();
            }
        });

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && productDetailModal.classList.contains('active')) {
                closeProductDetailModal();
            }
        });
    }

    // Live search input on landing page products
    const productSearchInput = document.getElementById('landingProductSearch') || document.getElementById('productSearchInput') || document.querySelector('.spec-search-input-wrap input');
    const landingSearchClear = document.getElementById('landingSearchClear');

    if (productSearchInput) {
        productSearchInput.addEventListener('input', (e) => {
            const val = e.target.value.toLowerCase().trim();
            if (landingSearchClear) {
                landingSearchClear.style.display = val ? 'flex' : 'none';
            }
            let visibleCount = 0;
            document.querySelectorAll('.products-travel-grid .product-travel-card').forEach(c => {
                const name = (c.dataset.name || '').toLowerCase();
                const cat = (c.dataset.category || '').toLowerCase();
                const desc = (c.querySelector('.travel-card-desc-data')?.textContent || '').toLowerCase();
                if (name.includes(val) || cat.includes(val) || desc.includes(val)) {
                    c.style.display = '';
                    visibleCount++;
                } else {
                    c.style.display = 'none';
                }
            });
            const emptyMsg = document.getElementById('productSearchEmptyMsg');
            if (emptyMsg) {
                emptyMsg.style.display = (visibleCount === 0) ? 'block' : 'none';
            }
        });

        if (landingSearchClear) {
            landingSearchClear.addEventListener('click', () => {
                productSearchInput.value = '';
                productSearchInput.dispatchEvent(new Event('input'));
                productSearchInput.focus();
            });
        }
    }

    // Auto open modal if URL has query param (e.g. ?modal=3) or hash (e.g. #prod-3)
    const urlParams = new URLSearchParams(window.location.search);
    const modalParam = urlParams.get('modal');
    if (modalParam) {
        const targetCard = document.getElementById(`prod-${modalParam}`) || document.getElementById(`prod-home-${modalParam}`);
        if (targetCard) {
            openProductDetailModal(targetCard);
        }
    } else if (window.location.hash) {
        const targetCard = document.querySelector(window.location.hash);
        if (targetCard && targetCard.classList.contains('product-travel-card')) {
            openProductDetailModal(targetCard);
        }
    }

    // ----------------------------------------------------
    // Track visitor clicks on CTA / WhatsApp / Contact
    // ----------------------------------------------------
    document.addEventListener('click', (e) => {
        const btn = e.target.closest('a, button');
        if (!btn) return;
        const href = btn.getAttribute('href') || '';
        const text = (btn.innerText || btn.getAttribute('title') || '').trim().substring(0, 30);
        
        let eventType = null;
        if (href.includes('wa.me') || href.includes('whatsapp') || btn.classList.contains('btn-wa-order')) {
            eventType = 'whatsapp_' + text;
        } else if (btn.classList.contains('btn-open-quote')) {
            eventType = 'open_quotation';
        } else if (btn.classList.contains('btn-glass')) {
            eventType = 'cta_' + text;
        }

        if (eventType) {
            try {
                if (navigator.sendBeacon) {
                    navigator.sendBeacon('track_click.php', new URLSearchParams({ event: eventType }));
                } else {
                    fetch('track_click.php', { method: 'POST', body: new URLSearchParams({ event: eventType }) });
                }
            } catch (err) {}
        }
    });
});

