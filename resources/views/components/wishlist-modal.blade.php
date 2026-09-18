{{-- Wishlist Modal --}}
<div id="wishlistModal" class="wl-modal-overlay" onclick="closeWishlistModal(event)">
  <div class="wl-modal-box">

    <div class="wl-modal-header">
      <div class="wl-modal-title">
        <i class="far fa-heart"></i>
        <span>My Wishlist</span>
        <span class="wl-modal-count-badge" id="wlModalCountBadge">0</span>
      </div>
      <button type="button" class="wl-modal-close" onclick="closeWishlistModal()" aria-label="Close">
        <i class="fas fa-times"></i>
      </button>
    </div>

    <div class="wl-modal-body" id="wlModalBody">
      <div class="wl-loading">
        <div class="wl-spinner"></div>
        <p>Loading wishlist...</p>
      </div>
    </div>

  </div>
</div>

<style>
/* ===================== WISHLIST MODAL ===================== */
.wl-modal-overlay {
  position: fixed;
  inset: 0;
  background: rgba(0,0,0,0.45);
  z-index: 9999;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 16px;
  opacity: 0;
  pointer-events: none;
  transition: opacity 0.25s ease;
}
.wl-modal-overlay.open {
  opacity: 1;
  pointer-events: all;
}

.wl-modal-box {
  background: var(--section-bg);
  border: 1px solid var(--border-color);
  border-radius: 16px;
  width: 100%;
  max-width: 860px;
  max-height: 88vh;
  display: flex;
  flex-direction: column;
  box-shadow: 0 20px 60px rgba(0,0,0,0.15);
  transform: translateY(16px);
  transition: transform 0.25s ease;
  overflow: hidden;
}
.wl-modal-overlay.open .wl-modal-box {
  transform: translateY(0);
}

.wl-modal-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 16px 20px;
  border-bottom: 1px solid var(--border-color);
  flex-shrink: 0;
}
.wl-modal-title {
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 16px;
  font-weight: 700;
  color: var(--text-main);
}
.wl-modal-title i {
  color: var(--danger, #e11d48);
  font-size: 17px;
}
.wl-modal-count-badge {
  background: var(--primary);
  color: #fff;
  font-size: 11px;
  font-weight: 700;
  border-radius: 20px;
  padding: 1px 8px;
  min-width: 22px;
  text-align: center;
}
.wl-modal-close {
  background: none;
  border: none;
  cursor: pointer;
  color: var(--text-muted);
  font-size: 17px;
  padding: 4px 8px;
  border-radius: 6px;
  transition: color .2s, background .2s;
}
.wl-modal-close:hover {
  color: var(--text-main);
  background: var(--primary-soft);
}

.wl-modal-body {
  overflow-y: auto;
  padding: 20px;
  flex: 1;
}

/* Loading spinner */
.wl-loading {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 12px;
  padding: 40px 0;
  color: var(--text-muted);
  font-size: 14px;
}
.wl-spinner {
  width: 32px;
  height: 32px;
  border: 3px solid var(--border-color);
  border-top-color: var(--primary);
  border-radius: 50%;
  animation: wl-spin .7s linear infinite;
}
@keyframes wl-spin { to { transform: rotate(360deg); } }

/* Empty state */
.wishlist-empty {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 10px;
  padding: 48px 0;
  color: var(--text-muted);
}
.wishlist-empty i {
  font-size: 42px;
  opacity: .4;
}
.wishlist-empty p {
  font-size: 15px;
  margin: 0;
}

/* Grid of wishlist cards */
#wlModalBody .wl-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
  gap: 14px;
}

/* Wishlist card */
.wl-card {
  background: var(--body-bg, #f8f9fa);
  border: 1px solid var(--border-color);
  border-radius: 12px;
  overflow: hidden;
  display: flex;
  flex-direction: column;
  transition: box-shadow .2s, transform .2s;
}
.wl-card:hover {
  transform: translateY(-3px);
  box-shadow: 0 8px 24px rgba(0,0,0,0.07);
}

.wl-card-img {
  position: relative;
  background: var(--section-bg);
  aspect-ratio: 1;
  overflow: hidden;
}
.wl-card-img img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  display: block;
  transition: transform .3s ease;
}
.wl-card:hover .wl-card-img img {
  transform: scale(1.04);
}
.wl-card-no-img {
  width: 100%;
  height: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 36px;
  color: var(--text-muted);
  background: var(--primary-soft);
}

.wl-badge-offer {
  position: absolute;
  top: 7px;
  left: 7px;
  background: var(--accent, #9000dc);
  color: #fff;
  font-size: 10px;
  font-weight: 700;
  padding: 2px 7px;
  border-radius: 20px;
}
.wl-badge-new {
  position: absolute;
  top: 7px;
  left: 7px;
  background: var(--primary);
  color: #fff;
  font-size: 10px;
  font-weight: 700;
  padding: 2px 7px;
  border-radius: 20px;
}

.wl-card-body {
  padding: 10px 12px 12px;
  display: flex;
  flex-direction: column;
  gap: 6px;
  flex: 1;
}
.wl-card-name {
  font-size: 13px;
  font-weight: 600;
  color: var(--text-main);
  margin: 0;
  line-height: 1.35;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
.wl-price-row {
  display: flex;
  align-items: center;
  gap: 6px;
  flex-wrap: wrap;
}
.wl-price {
  font-size: 14px;
  font-weight: 700;
  color: var(--primary);
}
.wl-old-price {
  font-size: 11.5px;
  color: var(--text-muted);
}
.wl-stock {
  font-size: 11px;
  font-weight: 500;
  color: #059669;
  display: flex;
  align-items: center;
  gap: 4px;
}
.wl-stock.out-of-stock {
  color: var(--danger, #e11d48);
}

.wl-card-actions {
  display: flex;
  gap: 6px;
  margin-top: auto;
}
.wl-add-cart-btn {
  flex: 1;
  background: var(--primary);
  color: #fff;
  border: none;
  border-radius: 7px;
  padding: 6px 10px;
  font-size: 11.5px;
  font-weight: 600;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 5px;
  transition: opacity .2s;
}
.wl-add-cart-btn:hover {
  opacity: .85;
}
.wl-remove-btn {
  background: none;
  border: 1px solid var(--border-color);
  color: var(--text-muted);
  border-radius: 7px;
  padding: 6px 9px;
  font-size: 12px;
  cursor: pointer;
  transition: color .2s, border-color .2s, background .2s;
  display: flex;
  align-items: center;
}
.wl-remove-btn:hover {
  color: var(--danger, #e11d48);
  border-color: var(--danger, #e11d48);
  background: rgba(225,29,72,.06);
}

@media (max-width: 576px) {
  .wl-modal-box {
    max-height: 92vh;
    border-radius: 14px;
  }
  #wlModalBody .wl-grid {
    grid-template-columns: repeat(2, 1fr);
    gap: 10px;
  }
}
</style>
