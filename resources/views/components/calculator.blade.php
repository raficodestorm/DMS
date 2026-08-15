<!-- Floating Draggable & Resizable 3D Calculator Component -->
<div id="dmsCalculatorWrapper" class="dms-calc-container hidden" aria-hidden="true">
  <div id="dmsCalculator" class="dms-calc-card">
    
    <!-- Top Header / Drag Bar -->
    <div class="dms-calc-header" id="dmsCalcHeader">
      <div class="dms-calc-title">
        
        <span>{{ config('app.name') }} Calculator</span>
        <span id="dmsCalcMemBadge" class="dms-calc-mem-badge hidden">M</span>
      </div>
      <div class="dms-calc-controls">
        <button type="button" class="dms-calc-btn-icon" id="dmsCalcHistoryBtn" title="Calculation History" onclick="dmsCalcToggleHistory(event)">
          <i class="fa-solid fa-clock-rotate-left"></i>
        </button>
        <button type="button" class="dms-calc-btn-icon" id="dmsCalcCopyBtn" title="Copy Result" onclick="dmsCalcCopyResult(event)">
          <i class="fa-regular fa-copy"></i>
        </button>
        <button type="button" class="dms-calc-btn-icon" title="Minimize / Restore" onclick="dmsCalcToggleMinimize(event)">
          <i class="fa-solid fa-minus"></i>
        </button>
        <button type="button" class="dms-calc-btn-icon dms-calc-close" title="Close" onclick="toggleCalculator(event)">
          <i class="fa-solid fa-xmark"></i>
        </button>
      </div>
    </div>

    <!-- Main Content Area -->
    <div class="dms-calc-body" id="dmsCalcBody">
      <!-- 3D Sunken Digital Screen -->
      <div class="dms-calc-display">
        <div class="dms-calc-expression" id="dmsCalcExpression" title="Formula display"></div>
        <div class="dms-calc-result-row">
          <span class="dms-calc-op-indicator" id="dmsCalcOpIndicator"></span>
          <input type="text" id="dmsCalcInput" class="dms-calc-result" value="0" readonly aria-label="Calculator Result" />
        </div>
      </div>

      <!-- Toast Feedback -->
      <div id="dmsCalcToast" class="dms-calc-toast">Copied to clipboard!</div>

      <!-- History Panel (Slide down drawer) -->
      <div id="dmsCalcHistoryDrawer" class="dms-calc-history-drawer hidden">
        <div class="dms-calc-history-header">
          <span>Recent Calculations</span>
          <button type="button" class="dms-calc-text-btn" onclick="dmsCalcClearHistory()">Clear</button>
        </div>
        <div class="dms-calc-history-list" id="dmsCalcHistoryList">
          <div class="dms-calc-history-empty">No calculations yet</div>
        </div>
      </div>

      <!-- Keypad Grid -->
      <div class="dms-calc-keypad">
        <!-- Row 1: Memory Row -->
        <button type="button" class="dms-calc-key key-mem" onclick="dmsCalcMemory('MC')" title="Memory Clear">MC</button>
        <button type="button" class="dms-calc-key key-mem" onclick="dmsCalcMemory('MR')" title="Memory Recall">MR</button>
        <button type="button" class="dms-calc-key key-mem" onclick="dmsCalcMemory('M+')" title="Memory Add">M+</button>
        <button type="button" class="dms-calc-key key-mem" onclick="dmsCalcMemory('M-')" title="Memory Subtract">M-</button>

        <!-- Row 2: Function Row 1 -->
        <button type="button" class="dms-calc-key key-func key-danger" onclick="dmsCalcClearAll()" title="All Clear (Esc)">AC</button>
        <button type="button" class="dms-calc-key key-func" onclick="dmsCalcClearEntry()" title="Clear Entry">C</button>
        <button type="button" class="dms-calc-key key-func" onclick="dmsCalcBackspace()" title="Backspace (Delete)"><i class="fa-solid fa-backspace"></i></button>
        <button type="button" class="dms-calc-key key-operator" onclick="dmsCalcInputOp('/')" title="Divide">÷</button>

        <!-- Row 3: Function Row 2 -->
        <button type="button" class="dms-calc-key key-fn-sm" onclick="dmsCalcSquareRoot()" title="Square Root">√</button>
        <button type="button" class="dms-calc-key key-fn-sm" onclick="dmsCalcSquare()" title="Square (x²)">x²</button>
        <button type="button" class="dms-calc-key key-fn-sm" onclick="dmsCalcPercentage()" title="Percentage">%</button>
        <button type="button" class="dms-calc-key key-operator" onclick="dmsCalcInputOp('*')" title="Multiply">×</button>

        <!-- Row 4: Digits 7-9 & Subtract -->
        <button type="button" class="dms-calc-key key-num" onclick="dmsCalcInputDigit('7')">7</button>
        <button type="button" class="dms-calc-key key-num" onclick="dmsCalcInputDigit('8')">8</button>
        <button type="button" class="dms-calc-key key-num" onclick="dmsCalcInputDigit('9')">9</button>
        <button type="button" class="dms-calc-key key-operator" onclick="dmsCalcInputOp('-')" title="Subtract">−</button>

        <!-- Row 5: Digits 4-6 & Add -->
        <button type="button" class="dms-calc-key key-num" onclick="dmsCalcInputDigit('4')">4</button>
        <button type="button" class="dms-calc-key key-num" onclick="dmsCalcInputDigit('5')">5</button>
        <button type="button" class="dms-calc-key key-num" onclick="dmsCalcInputDigit('6')">6</button>
        <button type="button" class="dms-calc-key key-operator" onclick="dmsCalcInputOp('+')" title="Add">+</button>

        <!-- Row 6 & 7: Digits 1-3, 0, dot, +/- & Equals -->
        <button type="button" class="dms-calc-key key-num" onclick="dmsCalcInputDigit('1')">1</button>
        <button type="button" class="dms-calc-key key-num" onclick="dmsCalcInputDigit('2')">2</button>
        <button type="button" class="dms-calc-key key-num" onclick="dmsCalcInputDigit('3')">3</button>
        <button type="button" class="dms-calc-key key-equals" onclick="dmsCalcCalculate()" title="Calculate (Enter)">=</button>

        <button type="button" class="dms-calc-key key-num" onclick="dmsCalcToggleSign()" title="Negate (+/-)">±</button>
        <button type="button" class="dms-calc-key key-num" onclick="dmsCalcInputDigit('0')">0</button>
        <button type="button" class="dms-calc-key key-num" onclick="dmsCalcInputDot('.')">.</button>
      </div>
    </div>

    <!-- Active JS Resizer Corner Handle -->
    <div id="dmsCalcResizeHandle" class="dms-calc-resize-handle" title="Drag to resize calculator window">
      <svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path d="M11 2L2 11M11 6L6 11M11 10L10 11" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
      </svg>
    </div>
  </div>
</div>

<style>
  /* Base Floating Calculator Container */
  .dms-calc-container {
    position: fixed;
    top: 80px;
    right: 25px;
    z-index: 99999;
    user-select: none;
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
    transition: opacity 0.2s ease;
    touch-action: none;
  }

  .dms-calc-container.hidden {
    display: none !important;
    opacity: 0;
    pointer-events: none;
  }

  /* 3D Realistic Card Chassis */
  .dms-calc-card {
    width: 330px;
    height: 520px;
    min-width: 280px;
    max-width: 650px;
    min-height: 440px;
    max-height: 850px;
    background: linear-gradient(145deg, #1e2430 0%, #11151c 100%);
    border: 2px solid #2d3748;
    border-radius: 22px;
    box-shadow: 
      0 25px 65px rgba(0, 0, 0, 0.85), 
      0 10px 20px rgba(0, 0, 0, 0.5),
      inset 0 1px 1px rgba(255, 255, 255, 0.25),
      inset 0 -3px 6px rgba(0, 0, 0, 0.8);
    display: flex;
    flex-direction: column;
    overflow: hidden;
    position: relative;
    color: #e2e8f0;
    box-sizing: border-box;
  }

  /* Header / Drag Bar */
  .dms-calc-header {
    background: linear-gradient(180deg, rgba(255, 255, 255, 0.08) 0%, rgba(255, 255, 255, 0.01) 100%);
    border-bottom: 1px solid rgba(255, 255, 255, 0.07);
    padding: 10px 14px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    cursor: grab;
    flex-shrink: 0;
  }

  .dms-calc-header:active {
    cursor: grabbing;
  }

  .dms-calc-title {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 13px;
    font-weight: 700;
    letter-spacing: 0.5px;
    color: #f1f5f9;
    text-shadow: 0 1px 2px rgba(0, 0, 0, 0.8);
  }

  .dms-calc-icon {
    color: #3b82f6;
    font-size: 14px;
    filter: drop-shadow(0 0 4px rgba(59, 130, 246, 0.5));
  }

  .dms-calc-mem-badge {
    background: #3b82f6;
    color: #fff;
    font-size: 10px;
    font-weight: 800;
    padding: 1px 5px;
    border-radius: 4px;
    box-shadow: 0 0 8px rgba(59, 130, 246, 0.6);
  }

  .dms-calc-mem-badge.hidden {
    display: none;
  }

  .dms-calc-controls {
    display: flex;
    align-items: center;
    gap: 4px;
  }

  .dms-calc-btn-icon {
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid rgba(255, 255, 255, 0.08);
    color: #94a3b8;
    width: 26px;
    height: 26px;
    border-radius: 6px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    font-size: 12px;
    transition: all 0.15s ease;
    box-shadow: 0 2px 4px rgba(0,0,0,0.3);
  }

  .dms-calc-btn-icon:hover {
    background: rgba(255, 255, 255, 0.14);
    color: #f8fafc;
  }

  .dms-calc-close:hover {
    background: #ef4444;
    border-color: #f87171;
    color: #ffffff;
  }

  /* Body Container */
  .dms-calc-body {
    padding: 14px;
    display: flex;
    flex-direction: column;
    flex: 1;
    gap: 12px;
    overflow: hidden;
    min-height: 0;
  }

  .dms-calc-card.minimized .dms-calc-body {
    display: none !important;
  }
  .dms-calc-card.minimized {
    min-height: initial !important;
    height: auto !important;
  }

  /* 3D Sunken LCD Display */
  .dms-calc-display {
    background: radial-gradient(circle at 50% 30%, #0d212c 0%, #040c12 100%);
    border: 3px solid #1a2332;
    border-radius: 14px;
    padding: 10px 14px;
    text-align: right;
    box-shadow: 
      inset 0 5px 12px rgba(0, 0, 0, 0.95), 
      inset 0 1px 3px rgba(0, 0, 0, 0.8),
      0 1px 0 rgba(255, 255, 255, 0.1);
    display: flex;
    flex-direction: column;
    justify-content: center;
    flex-shrink: 0;
    min-height: 80px;
    position: relative;
  }

  .dms-calc-expression {
    font-size: 12px;
    color: #475569;
    min-height: 18px;
    word-break: break-all;
    overflow-x: auto;
    white-space: nowrap;
    scrollbar-width: none;
    font-family: 'Consolas', monospace;
  }

  .dms-calc-expression::-webkit-scrollbar {
    display: none;
  }

  .dms-calc-result-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 8px;
    margin-top: 2px;
  }

  .dms-calc-op-indicator {
    font-size: 18px;
    font-weight: 800;
    color: #38bdf8;
    min-width: 15px;
    text-shadow: 0 0 10px rgba(56, 189, 248, 0.7);
  }

  .dms-calc-result {
    background: transparent;
    border: none;
    outline: none;
    color: #38bdf8;
    font-size: clamp(24px, 4.5vw, 34px);
    font-weight: 700;
    font-family: 'Consolas', 'Courier New', monospace;
    width: 100%;
    text-align: right;
    text-shadow: 0 0 12px rgba(56, 189, 248, 0.6), 0 0 25px rgba(56, 189, 248, 0.3);
  }

  /* Toast Notification */
  .dms-calc-toast {
    position: absolute;
    top: 60px;
    left: 50%;
    transform: translateX(-50%) translateY(-10px);
    background: #10b981;
    color: #fff;
    font-size: 11px;
    font-weight: 700;
    padding: 5px 14px;
    border-radius: 20px;
    opacity: 0;
    pointer-events: none;
    transition: all 0.25s ease;
    z-index: 10;
    box-shadow: 0 4px 14px rgba(16, 185, 129, 0.5);
  }

  .dms-calc-toast.show {
    opacity: 1;
    transform: translateX(-50%) translateY(0);
  }

  /* History Drawer */
  .dms-calc-history-drawer {
    background: rgba(15, 23, 42, 0.97);
    border: 1px solid rgba(255, 255, 255, 0.12);
    border-radius: 12px;
    padding: 10px;
    max-height: 140px;
    overflow-y: auto;
    font-size: 12px;
    transition: all 0.2s ease;
    flex-shrink: 0;
    box-shadow: 0 8px 20px rgba(0,0,0,0.6);
  }

  .dms-calc-history-drawer.hidden {
    display: none;
  }

  .dms-calc-history-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    color: #94a3b8;
    font-weight: 600;
    margin-bottom: 6px;
    font-size: 11px;
    text-transform: uppercase;
  }

  .dms-calc-text-btn {
    background: none;
    border: none;
    color: #ef4444;
    cursor: pointer;
    font-size: 11px;
    padding: 0;
  }

  .dms-calc-history-item {
    padding: 6px 8px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.05);
    cursor: pointer;
    border-radius: 4px;
    display: flex;
    flex-direction: column;
    align-items: flex-end;
  }

  .dms-calc-history-item:hover {
    background: rgba(255, 255, 255, 0.08);
  }

  .dms-calc-history-exp {
    color: #64748b;
    font-size: 11px;
  }

  .dms-calc-history-val {
    color: #38bdf8;
    font-weight: 700;
  }

  .dms-calc-history-empty {
    text-align: center;
    color: #475569;
    padding: 10px;
  }

  /* 3D Keypad Grid */
  .dms-calc-keypad {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    grid-template-rows: repeat(7, 1fr);
    gap: 10px;
    flex: 1;
    min-height: 0;
    padding: 4px 0;
  }

  /* Base 3D Tactile Key Styling */
  .dms-calc-key {
    background: linear-gradient(180deg, #334155 0%, #1e293b 100%);
    border: 1px solid #475569;
    color: #f8fafc;
    font-size: clamp(14px, 2.2vh, 18px);
    font-weight: 700;
    border-radius: 12px;
    height: 100%;
    width: 100%;
    padding: 0;
    margin: 0;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: transform 0.08s ease, box-shadow 0.08s ease, background 0.15s ease;
    box-shadow: 
      0 5px 0 #0f172a, 
      0 8px 12px rgba(0, 0, 0, 0.5), 
      inset 0 1px 1px rgba(255, 255, 255, 0.3);
    touch-action: manipulation;
    box-sizing: border-box;
    position: relative;
  }

  .dms-calc-key:hover {
    transform: translateY(-2px);
    box-shadow: 
      0 7px 0 #0f172a, 
      0 11px 16px rgba(0, 0, 0, 0.6), 
      inset 0 1px 1px rgba(255, 255, 255, 0.4);
  }

  .dms-calc-key:active {
    transform: translateY(4px) !important;
    box-shadow: 
      0 1px 0 #0f172a, 
      0 2px 4px rgba(0, 0, 0, 0.6), 
      inset 0 2px 5px rgba(0, 0, 0, 0.6) !important;
  }

  /* 3D Color Themes for Key Categories */
  .key-mem {
    background: linear-gradient(180deg, #1e3a8a 0%, #172554 100%);
    border-color: #2563eb;
    color: #93c5fd;
    font-size: clamp(11px, 1.8vh, 13px);
    box-shadow: 0 5px 0 #0f172a, 0 8px 12px rgba(0, 0, 0, 0.5), inset 0 1px 1px rgba(255, 255, 255, 0.3);
  }
  .key-mem:hover {
    background: linear-gradient(180deg, #2563eb 0%, #1e40af 100%);
  }

  .key-func {
    background: linear-gradient(180deg, #475569 0%, #334155 100%);
    border-color: #64748b;
    color: #e2e8f0;
    box-shadow: 0 5px 0 #1e293b, 0 8px 12px rgba(0, 0, 0, 0.5), inset 0 1px 1px rgba(255, 255, 255, 0.3);
  }

  .key-danger {
    background: linear-gradient(180deg, #dc2626 0%, #991b1b 100%);
    border-color: #f87171;
    color: #ffffff;
    box-shadow: 0 5px 0 #7f1d1d, 0 8px 12px rgba(0, 0, 0, 0.5), inset 0 1px 1px rgba(255, 255, 255, 0.4);
  }
  .key-danger:hover {
    background: linear-gradient(180deg, #ef4444 0%, #b91c1c 100%);
  }

  .key-operator {
    background: linear-gradient(180deg, #d97706 0%, #92400e 100%);
    border-color: #fbbf24;
    color: #ffffff;
    font-size: clamp(16px, 2.5vh, 20px);
    box-shadow: 0 5px 0 #78350f, 0 8px 12px rgba(0, 0, 0, 0.5), inset 0 1px 1px rgba(255, 255, 255, 0.4);
  }
  .key-operator:hover {
    background: linear-gradient(180deg, #f59e0b 0%, #b45309 100%);
  }

  .key-fn-sm {
    background: linear-gradient(180deg, #7e22ce 0%, #581c87 100%);
    border-color: #c084fc;
    color: #f3e8ff;
    font-size: clamp(12px, 1.9vh, 15px);
    box-shadow: 0 5px 0 #3b0764, 0 8px 12px rgba(0, 0, 0, 0.5), inset 0 1px 1px rgba(255, 255, 255, 0.3);
  }
  .key-fn-sm:hover {
    background: linear-gradient(180deg, #9333ea 0%, #6b21a8 100%);
  }

  .key-equals {
    grid-row: span 2;
    background: linear-gradient(180deg, #2563eb 0%, #1d4ed8 100%);
    border-color: #60a5fa;
    color: #ffffff;
    font-size: clamp(18px, 2.8vh, 24px);
    font-weight: 800;
    box-shadow: 
      0 5px 0 #1e3a8a, 
      0 10px 16px rgba(37, 99, 235, 0.5), 
      inset 0 1px 1px rgba(255, 255, 255, 0.4);
  }
  .key-equals:hover {
    background: linear-gradient(180deg, #3b82f6 0%, #2563eb 100%);
  }

  /* Custom Resizer Handle */
  .dms-calc-resize-handle {
    position: absolute;
    bottom: 0;
    right: 0;
    width: 24px;
    height: 24px;
    color: rgba(255, 255, 255, 0.4);
    cursor: nwse-resize;
    display: flex;
    align-items: flex-end;
    justify-content: flex-end;
    padding: 3px;
    z-index: 20;
    touch-action: none;
    transition: color 0.15s ease;
  }

  .dms-calc-resize-handle:hover,
  .dms-calc-resize-handle:active {
    color: #3b82f6;
  }

  /* Responsive Screen Adjustments */
  @media (max-width: 576px) {
    .dms-calc-container {
      top: 65px !important;
      left: 10px !important;
      right: 10px !important;
      width: calc(100vw - 20px) !important;
    }
    .dms-calc-card {
      width: 100% !important;
      height: 490px !important;
      max-height: calc(100vh - 80px) !important;
    }
    .dms-calc-solar {
      display: none;
    }
  }
</style>

<script>
  (function () {
    "use strict";

    // Internal State
    let dmsCalcState = {
      currentValue: '0',
      previousValue: null,
      operator: null,
      expression: '',
      waitingForOperand: false,
      memory: 0,
      history: []
    };

    // DOM Elements
    let wrapper, card, header, resizer, inputEl, exprEl, opIndicatorEl, memBadgeEl, toastEl, historyDrawer, historyList;

    function initElements() {
      wrapper = document.getElementById('dmsCalculatorWrapper');
      card = document.getElementById('dmsCalculator');
      header = document.getElementById('dmsCalcHeader');
      resizer = document.getElementById('dmsCalcResizeHandle');
      inputEl = document.getElementById('dmsCalcInput');
      exprEl = document.getElementById('dmsCalcExpression');
      opIndicatorEl = document.getElementById('dmsCalcOpIndicator');
      memBadgeEl = document.getElementById('dmsCalcMemBadge');
      toastEl = document.getElementById('dmsCalcToast');
      historyDrawer = document.getElementById('dmsCalcHistoryDrawer');
      historyList = document.getElementById('dmsCalcHistoryList');
    }

    // Toggle Visibility
    window.toggleCalculator = function (e) {
      if (e) e.stopPropagation();
      if (!wrapper) initElements();
      if (!wrapper) return;

      const isHidden = wrapper.classList.contains('hidden');
      if (isHidden) {
        wrapper.classList.remove('hidden');
        wrapper.setAttribute('aria-hidden', 'false');
        restoreState();
      } else {
        wrapper.classList.add('hidden');
        wrapper.setAttribute('aria-hidden', 'true');
      }
    };

    function restoreState() {
      const savedPos = localStorage.getItem('dms_calc_pos');
      if (savedPos) {
        try {
          const { left, top } = JSON.parse(savedPos);
          wrapper.style.left = left;
          wrapper.style.top = top;
          wrapper.style.right = 'auto';
        } catch(err) {}
      }

      const savedSize = localStorage.getItem('dms_calc_size');
      if (savedSize && card) {
        try {
          const { width, height } = JSON.parse(savedSize);
          if (width) card.style.width = width;
          if (height) card.style.height = height;
        } catch(err) {}
      }
    }

    // Toggle Minimize
    window.dmsCalcToggleMinimize = function (e) {
      if (e) e.stopPropagation();
      if (!card) initElements();
      card.classList.toggle('minimized');
    };

    // Copy Result
    window.dmsCalcCopyResult = function (e) {
      if (e) e.stopPropagation();
      if (!inputEl) initElements();
      const val = inputEl.value;
      navigator.clipboard.writeText(val).then(() => {
        if (toastEl) {
          toastEl.classList.add('show');
          setTimeout(() => toastEl.classList.remove('show'), 1800);
        }
      });
    };

    // Toggle History Drawer
    window.dmsCalcToggleHistory = function (e) {
      if (e) e.stopPropagation();
      if (!historyDrawer) initElements();
      historyDrawer.classList.toggle('hidden');
    };

    // Clear History
    window.dmsCalcClearHistory = function () {
      dmsCalcState.history = [];
      renderHistory();
    };

    function renderHistory() {
      if (!historyList) return;
      if (dmsCalcState.history.length === 0) {
        historyList.innerHTML = '<div class="dms-calc-history-empty">No calculations yet</div>';
        return;
      }
      historyList.innerHTML = dmsCalcState.history.map(item => `
        <div class="dms-calc-history-item" onclick="dmsCalcUseHistoryVal('${item.val}')">
          <span class="dms-calc-history-exp">${item.exp} =</span>
          <span class="dms-calc-history-val">${item.val}</span>
        </div>
      `).join('');
    }

    window.dmsCalcUseHistoryVal = function (val) {
      dmsCalcState.currentValue = String(val);
      dmsCalcState.waitingForOperand = false;
      updateDisplay();
      if (historyDrawer) historyDrawer.classList.add('hidden');
    };

    // Update Display UI
    function updateDisplay() {
      if (!inputEl) initElements();
      if (!inputEl) return;

      inputEl.value = formatNumber(dmsCalcState.currentValue);
      exprEl.innerText = dmsCalcState.expression;
      opIndicatorEl.innerText = dmsCalcState.operator ? getOpSymbol(dmsCalcState.operator) : '';

      if (dmsCalcState.memory !== 0) {
        memBadgeEl.classList.remove('hidden');
      } else {
        memBadgeEl.classList.add('hidden');
      }
    }

    function getOpSymbol(op) {
      switch (op) {
        case '+': return '+';
        case '-': return '−';
        case '*': return '×';
        case '/': return '÷';
        default: return '';
      }
    }

    function formatNumber(numStr) {
      if (numStr === 'Error' || numStr === 'NaN') return 'Error';
      if (numStr.endsWith('.')) return numStr;
      return numStr;
    }

    // Input Handling
    window.dmsCalcInputDigit = function (digit) {
      if (dmsCalcState.waitingForOperand) {
        dmsCalcState.currentValue = digit;
        dmsCalcState.waitingForOperand = false;
      } else {
        dmsCalcState.currentValue = dmsCalcState.currentValue === '0' ? digit : dmsCalcState.currentValue + digit;
      }
      updateDisplay();
    };

    window.dmsCalcInputDot = function () {
      if (dmsCalcState.waitingForOperand) {
        dmsCalcState.currentValue = '0.';
        dmsCalcState.waitingForOperand = false;
      } else if (!dmsCalcState.currentValue.includes('.')) {
        dmsCalcState.currentValue += '.';
      }
      updateDisplay();
    };

    window.dmsCalcInputOp = function (nextOperator) {
      const inputValue = parseFloat(dmsCalcState.currentValue);

      if (dmsCalcState.operator && dmsCalcState.waitingForOperand) {
        dmsCalcState.operator = nextOperator;
        dmsCalcState.expression = `${formatNumber(String(dmsCalcState.previousValue))} ${getOpSymbol(nextOperator)}`;
        updateDisplay();
        return;
      }

      if (dmsCalcState.previousValue == null) {
        dmsCalcState.previousValue = inputValue;
      } else if (dmsCalcState.operator) {
        const currentResult = performCalculation(dmsCalcState.previousValue, inputValue, dmsCalcState.operator);
        dmsCalcState.currentValue = String(currentResult);
        dmsCalcState.previousValue = currentResult;
      }

      dmsCalcState.waitingForOperand = true;
      dmsCalcState.operator = nextOperator;
      dmsCalcState.expression = `${formatNumber(String(dmsCalcState.previousValue))} ${getOpSymbol(nextOperator)}`;
      updateDisplay();
    };

    function performCalculation(first, second, op) {
      switch (op) {
        case '+': return first + second;
        case '-': return first - second;
        case '*': return first * second;
        case '/': return second !== 0 ? first / second : 'Error';
        default: return second;
      }
    }

    window.dmsCalcCalculate = function () {
      if (!dmsCalcState.operator || dmsCalcState.previousValue == null) return;

      const inputValue = parseFloat(dmsCalcState.currentValue);
      const fullExp = `${formatNumber(String(dmsCalcState.previousValue))} ${getOpSymbol(dmsCalcState.operator)} ${formatNumber(String(inputValue))}`;
      const result = performCalculation(dmsCalcState.previousValue, inputValue, dmsCalcState.operator);

      const formattedResult = typeof result === 'number' ? Math.round(result * 1e10) / 1e10 : result;

      // Log to history
      dmsCalcState.history.unshift({ exp: fullExp, val: String(formattedResult) });
      if (dmsCalcState.history.length > 20) dmsCalcState.history.pop();
      renderHistory();

      dmsCalcState.expression = `${fullExp} =`;
      dmsCalcState.currentValue = String(formattedResult);
      dmsCalcState.previousValue = null;
      dmsCalcState.operator = null;
      dmsCalcState.waitingForOperand = true;
      updateDisplay();
    };

    // Percentage functionality (%)
    window.dmsCalcPercentage = function () {
      const current = parseFloat(dmsCalcState.currentValue);
      if (isNaN(current)) return;

      let result;
      if (dmsCalcState.previousValue != null && dmsCalcState.operator) {
        if (dmsCalcState.operator === '+' || dmsCalcState.operator === '-') {
          result = (dmsCalcState.previousValue * current) / 100;
        } else if (dmsCalcState.operator === '*' || dmsCalcState.operator === '/') {
          result = current / 100;
        }
      } else {
        result = current / 100;
      }

      dmsCalcState.currentValue = String(Math.round(result * 1e10) / 1e10);
      updateDisplay();
    };

    // Square Root
    window.dmsCalcSquareRoot = function () {
      const current = parseFloat(dmsCalcState.currentValue);
      if (current < 0) {
        dmsCalcState.currentValue = 'Error';
      } else {
        dmsCalcState.currentValue = String(Math.round(Math.sqrt(current) * 1e10) / 1e10);
      }
      updateDisplay();
    };

    // Square (x²)
    window.dmsCalcSquare = function () {
      const current = parseFloat(dmsCalcState.currentValue);
      dmsCalcState.currentValue = String(Math.round(Math.pow(current, 2) * 1e10) / 1e10);
      updateDisplay();
    };

    // Toggle Sign (+/-)
    window.dmsCalcToggleSign = function () {
      const current = parseFloat(dmsCalcState.currentValue);
      dmsCalcState.currentValue = String(current * -1);
      updateDisplay();
    };

    // Clear Options
    window.dmsCalcClearAll = function () {
      dmsCalcState.currentValue = '0';
      dmsCalcState.previousValue = null;
      dmsCalcState.operator = null;
      dmsCalcState.expression = '';
      dmsCalcState.waitingForOperand = false;
      updateDisplay();
    };

    window.dmsCalcClearEntry = function () {
      dmsCalcState.currentValue = '0';
      updateDisplay();
    };

    window.dmsCalcBackspace = function () {
      if (dmsCalcState.waitingForOperand) return;
      if (dmsCalcState.currentValue.length > 1) {
        dmsCalcState.currentValue = dmsCalcState.currentValue.slice(0, -1);
      } else {
        dmsCalcState.currentValue = '0';
      }
      updateDisplay();
    };

    // Memory operations
    window.dmsCalcMemory = function (type) {
      const current = parseFloat(dmsCalcState.currentValue) || 0;
      switch (type) {
        case 'MC': dmsCalcState.memory = 0; break;
        case 'MR': dmsCalcState.currentValue = String(dmsCalcState.memory); dmsCalcState.waitingForOperand = false; break;
        case 'M+': dmsCalcState.memory += current; break;
        case 'M-': dmsCalcState.memory -= current; break;
      }
      updateDisplay();
    };

    // Draggable Logic (Mouse + Touch)
    let isDragging = false;
    let dragStartX = 0, dragStartY = 0;
    let initialLeft = 0, initialTop = 0;

    function initDrag() {
      if (!header || !wrapper) return;

      header.addEventListener('mousedown', startDrag);
      header.addEventListener('touchstart', startDrag, { passive: false });

      function startDrag(e) {
        if (e.target.closest('.dms-calc-controls')) return;
        isDragging = true;
        const clientX = e.touches ? e.touches[0].clientX : e.clientX;
        const clientY = e.touches ? e.touches[0].clientY : e.clientY;

        const rect = wrapper.getBoundingClientRect();
        dragStartX = clientX;
        dragStartY = clientY;
        initialLeft = rect.left;
        initialTop = rect.top;

        document.addEventListener('mousemove', onDrag);
        document.addEventListener('touchmove', onDrag, { passive: false });
        document.addEventListener('mouseup', stopDrag);
        document.addEventListener('touchend', stopDrag);
      }

      function onDrag(e) {
        if (!isDragging) return;
        if (e.cancelable) e.preventDefault();

        const clientX = e.touches ? e.touches[0].clientX : e.clientX;
        const clientY = e.touches ? e.touches[0].clientY : e.clientY;

        const deltaX = clientX - dragStartX;
        const deltaY = clientY - dragStartY;

        let newLeft = initialLeft + deltaX;
        let newTop = initialTop + deltaY;

        const maxLeft = window.innerWidth - wrapper.offsetWidth - 10;
        const maxTop = window.innerHeight - wrapper.offsetHeight - 10;

        newLeft = Math.max(10, Math.min(newLeft, maxLeft));
        newTop = Math.max(10, Math.min(newTop, maxTop));

        wrapper.style.left = newLeft + 'px';
        wrapper.style.top = newTop + 'px';
        wrapper.style.right = 'auto';
      }

      function stopDrag() {
        if (!isDragging) return;
        isDragging = false;
        document.removeEventListener('mousemove', onDrag);
        document.removeEventListener('touchmove', onDrag);
        document.removeEventListener('mouseup', stopDrag);
        document.removeEventListener('touchend', stopDrag);

        localStorage.setItem('dms_calc_pos', JSON.stringify({
          left: wrapper.style.left,
          top: wrapper.style.top
        }));
      }
    }

    // Custom Interactive Resizer Logic (Mouse + Touch)
    let isResizing = false;
    let resizeStartX = 0, resizeStartY = 0;
    let startWidth = 0, startHeight = 0;

    function initResize() {
      if (!resizer || !card) return;

      resizer.addEventListener('mousedown', startResize);
      resizer.addEventListener('touchstart', startResize, { passive: false });

      function startResize(e) {
        e.stopPropagation();
        if (e.cancelable) e.preventDefault();
        isResizing = true;

        const clientX = e.touches ? e.touches[0].clientX : e.clientX;
        const clientY = e.touches ? e.touches[0].clientY : e.clientY;

        const rect = card.getBoundingClientRect();
        resizeStartX = clientX;
        resizeStartY = clientY;
        startWidth = rect.width;
        startHeight = rect.height;

        document.addEventListener('mousemove', onResize);
        document.addEventListener('touchmove', onResize, { passive: false });
        document.addEventListener('mouseup', stopResize);
        document.addEventListener('touchend', stopResize);
      }

      function onResize(e) {
        if (!isResizing) return;
        if (e.cancelable) e.preventDefault();

        const clientX = e.touches ? e.touches[0].clientX : e.clientX;
        const clientY = e.touches ? e.touches[0].clientY : e.clientY;

        const deltaX = clientX - resizeStartX;
        const deltaY = clientY - resizeStartY;

        let newWidth = startWidth + deltaX;
        let newHeight = startHeight + deltaY;

        const maxW = Math.min(650, window.innerWidth - wrapper.offsetLeft - 15);
        const maxH = Math.min(850, window.innerHeight - wrapper.offsetTop - 15);

        newWidth = Math.max(280, Math.min(newWidth, maxW));
        newHeight = Math.max(440, Math.min(newHeight, maxH));

        card.style.width = newWidth + 'px';
        card.style.height = newHeight + 'px';
      }

      function stopResize() {
        if (!isResizing) return;
        isResizing = false;
        document.removeEventListener('mousemove', onResize);
        document.removeEventListener('touchmove', onResize);
        document.removeEventListener('mouseup', stopResize);
        document.removeEventListener('touchend', stopResize);

        localStorage.setItem('dms_calc_size', JSON.stringify({
          width: card.style.width,
          height: card.style.height
        }));
      }
    }

    // Keyboard Shortcuts Support
    function handleKeyboard(e) {
      if (!wrapper || wrapper.classList.contains('hidden')) return;

      const tag = document.activeElement ? document.activeElement.tagName.toLowerCase() : '';
      if (tag === 'input' || tag === 'textarea' || document.activeElement.isContentEditable) return;

      const key = e.key;

      if (key >= '0' && key <= '9') {
        dmsCalcInputDigit(key);
      } else if (key === '.') {
        dmsCalcInputDot();
      } else if (key === '+' || key === '-' || key === '*' || key === '/') {
        dmsCalcInputOp(key);
      } else if (key === '%') {
        dmsCalcPercentage();
      } else if (key === 'Enter' || key === '=') {
        e.preventDefault();
        dmsCalcCalculate();
      } else if (key === 'Backspace') {
        dmsCalcBackspace();
      } else if (key === 'Escape' || key === 'c' || key === 'C') {
        dmsCalcClearAll();
      }
    }

    // Auto Init on DOM Loaded
    document.addEventListener('DOMContentLoaded', function () {
      initElements();
      initDrag();
      initResize();
      document.addEventListener('keydown', handleKeyboard);
    });

  })();
</script>
