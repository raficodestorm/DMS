// --------------------- Scripts for Navbar & Theme ---------------------

// Sidebar Menu Toggle
function toggleSidebar() {
    const sidebar = document.getElementById('mobileSidebar');
    const overlay = document.getElementById('sidebarOverlay');
    if (sidebar) sidebar.classList.toggle('active');
    if (overlay) overlay.classList.toggle('active');
}

// Mobile Search Bar Drawer Toggle
function toggleMobileSearch() {
    const searchDrawer = document.getElementById('mobileSearchDrawer');
    if (searchDrawer) {
        searchDrawer.classList.toggle('active');
        if (searchDrawer.classList.contains('active')) {
            const input = searchDrawer.querySelector('input');
            if (input) setTimeout(() => input.focus(), 100);
        }
    }
}

// Mobile Category Accordion Toggle
function toggleMobileCategory() {
    const accordion = document.getElementById('mobileCatAccordion');
    if (accordion) {
        accordion.classList.toggle('active');
    }
}

// Desktop & Mobile User Profile Dropdown Click Toggle
function toggleMobileUserDropdown(e) {
    if (e) e.stopPropagation();
    const wrap = document.getElementById('mobileUserDropdownWrap');
    if (wrap) wrap.classList.toggle('active');
}

// Desktop Category & User Dropdown Click Toggle
document.addEventListener('DOMContentLoaded', () => {
    const browseBtn = document.getElementById('browseCatBtn');
    const dropdownWrap = document.getElementById('categoriesDropdownWrap');
    if (browseBtn && dropdownWrap) {
        browseBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            dropdownWrap.classList.toggle('active');
        });

        document.addEventListener('click', (e) => {
            if (!dropdownWrap.contains(e.target)) {
                dropdownWrap.classList.remove('active');
            }
        });
    }

    // User Profile Dropdown Click Toggle (Desktop & Mobile)
    const userDropdownWrap = document.getElementById('headerUserDropdownWrap');
    const userBtn = document.getElementById('headerUserBtn');
    if (userBtn && userDropdownWrap) {
        userBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            userDropdownWrap.classList.toggle('active');
        });
    }

    // Global click listener to close dropdowns when clicking outside
    document.addEventListener('click', (e) => {
        if (userDropdownWrap && !userDropdownWrap.contains(e.target)) {
            userDropdownWrap.classList.remove('active');
        }
        const mobileWrap = document.getElementById('mobileUserDropdownWrap');
        if (mobileWrap && !mobileWrap.contains(e.target)) {
            mobileWrap.classList.remove('active');
        }
    });

    updateThemeIcons();

    // Initialize wishlist badge count on page load
    loadWishlistCount();
});

// Theme Switcher (Dark / Light Mode)
function toggleTheme() {
    const html = document.documentElement;
    const isDark = html.getAttribute("data-theme") === "dark";

    if (isDark) {
        html.removeAttribute("data-theme");
        localStorage.setItem("theme", "light");
    } else {
        html.setAttribute("data-theme", "dark");
        localStorage.setItem("theme", "dark");
    }
    updateThemeIcons();
}

function updateThemeIcons() {
    const isDark = document.documentElement.getAttribute("data-theme") === "dark";
    document.querySelectorAll(".theme-toggle-icon").forEach(icon => {
        if (isDark) {
            icon.classList.remove("fa-moon");
            icon.classList.add("fa-sun");
        } else {
            icon.classList.remove("fa-sun");
            icon.classList.add("fa-moon");
        }
    });
}


// ===================== WISHLIST =====================

/**
 * Update all wishlist badge elements across navbar (desktop + mobile)
 */
function updateWishlistBadges(count) {
    document.querySelectorAll('.wishlist-count-badge').forEach(badge => {
        if (count > 0) {
            badge.textContent = count > 99 ? '99+' : count;
            badge.style.display = '';
        } else {
            badge.style.display = 'none';
        }
    });
    // Update the text label on desktop pill
    document.querySelectorAll('.wishlist-total-text').forEach(el => {
        el.textContent = count > 0 ? count + (count === 1 ? ' Item' : ' Items') : 'Items';
    });
}

/**
 * Load & display wishlist count (runs on page load)
 */
function loadWishlistCount() {
    fetch('/wishlist/count', {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(r => r.json())
    .then(data => {
        updateWishlistBadges(data.count || 0);
    })
    .catch(() => {});
}

/**
 * Toggle wishlist for a product from a product card heart button
 */
function toggleWishlist(btn, event) {
    if (event) event.preventDefault();

    const productId = btn.dataset.productId;
    if (!productId) return;

    const csrfToken = document.querySelector('meta[name="csrf-token"]')
        ? document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        : '';

    fetch('/wishlist/toggle', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'X-Requested-With': 'XMLHttpRequest',
        },
        body: JSON.stringify({ product_id: productId }),
    })
    .then(r => r.json())
    .then(data => {
        if (!data.success) return;

        const icon = btn.querySelector('i');
        if (data.in_wishlist) {
            btn.classList.add('wishlisted');
            btn.title = 'Remove from Wishlist';
            if (icon) { icon.classList.remove('far'); icon.classList.add('fas'); }
        } else {
            btn.classList.remove('wishlisted');
            btn.title = 'Add to Wishlist';
            if (icon) { icon.classList.remove('fas'); icon.classList.add('far'); }
        }

        updateWishlistBadges(data.count || 0);

        // Brief toast notification
        showWishlistToast(data.message || '', data.in_wishlist);
    })
    .catch(() => {});
}

/**
 * Open the wishlist modal and load items
 */
function openWishlistModal() {
    const overlay = document.getElementById('wishlistModal');
    const body    = document.getElementById('wlModalBody');
    if (!overlay) return;

    overlay.classList.add('open');
    document.body.style.overflow = 'hidden';

    // Show loading
    body.innerHTML = '<div class="wl-loading"><div class="wl-spinner"></div><p>Loading wishlist...</p></div>';

    fetch('/wishlist/items', {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(r => r.json())
    .then(data => {
        const count = data.count || 0;

        // Update modal badge
        const modalBadge = document.getElementById('wlModalCountBadge');
        if (modalBadge) modalBadge.textContent = count;

        updateWishlistBadges(count);

        if (count === 0 || !data.html) {
            body.innerHTML = '<div class="wishlist-empty"><i class="far fa-heart"></i><p>Your wishlist is empty.</p></div>';
        } else {
            body.innerHTML = '<div class="wl-grid">' + data.html + '</div>';
        }
    })
    .catch(() => {
        body.innerHTML = '<div class="wishlist-empty"><i class="fas fa-exclamation-circle"></i><p>Failed to load wishlist.</p></div>';
    });
}

/**
 * Close the wishlist modal
 */
function closeWishlistModal(event) {
    if (event && event.target !== document.getElementById('wishlistModal')) return;
    const overlay = document.getElementById('wishlistModal');
    if (overlay) overlay.classList.remove('open');
    document.body.style.overflow = '';
}

/**
 * Remove a single item from the wishlist (called from modal card)
 */
function removeFromWishlist(productId) {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')
        ? document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        : '';

    fetch('/wishlist/toggle', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'X-Requested-With': 'XMLHttpRequest',
        },
        body: JSON.stringify({ product_id: productId }),
    })
    .then(r => r.json())
    .then(data => {
        if (!data.success) return;

        // Remove card from modal DOM
        const card = document.querySelector('.wl-card[data-product-id="' + productId + '"]');
        if (card) {
            card.style.transition = 'opacity .25s ease, transform .25s ease';
            card.style.opacity = '0';
            card.style.transform = 'scale(0.92)';
            setTimeout(() => {
                card.remove();
                // Check if grid is now empty
                const grid = document.querySelector('#wlModalBody .wl-grid');
                if (grid && grid.children.length === 0) {
                    document.getElementById('wlModalBody').innerHTML =
                        '<div class="wishlist-empty"><i class="far fa-heart"></i><p>Your wishlist is empty.</p></div>';
                }
            }, 260);
        }

        updateWishlistBadges(data.count || 0);

        // Update count badge in modal header
        const modalBadge = document.getElementById('wlModalCountBadge');
        if (modalBadge) modalBadge.textContent = data.count || 0;

        // Also update heart button on the product cards in the background page (if visible)
        const heartBtn = document.querySelector('.product-btn-wishlist[data-product-id="' + productId + '"]');
        if (heartBtn) {
            heartBtn.classList.remove('wishlisted');
            heartBtn.title = 'Add to Wishlist';
            const icon = heartBtn.querySelector('i');
            if (icon) { icon.classList.remove('fas'); icon.classList.add('far'); }
        }
    })
    .catch(() => {});
}

/**
 * Simple toast notification for wishlist actions
 */
function showWishlistToast(message, isAdd) {
    const existing = document.getElementById('wlToast');
    if (existing) existing.remove();

    const toast = document.createElement('div');
    toast.id = 'wlToast';
    toast.style.cssText = [
        'position:fixed',
        'bottom:24px',
        'right:20px',
        'background:' + (isAdd ? 'var(--primary)' : '#64748b'),
        'color:#fff',
        'padding:10px 18px',
        'border-radius:8px',
        'font-size:13.5px',
        'font-weight:600',
        'z-index:99999',
        'box-shadow:0 4px 18px rgba(0,0,0,.2)',
        'display:flex',
        'align-items:center',
        'gap:8px',
        'transition:opacity .3s ease',
        'opacity:0',
    ].join(';');
    toast.innerHTML = '<i class="' + (isAdd ? 'fas fa-heart' : 'far fa-heart') + '"></i> ' + message;
    document.body.appendChild(toast);
    setTimeout(() => toast.style.opacity = '1', 10);
    setTimeout(() => {
        toast.style.opacity = '0';
        setTimeout(() => toast.remove(), 300);
    }, 2500);
}