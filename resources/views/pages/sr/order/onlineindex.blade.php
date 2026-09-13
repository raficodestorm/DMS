@extends('layouts.srlayout')

@section('content')

<style>
  /* ── Page Container ── */
  .oi-page {
    max-width: 760px;
    margin: 0 auto;
    padding: 0 0 80px;
  }

  /* ── Page Header ── */
  .oi-hero {
    padding: 16px 4px 12px;
    margin-bottom: 16px;
    border-bottom: 1px solid var(--border-color);
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 10px;
  }
  .oi-hero-left {
    display: flex;
    align-items: center;
    gap: 12px;
  }
  .oi-hero-icon {
    width: 38px;
    height: 38px;
    border-radius: 10px;
    background: var(--primary-soft);
    color: var(--primary);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 15px;
    flex-shrink: 0;
  }
  .oi-hero-heading {
    font-size: 17px;
    font-weight: 800;
    color: var(--text-main);
    margin: 0;
    letter-spacing: -.3px;
  }
  .oi-hero-sub {
    font-size: 11.5px;
    color: var(--text-muted);
    font-weight: 500;
    margin: 0;
  }

  /* ── Search Card ── */
  .oi-search-card {
    background: var(--section-bg);
    border: 1px solid var(--border-color);
    border-radius: 14px;
    padding: 14px;
    box-shadow: 0 2px 12px var(--glass);
    margin-bottom: 18px;
  }
  .oi-search-bar {
    display: flex;
    align-items: center;
    gap: 8px;
  }
  .oi-search-field {
    flex: 1;
    display: flex;
    align-items: center;
    background: var(--background);
    border: 1.5px solid var(--border-color);
    border-radius: 10px;
    padding: 0 12px;
    gap: 8px;
    transition: border-color .18s, box-shadow .18s;
  }
  .oi-search-field:focus-within {
    border-color: var(--primary);
    box-shadow: 0 0 0 3px var(--primary-soft);
    background: var(--section-bg);
  }
  .oi-search-field i { color: var(--text-muted); font-size: 13px; flex-shrink: 0; }
  .oi-search-field input {
    flex: 1;
    border: none;
    background: transparent;
    padding: 9px 0;
    font-size: 13px;
    font-family: inherit;
    color: var(--text-main);
    outline: none;
  }
  .oi-clear {
    background: none;
    border: none;
    cursor: pointer;
    color: var(--text-muted);
    font-size: 12px;
    padding: 4px;
    display: none;
  }
  .oi-clear:hover { color: var(--text-main); }
  .oi-btn {
    background: var(--primary);
    color: #fff;
    border: none;
    border-radius: 8px;
    padding: 9px 14px;
    font-size: 12.5px;
    font-weight: 700;
    cursor: pointer;
    white-space: nowrap;
    flex-shrink: 0;
    transition: filter .18s, transform .18s;
    display: flex;
    align-items: center;
    gap: 6px;
  }
  .oi-btn:hover { filter: brightness(1.1); transform: translateY(-1px); }
  .oi-btn:disabled { opacity: .6; cursor: not-allowed; transform: none; }

  /* chips */
  .oi-chips { display: flex; gap: 7px; flex-wrap: wrap; margin-top: 10px; }
  .oi-chip {
    font-size: 10.5px; font-weight: 600; padding: 3px 9px;
    border-radius: 20px; display: flex; align-items: center; gap: 4px;
  }
  .oi-chip-online { background: var(--primary-soft); color: var(--primary); border: 1px solid var(--border-color); }
  .oi-chip-field  { background: var(--success);      color: var(--green);   border: 1px solid var(--border-color); }

  /* ── States ── */
  .oi-state {
    text-align: center;
    padding: 50px 16px;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 10px;
  }
  .oi-state-ico {
    width: 54px;
    height: 54px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 22px;
  }
  .oi-state-ico.blue   { background: var(--primary-soft); color: var(--primary); }
  .oi-state-ico.yellow { background: var(--warning);      color: var(--text-main); }
  .oi-state-ico.spin   { background: var(--primary-soft); color: var(--primary); }
  .oi-state h3 { font-size: 15px; font-weight: 700; color: var(--text-main); margin: 0; }
  .oi-state p  { font-size: 12.5px; color: var(--text-muted); margin: 0; max-width: 320px; line-height: 1.5; }

  /* ── Result Cards ── */
  .oi-results { display: flex; flex-direction: column; gap: 14px; }

  .oi-card {
    background: var(--section-bg);
    border: 1px solid var(--border-color);
    border-radius: 14px;
    padding: 16px;
    box-shadow: 0 2px 10px var(--glass);
    transition: box-shadow .18s, border-color .18s;
  }
  .oi-card:hover {
    box-shadow: 0 6px 20px var(--glass);
    border-color: var(--border-color);
  }

  /* Card Head */
  .oi-card-head {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 10px;
    padding-bottom: 12px;
    border-bottom: 1px solid var(--border-color);
  }
  .oi-card-id {
    font-size: 15px;
    font-weight: 800;
    color: var(--text-main);
    letter-spacing: -.2px;
  }
  .oi-card-date {
    font-size: 11px;
    color: var(--text-muted);
    margin-top: 3px;
    display: flex;
    align-items: center;
    gap: 4px;
  }
  .oi-badges {
    display: flex;
    align-items: center;
    gap: 5px;
    flex-wrap: wrap;
    justify-content: flex-end;
  }
  .oi-type-pill {
    font-size: 10px;
    font-weight: 700;
    padding: 3px 8px;
    border-radius: 20px;
    white-space: nowrap;
    text-transform: uppercase;
  }
  .pill-online  { background: var(--primary-soft); color: var(--primary); }
  .pill-field   { background: var(--success);      color: var(--green);   }
  .pill-retail  { background: var(--warning);      color: var(--text-main); }

  .oi-badge {
    font-size: 10px;
    font-weight: 700;
    padding: 3px 8px;
    border-radius: 20px;
    white-space: nowrap;
  }
  .b-pending  { background: var(--warning); color: var(--text-main); }
  .b-approved { background: var(--success); color: var(--green);     }
  .b-complete { background: var(--primary-soft); color: var(--primary); }
  .b-delivered{ background: var(--success); color: var(--green);     }
  .b-rejected { background: var(--danger);  color: var(--text-main); }
  .b-other    { background: var(--border-color); color: var(--text-muted); }
  .b-unpaid   { background: var(--warning); color: var(--text-main); }
  .b-partial  { background: var(--primary-soft); color: var(--primary); }
  .b-paid     { background: var(--success); color: var(--green);     }

  /* Card Grid Info (Always 2 Columns) */
  .oi-details-grid {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 8px;
    padding: 10px 0;
  }

  .oi-info-item {
    display: flex;
    align-items: flex-start;
    gap: 7px;
    background: var(--background);
    padding: 7px 9px;
    border-radius: 8px;
    border: 1px solid var(--border-color);
    min-width: 0;
  }
  .oi-info-item.full-width {
    grid-column: 1 / -1;
  }
  .oi-info-icon {
    width: 24px;
    height: 24px;
    border-radius: 6px;
    background: var(--primary-soft);
    color: var(--primary);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 10.5px;
    flex-shrink: 0;
    margin-top: 1px;
  }
  .oi-info-content {
    min-width: 0;
    flex: 1;
    overflow: hidden;
  }
  .oi-info-label {
    font-size: 9.5px;
    font-weight: 600;
    color: var(--text-muted);
    text-transform: uppercase;
    letter-spacing: .3px;
    margin-bottom: 1px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }
  .oi-info-val {
    font-size: 11.5px;
    font-weight: 700;
    color: var(--text-main);
    word-break: break-word;
    margin: 0;
    line-height: 1.35;
  }
  .oi-info-val a {
    color: var(--primary);
    text-decoration: none;
  }
  .oi-info-val a:hover { text-decoration: underline; }

  /* Financial Summary Bar */
  .oi-amount-bar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    background: var(--primary-soft);
    border: 1px solid var(--border-color);
    border-radius: 8px;
    padding: 7px 10px;
    margin: 3px 0 10px;
    gap: 6px;
  }
  .oi-amount-title {
    font-size: 11px;
    font-weight: 700;
    color: var(--text-main);
    display: flex;
    align-items: center;
    gap: 5px;
  }
  .oi-net-amount {
    font-size: 15px;
    font-weight: 800;
    color: var(--primary);
    white-space: nowrap;
  }

  /* Action Buttons (Always 2 Columns) */
  .oi-card-actions {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 7px;
    padding-top: 8px;
    border-top: 1px solid var(--border-color);
  }

  .oi-action-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 5px;
    padding: 7.5px 8px;
    font-size: 11.5px;
    font-weight: 700;
    border-radius: 7px;
    text-decoration: none;
    cursor: pointer;
    border: 1px solid transparent;
    transition: all .16s ease;
    white-space: nowrap;
    width: 100%;
  }
  .btn-view {
    background: var(--background);
    color: var(--text-main);
    border-color: var(--border-color);
  }
  .btn-view:hover {
    background: var(--primary-soft);
    color: var(--primary);
    border-color: var(--primary);
  }
  .btn-deliver {
    background: var(--primary);
    color: #fff;
  }
  .btn-deliver:hover {
    filter: brightness(1.1);
    transform: translateY(-1px);
  }
  .btn-delivered-done {
    background: var(--success);
    color: var(--green);
    border-color: var(--border-color);
    cursor: default;
  }

  /* ── Mobile Optimization (Under 576px) ── */
  @media (max-width: 576px) {
    .oi-card {
      padding: 12px 10px;
      border-radius: 11px;
    }
    .oi-card-id {
      font-size: 13.5px;
    }
    .oi-card-date {
      font-size: 10px;
    }
    .oi-badges {
      gap: 3px;
    }
    .oi-type-pill,
    .oi-badge {
      font-size: 9px;
      padding: 2px 6px;
    }
    .oi-details-grid {
      gap: 6px;
      padding: 8px 0;
    }
    .oi-info-item {
      padding: 5px 6px;
      gap: 5px;
      border-radius: 6px;
    }
    .oi-info-icon {
      width: 20px;
      height: 20px;
      font-size: 9.5px;
      border-radius: 5px;
    }
    .oi-info-label {
      font-size: 8.5px;
    }
    .oi-info-val {
      font-size: 10.5px;
    }
    .oi-amount-bar {
      padding: 6px 8px;
    }
    .oi-amount-title {
      font-size: 10px;
    }
    .oi-net-amount {
      font-size: 13.5px;
    }
    .oi-card-actions {
      gap: 6px;
      padding-top: 7px;
    }
    .oi-action-btn {
      padding: 6.5px 6px;
      font-size: 10.5px;
      border-radius: 6px;
    }
  }
</style>
<div class="oi-page">

  {{-- Page Header --}}
  <div class="oi-hero">
    <div class="oi-hero-left">
      <div class="oi-hero-icon"><i class="fas fa-search"></i></div>
      <div>
        <h1 class="oi-hero-heading">Order Lookup & Delivery</h1>
        <p class="oi-hero-sub">Search order by Order ID</p>
      </div>
    </div>
    <a href="{{ route('sr.order.index') }}" style="font-size:12px; font-weight:600; color:var(--text-muted); text-decoration:none; display:flex; align-items:center; gap:5px;">
      <i class="fas fa-arrow-left"></i> My Orders
    </a>
  </div>

  {{-- Search Bar --}}
  <div class="oi-search-card">
    <div class="oi-search-bar">
      <div class="oi-search-field">
        <i class="fas fa-hashtag"></i>
        <input type="text" id="oiInput" placeholder="Enter Order ID (e.g. BRS-20240001)..." autocomplete="off">
        <button class="oi-clear" id="oiClear"><i class="fas fa-times"></i></button>
      </div>
      <button class="oi-btn" id="oiBtn">
        <i class="fas fa-search"></i>
        <span>Search</span>
      </button>
    </div>
    <div class="oi-chips">
      <span class="oi-chip oi-chip-online"><i class="fas fa-globe"></i> Online — all branches</span>
      <span class="oi-chip oi-chip-field"><i class="fas fa-store"></i> Field — your branch only</span>
    </div>
  </div>

  {{-- States & Results --}}
  <div id="oiBody">

    <div class="oi-state" id="oiInit">
      <div class="oi-state-ico blue"><i class="fas fa-search"></i></div>
      <h3>Find an Order</h3>
      <p>Enter an Order ID above to see customer & delivery details.</p>
    </div>

    <div class="oi-state" id="oiLoading" style="display:none;">
      <div class="oi-state-ico spin"><i class="fas fa-circle-notch fa-spin"></i></div>
      <h3>Searching...</h3>
    </div>

    <div class="oi-state" id="oiEmpty" style="display:none;">
      <div class="oi-state-ico yellow"><i class="fas fa-box-open"></i></div>
      <h3>No Orders Found</h3>
      <p id="oiEmptyMsg"></p>
    </div>

    <div class="oi-results" id="oiResults" style="display:none;"></div>

  </div>

</div>

@push('scripts')
<script>
(function () {
  const input    = document.getElementById('oiInput');
  const btn      = document.getElementById('oiBtn');
  const clear    = document.getElementById('oiClear');

  const init     = document.getElementById('oiInit');
  const loading  = document.getElementById('oiLoading');
  const empty    = document.getElementById('oiEmpty');
  const emptyMsg = document.getElementById('oiEmptyMsg');
  const results  = document.getElementById('oiResults');
  const csrfToken = '{{ csrf_token() }}';

  function show(state) {
    [init, loading, empty, results].forEach(el => el.style.display = 'none');
    if (state === 'init')    { init.style.display    = 'flex'; }
    if (state === 'loading') { loading.style.display = 'flex'; }
    if (state === 'empty')   { empty.style.display   = 'flex'; }
    if (state === 'results') { results.style.display = 'flex'; }
  }

  input.addEventListener('input', () => {
    clear.style.display = input.value ? 'block' : 'none';
  });

  clear.addEventListener('click', () => {
    input.value = '';
    clear.style.display = 'none';
    show('init');
    input.focus();
  });

  input.addEventListener('keydown', e => { if (e.key === 'Enter') search(); });
  btn.addEventListener('click', search);

  function search() {
    const q = input.value.trim();
    if (!q) { show('init'); return; }

    show('loading');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-circle-notch fa-spin"></i>';

    fetch(`{{ route('sr.order.online.search') }}?search=${encodeURIComponent(q)}`, {
      headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
    })
    .then(r => { if (!r.ok) throw new Error(); return r.json(); })
    .then(data => {
      btn.disabled = false;
      btn.innerHTML = '<i class="fas fa-search"></i> <span>Search</span>';

      if (!data.orders?.length) {
        emptyMsg.textContent = data.message || 'No orders found.';
        show('empty');
        return;
      }
      results.innerHTML = data.orders.map(renderCard).join('');
      show('results');
    })
    .catch(() => {
      btn.disabled = false;
      btn.innerHTML = '<i class="fas fa-search"></i> <span>Search</span>';
      emptyMsg.textContent = 'Something went wrong. Please try again.';
      show('empty');
    });
  }

  /* ── Badges Mapping ── */
  const typePill = t => ({
    online:      ['pill-online', 'Online'],
    field_order: ['pill-field',  'Field Order'],
    retail:      ['pill-retail', 'Retail'],
  }[t] ?? ['pill-retail', t || 'Order']);

  const statusBadge = s => ({
    pending_sr:      ['b-pending',   'Pending'],
    pending_manager: ['b-pending',   'Pending'],
    approved:        ['b-approved',  'Processing'],
    complete:        ['b-complete',  'Shipped'],
    delivered:       ['b-delivered', 'Delivered'],
    rejected:        ['b-rejected',  'Rejected'],
  }[s] ?? ['b-other', s || 'Pending']);

  const payBadge = p => ({
    unpaid:  ['b-unpaid',  'Unpaid'],
    partial: ['b-partial', 'Partial'],
    paid:    ['b-paid',    'Paid'],
  }[p] ?? ['b-unpaid', 'Unpaid']);

  function esc(s) {
    return String(s ?? '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
  }

  function renderCard(o) {
    const [pillCls, pillLabel] = typePill(o.order_type);
    const [sCls, sLabel]       = statusBadge(o.status);
    const [payCls, payLabel]   = payBadge(o.payment_status);

    const isDelivered = (o.status === 'delivered' || o.is_delivered);
    const isRetailOrGuest = (o.is_retail_or_guest === true || o.customer_id === null || !o.customer_id || o.customer_group === 'retail');

    return `
    <div class="oi-card" id="card-order-${esc(o.id)}">
      
      {{-- Card Header --}}
      <div class="oi-card-head">
        <div>
          <div class="oi-card-id">${esc(o.order_id)}</div>
          <div class="oi-card-date"><i class="fas fa-calendar-alt"></i> ${esc(o.date)}</div>
        </div>
        <div class="oi-badges">
          <span class="oi-type-pill ${pillCls}">${pillLabel}</span>
          <span class="oi-badge ${sCls}" id="status-badge-${esc(o.id)}">${sLabel}</span>
          <span class="oi-badge ${payCls}" id="pay-badge-${esc(o.id)}">${payLabel}</span>
        </div>
      </div>

      {{-- Details Grid --}}
      <div class="oi-details-grid">

        {{-- Customer Name --}}
        <div class="oi-info-item">
          <div class="oi-info-icon"><i class="fas fa-user"></i></div>
          <div class="oi-info-content">
            <div class="oi-info-label">Customer Name</div>
            <p class="oi-info-val">${esc(o.customer_name)}</p>
          </div>
        </div>

        {{-- Customer Phone --}}
        <div class="oi-info-item">
          <div class="oi-info-icon"><i class="fas fa-phone-alt"></i></div>
          <div class="oi-info-content">
            <div class="oi-info-label">Customer Phone</div>
            <p class="oi-info-val">
              ${o.customer_phone && o.customer_phone !== 'N/A' 
                ? `<a href="tel:${esc(o.customer_phone)}"><i class="fas fa-phone-volume me-1"></i>${esc(o.customer_phone)}</a>` 
                : 'N/A'}
            </p>
          </div>
        </div>

        {{-- City & Country --}}
        <div class="oi-info-item">
          <div class="oi-info-icon"><i class="fas fa-map-marked-alt"></i></div>
          <div class="oi-info-content">
            <div class="oi-info-label">City / Country</div>
            <p class="oi-info-val">${esc(o.city || 'N/A')}, ${esc(o.country || 'N/A')}</p>
          </div>
        </div>

        {{-- Payment Method --}}
        <div class="oi-info-item">
          <div class="oi-info-icon"><i class="fas fa-wallet"></i></div>
          <div class="oi-info-content">
            <div class="oi-info-label">Payment Method</div>
            <p class="oi-info-val">${esc(o.payment_method || 'Cash on Delivery')}</p>
          </div>
        </div>

        {{-- Branch --}}
        <div class="oi-info-item">
          <div class="oi-info-icon"><i class="fas fa-building"></i></div>
          <div class="oi-info-content">
            <div class="oi-info-label">Branch</div>
            <p class="oi-info-val">${esc(o.branch || 'N/A')}</p>
          </div>
        </div>

        {{-- Order Type --}}
        <div class="oi-info-item">
          <div class="oi-info-icon"><i class="fas fa-tag"></i></div>
          <div class="oi-info-content">
            <div class="oi-info-label">Order Type</div>
            <p class="oi-info-val" style="text-transform: capitalize;">${esc(String(o.order_type || '').replace('_', ' '))}</p>
          </div>
        </div>

        {{-- Delivery Address --}}
        ${o.address && o.address !== 'N/A' ? `
        <div class="oi-info-item full-width">
          <div class="oi-info-icon"><i class="fas fa-location-dot"></i></div>
          <div class="oi-info-content">
            <div class="oi-info-label">Delivery Address</div>
            <p class="oi-info-val">${esc(o.address)}</p>
          </div>
        </div>` : ''}

        {{-- Note (if present) --}}
        ${o.note ? `
        <div class="oi-info-item full-width" style="border-left: 3px solid var(--primary);">
          <div class="oi-info-icon"><i class="fas fa-comment-dots"></i></div>
          <div class="oi-info-content">
            <div class="oi-info-label">Order Note</div>
            <p class="oi-info-val" style="font-weight: 500; font-style: italic;">${esc(o.note)}</p>
          </div>
        </div>` : ''}

      </div>

      {{-- Financial Summary --}}
      <div class="oi-amount-bar">
        <div class="oi-amount-title">
          <i class="fas fa-receipt"></i> Total Payable Amount:
        </div>
        <div class="oi-net-amount">৳${esc(o.net_total)}</div>
      </div>

      {{-- Bottom Actions --}}
      <div class="oi-card-actions">
        <a href="${esc(o.show_url)}" class="oi-action-btn btn-view">
          <i class="fas fa-eye"></i> View Details
        </a>

        <div id="deliver-btn-box-${esc(o.id)}">
          ${isDelivered ? `
            <button class="oi-action-btn btn-delivered-done" disabled style="width:100%;">
              <i class="fas fa-check-circle"></i> Delivered
            </button>
          ` : `
            <button class="oi-action-btn btn-deliver" style="width:100%;" onclick="confirmDelivery('${esc(o.id)}', '${esc(o.delivered_url)}', ${isRetailOrGuest}, '${esc(o.net_total)}', '${esc(o.order_id)}', this)">
              <i class="fas fa-truck-ramp-box"></i> Confirm Deliver
            </button>
          `}
        </div>
      </div>

    </div>`;
  }

  /* ── Confirm Deliver Handler (Bangla & Dynamic Instructions) ── */
  window.confirmDelivery = function (orderId, deliveredUrl, isRetailOrGuest, netTotal, orderIdText, btnElement) {
    const doDeliver = () => {
      btnElement.disabled = true;
      btnElement.innerHTML = '<i class="fas fa-circle-notch fa-spin"></i> প্রসেসিং...';

      fetch(deliveredUrl, {
        method: 'PATCH',
        headers: {
          'X-CSRF-TOKEN': csrfToken,
          'X-Requested-With': 'XMLHttpRequest',
          'Accept': 'application/json'
        }
      })
      .then(r => {
        if (!r.ok) throw new Error();
        return r.json();
      })
      .then(data => {
        // Update button UI
        const btnBox = document.getElementById(`deliver-btn-box-${orderId}`);
        if (btnBox) {
          btnBox.innerHTML = `
            <button class="oi-action-btn btn-delivered-done" disabled style="width:100%;">
              <i class="fas fa-check-circle"></i> Delivered
            </button>
          `;
        }
        // Update Status & Payment Badges
        const badge = document.getElementById(`status-badge-${orderId}`);
        if (badge) {
          badge.className = 'oi-badge b-delivered';
          badge.textContent = 'Delivered';
        }
        const payBadge = document.getElementById(`pay-badge-${orderId}`);
        if (payBadge) {
          payBadge.className = 'oi-badge b-paid';
          payBadge.textContent = 'Paid';
        }

        // Notification (Bangla)
        const successMsg = `অর্ডার #${orderIdText} সফলভাবে ডেলিভারি নিশ্চিত করা হয়েছে।`;
        if (typeof Swal !== 'undefined') {
          Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'success',
            title: successMsg,
            showConfirmButton: false,
            timer: 3000
          });
        } else {
          alert(successMsg);
        }
      })
      .catch(err => {
        btnElement.disabled = false;
        btnElement.innerHTML = '<i class="fas fa-truck-ramp-box"></i> Confirm Deliver';
        const errorMsg = 'ডেলিভারি স্ট্যাটাস আপডেট করতে সমস্যা হয়েছে। আবার চেষ্টা করুন।';
        if (typeof Swal !== 'undefined') {
          Swal.fire({
            icon: 'error',
            title: 'ব্যর্থ হয়েছে!',
            text: errorMsg,
            confirmButtonText: 'ঠিক আছে',
            confirmButtonColor: '#0202e2'
          });
        } else {
          alert(errorMsg);
        }
      });
    };

    if (typeof Swal !== 'undefined') {
      if (isRetailOrGuest) {
        // Dynamic Guest / Retail COD Instruction Popup
        Swal.fire({
          title: '<span style="font-size:17px; font-weight:800; color:var(--text-main);">ডেলিভারি ও ক্যাশ কালেকশন</span>',
          html: `
            <div style="text-align: left; font-size: 13px; line-height: 1.55; color: var(--text-main);">
              <div style="background: var(--warning); padding: 9px 12px; border-radius: 8px; margin-bottom: 10px; font-weight: 700;">
                <i class="fas fa-exclamation-circle me-1"></i> বিশেষ ক্যাশ অন ডেলিভারি নির্দেশনা:
              </div>
              <p style="margin-bottom: 8px;">
                এটি একটি ক্যাশ অন ডেলিভারি অর্ডার (অর্ডার নং: <strong>#${esc(orderIdText)}</strong>)।
              </p>
              <div style="background: var(--primary-soft); padding: 10px 12px; border-radius: 8px; margin-bottom: 10px; border-left: 3px solid var(--primary);">
                <span style="font-size: 11px; color: var(--text-muted); display:block; font-weight:600; text-transform:uppercase;">কাস্টমার থেকে আদায়যোগ্য ক্যাশ:</span>
                <strong style="font-size: 18px; color: var(--primary); font-weight:800;">৳ ${esc(netTotal)}</strong>
              </div>
              <ul style="padding-left: 18px; margin: 0; font-size: 12.5px; color: var(--text-main);">
                <li style="margin-bottom: 4px;">পণ্য হস্তান্তরের পূর্বে কাস্টমারের কাছ থেকে সম্পূর্ণ <strong>৳ ${esc(netTotal)}</strong> টাকা ক্যাশ বুঝে নিন।</li>
                <li>সংগৃহীত এই ক্যাশ টাকা আপনাকে <strong>অবশ্যই ব্রাঞ্চ ম্যানেজারের নিকট জমা</strong> দিতে হবে।</li>
              </ul>
            </div>
          `,
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#0202e2',
          cancelButtonColor: '#64748b',
          confirmButtonText: '<i class="fas fa-check-circle me-1"></i> ক্যাশ পেয়েছি, ডেলিভারি সম্পন্ন করুন',
          cancelButtonText: 'বাতিল'
        }).then(res => {
          if (res.isConfirmed) {
            doDeliver();
          }
        });
      } else {
        // Registered Customer (Wholesale/General) Popup
        Swal.fire({
          title: '<span style="font-size:16px; font-weight:800; color:var(--text-main);">ডেলিভারি নিশ্চিতকরণ</span>',
          html: `
            <div style="text-align: center; font-size: 13px; line-height: 1.5; color: var(--text-main);">
              <p style="margin-bottom: 10px;">
                আপনি কি নিশ্চিত যে অর্ডার <strong>#${esc(orderIdText)}</strong> কাস্টমারকে সফলভাবে ডেলিভারি করা হয়েছে?
              </p>
              <div style="background: var(--primary-soft); display: inline-block; padding: 5px 14px; border-radius: 20px; font-weight: 700; color: var(--primary); font-size: 13.5px;">
                মোট বিল: ৳ ${esc(netTotal)}
              </div>
            </div>
          `,
          icon: 'question',
          showCancelButton: true,
          confirmButtonColor: '#0202e2',
          cancelButtonColor: '#64748b',
          confirmButtonText: 'হ্যাঁ, ডেলিভারি নিশ্চিত করুন',
          cancelButtonText: 'বাতিল'
        }).then(res => {
          if (res.isConfirmed) {
            doDeliver();
          }
        });
      }
    } else {
      let confirmMsg = "";
      if (isRetailOrGuest) {
        confirmMsg = `⚠️ ক্যাশ অন ডেলিভারি নির্দেশনা:\n\nঅর্ডার নং: #${orderIdText}\nকাস্টমারের কাছ থেকে মোট ৳ ${netTotal} টাকা ক্যাশ আদায় করুন। সংগৃহীত এই টাকা আপনাকে অবশ্যই ম্যানেজারের নিকট জমা দিতে হবে।\n\nআপনি কি ক্যাশ টাকা বুঝে পেয়ে ডেলিভারি নিশ্চিত করতে চান?`;
      } else {
        confirmMsg = `আপনি কি নিশ্চিত যে অর্ডার #${orderIdText} (মোট বিল: ৳ ${netTotal}) সফলভাবে ডেলিভারি করা হয়েছে?`;
      }
      if (confirm(confirmMsg)) {
        doDeliver();
      }
    }
  };

})();
</script>
@endpush

@endsection
