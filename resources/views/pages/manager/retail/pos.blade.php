@extends('layouts.managerlayout')

@section('content')
<style>
  /* ── POS Overall Container Layout ───────────────────────────── */
  .pos-container {
    display: flex;
    gap: 16px;
    height: calc(100vh - 110px);
    min-height: 580px;
    margin-top: 5px;
    font-family: 'Segoe UI', -apple-system, BlinkMacSystemFont, Roboto, sans-serif;
  }

  /* ── Left Side: Product Catalog ────────────────────────────── */
  .pos-catalog-panel {
    flex: 1;
    display: flex;
    flex-direction: column;
    background: var(--section-bg);
    border: 1px solid var(--border-color);
    border-radius: 14px;
    padding: 14px;
    overflow: hidden;
    box-shadow: 0 4px 20px var(--glass);
  }

  .pos-catalog-header {
    display: flex;
    flex-direction: column;
    gap: 10px;
    margin-bottom: 12px;
    flex-shrink: 0;
  }

  .pos-search-row {
    display: flex;
    align-items: center;
    gap: 10px;
  }

  .pos-search-box {
    position: relative;
    flex: 1;
  }

  .pos-search-box i.search-icon {
    position: absolute;
    left: 14px;
    top: 50%;
    transform: translateY(-50%);
    color: var(--text-muted);
    font-size: 14px;
  }

  .pos-search-input {
    width: 100%;
    padding: 10px 36px 10px 38px;
    border-radius: 10px;
    border: 1.5px solid var(--border-color);
    background: var(--background);
    color: var(--text-main);
    font-size: 13.5px;
    font-weight: 500;
    transition: all 0.2s ease;
    outline: none;
  }

  .pos-search-input:focus {
    border-color: var(--primary);
    box-shadow: 0 0 0 3px var(--primary-soft);
    background: var(--section-bg);
  }

  .pos-search-clear {
    position: absolute;
    right: 10px;
    top: 50%;
    transform: translateY(-50%);
    background: none;
    border: none;
    color: var(--text-muted);
    font-size: 13px;
    cursor: pointer;
    display: none;
  }

  .pos-categories-pills {
    display: flex;
    gap: 6px;
    overflow-x: auto;
    padding-bottom: 4px;
    scrollbar-width: none;
  }

  .pos-categories-pills::-webkit-scrollbar {
    display: none;
  }

  .pos-cat-pill {
    padding: 5px 12px;
    border-radius: 20px;
    background: var(--background);
    color: var(--text-muted);
    font-size: 12px;
    font-weight: 600;
    white-space: nowrap;
    cursor: pointer;
    border: 1px solid var(--border-color);
    transition: all 0.2s ease;
    user-select: none;
  }

  .pos-cat-pill:hover,
  .pos-cat-pill.active {
    background: var(--primary);
    color: #ffffff;
    border-color: var(--primary);
    box-shadow: 0 2px 8px var(--primary-soft);
  }

  /* ── Product Grid ─────────────────────────────────────────── */
  .pos-product-grid {
    flex: 1;
    overflow-y: auto;
    padding-right: 4px;
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(175px, 1fr));
    gap: 12px;
    align-content: start;
  }

  .pos-product-grid::-webkit-scrollbar {
    width: 5px;
  }

  .pos-product-grid::-webkit-scrollbar-thumb {
    background: var(--border-color);
    border-radius: 10px;
  }

  /* ── Compact Product Card (Welcome Style, POS Optimized) ──── */
  .pos-prod-card {
    background: var(--section-bg);
    border: 1px solid var(--border-color);
    border-radius: 12px;
    padding: 10px;
    display: flex;
    flex-direction: column;
    cursor: pointer;
    position: relative;
    user-select: none;
    transition: transform 0.22s ease, box-shadow 0.22s ease, border-color 0.22s ease;
  }

  .pos-prod-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 24px rgba(0, 0, 0, 0.08);
    border-color: var(--primary);
  }

  .pos-prod-card:active {
    transform: scale(0.98);
  }

  .pos-prod-card.in-cart {
    border-color: var(--primary);
    background: var(--primary-soft);
  }

  .pos-prod-card.out-of-stock {
    opacity: 0.55;
    cursor: not-allowed;
    pointer-events: none;
  }

  .pos-prod-badge-wrap {
    position: absolute;
    top: 8px;
    left: 8px;
    right: 8px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    z-index: 2;
    pointer-events: none;
  }

  .pos-badge-normal {
    width: 22px;
    height: 22px;
    border-radius: 50%;
    border: 1px solid var(--border-color);
    background: var(--section-bg);
    color: var(--text-muted);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 11px;
    font-weight: 700;
  }

  .pos-badge-deduct {
    font-size: 9.5px;
    font-weight: 700;
    padding: 2px 6px;
    border-radius: 4px;
    background: var(--background);
    color: var(--text-main);
    border: 1px solid var(--border-color);
  }

  @keyframes offerBadgeGlow {
    0%, 100% { transform: translateY(0) scale(1); box-shadow: 0 3px 10px rgba(225, 29, 72, 0.22); }
    50% { transform: translateY(-1.5px) scale(1.025); box-shadow: 0 5px 16px rgba(225, 29, 72, 0.35); }
  }

  @keyframes badgeShimmer {
    0% { left: -120%; }
    35%, 100% { left: 150%; }
  }

  .product-badge-offer {
    position: relative;
    overflow: hidden;
    background: linear-gradient(135deg, #e11d48 0%, #f8640e 55%, #f9da52 100%);
    color: #ffffff;
    font-size: 9.5px;
    font-weight: 800;
    padding: 2.5px 7px;
    border-radius: 20px;
    line-height: 1.25;
    display: inline-flex;
    align-items: center;
    gap: 3px;
    letter-spacing: 0.3px;
    text-transform: uppercase;
    box-shadow: 0 2px 8px rgba(225, 29, 72, 0.25);
    border: 1px solid rgba(255, 255, 255, 0.4);
    animation: offerBadgeGlow 3s infinite ease-in-out;
  }

  .product-badge-offer::after {
    content: '';
    position: absolute;
    top: -50%;
    left: -120%;
    width: 50%;
    height: 200%;
    background: linear-gradient(90deg, transparent 0%, rgba(255, 255, 255, 0.5) 50%, transparent 100%);
    transform: rotate(25deg);
    animation: badgeShimmer 3.2s infinite cubic-bezier(0.4, 0, 0.2, 1);
    pointer-events: none;
  }

  .pos-cart-qty-indicator {
    background: var(--primary);
    color: #fff;
    font-size: 10px;
    font-weight: 800;
    min-width: 20px;
    height: 20px;
    border-radius: 10px;
    padding: 0 5px;
    display: none;
    align-items: center;
    justify-content: center;
    box-shadow: 0 2px 6px var(--primary-soft);
    margin-left: auto;
  }

  .pos-prod-card.in-cart .pos-cart-qty-indicator {
    display: inline-flex;
  }

  .pos-img-container {
    width: 100%;
    height: 110px;
    border-radius: 8px;
    overflow: hidden;
    background: transparent;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 6px;
    padding-top: 10px;
  }

  .pos-img-container img {
    max-width: 100%;
    max-height: 100%;
    object-fit: contain;
    transition: transform 0.25s ease;
  }

  .pos-prod-card:hover .pos-img-container img {
    transform: scale(1.06);
  }

  .pos-prod-body {
    display: flex;
    flex-direction: column;
    flex: 1;
  }

  .pos-prod-title {
    font-size: 12.5px;
    font-weight: 600;
    color: var(--text-main);
    line-height: 1.35;
    margin-bottom: 5px;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    min-height: 34px;
    transition: color 0.2s ease;
  }

  .pos-prod-card:hover .pos-prod-title {
    color: var(--primary);
  }

  .pos-price-group {
    display: flex;
    align-items: baseline;
    gap: 6px;
    margin-bottom: 5px;
    flex-wrap: wrap;
  }

  .pos-selling-rate {
    font-size: 14.5px;
    font-weight: 800;
    color: var(--text-main);
  }

  .pos-base-rate {
    font-size: 11px;
    font-weight: 500;
    color: var(--text-muted);
    text-decoration: line-through;
    opacity: 0.8;
  }

  .pos-card-stock {
    font-size: 10.5px;
    font-weight: 600;
    color: #059669;
    margin-bottom: 8px;
    display: flex;
    align-items: center;
    gap: 4px;
  }

  .btn-card-cart {
    margin-top: auto;
    background: var(--primary-soft);
    color: var(--primary);
    border: 1px dashed var(--primary);
    border-radius: 8px;
    padding: 6px 10px;
    font-size: 12px;
    font-weight: 700;
    width: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    cursor: pointer;
    transition: all 0.2s ease;
  }

  .btn-card-cart:hover {
    background: var(--primary);
    color: #ffffff;
    border-style: solid;
    box-shadow: 0 4px 12px var(--primary-soft);
    transform: translateY(-1px);
  }

  .btn-card-cart:active {
    transform: scale(0.97);
  }

  /* ── Product Details Modal Overlay (product/show.blade.php Style) ── */
  .pos-prod-modal-overlay {
    display: none;
    position: fixed;
    inset: 0;
    background: rgba(0, 0, 0, 0.55);
    backdrop-filter: blur(4px);
    z-index: 1070;
    align-items: center;
    justify-content: center;
    padding: 16px;
  }

  .pos-prod-modal-overlay.open {
    display: flex;
  }

  .pos-prod-modal {
    background: var(--section-bg);
    border: 1px solid var(--border-color);
    border-radius: 18px;
    width: 880px;
    max-width: 95vw;
    max-height: 90vh;
    display: flex;
    flex-direction: column;
    overflow: hidden;
    box-shadow: 0 24px 64px rgba(0,0,0,0.35);
    animation: modalPop 0.22s cubic-bezier(0.4, 0, 0.2, 1);
  }

  @keyframes modalPop {
    from { opacity: 0; transform: scale(0.95); }
    to   { opacity: 1; transform: scale(1); }
  }

  .pos-prod-modal-header {
    padding: 14px 20px;
    border-bottom: 1px solid var(--border-color);
    display: flex;
    align-items: center;
    justify-content: space-between;
    background: var(--section-bg);
    flex-shrink: 0;
  }

  .pos-prod-modal-title {
    font-size: 13.5px;
    font-weight: 700;
    color: var(--text-muted);
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 8px;
  }

  .pos-prod-modal-body {
    padding: 22px;
    overflow-y: auto;
    display: grid;
    grid-template-columns: 5fr 6fr;
    gap: 24px;
    align-items: start;
  }

  .pos-prod-modal-body::-webkit-scrollbar {
    width: 5px;
  }

  .pos-prod-modal-body::-webkit-scrollbar-thumb {
    background: var(--border-color);
    border-radius: 10px;
  }

  @media (max-width: 768px) {
    .pos-prod-modal-body {
      grid-template-columns: 1fr;
      gap: 16px;
      padding: 16px;
    }
  }

  /* Gallery inside modal */
  .pd-gallery {
    display: flex;
    flex-direction: column;
    gap: 10px;
  }

  .pd-main-img-wrap {
    background: var(--background);
    border: 1px solid var(--border-color);
    border-radius: 14px;
    overflow: hidden;
    aspect-ratio: 1;
    max-height: 320px;
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
    padding: 14px;
  }

  .pd-main-img-wrap img {
    max-width: 100%;
    max-height: 100%;
    object-fit: contain;
    transition: transform 0.3s ease;
  }

  .pd-main-img-wrap:hover img {
    transform: scale(1.05);
  }

  .pd-thumbs {
    display: flex;
    gap: 8px;
    overflow-x: auto;
    padding-bottom: 4px;
  }

  .pd-thumb {
    width: 54px;
    height: 54px;
    border-radius: 8px;
    overflow: hidden;
    border: 2px solid var(--border-color);
    cursor: pointer;
    background: var(--background);
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    transition: all 0.2s ease;
    padding: 4px;
  }

  .pd-thumb img {
    max-width: 100%;
    max-height: 100%;
    object-fit: contain;
  }

  .pd-thumb:hover, .pd-thumb.active {
    border-color: var(--primary);
    transform: translateY(-2px);
  }

  /* Info inside modal */
  .pd-info {
    display: flex;
    flex-direction: column;
    gap: 12px;
  }

  .pd-category-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: var(--primary-soft);
    color: var(--primary);
    border: 1px solid rgba(2, 2, 226, 0.15);
    font-size: 11px;
    font-weight: 700;
    padding: 3px 10px;
    border-radius: 20px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    width: fit-content;
  }

  .pd-name {
    font-size: 20px;
    font-weight: 700;
    color: var(--text-main);
    line-height: 1.3;
    margin: 0;
  }

  .pd-meta-row {
    display: flex;
    flex-wrap: wrap;
    gap: 12px;
    align-items: center;
    font-size: 12px;
    color: var(--text-muted);
  }

  .pd-meta-item strong {
    color: var(--text-main);
  }

  .pd-stock-pill {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: 11.5px;
    font-weight: 700;
    padding: 3px 10px;
    border-radius: 20px;
    width: fit-content;
  }

  .pd-stock-pill.in-stock {
    background: rgba(5, 150, 105, 0.1);
    color: #059669;
    border: 1px solid rgba(5, 150, 105, 0.25);
  }

  .pd-price-box {
    background: var(--background);
    border: 1px solid var(--border-color);
    border-radius: 12px;
    padding: 14px 16px;
  }

  .pd-price-label {
    font-size: 11px;
    font-weight: 600;
    color: var(--text-muted);
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 4px;
  }

  .pd-price-row {
    display: flex;
    align-items: baseline;
    gap: 8px;
    flex-wrap: wrap;
    margin-bottom: 6px;
  }

  .pd-price-main {
    font-size: 28px;
    font-weight: 800;
    color: var(--primary);
    line-height: 1;
  }

  .pd-price-original {
    font-size: 15px;
    color: var(--text-muted);
    text-decoration: line-through;
    font-weight: 500;
  }

  .pd-savings-pill {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    background: rgba(225, 29, 72, 0.1);
    color: #e11d48;
    border: 1px solid rgba(225, 29, 72, 0.2);
    font-size: 11px;
    font-weight: 700;
    padding: 2px 8px;
    border-radius: 20px;
  }

  .pd-offer-badge {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    background: linear-gradient(135deg, #e11d48, #f8640e);
    color: #fff;
    font-size: 11px;
    font-weight: 800;
    padding: 3px 10px;
    border-radius: 20px;
    margin-top: 4px;
    width: fit-content;
  }

  .pd-desc-box {
    background: var(--background);
    border: 1px solid var(--border-color);
    border-radius: 10px;
    padding: 12px 14px;
    max-height: 140px;
    overflow-y: auto;
  }

  .pd-desc-title {
    font-size: 12.5px;
    font-weight: 700;
    color: var(--text-main);
    margin-bottom: 6px;
    display: flex;
    align-items: center;
    gap: 6px;
  }

  .pd-desc-text {
    font-size: 12.5px;
    color: var(--text-muted);
    line-height: 1.55;
    margin: 0;
    white-space: pre-wrap;
  }

  .pd-actions {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-top: 6px;
  }

  .pd-qty-group {
    display: flex;
    align-items: center;
    gap: 4px;
  }

  .pd-btn-cart {
    flex: 1;
    background: linear-gradient(135deg, var(--primary), var(--secondary));
    color: #fff;
    border: none;
    border-radius: 10px;
    padding: 10px 18px;
    font-size: 13.5px;
    font-weight: 700;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    box-shadow: 0 4px 14px var(--primary-soft);
    transition: all 0.2s ease;
  }

  .pd-btn-cart:hover {
    opacity: 0.92;
    transform: translateY(-1px);
    box-shadow: 0 6px 18px var(--primary-soft);
  }

  /* ── Cart Modal Overlay ─────────────────────────────────────── */
  .pos-cart-modal-overlay {
    display: none;
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,0.45);
    z-index: 1050;
    align-items: flex-end;
    justify-content: flex-end;
    padding: 16px;
  }

  .pos-cart-modal-overlay.open {
    display: flex;
  }

  .pos-cart-panel {
    width: 420px;
    height: 680px;
    max-width: calc(100vw - 32px);
    max-height: calc(100vh - 50px);
    display: flex;
    flex-direction: column;
    background: var(--section-bg);
    border: 1px solid var(--border-color);
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 20px 60px rgba(0,0,0,0.3);
    animation: slideUpCart 0.25s cubic-bezier(0.4,0,0.2,1);
  }

  @keyframes slideUpCart {
    from { opacity: 0; transform: translateY(30px) scale(0.97); }
    to   { opacity: 1; transform: translateY(0)   scale(1); }
  }

  .pos-cart-header {
    padding: 12px 14px;
    background: var(--section-bg);
    border-bottom: 1px solid var(--border-color);
    flex-shrink: 0;
  }

  .pos-cart-title-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 10px;
  }

  .pos-cart-title {
    font-size: 15px;
    font-weight: 700;
    color: var(--primary);
    margin: 0;
    display: flex;
    align-items: center;
    gap: 7px;
  }

  .pos-cart-title-actions {
    display: flex;
    align-items: center;
    gap: 6px;
  }

  .pos-btn-clear-cart {
    background: transparent;
    border: none;
    color: #ef4444;
    font-size: 11.5px;
    font-weight: 600;
    cursor: pointer;
    padding: 3px 8px;
    border-radius: 6px;
    transition: background 0.15s ease;
  }

  .pos-btn-clear-cart:hover {
    background: rgba(239, 68, 68, 0.1);
  }

  .pos-cart-close-btn {
    background: var(--background);
    border: 1px solid var(--border-color);
    color: var(--text-muted);
    font-size: 14px;
    width: 28px;
    height: 28px;
    border-radius: 8px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.15s ease;
  }

  .pos-cart-close-btn:hover {
    background: var(--primary);
    color: #fff;
    border-color: var(--primary);
  }

  /* ── Floating Cart FAB ──────────────────────────────────────── */
  .pos-cart-fab {
    position: fixed;
    bottom: 28px;
    right: 28px;
    z-index: 1040;
    width: 58px;
    height: 58px;
    border-radius: 50%;
    background: var(--primary);
    color: #fff;
    border: none;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 22px;
    box-shadow: 0 6px 20px rgba(0,0,0,0.25);
    transition: all 0.2s ease;
  }

  .pos-cart-fab:hover {
    transform: scale(1.1);
    box-shadow: 0 8px 28px rgba(0,0,0,0.3);
  }

  .pos-cart-fab:active {
    transform: scale(0.95);
  }

  .pos-cart-fab.pulse {
    transform: scale(1.22);
    box-shadow: 0 0 24px var(--primary);
  }

  .pos-cart-fab-badge {
    position: absolute;
    top: -4px;
    right: -4px;
    background: #ef4444;
    color: #fff;
    font-size: 11px;
    font-weight: 800;
    min-width: 20px;
    height: 20px;
    border-radius: 10px;
    display: none;
    align-items: center;
    justify-content: center;
    padding: 0 4px;
    box-shadow: 0 2px 6px rgba(239,68,68,0.5);
    border: 2px solid var(--section-bg);
  }

  .pos-cart-fab-badge.has-items {
    display: flex;
  }

  /* Customer Information Box */
  .pos-cust-box {
    background: var(--background);
    border: 1px solid var(--border-color);
    border-radius: 10px;
    padding: 8px 10px;
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 8px;
  }

  .pos-cust-field {
    display: flex;
    flex-direction: column;
  }

  .pos-cust-field label {
    font-size: 10.5px;
    font-weight: 700;
    color: var(--text-muted);
    margin-bottom: 2px;
    text-transform: uppercase;
    letter-spacing: 0.3px;
  }

  .pos-cust-field input {
    border: 1px solid var(--border-color);
    background: var(--section-bg);
    color: var(--text-main);
    border-radius: 6px;
    padding: 5px 8px;
    font-size: 12px;
    outline: none;
    transition: border-color 0.2s ease;
  }

  .pos-cust-field input:focus {
    border-color: var(--primary);
  }

  /* ── Cart Items List ──────────────────────────────────────── */
  .pos-cart-body {
    flex: 1;
    overflow-y: auto;
    padding: 10px;
    display: flex;
    flex-direction: column;
    gap: 8px;
  }

  .pos-cart-body::-webkit-scrollbar {
    width: 5px;
  }

  .pos-cart-body::-webkit-scrollbar-thumb {
    background: var(--border-color);
    border-radius: 10px;
  }

  .pos-cart-empty-state {
    flex: 1;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    color: var(--text-muted);
    text-align: center;
    padding: 30px 15px;
  }

  .pos-cart-empty-state i {
    font-size: 42px;
    margin-bottom: 12px;
    opacity: 0.35;
    color: var(--primary);
  }

  .pos-cart-item {
    background: var(--background);
    border: 1px solid var(--border-color);
    border-radius: 8px;
    padding: 8px 10px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 8px;
    animation: fadeIn 0.15s ease-out;
  }

  .pos-cart-item-info {
    flex: 1;
    min-width: 0;
  }

  .pos-cart-item-title {
    font-size: 12px;
    font-weight: 700;
    color: var(--text-main);
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    margin-bottom: 2px;
  }

  .pos-cart-item-price-row {
    font-size: 11px;
    color: var(--text-muted);
    display: flex;
    align-items: center;
    gap: 6px;
  }

  .pos-cart-item-rate {
    color: #10b981;
    font-weight: 700;
  }

  .pos-cart-item-disc-tag {
    font-size: 9.5px;
    color: #ef4444;
    font-weight: 600;
  }

  .pos-cart-item-controls {
    display: flex;
    align-items: center;
    gap: 4px;
  }

  .pos-qty-btn {
    width: 24px;
    height: 24px;
    border-radius: 6px;
    border: 1px solid var(--border-color);
    background: var(--section-bg);
    color: var(--text-main);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    font-size: 11px;
    transition: all 0.15s ease;
  }

  .pos-qty-btn:hover {
    background: var(--primary);
    color: #fff;
    border-color: var(--primary);
  }

  .pos-qty-input {
    width: 36px;
    height: 24px;
    text-align: center;
    border: 1px solid var(--border-color);
    background: var(--section-bg);
    color: var(--text-main);
    font-size: 12px;
    font-weight: 700;
    border-radius: 6px;
    outline: none;
    -moz-appearance: textfield;
  }

  .pos-qty-input::-webkit-outer-spin-button,
  .pos-qty-input::-webkit-inner-spin-button {
    -webkit-appearance: none;
    margin: 0;
  }

  .pos-cart-item-subtotal {
    font-size: 12.5px;
    font-weight: 800;
    color: var(--text-main);
    min-width: 55px;
    text-align: right;
  }

  .pos-cart-item-del {
    background: none;
    border: none;
    color: #94a3b8;
    font-size: 12px;
    cursor: pointer;
    padding: 3px;
    margin-left: 2px;
    transition: color 0.15s;
  }

  .pos-cart-item-del:hover {
    color: #ef4444;
  }

  /* ── Cart Bottom / Summary ────────────────────────────────── */
  .pos-cart-footer {
    padding: 12px 14px;
    background: var(--section-bg);
    border-top: 1px solid var(--border-color);
    flex-shrink: 0;
    display: flex;
    flex-direction: column;
    gap: 8px;
  }

  .pos-summary-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 12px;
    color: var(--text-muted);
  }

  .pos-summary-row.special-disc-row {
    background: var(--background);
    padding: 6px 8px;
    border-radius: 8px;
    border: 1px solid var(--border-color);
  }

  .pos-special-disc-input {
    width: 90px;
    padding: 4px 6px;
    font-size: 12px;
    font-weight: 700;
    text-align: right;
    border: 1px solid var(--border-color);
    border-radius: 6px;
    background: var(--section-bg);
    color: var(--text-main);
    outline: none;
  }

  .pos-special-disc-input:focus {
    border-color: var(--primary);
  }

  .pos-net-total-box {
    background: var(--primary-soft);
    border: 1.5px solid var(--primary);
    border-radius: 10px;
    padding: 8px 12px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 2px;
  }

  .pos-net-total-box .label {
    font-size: 13px;
    font-weight: 700;
    color: var(--primary);
  }

  .pos-net-total-box .val {
    font-size: 24px;
    font-weight: 800;
    color: var(--primary);
    line-height: 1;
  }

  .pos-btn-submit {
    background: #10b981;
    color: #ffffff;
    border: none;
    border-radius: 10px;
    padding: 12px;
    font-size: 14px;
    font-weight: 700;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    box-shadow: 0 4px 14px rgba(16, 185, 129, 0.25);
    transition: all 0.2s ease;
  }

  .pos-btn-submit:hover:not(:disabled) {
    background: #059669;
    transform: translateY(-1px);
    box-shadow: 0 6px 18px rgba(16, 185, 129, 0.35);
  }

  .pos-btn-submit:disabled {
    background: #94a3b8;
    cursor: not-allowed;
    opacity: 0.6;
    box-shadow: none;
  }

  /* ── Responsive Behavior ──────────────────────────────────── */
  @media (max-width: 991px) {
    .pos-container {
      height: calc(100vh - 90px);
    }

    .pos-product-grid {
      grid-template-columns: repeat(auto-fill, minmax(130px, 1fr));
    }

    .pos-cart-panel {
      width: calc(100vw - 32px);
    }

    .pos-cart-fab {
      bottom: 20px;
      right: 20px;
    }
  }
</style>

<div class="pos-container">
  {{-- Left Side: Product Catalog --}}
  <div class="pos-catalog-panel">
    <div class="pos-catalog-header">
      <div class="pos-search-row">
        <div class="pos-search-box">
          <i class="fas fa-search search-icon"></i>
          <input type="text" id="posSearchInput" class="pos-search-input" placeholder="Search products by name or scan barcode..." autocomplete="off" autofocus>
          <button type="button" id="posSearchClear" class="pos-search-clear"><i class="fas fa-times"></i></button>
        </div>
      </div>

      {{-- Category Filter Pills --}}
      <div class="pos-categories-pills">
        <span class="pos-cat-pill active" data-cat-id="all">
          <i class="fas fa-th-large me-1"></i> All (<span id="totalProductsCount">{{ count($products) }}</span>)
        </span>
        @foreach($categories as $catId => $catName)
          <span class="pos-cat-pill" data-cat-id="{{ $catId }}">{{ $catName }}</span>
        @endforeach
      </div>
    </div>

    {{-- Product Cards Grid --}}
    <div class="pos-product-grid" id="posProductGrid">
      @forelse($products as $p)
        @php
          $hasOffer = !empty($p['has_offer']) && !empty($p['offer_text']);
          $hasDeduction = ($p['retail_deduction'] > 0 && $p['selling_rate'] < $p['price']);
          $imageSrc = (!empty($p['image']) && $p['image'] !== 'null') 
            ? (str_starts_with($p['image'], 'uploads/') ? '/' . $p['image'] : '/uploads/' . $p['image']) 
            : null;
        @endphp
        <div class="pos-prod-card" 
             data-id="{{ $p['id'] }}" 
             data-name="{{ strtolower($p['name']) }}" 
             data-barcode="{{ strtolower($p['barcode'] ?? '') }}"
             data-sku="{{ strtolower($p['sku'] ?? '') }}"
             data-cat="{{ $p['category_id'] }}"
             data-price="{{ $p['price'] }}"
             data-selling-rate="{{ $p['selling_rate'] }}"
             data-stock="{{ $p['available_qty'] }}"
             data-offer-disc="{{ $p['offer_discount'] }}"
             data-offer-type="{{ $p['offer_type'] }}"
             onclick="openProductDetailModal({{ $p['id'] }})">
          
          {{-- Top Badges & Cart Indicator --}}
          <div class="pos-prod-badge-wrap">
            @if($hasOffer)
              <span class="product-badge-offer">
                <i class="fas fa-bolt"></i> {{ $p['offer_text'] }}
              </span>
            @elseif($hasDeduction)
              <span class="pos-badge-deduct">-{{ (float)$p['retail_deduction'] }}%</span>
            @else
              <span class="pos-badge-normal">R</span>
            @endif

            <span class="pos-cart-qty-indicator" id="cardQtyBadge_{{ $p['id'] }}" title="In Cart">0</span>
          </div>

          <div class="pos-img-container">
            @if($imageSrc)
              <img src="{{ asset($imageSrc) }}" alt="{{ $p['name'] }}" loading="lazy" onerror="this.onerror=null;this.src='https://ui-avatars.com/api/?name={{ urlencode($p['name']) }}&background=0202e2&color=fff'">
            @else
              <img src="https://ui-avatars.com/api/?name={{ urlencode($p['name']) }}&background=0202e2&color=fff" alt="{{ $p['name'] }}" loading="lazy">
            @endif
          </div>

          <div class="pos-prod-body">
            <div class="pos-prod-title" title="{{ $p['name'] }}">{{ $p['name'] }}</div>

            <div class="pos-price-group">
              <span class="pos-selling-rate">৳{{ number_format($p['selling_rate']) }}</span>
              @if($hasDeduction || $hasOffer)
                <del class="pos-base-rate">৳{{ number_format($p['price']) }}</del>
              @endif
            </div>

            <div class="pos-card-stock">
              <i class="fas fa-check-circle"></i> Stock: {{ $p['available_qty'] }} pcs
            </div>

            <button type="button" class="btn-card-cart" onclick="event.stopPropagation(); addToPosCart({{ $p['id'] }})" title="Add to Cart">
              <i class="fas fa-basket-shopping"></i> Add to Cart
            </button>
          </div>
        </div>
      @empty
        <div style="grid-column: 1 / -1; text-align: center; padding: 50px 10px; color: var(--text-muted);">
          <i class="fas fa-box-open" style="font-size: 40px; margin-bottom: 10px; opacity: 0.4;"></i>
          <p class="m-0">No products in stock for this branch.</p>
        </div>
      @endforelse
    </div>
  </div>

  {{-- Right Side: POS Bill & Cart Terminal --}}
</div>{{-- /.pos-container --}}

{{-- Floating Cart FAB --}}
<button type="button" class="pos-cart-fab" onclick="openPosCartModal()" title="Open Cart">
  <i class="fas fa-shopping-cart"></i>
  <span class="pos-cart-fab-badge" id="posFabBadge">0</span>
</button>

{{-- Cart Modal Overlay --}}
<div class="pos-cart-modal-overlay" id="posCartModalOverlay" onclick="closePosCartOnBackdrop(event)">
  <div class="pos-cart-panel">
    {{-- Cart Header & Customer Inputs --}}
    <div class="pos-cart-header">
      <div class="pos-cart-title-row">
        <h3 class="pos-cart-title"><i class="fas fa-cash-register"></i> Retail Sale</h3>
        <div class="pos-cart-title-actions">
          <button type="button" class="pos-btn-clear-cart" onclick="clearPosCart()"><i class="fas fa-trash-alt me-1"></i> Reset</button>
          <button type="button" class="pos-cart-close-btn" onclick="closePosCartModal()" title="Close Cart"><i class="fas fa-times"></i></button>
        </div>
      </div>

      <div class="pos-cust-box">
        <div class="pos-cust-field">
          <label><i class="fas fa-user text-primary me-1"></i> Customer</label>
          <input type="text" id="posCustName" placeholder="Name (Optional)">
        </div>
        <div class="pos-cust-field">
          <label><i class="fas fa-phone text-primary me-1"></i> Phone</label>
          <input type="text" id="posCustPhone" placeholder="01XXXXXXXXX">
        </div>
      </div>
    </div>

    {{-- Cart Items Body --}}
    <div class="pos-cart-body" id="posCartBody">
      <div class="pos-cart-empty-state" id="posEmptyState">
        <i class="fas fa-shopping-basket"></i>
        <h6 class="fw-bold mb-1">Cart is Empty</h6>
        <p class="small m-0">Click any product from the catalog to start selling.</p>
      </div>
    </div>

    {{-- Cart Bottom / Financial Summary --}}
    <div class="pos-cart-footer">
      <div class="pos-summary-row">
        <span>Total Items:</span>
        <strong id="posSummaryItemCount">0 pcs (0 lines)</strong>
      </div>
      
      <div class="pos-summary-row">
        <span>Subtotal:</span>
        <strong id="posSummarySubtotal">৳ 0</strong>
      </div>

      <div class="pos-summary-row" id="posOfferDiscRow" style="display:none;">
        <span class="text-danger">Offer Discount:</span>
        <strong class="text-danger">- ৳ <span id="posSummaryOfferDisc">0</span></strong>
      </div>

      <div class="pos-summary-row special-disc-row">
        <span class="fw-bold text-warning"><i class="fas fa-tag me-1"></i> Special Discount:</span>
        <div>
          ৳ <input type="number" id="posSpecialDiscInput" class="pos-special-disc-input" placeholder="0" min="0" step="1" value="0" oninput="recalcPosTotals()">
        </div>
      </div>

      <div class="pos-net-total-box">
        <span class="label">Net Payable:</span>
        <span class="val">৳ <span id="posNetPayableDisplay">0</span></span>
      </div>

      {{-- Hidden Final Submission Form --}}
      <form method="POST" action="{{ route('manager.retail.store') }}" id="posOrderForm" style="display:none;">
        @csrf
        <input type="hidden" name="customer_name" id="formCustName">
        <input type="hidden" name="customer_phone" id="formCustPhone">
        <input type="hidden" name="special_discount" id="formSpecialDiscount" value="0">
        <input type="hidden" name="total_discount" id="formTotalDiscount" value="0">
        <input type="hidden" name="net_total" id="formNetTotal" value="0">
        <div id="formProductsWrapper"></div>
      </form>

      <button type="button" class="pos-btn-submit" id="posBtnSubmit" onclick="confirmPosSale()" disabled>
        <i class="fas fa-check-circle"></i> Confirm Sale (৳ <span id="posBtnTotalDisplay">0</span>)
      </button>
    </div>
  </div>
</div>

{{-- Product Details Modal Overlay (product/show.blade.php Style) --}}
<div class="pos-prod-modal-overlay" id="posProductDetailModalOverlay" onclick="closeProductDetailOnBackdrop(event)">
  <div class="pos-prod-modal">
    {{-- Modal Header --}}
    <div class="pos-prod-modal-header">
      <div class="pos-prod-modal-title">
        <i class="fas fa-cube text-primary"></i>
        <span>Product Details & Specs</span>
      </div>
      <button type="button" class="pos-cart-close-btn" onclick="closeProductDetailModal()" title="Close Details">
        <i class="fas fa-times"></i>
      </button>
    </div>

    {{-- Modal Body --}}
    <div class="pos-prod-modal-body">
      {{-- Gallery Column --}}
      <div class="pd-gallery">
        <div class="pd-main-img-wrap">
          <img id="modalMainImg" src="" alt="Product Image">
        </div>
        <div class="pd-thumbs" id="modalGalleryThumbs" style="display:none;"></div>
      </div>

      {{-- Info & Action Column --}}
      <div class="pd-info">
        <span class="pd-category-badge" id="modalProdCategory"></span>
        <h3 class="pd-name" id="modalProdName"></h3>

        <div class="pd-meta-row">
          <div class="pd-meta-item" id="modalProdSkuWrap">
            SKU: <strong id="modalProdSku"></strong>
          </div>
          <div class="pd-meta-item" id="modalProdBarcodeWrap">
            Barcode: <strong id="modalProdBarcode"></strong>
          </div>
          <div class="pd-meta-item" id="modalProdSupplierWrap">
            Brand/Supplier: <strong id="modalProdSupplier"></strong>
          </div>
        </div>

        <div class="pd-stock-pill in-stock" id="modalProdStockPill">
          <i class="fas fa-check-circle"></i> In Stock (<span id="modalProdStockQty">0</span> pcs available)
        </div>

        {{-- Pricing Box --}}
        <div class="pd-price-box">
          <div class="pd-price-label">Retail Selling Price</div>
          <div class="pd-price-row">
            <span class="pd-price-main">৳ <span id="modalProdSellingPrice">0</span></span>
            <del class="pd-price-original" id="modalProdOriginalPrice" style="display:none;"></del>
            <span class="pd-savings-pill" id="modalProdSavingsPill" style="display:none;"></span>
          </div>
          <div class="pd-offer-badge" id="modalProdOfferBadge" style="display:none;"></div>
        </div>

        {{-- Short Description --}}
        <p class="pd-desc-text" id="modalProdShortDesc" style="display:none;"></p>

        {{-- Long Description / Specs Box --}}
        <div class="pd-desc-box" id="modalProdLongDescBox" style="display:none;">
          <div class="pd-desc-title"><i class="fas fa-align-left"></i> Specifications / Details</div>
          <div class="pd-desc-text" id="modalProdLongDesc"></div>
        </div>

        {{-- Actions: Quantity & Add to Cart --}}
        <div class="pd-actions">
          <div class="pd-qty-group">
            <button type="button" class="pos-qty-btn" onclick="updateModalQty(-1)">-</button>
            <input type="number" id="modalQtyInput" class="pos-qty-input" value="1" min="1" onchange="validateModalQty(this)">
            <button type="button" class="pos-qty-btn" onclick="updateModalQty(1)">+</button>
          </div>
          <button type="button" class="pd-btn-cart" id="modalBtnAddToCart" onclick="addFromModalToCart()">
            <i class="fas fa-basket-shopping"></i> Add to POS Cart
          </button>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
  // In-memory products database & cart state
  const allProductsList = @json($products);
  const productsMap = {};
  allProductsList.forEach(p => { productsMap[p.id] = p; });

  let posCart = {}; // { [productId]: { product, qty } }

  // ── Add to Cart ──────────────────────────────────────────────
  function addToPosCart(productId) {
    const prod = productsMap[productId];
    if (!prod) return;

    if (prod.available_qty <= 0) {
      if (typeof Swal !== 'undefined') {
        Swal.fire({ icon: 'warning', title: 'Out of Stock', text: 'This item is not available in stock.', timer: 1500, showConfirmButton: false });
      }
      return;
    }

    if (!posCart[productId]) {
      posCart[productId] = {
        id: prod.id,
        name: prod.name,
        price: parseFloat(prod.price) || 0,
        selling_rate: parseFloat(prod.selling_rate) || 0,
        offer_discount: parseFloat(prod.offer_discount) || 0,
        offer_text: prod.offer_text,
        max_qty: parseInt(prod.available_qty) || 1,
        qty: 1
      };
    } else {
      if (posCart[productId].qty < posCart[productId].max_qty) {
        posCart[productId].qty += 1;
      } else {
        if (typeof Swal !== 'undefined') {
          Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'info',
            title: `Max available stock reached (${prod.available_qty} pcs)`,
            showConfirmButton: false,
            timer: 1600
          });
        }
      }
    }

    renderPosCart();

    // Pulse animation on floating cart button
    const $fab = $('.pos-cart-fab');
    $fab.addClass('pulse');
    setTimeout(() => $fab.removeClass('pulse'), 250);
  }

  // ── Update Item Quantity ────────────────────────────────────
  function updatePosCartQty(productId, change) {
    if (!posCart[productId]) return;

    let newQty = posCart[productId].qty + change;
    if (newQty <= 0) {
      removePosCartItem(productId);
      return;
    }

    if (newQty > posCart[productId].max_qty) {
      newQty = posCart[productId].max_qty;
      if (typeof Swal !== 'undefined') {
        Swal.fire({
          toast: true,
          position: 'top-end',
          icon: 'info',
          title: `Max stock is ${posCart[productId].max_qty} pcs`,
          showConfirmButton: false,
          timer: 1500
        });
      }
    }

    posCart[productId].qty = newQty;
    renderPosCart();
  }

  // ── Direct Quantity Input ────────────────────────────────────
  function onPosQtyInput(productId, inputEl) {
    if (!posCart[productId]) return;

    let val = parseInt(inputEl.value);
    if (isNaN(val) || val <= 0) {
      val = 1;
    }

    if (val > posCart[productId].max_qty) {
      val = posCart[productId].max_qty;
      inputEl.value = val;
    }

    posCart[productId].qty = val;
    renderPosCart();
  }

  // ── Remove Single Item ──────────────────────────────────────
  function removePosCartItem(productId) {
    if (posCart[productId]) {
      delete posCart[productId];
      renderPosCart();
    }
  }

  // ── Clear Entire Cart ───────────────────────────────────────
  function clearPosCart() {
    if (Object.keys(posCart).length === 0) return;
    posCart = {};
    $('#posSpecialDiscInput').val(0);
    renderPosCart();
  }

  // ── Render Cart UI ──────────────────────────────────────────
  function renderPosCart() {
    const $cartBody = $('#posCartBody');
    const items = Object.values(posCart);

    // Update product card indicators in catalog
    $('.pos-prod-card').removeClass('in-cart');
    $('.pos-cart-qty-indicator').text('0');

    if (items.length === 0) {
      $cartBody.html(`
        <div class="pos-cart-empty-state" id="posEmptyState">
          <i class="fas fa-shopping-basket"></i>
          <h6 class="fw-bold mb-1">Cart is Empty</h6>
          <p class="small m-0">Click any product from the catalog to start selling.</p>
        </div>
      `);
      $('#posBtnSubmit').prop('disabled', true);
    } else {
      let html = '';
      items.forEach(item => {
        // Highlight in catalog grid
        const $card = $(`.pos-prod-card[data-id="${item.id}"]`);
        $card.addClass('in-cart');
        $card.find('.pos-cart-qty-indicator').text(item.qty);

        const unitEffectivePrice = Math.max(0, item.selling_rate - item.offer_discount);
        const itemSubtotal = Math.round(unitEffectivePrice * item.qty);

        html += `
          <div class="pos-cart-item" data-id="${item.id}">
            <div class="pos-cart-item-info">
              <div class="pos-cart-item-title" title="${item.name}">${item.name}</div>
              <div class="pos-cart-item-price-row">
                <span class="pos-cart-item-rate">৳${Math.round(item.selling_rate)}</span>
                ${item.offer_discount > 0 ? `<span class="pos-cart-item-disc-tag">-${Math.round(item.offer_discount)}</span>` : ''}
                <span style="color:var(--text-muted);">x ${item.qty}</span>
              </div>
            </div>

            <div class="pos-cart-item-controls">
              <button type="button" class="pos-qty-btn" onclick="updatePosCartQty(${item.id}, -1)">-</button>
              <input type="number" class="pos-qty-input" value="${item.qty}" min="1" max="${item.max_qty}" onchange="onPosQtyInput(${item.id}, this)">
              <button type="button" class="pos-qty-btn" onclick="updatePosCartQty(${item.id}, 1)">+</button>
            </div>

            <div class="pos-cart-item-subtotal">৳${itemSubtotal}</div>

            <button type="button" class="pos-cart-item-del" title="Remove Item" onclick="removePosCartItem(${item.id})">
              <i class="fas fa-trash-alt"></i>
            </button>
          </div>
        `;
      });
      $cartBody.html(html);
      $('#posBtnSubmit').prop('disabled', false);
    }

    recalcPosTotals();
  }

  // ── Recalculate Totals (Rounded Integers) ────────────────────
  function recalcPosTotals() {
    const items = Object.values(posCart);
    let totalQty = 0;
    let rawSubtotal = 0;
    let totalOfferDiscount = 0;

    items.forEach(item => {
      totalQty += item.qty;
      rawSubtotal += (item.selling_rate * item.qty);
      totalOfferDiscount += (item.offer_discount * item.qty);
    });

    let specialDisc = parseFloat($('#posSpecialDiscInput').val()) || 0;
    if (specialDisc < 0) specialDisc = 0;

    let subtotalRounded = Math.round(rawSubtotal);
    let offerDiscRounded = Math.round(totalOfferDiscount);
    let specialDiscRounded = Math.round(specialDisc);

    let effectiveAfterOffer = rawSubtotal - totalOfferDiscount;
    let finalNetPayable = Math.max(0, Math.round(effectiveAfterOffer - specialDiscRounded));
    let totalCombinedDiscount = offerDiscRounded + specialDiscRounded;

    // Summary UI Updates
    $('#posSummaryItemCount').text(`${totalQty} pcs (${items.length} lines)`);
    $('#posSummarySubtotal').text(`৳ ${subtotalRounded}`);

    if (offerDiscRounded > 0) {
      $('#posOfferDiscRow').show();
      $('#posSummaryOfferDisc').text(offerDiscRounded);
    } else {
      $('#posOfferDiscRow').hide();
    }

    $('#posNetPayableDisplay').text(finalNetPayable);
    $('#posBtnTotalDisplay').text(finalNetPayable);

    // Update Floating Cart FAB Badge
    const $fabBadge = $('#posFabBadge');
    if (totalQty > 0) {
      $fabBadge.text(totalQty).addClass('has-items');
    } else {
      $fabBadge.text('0').removeClass('has-items');
    }

    // Sync Hidden Form Fields
    $('#formSpecialDiscount').val(specialDiscRounded);
    $('#formTotalDiscount').val(totalCombinedDiscount);
    $('#formNetTotal').val(finalNetPayable);
  }

  // ── Cart Modal Overlay Controls ─────────────────────────────
  function openPosCartModal() {
    $('#posCartModalOverlay').addClass('open');
  }

  function closePosCartModal() {
    $('#posCartModalOverlay').removeClass('open');
  }

  function closePosCartOnBackdrop(event) {
    if (event.target === document.getElementById('posCartModalOverlay')) {
      closePosCartModal();
    }
  }

  // ── Product Details Modal Controls ─────────────────────────
  let currentModalProductId = null;

  function openProductDetailModal(productId) {
    const prod = productsMap[productId];
    if (!prod) return;

    currentModalProductId = productId;
    
    // Set text and attributes
    $('#modalProdName').text(prod.name);
    $('#modalProdCategory').text(prod.category_name || 'General');
    
    if (prod.sku) {
      $('#modalProdSkuWrap').show();
      $('#modalProdSku').text(prod.sku);
    } else {
      $('#modalProdSkuWrap').hide();
    }

    if (prod.barcode) {
      $('#modalProdBarcodeWrap').show();
      $('#modalProdBarcode').text(prod.barcode);
    } else {
      $('#modalProdBarcodeWrap').hide();
    }

    if (prod.supplier_name) {
      $('#modalProdSupplierWrap').show();
      $('#modalProdSupplier').text(prod.supplier_name);
    } else {
      $('#modalProdSupplierWrap').hide();
    }

    $('#modalProdStockQty').text(prod.available_qty);
    
    // Pricing
    const sellingRate = Math.round(prod.selling_rate);
    const basePrice = Math.round(prod.price);
    $('#modalProdSellingPrice').text(sellingRate.toLocaleString());

    if (sellingRate < basePrice) {
      const diff = basePrice - sellingRate;
      const pct = Math.round((diff / basePrice) * 100);
      $('#modalProdOriginalPrice').text('৳' + basePrice.toLocaleString()).show();
      $('#modalProdSavingsPill').html(`<i class="fas fa-tag me-1"></i> Save ৳${diff.toLocaleString()} (${pct}%)`).show();
    } else {
      $('#modalProdOriginalPrice').hide();
      $('#modalProdSavingsPill').hide();
    }

    // Offer badge
    if (prod.has_offer && prod.offer_text) {
      $('#modalProdOfferBadge').show().html(`<i class="fas fa-bolt"></i> ${prod.offer_text}`);
    } else if (prod.retail_deduction > 0) {
      $('#modalProdOfferBadge').show().html(`<i class="fas fa-percent"></i> ${prod.retail_deduction}% Retail Deduction`);
    } else {
      $('#modalProdOfferBadge').hide();
    }

    // Main image & gallery
    let imgSrc = prod.image;
    if (imgSrc && !imgSrc.startsWith('/') && !imgSrc.startsWith('http')) {
      imgSrc = '/' + (imgSrc.startsWith('uploads/') ? imgSrc : 'uploads/' + imgSrc);
    }
    const fallbackImg = `https://ui-avatars.com/api/?name=${encodeURIComponent(prod.name)}&background=0202e2&color=fff`;
    $('#modalMainImg').attr('src', imgSrc || fallbackImg);

    // Gallery thumbs
    const $thumbs = $('#modalGalleryThumbs');
    $thumbs.empty();

    let allImgs = [];
    if (imgSrc) allImgs.push(imgSrc);
    if (Array.isArray(prod.images) && prod.images.length > 0) {
      prod.images.forEach(img => {
        let path = img;
        if (path && !path.startsWith('/') && !path.startsWith('http')) {
          path = '/' + (path.startsWith('uploads/') ? path : 'uploads/' + path);
        }
        if (!allImgs.includes(path)) allImgs.push(path);
      });
    }

    if (allImgs.length > 1) {
      $thumbs.show();
      allImgs.forEach((img, idx) => {
        const activeClass = idx === 0 ? 'active' : '';
        $thumbs.append(`
          <div class="pd-thumb ${activeClass}" onclick="switchModalMainImg('${img}', this)">
            <img src="${img}" alt="Thumbnail">
          </div>
        `);
      });
    } else {
      $thumbs.hide();
    }

    // Descriptions
    if (prod.short_description) {
      $('#modalProdShortDesc').text(prod.short_description).show();
    } else {
      $('#modalProdShortDesc').hide();
    }

    if (prod.long_description) {
      $('#modalProdLongDesc').html(prod.long_description);
      $('#modalProdLongDescBox').show();
    } else {
      $('#modalProdLongDescBox').hide();
    }

    // Reset modal quantity input to 1
    $('#modalQtyInput').val(1).attr('max', prod.available_qty);

    // Show modal
    $('#posProductDetailModalOverlay').addClass('open');
  }

  function closeProductDetailModal() {
    $('#posProductDetailModalOverlay').removeClass('open');
    currentModalProductId = null;
  }

  function closeProductDetailOnBackdrop(event) {
    if (event.target === document.getElementById('posProductDetailModalOverlay')) {
      closeProductDetailModal();
    }
  }

  function switchModalMainImg(src, thumbEl) {
    $('#modalMainImg').attr('src', src);
    $('.pd-thumb').removeClass('active');
    $(thumbEl).addClass('active');
  }

  function updateModalQty(change) {
    if (!currentModalProductId) return;
    const prod = productsMap[currentModalProductId];
    if (!prod) return;

    let $input = $('#modalQtyInput');
    let currentVal = parseInt($input.val()) || 1;
    let nextVal = currentVal + change;
    if (nextVal < 1) nextVal = 1;
    if (nextVal > prod.available_qty) nextVal = prod.available_qty;
    $input.val(nextVal);
  }

  function validateModalQty(inputEl) {
    if (!currentModalProductId) return;
    const prod = productsMap[currentModalProductId];
    if (!prod) return;

    let val = parseInt(inputEl.value) || 1;
    if (val < 1) val = 1;
    if (val > prod.available_qty) val = prod.available_qty;
    inputEl.value = val;
  }

  function addFromModalToCart() {
    if (!currentModalProductId) return;
    const prod = productsMap[currentModalProductId];
    if (!prod) return;

    const qty = parseInt($('#modalQtyInput').val()) || 1;
    for (let i = 0; i < qty; i++) {
      addToPosCart(currentModalProductId);
    }
    closeProductDetailModal();
    if (typeof Swal !== 'undefined') {
      Swal.fire({
        toast: true,
        position: 'top-end',
        icon: 'success',
        title: `Added ${qty} pcs to Cart`,
        showConfirmButton: false,
        timer: 1400
      });
    }
  }

  // ── Instant Live Catalog Search & Filtering ─────────────────
  function filterCatalog() {
    const query = $('#posSearchInput').val().trim().toLowerCase();
    const activeCat = $('.pos-cat-pill.active').data('cat-id');

    if (query.length > 0) {
      $('#posSearchClear').show();
    } else {
      $('#posSearchClear').hide();
    }

    let matchCount = 0;
    $('.pos-prod-card').each(function() {
      const $card = $(this);
      const name = String($card.data('name') || '').toLowerCase();
      const barcode = String($card.data('barcode') || '').toLowerCase();
      const sku = String($card.data('sku') || '').toLowerCase();
      const cat = String($card.data('cat') || '');

      const matchesSearch = query === '' || 
                            name.includes(query) || 
                            (barcode !== '' && barcode.includes(query)) || 
                            (sku !== '' && sku.includes(query));
      const matchesCat = activeCat === 'all' || cat === String(activeCat);

      if (matchesSearch && matchesCat) {
        $card.show();
        matchCount++;
      } else {
        $card.hide();
      }
    });
  }

  // ── Document Ready Event Bindings ───────────────────────────
  $(document).ready(function() {
    // Search input
    $('#posSearchInput').on('input', filterCatalog);

    $('#posSearchClear').on('click', function() {
      $('#posSearchInput').val('').focus();
      filterCatalog();
    });

    // Category pills click
    $('.pos-cat-pill').on('click', function() {
      $('.pos-cat-pill').removeClass('active');
      $(this).addClass('active');
      filterCatalog();
    });

    // Barcode scanning / Enter key in search box
    $('#posSearchInput').on('keydown', function(e) {
      if (e.key === 'Enter') {
        e.preventDefault();
        const query = $(this).val().trim().toLowerCase();
        if (!query) return;

        // 1. Direct barcode / SKU match in allProductsList (instant scan regardless of active category)
        const exactMatch = allProductsList.find(p => 
          (p.barcode && String(p.barcode).trim().toLowerCase() === query) ||
          (p.sku && String(p.sku).trim().toLowerCase() === query)
        );

        if (exactMatch) {
          addToPosCart(exactMatch.id);
          $(this).val('');
          filterCatalog();
          if (typeof Swal !== 'undefined') {
            Swal.fire({
              toast: true,
              position: 'top-end',
              icon: 'success',
              title: `Scanned: ${exactMatch.name}`,
              showConfirmButton: false,
              timer: 1300
            });
          }
          return;
        }

        // 2. Exact barcode match among currently visible cards
        const $visibleCards = $('.pos-prod-card:visible');
        if ($visibleCards.length === 1) {
          const id = $visibleCards.first().data('id');
          addToPosCart(id);
          $(this).val('');
          filterCatalog();
          return;
        } else if ($visibleCards.length > 1) {
          const $exactBarcodeCard = $visibleCards.filter(function() {
            return String($(this).data('barcode') || '').toLowerCase() === query;
          });
          if ($exactBarcodeCard.length === 1) {
            addToPosCart($exactBarcodeCard.first().data('id'));
            $(this).val('');
            filterCatalog();
            return;
          }
        } else if ($visibleCards.length === 0) {
          if (typeof Swal !== 'undefined') {
            Swal.fire({
              toast: true,
              position: 'top-end',
              icon: 'warning',
              title: `No product found for "${query}"`,
              showConfirmButton: false,
              timer: 1500
            });
          }
        }
      }
    });

    // Keyboard shortcuts (F2 to checkout, Esc to close modals)
    $(document).on('keydown', function(e) {
      if (e.key === 'F2') {
        e.preventDefault();
        if (!$('#posBtnSubmit').prop('disabled')) {
          confirmPosSale();
        }
      } else if (e.key === 'Escape') {
        if ($('#posProductDetailModalOverlay').hasClass('open')) {
          closeProductDetailModal();
        } else if ($('#posCartModalOverlay').hasClass('open')) {
          closePosCartModal();
        }
      }
    });
  });

  // ── Checkout Confirmation & Submit ──────────────────────────
  function confirmPosSale() {
    const items = Object.values(posCart);
    if (items.length === 0) return;

    const custName = $('#posCustName').val().trim();
    const custPhone = $('#posCustPhone').val().trim();
    const netTotal = $('#posNetPayableDisplay').text().trim() || '0';

    $('#formCustName').val(custName);
    $('#formCustPhone').val(custPhone);

    // Build product hidden inputs
    let inputsHtml = '';
    items.forEach((item, idx) => {
      inputsHtml += `
        <input type="hidden" name="products[${idx}][product_id]" value="${item.id}">
        <input type="hidden" name="products[${idx}][price]" value="${item.price}">
        <input type="hidden" name="products[${idx}][selling_rate]" value="${item.selling_rate}">
        <input type="hidden" name="products[${idx}][discount]" value="${item.offer_discount}">
        <input type="hidden" name="products[${idx}][qty]" value="${item.qty}">
      `;
    });
    $('#formProductsWrapper').html(inputsHtml);

    if (typeof Swal !== 'undefined') {
      Swal.fire({
        title: '⚠️ নিশ্চিত করুন',
        html: `
          <div style="text-align: left; font-size: 14px; line-height: 1.8;">
            <div style="background: var(--primary-soft); border: 1px solid var(--primary); border-radius: 10px; padding: 12px; text-align: center; margin-bottom: 15px;">
              <span style="font-size: 13px; color: var(--primary); font-weight: 600; display: block;">কাস্টমারের কাছ থেকে নগদ গ্রহণ করুন:</span>
              <span style="font-size: 26px; color: var(--primary); font-weight: 800;">৳ ${netTotal}</span>
            </div>
            <p style="font-weight: 700; color: #dc2626; margin-bottom: 8px;">Retail Order সম্পর্কে গুরুত্বপূর্ণ তথ্য:</p>
            <ul style="padding-left: 18px; color: var(--text-main); margin-bottom: 0;">
              <li>এই Retail Order <strong>স্বয়ংক্রিয়ভাবে Delivered</strong> হিসেবে মার্ক হবে।</li>
              <li>Payment স্বয়ংক্রিয়ভাবে <strong>Paid</strong> হিসেবে মার্ক হবে।</li>
              <li>কাস্টমারের কাছ থেকে <strong>সাথে সাথে টাকা নিতে হবে।</strong></li>
              <li style="color: #dc2626; font-weight: 600;">⛔ Due রাখার কোনো সুযোগ নেই।</li>
            </ul>
          </div>
        `,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#16a34a',
        cancelButtonColor: '#6c757d',
        confirmButtonText: '✅ নিশ্চিত, Submit করুন',
        cancelButtonText: '← ফিরে যান',
        reverseButtons: true,
        width: 480
      }).then((result) => {
        if (result.isConfirmed) {
          document.getElementById('posOrderForm').submit();
        }
      });
    } else {
      if (confirm(`Total Payable Amount: ৳ ${netTotal}\n\nThis Retail Order will be auto-delivered and payment auto-marked as Paid. Collect payment immediately — no due allowed. Confirm?`)) {
        document.getElementById('posOrderForm').submit();
      }
    }
  }
</script>
@endpush
