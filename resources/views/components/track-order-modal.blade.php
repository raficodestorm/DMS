<!-- World-Class 3D Order Tracking Modal -->
<style>
  .track-modal-overlay {
    position: fixed;
    inset: 0;
    width: 100vw;
    height: 100vh;
    background: rgba(10, 15, 30, 0.72);
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    z-index: 100000;
    display: none;
    justify-content: center;
    align-items: center;
    padding: 16px;
    opacity: 0;
    transition: opacity 0.25s ease;
  }
  .track-modal-overlay.show { display: flex; opacity: 1; }

  .track-modal-card {
    background: var(--section-bg);
    border: 1px solid var(--border-color);
    border-radius: 24px;
    box-shadow: 0 25px 60px rgba(0, 0, 0, 0.3);
    width: 100%;
    max-width: 550px;
    max-height: 90vh;
    display: flex;
    flex-direction: column;
    overflow: hidden;
    transform: scale(0.94) translateY(10px);
    transition: transform 0.25s cubic-bezier(0.16, 1, 0.3, 1);
  }
  .track-modal-overlay.show .track-modal-card { transform: scale(1) translateY(0); }

  .track-modal-header {
    padding: 18px 22px 14px;
    border-bottom: 1px solid var(--border-color);
    position: relative;
    background: var(--section-bg);
  }
  .track-modal-close {
    position: absolute;
    top: 16px;
    right: 18px;
    background: var(--primary-soft);
    border: none;
    color: var(--text-muted);
    cursor: pointer;
    width: 32px;
    height: 32px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    font-size: 13px;
    transition: all 0.2s;
  }
  .track-modal-close:hover { background: #fee2e2; color: #ef4444; transform: rotate(90deg); }

  .track-modal-body {
    padding: 18px 22px 22px;
    overflow-y: auto;
    max-height: calc(90vh - 85px);
  }

  /* Search Input */
  .track-search-box {
    display: flex;
    gap: 8px;
    background: var(--background);
    border: 1.5px solid var(--border-color);
    border-radius: 14px;
    padding: 4px;
    transition: border-color 0.2s, box-shadow 0.2s;
  }
  .track-search-box:focus-within {
    border-color: var(--primary);
    box-shadow: 0 0 0 4px var(--primary-soft);
  }
  .track-input {
    flex: 1;
    border: none;
    background: transparent;
    padding: 10px 14px;
    font-size: 14px;
    font-weight: 500;
    color: var(--text-main);
    outline: none;
  }
  .track-input::placeholder { color: var(--text-muted); opacity: 0.7; }
  .track-btn-submit {
    background: var(--primary);
    color: #fff;
    border: none;
    border-radius: 10px;
    padding: 0 18px;
    font-size: 13.5px;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 7px;
    cursor: pointer;
    transition: all 0.2s ease;
    white-space: nowrap;
  }
  .track-btn-submit:hover { filter: brightness(1.1); transform: translateY(-1px); }

  /* =========================================================
     3D REAL-TIME CENTER PULSE RADAR SCANNER
     ========================================================= */
  .track-3d-stage {
    perspective: 800px;
    width: 100%;
    height: 190px;
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
    overflow: hidden;
    margin: 6px 0 12px;
  }

  /* 3D Isometric Map Platform */
  .track-3d-ground {
    position: relative;
    width: 200px;
    height: 200px;
    border-radius: 50%;
    background: 
      radial-gradient(circle, rgba(14, 105, 252, 0.2) 0%, rgba(14, 105, 252, 0.03) 65%, transparent 75%),
      linear-gradient(rgba(14, 105, 252, 0.08) 1px, transparent 1px),
      linear-gradient(90deg, rgba(14, 105, 252, 0.08) 1px, transparent 1px);
    background-size: 100% 100%, 20px 20px, 20px 20px;
    border: 1.5px solid rgba(14, 105, 252, 0.4);
    transform: rotateX(62deg) rotateZ(-25deg);
    transform-style: preserve-3d;
    box-shadow: 0 0 40px rgba(14, 105, 252, 0.25);
    display: flex;
    align-items: center;
    justify-content: center;
  }

  /* Concentric Radar Grid Rings */
  .track-radar-ring {
    position: absolute;
    border-radius: 50%;
    border: 1px dashed rgba(14, 105, 252, 0.45);
    pointer-events: none;
  }
  .track-radar-ring-1 { inset: 20px; }
  .track-radar-ring-2 { inset: 48px; border-style: dotted; }
  .track-radar-ring-3 { inset: 74px; border-color: rgba(56, 189, 248, 0.6); }

  /* Radar Crosshairs */
  .track-radar-crosshair-x {
    position: absolute;
    left: 10px; right: 10px; height: 1px;
    background: linear-gradient(90deg, transparent, rgba(14, 105, 252, 0.5), transparent);
  }
  .track-radar-crosshair-y {
    position: absolute;
    top: 10px; bottom: 10px; width: 1px;
    background: linear-gradient(180deg, transparent, rgba(14, 105, 252, 0.5), transparent);
  }

  /* 360-Degree Sweeping Radar Beam */
  .track-radar-sweep {
    position: absolute;
    inset: 0;
    border-radius: 50%;
    background: conic-gradient(from 0deg at 50% 50%, transparent 0deg, transparent 280deg, rgba(14, 105, 252, 0.12) 320deg, rgba(56, 189, 248, 0.65) 360deg);
    animation: radarSpin 2.4s linear infinite;
  }

  /* Center Pulse Wave Emitters */
  .track-pulse-wave {
    position: absolute;
    width: 24px;
    height: 24px;
    border-radius: 50%;
    border: 2px solid #38bdf8;
    box-shadow: 0 0 14px rgba(56, 189, 248, 0.6);
    opacity: 0;
    animation: centerPulse 2.4s cubic-bezier(0.1, 0.8, 0.2, 1) infinite;
  }
  .track-pulse-wave:nth-child(2) { animation-delay: 0.6s; }
  .track-pulse-wave:nth-child(3) { animation-delay: 1.2s; }
  .track-pulse-wave:nth-child(4) { animation-delay: 1.8s; }

  /* Center Satellite Core Orb */
  .track-center-core {
    position: absolute;
    width: 22px;
    height: 22px;
    border-radius: 50%;
    background: radial-gradient(circle at 35% 35%, #ffffff 0%, #38bdf8 40%, #0e69fc 90%);
    box-shadow: 0 0 16px #38bdf8, 0 0 30px rgba(14, 105, 252, 0.8);
    z-index: 5;
    animation: coreGlow 1.6s ease-in-out infinite alternate;
  }
  .track-center-core-beacon {
    position: absolute;
    width: 8px;
    height: 8px;
    background: #10b981;
    border-radius: 50%;
    top: -2px;
    right: -2px;
    box-shadow: 0 0 8px #10b981, 0 0 16px #10b981;
    animation: beaconBlink 0.9s infinite alternate;
  }

  @keyframes radarSpin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
  }
  @keyframes centerPulse {
    0% { transform: scale(0.4); opacity: 1; }
    100% { transform: scale(7.8); opacity: 0; }
  }
  @keyframes coreGlow {
    0% { transform: scale(0.92); box-shadow: 0 0 14px #38bdf8; }
    100% { transform: scale(1.1); box-shadow: 0 0 24px #38bdf8, 0 0 40px rgba(14, 105, 252, 0.9); }
  }
  @keyframes beaconBlink {
    0% { opacity: 0.4; transform: scale(0.8); }
    100% { opacity: 1; transform: scale(1.3); }
  }

  /* HUD Live Telemetry */
  .track-hud-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 3px 10px;
    border-radius: 20px;
    font-size: 10.5px;
    font-weight: 700;
    letter-spacing: 0.5px;
    text-transform: uppercase;
    background: var(--primary-soft);
    color: var(--primary);
    margin-bottom: 5px;
  }
  .track-hud-dot { width: 7px; height: 7px; background: #10b981; border-radius: 50%; box-shadow: 0 0 8px #10b981; animation: beaconBlink 0.9s infinite alternate; }

  /* 4-Second Dynamic Progress Bar */
  .track-hud-meter {
    height: 4px;
    width: 240px;
    max-width: 85%;
    margin: 10px auto 0;
    background: var(--border-color);
    border-radius: 99px;
    overflow: hidden;
    position: relative;
  }
  .track-hud-meter-fill {
    height: 100%;
    width: 0%;
    background: linear-gradient(90deg, var(--primary), #38bdf8, #10b981);
    border-radius: 99px;
    transition: width 0.3s ease;
  }

  /* Stepper Timeline */
  .track-stepper-wrap { padding: 20px 4px 10px; }
  .track-stepper { display: flex; justify-content: space-between; position: relative; }
  .track-stepper-line {
    position: absolute;
    top: 18px;
    left: 34px;
    right: 34px;
    height: 3px;
    background: var(--border-color);
    z-index: 1;
    overflow: hidden;
    border-radius: 99px;
  }
  .track-stepper-line-fill {
    height: 100%;
    width: 0%;
    background: var(--primary);
    border-radius: 99px;
    transition: width 0.4s ease;
  }
  .track-step-item {
    position: relative;
    z-index: 3;
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
    width: 68px;
  }
  .track-step-circle {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    background: var(--section-bg);
    border: 2px solid var(--border-color);
    color: var(--text-muted);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 13px;
    margin-bottom: 6px;
    transition: all 0.3s;
  }
  .track-step-item.completed .track-step-circle { background: var(--primary); border-color: var(--primary); color: #fff; box-shadow: 0 4px 12px rgba(49, 49, 255, 0.35); }
  .track-step-item.active .track-step-circle {
    background: var(--primary); border-color: var(--primary); color: #fff;
    box-shadow: 0 0 0 4px var(--primary-soft), 0 4px 14px rgba(49, 49, 255, 0.4);
    animation: stepPulse 2s infinite;
  }
  .track-step-item.cancelled .track-step-circle { background: #ef4444; border-color: #ef4444; color: #fff; box-shadow: 0 4px 12px rgba(239, 68, 68, 0.35); }
  @keyframes stepPulse { 0%, 100% { transform: scale(1); } 50% { transform: scale(1.08); } }

  .track-step-label { font-size: 11px; font-weight: 700; color: var(--text-muted); line-height: 1.2; }
  .track-step-item.completed .track-step-label, .track-step-item.active .track-step-label { color: var(--text-main); }
  .track-step-date { font-size: 9px; color: var(--text-muted); }

  /* Summary Card */
  .track-summary-card {
    background: var(--background);
    border: 1px solid var(--border-color);
    border-radius: 14px;
    padding: 14px 16px;
    margin-top: 12px;
  }

  @media(max-width: 575px) {
    .track-search-box { flex-direction: column; background: transparent; border: none; padding: 0; }
    .track-input { background: var(--background); border: 1.5px solid var(--border-color); border-radius: 10px; }
    .track-btn-submit { width: 100%; justify-content: center; padding: 10px; border-radius: 10px; }
    .track-step-item { width: 50px; }
    .track-step-circle { width: 30px; height: 30px; font-size: 12px; }
    .track-stepper-line { top: 15px; left: 25px; right: 25px; }
    .track-step-label { font-size: 9.5px; }
    .track-3d-stage { height: 155px; }
    .track-3d-ground { width: 165px; height: 165px; }
  }
</style>

<div class="track-modal-overlay" id="trackOrderModalOverlay" onclick="handleTrackModalBackdrop(event)">
  <div class="track-modal-card">
    
    {{-- Modal Header --}}
    <div class="track-modal-header">
      <button type="button" class="track-modal-close" onclick="closeTrackOrderModal()" title="Close">
        <i class="fas fa-times"></i>
      </button>
      <div class="d-flex align-items-center gap-2">
        <div style="width: 34px; height: 34px; border-radius: 10px; background: var(--primary-soft); color: var(--primary); display: flex; align-items: center; justify-content: center; font-size: 15px;">
          <i class="fas fa-location-crosshairs"></i>
        </div>
        <div>
          <h5 class="mb-0 fw-bold" style="font-family: 'El Messiri', sans-serif; color: var(--text-main); font-size: 18px;">
            Track Your Order
          </h5>
          <small class="text-muted" style="font-size: 11.5px;">Enter your Order ID (e.g. BRS1358701)</small>
        </div>
      </div>
    </div>

    {{-- Modal Body --}}
    <div class="track-modal-body">
      
      {{-- Search Bar --}}
      <form id="trackOrderForm" onsubmit="handleTrackOrderSubmit(event)">
        <div class="track-search-box">
          <input type="text" id="trackOrderQueryInput" class="track-input" placeholder="Enter Your Order ID" autocomplete="off" required>
          <button type="submit" class="track-btn-submit" id="trackSubmitBtn">
            <i class="fas fa-magnifying-glass"></i>
            <span>Track</span>
          </button>
        </div>
      </form>

      <div class="mt-3">
        {{-- Initial State --}}
        <div id="trackInitialState" class="text-center py-4 text-muted">
          <div style="width: 54px; height: 54px; border-radius: 50%; background: var(--primary-soft); color: var(--primary); display: inline-flex; align-items: center; justify-content: center; font-size: 22px; margin-bottom: 10px;">
            <i class="fas fa-truck-ramp-box"></i>
          </div>
          <h6 class="fw-bold mb-1" style="color: var(--text-main); font-size: 14px;">{{ config('app.name') }} Live Tracking</h6>
          <p class="mb-0 text-muted" style="font-size: 12px; max-width: 320px; margin: 0 auto;">
            Enter your Order ID to track real-time dispatch and delivery status.
          </p>
        </div>

        {{-- 3D Real-Time Center Pulse Radar Scanner --}}
        <div id="trackLoadingState" class="d-none text-center py-2">
          <div class="track-3d-stage">
            <div class="track-3d-ground">
              {{-- Concentric Radar Rings & Crosshairs --}}
              <div class="track-radar-ring track-radar-ring-1"></div>
              <div class="track-radar-ring track-radar-ring-2"></div>
              <div class="track-radar-ring track-radar-ring-3"></div>
              <div class="track-radar-crosshair-x"></div>
              <div class="track-radar-crosshair-y"></div>

              {{-- 360-Degree Sweeping Beam --}}
              <div class="track-radar-sweep"></div>

              {{-- Concentric Pulse Waves Emitting from Center --}}
              <div class="track-pulse-wave"></div>
              <div class="track-pulse-wave"></div>
              <div class="track-pulse-wave"></div>
              <div class="track-pulse-wave"></div>

              {{-- Center Glowing Satellite Core Orb --}}
              <div class="track-center-core">
                <span class="track-center-core-beacon"></span>
              </div>
            </div>
          </div>

          {{-- Live HUD Telemetry --}}
          <div>
            <div class="track-hud-badge">
              <span class="track-hud-dot"></span>
              <span>Satellite Pulse Live</span>
            </div>
            <h6 class="fw-bold mb-1" style="color: var(--text-main); font-size: 14px;" id="trackScanStatusText">
              Connecting to Global Logistics Satellite...
            </h6>
            <div class="text-muted" style="font-size: 11.5px;" id="trackScanSubText">
              Establishing encrypted telemetry link
            </div>

            {{-- Smooth 4-Second Fill Meter --}}
            <div class="track-hud-meter">
              <div class="track-hud-meter-fill" id="trackHudMeterFill"></div>
            </div>
          </div>
        </div>

        {{-- Error Alert --}}
        <div id="trackErrorAlert" class="d-none alert alert-danger d-flex align-items-center gap-2 p-3 rounded-3 mb-0" role="alert">
          <i class="fas fa-triangle-exclamation flex-shrink-0"></i>
          <div id="trackErrorMessage" style="font-size: 13px;"></div>
        </div>

        {{-- Order Result & Stepper --}}
        <div id="trackDetailsContent" class="d-none">
          <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 p-3 rounded-3 mb-2" style="background: var(--background); border: 1px solid var(--border-color);">
            <div>
              <div class="fw-bold" style="font-size: 15px; color: var(--text-main);">
                Order #<span id="trackDisplayId"></span>
              </div>
              <small class="text-muted" style="font-size: 11.5px;"><i class="fas fa-calendar-alt me-1"></i><span id="trackOrderDate"></span></small>
            </div>
            <div class="d-flex flex-column align-items-end">
              <span class="badge" id="trackStatusBadge" style="padding: 5px 10px; border-radius: 20px; font-size: 11.5px; font-weight: 700;"></span>
              <span class="fw-bold mt-1" id="trackNetTotal" style="color: var(--primary); font-size: 14.5px;"></span>
            </div>
          </div>

          {{-- Stepper --}}
          <div class="track-stepper-wrap">
            <div class="track-stepper" id="trackStepperNodes"></div>
          </div>

          {{-- Summary Details --}}
          <div class="track-summary-card">
            <div class="row g-3" style="font-size: 12.5px;">
              <div class="col-sm-6">
                <div class="d-flex align-items-center gap-2 mb-2">
                  <span style="width: 24px; height: 24px; border-radius: 6px; background: var(--primary-soft); color: var(--primary); display: inline-flex; align-items: center; justify-content: center; font-size: 11px;">
                    <i class="fas fa-location-dot"></i>
                  </span>
                  <strong style="color: var(--text-main);">Delivery Details</strong>
                </div>
                <div class="ps-1" style="line-height: 1.5;">
                  <div class="fw-bold" style="color: var(--text-main);" id="trackCustomerName"></div>
                  <div class="text-muted" id="trackCustomerPhone"></div>
                  <div class="text-muted mt-1" id="trackDeliveryAddress"></div>
                </div>
              </div>

              <div class="col-sm-6">
                <div class="d-flex align-items-center gap-2 mb-2">
                  <span style="width: 24px; height: 24px; border-radius: 6px; background: var(--primary-soft); color: var(--primary); display: inline-flex; align-items: center; justify-content: center; font-size: 11px;">
                    <i class="fas fa-file-invoice-dollar"></i>
                  </span>
                  <strong style="color: var(--text-main);">Order Overview</strong>
                </div>
                <div class="ps-1" style="line-height: 1.6;">
                  <div class="d-flex justify-content-between">
                    <span class="text-muted">Total Quantity:</span>
                    <strong style="color: var(--text-main);" id="trackSummaryItemsCount">0 items</strong>
                  </div>
                  <div class="d-flex justify-content-between">
                    <span class="text-muted">Payment Method:</span>
                    <strong style="color: var(--text-main);" id="trackPaymentMethod">COD</strong>
                  </div>
                  <div class="d-flex justify-content-between">
                    <span class="text-muted">Payment Status:</span>
                    <span id="trackPaymentStatus" class="fw-bold"></span>
                  </div>
                  <div class="d-flex justify-content-between mt-1 pt-1 border-top">
                    <span class="text-muted">Total Amount:</span>
                    <strong style="color: var(--primary); font-size: 13.5px;" id="trackSummaryAmount">৳0.00</strong>
                  </div>
                </div>
              </div>
            </div>

            <div id="trackDeliveryDateRow" class="d-none mt-2 pt-2 border-top text-success fw-bold d-flex align-items-center justify-content-between" style="font-size: 12px;">
              <span><i class="fas fa-circle-check me-1"></i> Delivered On:</span>
              <span id="trackDeliveredDate"></span>
            </div>
          </div>
        </div>

      </div>
    </div>

  </div>
</div>

@push('scripts')
<script>
window.openTrackOrderModal = function(prefillId = '') {
    const overlay = document.getElementById('trackOrderModalOverlay');
    const input = document.getElementById('trackOrderQueryInput');
    if (!overlay) return;
    overlay.classList.add('show');
    document.body.style.overflow = 'hidden';
    if (input) {
        if (prefillId) {
            input.value = prefillId;
            handleTrackOrderSubmit(null, prefillId);
        }
        setTimeout(() => input.focus(), 150);
    }
};

window.closeTrackOrderModal = function() {
    const overlay = document.getElementById('trackOrderModalOverlay');
    if (overlay) {
        overlay.classList.remove('show');
        document.body.style.overflow = '';
    }
};

window.handleTrackModalBackdrop = function(e) {
    if (e.target && e.target.id === 'trackOrderModalOverlay') closeTrackOrderModal();
};

document.addEventListener('keydown', e => {
    if (e.key === 'Escape') closeTrackOrderModal();
});

let trackTelemetryTimer = null;
let trackProgressTimer = null;

window.handleTrackOrderSubmit = async function(e, forcedQuery = '') {
    if (e) e.preventDefault();

    const input = document.getElementById('trackOrderQueryInput');
    const query = forcedQuery || (input ? input.value.trim() : '');
    if (!query) return;

    const el = id => document.getElementById(id);
    const submitBtn = el('trackSubmitBtn');
    const initialState = el('trackInitialState');
    const loadingState = el('trackLoadingState');
    const errorAlert = el('trackErrorAlert');
    const detailsContent = el('trackDetailsContent');
    const statusText = el('trackScanStatusText');
    const subText = el('trackScanSubText');
    const meterFill = el('trackHudMeterFill');

    if (initialState) initialState.classList.add('d-none');
    if (errorAlert) errorAlert.classList.add('d-none');
    if (detailsContent) detailsContent.classList.add('d-none');
    if (loadingState) loadingState.classList.remove('d-none');
    
    if (submitBtn) {
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-radar fa-spin"></i><span>Tracking (4s)...</span>';
    }

    if (meterFill) meterFill.style.width = '0%';

    // 5-Stage 4-Second Realistic Telemetry Sequence (every 800ms)
    if (trackTelemetryTimer) clearInterval(trackTelemetryTimer);
    if (trackProgressTimer) clearInterval(trackProgressTimer);

    const steps = [
        { main: 'Connecting to Global Logistics Satellite...', sub: 'Establishing encrypted telemetry link', pct: 20 },
        { main: 'Scanning Regional Dispatch Nodes...', sub: 'Triangulating package GPS coordinates', pct: 45 },
        { main: `Locating Order #${query}...`, sub: 'Matching transit checkpoints & hubs', pct: 70 },
        { main: 'Verifying Real-time Milestone Status...', sub: 'Authenticating delivery logs', pct: 90 },
        { main: 'Live Telemetry Synchronized!', sub: 'Preparing shipment overview', pct: 100 },
    ];

    let stepIdx = 0;
    if (statusText) statusText.innerText = steps[0].main;
    if (subText) subText.innerText = steps[0].sub;
    if (meterFill) meterFill.style.width = steps[0].pct + '%';

    trackTelemetryTimer = setInterval(() => {
        stepIdx++;
        if (stepIdx < steps.length) {
            if (statusText) statusText.innerText = steps[stepIdx].main;
            if (subText) subText.innerText = steps[stepIdx].sub;
            if (meterFill) meterFill.style.width = steps[stepIdx].pct + '%';
        }
    }, 800);

    try {
        // 4.0 Seconds Total Animation Load Time
        const [data] = await Promise.all([
            fetch("{{ route('order.track') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ query })
            }).then(r => r.json()),
            new Promise(res => setTimeout(res, 4000))
        ]);

        if (trackTelemetryTimer) clearInterval(trackTelemetryTimer);
        if (loadingState) loadingState.classList.add('d-none');

        if (!data.success) {
            if (el('trackErrorMessage')) el('trackErrorMessage').innerText = data.message || 'Order could not be found.';
            if (errorAlert) errorAlert.classList.remove('d-none');
            return;
        }

        renderSingleOrderTracking(data.order);
    } catch (err) {
        if (trackTelemetryTimer) clearInterval(trackTelemetryTimer);
        if (loadingState) loadingState.classList.add('d-none');
        if (el('trackErrorMessage')) el('trackErrorMessage').innerText = 'Unable to connect to server. Please try again.';
        if (errorAlert) errorAlert.classList.remove('d-none');
    } finally {
        if (submitBtn) {
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="fas fa-magnifying-glass"></i><span>Track</span>';
        }
    }
};

function renderSingleOrderTracking(order) {
    const el = id => document.getElementById(id);
    const detailsContent = el('trackDetailsContent');
    if (!detailsContent) return;

    el('trackDisplayId').innerText = order.order_id;
    el('trackOrderDate').innerText = order.created_at;
    el('trackNetTotal').innerText = order.net_total_formatted;

    const statusBadge = el('trackStatusBadge');
    if (statusBadge) {
        statusBadge.innerText = order.status_label;
        statusBadge.style.background = order.status_bg;
        statusBadge.style.color = order.status_color;
    }

    el('trackCustomerName').innerText = order.customer_name || 'Customer';
    el('trackCustomerPhone').innerText = order.customer_phone || '';
    el('trackDeliveryAddress').innerText = order.delivery_address || 'Standard Delivery';
    
    el('trackSummaryItemsCount').innerText = (order.total_items_count || 1) + ' items';
    el('trackPaymentMethod').innerText = order.payment_method || 'COD';
    el('trackSummaryAmount').innerText = order.net_total_formatted;
    
    const paymentStatusEl = el('trackPaymentStatus');
    if (paymentStatusEl) {
        paymentStatusEl.innerText = order.payment_status;
        paymentStatusEl.className = order.payment_status.toLowerCase() === 'paid' ? 'text-success fw-bold' : 'text-warning fw-bold';
    }

    const deliveryRow = el('trackDeliveryDateRow');
    if (deliveryRow) {
        if (order.delivered_at) {
            el('trackDeliveredDate').innerText = order.delivered_at;
            deliveryRow.classList.remove('d-none');
        } else {
            deliveryRow.classList.add('d-none');
        }
    }

    renderStepper(order.steps, order.is_cancelled);
    detailsContent.classList.remove('d-none');
}

function renderStepper(steps, isCancelled) {
    const stepperNode = document.getElementById('trackStepperNodes');
    if (!stepperNode || !steps || !steps.length) return;

    stepperNode.innerHTML = '';

    const completedCount = steps.filter(s => s.completed).length;
    const progressPercent = steps.length > 1 ? ((completedCount - 1) / (steps.length - 1)) * 100 : 0;

    // Track line bounded strictly between the first and last step circle centers
    const lineWrap = document.createElement('div');
    lineWrap.className = 'track-stepper-line';
    
    const lineFill = document.createElement('div');
    lineFill.className = 'track-stepper-line-fill';
    lineFill.style.width = isCancelled ? '25%' : (progressPercent + '%');
    if (isCancelled) lineFill.style.background = '#ef4444';
    
    lineWrap.appendChild(lineFill);
    stepperNode.appendChild(lineWrap);

    steps.forEach(step => {
        const stepDiv = document.createElement('div');
        let classes = 'track-step-item';
        if (step.is_danger || step.key === 'cancelled') classes += ' cancelled';
        else if (step.completed) classes += ' completed';
        else if (step.active) classes += ' active';
        stepDiv.className = classes;

        const iconMarkup = (step.completed && !step.is_danger && step.key !== 'cancelled') 
            ? '<i class="fas fa-check"></i>' 
            : `<i class="${step.icon}"></i>`;

        stepDiv.innerHTML = `
            <div class="track-step-circle">${iconMarkup}</div>
            <div class="track-step-label">${step.title}</div>
            <div class="track-step-date">${step.date || ''}</div>
        `;
        stepperNode.appendChild(stepDiv);
    });
}
</script>
@endpush
