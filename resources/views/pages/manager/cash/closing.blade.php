@extends('layouts.managerlayout')

@section('content')
<div class="closing-page-container py-2">

    {{-- Breadcrumb / Back Link --}}
    <div class="mb-3">
        <a href="{{ route('manager.cash.index') }}" class="btn-back-link">
            <i class="fa-solid fa-arrow-left me-1"></i> Back to Cash Overview
        </a>
    </div>

    {{-- Page Header --}}
    <div class="d-flex justify-content-between align-items-start align-items-sm-center flex-wrap gap-2 mb-4">
        <div>
            <h3 class="fw-bold mb-1 text-main">Daily Cash Closing</h3>
            <p class="text-muted small mb-0">
                Finalizing day-end register for <strong class="text-primary">{{ $branch->name }}</strong> • 
                <span>{{ $today->format('l, d F Y') }}</span>
            </p>
        </div>
        <span class="badge-closing-date">
            <i class="fa-solid fa-calendar-day me-1 text-primary"></i> Business Date: {{ $today->format('d M, Y') }}
        </span>
    </div>

    @if($errors->any())
        <div class="alert alert-danger border-0 rounded-3 shadow-sm mb-4">
            <div class="d-flex align-items-center gap-2 mb-1">
                <i class="fa-solid fa-circle-exclamation fs-5"></i>
                <h6 class="fw-bold mb-0">Please correct the following errors:</h6>
            </div>
            <ul class="mb-0 small ps-3">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- 4 Summary Cards Grid --}}
    <div class="row g-3 mb-4">

        {{-- 1. Opening Cash --}}
        <div class="col-6 col-lg-3">
            <div class="metric-card h-100 p-3 p-md-4 rounded-4 border bg-card">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="metric-tag text-muted text-uppercase x-small">Opening Cash</span>
                    <span class="metric-icon-bubble bg-info-subtle text-info">
                        <i class="fa-solid fa-door-open"></i>
                    </span>
                </div>
                <h4 class="metric-value mb-1">৳ {{ number_format($openingCash, 2) }}</h4>
                <span class="text-muted xx-small">From previous day actual cash</span>
            </div>
        </div>

        {{-- 2. Today's Total Cash In --}}
        <div class="col-6 col-lg-3">
            <div class="metric-card h-100 p-3 p-md-4 rounded-4 border bg-card">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="metric-tag text-muted text-uppercase x-small">Total Cash In</span>
                    <span class="metric-icon-bubble bg-success-subtle text-success">
                        <i class="fa-solid fa-arrow-down-left"></i>
                    </span>
                </div>
                <h4 class="metric-value text-success mb-1">+৳ {{ number_format($totalCashIn, 2) }}</h4>
                <div class="xx-small text-muted text-truncate" title="Payments: ৳{{ number_format($todayPayments, 2) }} | Retail: ৳{{ number_format($todayRetailSales, 2) }} | Online: ৳{{ number_format($todayOnlineDelivered, 2) }}">
                    Payments ({{ number_format($todayPayments, 2) }}) + Sales
                </div>
            </div>
        </div>

        {{-- 3. Today's Total Cash Out --}}
        <div class="col-6 col-lg-3">
            <div class="metric-card h-100 p-3 p-md-4 rounded-4 border bg-card">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="metric-tag text-muted text-uppercase x-small">Total Cash Out</span>
                    <span class="metric-icon-bubble bg-danger-subtle text-danger">
                        <i class="fa-solid fa-arrow-up-right"></i>
                    </span>
                </div>
                <h4 class="metric-value text-danger mb-1">-৳ {{ number_format($totalCashOut, 2) }}</h4>
                <div class="xx-small text-muted text-truncate" title="Costs: ৳{{ number_format($todayBranchCosts, 2) }} | Transfers: ৳{{ number_format($todayCashTransfers, 2) }}">
                    Expenses ({{ number_format($todayBranchCosts, 2) }}) + Transfers
                </div>
            </div>
        </div>

        {{-- 4. Expected Cash --}}
        <div class="col-6 col-lg-3">
            <div class="metric-card expected-highlight-card h-100 p-3 p-md-4 rounded-4 border">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="metric-tag text-uppercase x-small">Expected Cash</span>
                    <span class="metric-icon-bubble bg-primary text-white">
                        <i class="fa-solid fa-calculator"></i>
                    </span>
                </div>
                <h4 class="metric-value mb-1 text-primary">৳ {{ number_format($expectedCash, 2) }}</h4>
                <span class="text-muted xx-small">Branch registered vault cash</span>
            </div>
        </div>

    </div>

    {{-- Main Closing Form Card --}}
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <div class="closing-form-card border rounded-4 p-4 p-md-5 bg-card shadow-sm">
                
                <div class="border-bottom pb-3 mb-4">
                    <h5 class="fw-bold mb-1 text-main">
                        <i class="fa-solid fa-cash-register text-primary me-2"></i>Physical Cash Count & Seal
                    </h5>
                    <p class="text-muted small mb-0">Enter the counted physical cash amount to calculate variance.</p>
                </div>

                <form action="{{ route('manager.cash.closing.store') }}" method="POST" id="dailyClosingForm">
                    @csrf

                    {{-- Hidden Expected Cash value for JS comparison --}}
                    <input type="hidden" id="expectedCashVal" value="{{ $expectedCash }}">

                    {{-- Actual Cash Input --}}
                    <div class="mb-4">
                        <label for="actualCashInput" class="form-label fw-bold text-main">
                            Actual Physical Cash <span class="text-danger">*</span>
                        </label>
                        <div class="input-group input-group-lg custom-input-group">
                            <span class="input-group-text currency-addon">৳</span>
                            <input type="number" 
                                   step="0.01" 
                                   min="0" 
                                   name="actual_cash" 
                                   id="actualCashInput" 
                                   class="form-control actual-cash-field" 
                                   placeholder="0.00" 
                                   value="{{ old('actual_cash') }}" 
                                   required 
                                   autocomplete="off">
                        </div>
                        <small class="text-muted">Count every note and coin present in the branch drawer/safe.</small>
                    </div>

                    {{-- Real-time Variance Display Box --}}
                    <div id="varianceBox" class="variance-display-panel p-3 rounded-3 mb-4 d-none">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <span class="text-muted x-small d-block text-uppercase fw-semibold" id="varianceLabel">Variance</span>
                                <h4 class="mb-0 fw-bold" id="varianceFigure">৳ 0.00</h4>
                            </div>
                            <div id="varianceBadge"></div>
                        </div>
                    </div>

                    {{-- Conditional Notes / Reason Field (Shown only when Actual != Expected) --}}
                    <div id="notesContainer" class="mb-4 d-none">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <label for="closingNotes" class="form-label fw-bold text-danger mb-0">
                                Reason for Cash Shortage / Excess <span class="text-danger">*</span>
                            </label>
                            <span class="badge bg-danger-subtle text-danger x-small">Required</span>
                        </div>
                        <p class="text-muted x-small mb-2">
                            Actual cash differs from expected cash. Please explain the reason for the discrepancy before sealing today's register.
                        </p>
                        <textarea name="notes" 
                                  id="closingNotes" 
                                  rows="3" 
                                  class="form-control custom-textarea" 
                                  placeholder="Write a clear explanation (e.g. pending petty expense receipt, change calculation, etc.)...">{{ old('notes') }}</textarea>
                    </div>

                    {{-- Action Buttons --}}
                    <div class="d-flex justify-content-between align-items-center pt-3 border-top flex-wrap gap-2">
                        <a href="{{ route('manager.cash.index') }}" class="btn btn-light rounded-pill px-4">
                            Cancel
                        </a>
                        <button type="submit" class="btn btn-primary rounded-pill px-4 py-2 fw-semibold" id="btnSubmitClosing">
                            <i class="fa-solid fa-lock me-2"></i> Submit & Seal Today's Closing
                        </button>
                    </div>

                </form>

            </div>
        </div>
    </div>

</div>

<style>
    .text-main {
        color: var(--text-main);
    }

    .bg-card {
        background: var(--section-bg);
    }

    .x-small {
        font-size: 0.8rem;
    }

    .xx-small {
        font-size: 0.73rem;
    }

    .btn-back-link {
        color: var(--text-muted);
        text-decoration: none;
        font-size: 0.85rem;
        font-weight: 500;
        transition: color 0.2s;
    }

    .btn-back-link:hover {
        color: var(--primary);
    }

    .badge-closing-date {
        background: var(--section-bg);
        border: 1px solid var(--border-color);
        color: var(--text-main);
        font-size: 0.82rem;
        font-weight: 600;
        padding: 6px 14px;
        border-radius: 20px;
    }

    /* Metric Cards */
    .metric-card {
        border-color: var(--border-color) !important;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .metric-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 18px rgba(0, 0, 0, 0.04);
    }

    .metric-icon-bubble {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 0.88rem;
    }

    .metric-value {
        font-family: 'Inter', sans-serif;
        font-weight: 700;
        font-size: 1.25rem;
    }

    .expected-highlight-card {
        background: var(--section-bg);
        border: 1.5px solid var(--primary-light) !important;
    }

    /* Form Card */
    .closing-form-card {
        border-color: var(--border-color) !important;
    }

    .custom-input-group .currency-addon {
        background: var(--background);
        border-color: var(--border-color);
        color: var(--primary);
        font-weight: 700;
        font-size: 1.3rem;
        padding-left: 1.2rem;
        padding-right: 1.2rem;
    }

    .actual-cash-field {
        background: var(--section-bg);
        border-color: var(--border-color);
        color: var(--text-main);
        font-size: 1.4rem;
        font-weight: 700;
        font-family: 'Inter', sans-serif;
    }

    .actual-cash-field:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 0.25rem rgba(2, 2, 226, 0.12);
        background: var(--section-bg);
        color: var(--text-main);
    }

    .variance-display-panel {
        background: var(--background);
        border: 1px solid var(--border-color);
        transition: all 0.3s ease;
    }

    .custom-textarea {
        background: var(--section-bg);
        border-color: var(--border-color);
        color: var(--text-main);
        border-radius: 10px;
    }

    .custom-textarea:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 0.2rem rgba(2, 2, 226, 0.1);
        background: var(--section-bg);
        color: var(--text-main);
    }
</style>

{{-- Real-Time Interactive Script --}}
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const expectedCash = parseFloat(document.getElementById('expectedCashVal').value) || 0;
        const actualInput = document.getElementById('actualCashInput');
        const varianceBox = document.getElementById('varianceBox');
        const varianceLabel = document.getElementById('varianceLabel');
        const varianceFigure = document.getElementById('varianceFigure');
        const varianceBadge = document.getElementById('varianceBadge');
        const notesContainer = document.getElementById('notesContainer');
        const closingNotes = document.getElementById('closingNotes');

        function updateVariance() {
            const rawVal = actualInput.value.trim();
            if (rawVal === '') {
                varianceBox.classList.add('d-none');
                notesContainer.classList.add('d-none');
                closingNotes.removeAttribute('required');
                return;
            }

            const actualCash = parseFloat(rawVal) || 0;
            const diff = actualCash - expectedCash;
            const absDiff = Math.abs(diff);

            varianceBox.classList.remove('d-none');

            if (absDiff < 0.01) {
                // Balanced
                varianceLabel.textContent = "Status: Balanced";
                varianceFigure.textContent = "৳ 0.00";
                varianceFigure.className = "mb-0 fw-bold text-success";
                varianceBadge.innerHTML = `<span class="badge bg-success text-white px-3 py-2 rounded-pill"><i class="fa-solid fa-check me-1"></i> Exact Match</span>`;
                
                // Hide notes field
                notesContainer.classList.add('d-none');
                closingNotes.removeAttribute('required');
            } else if (diff < 0) {
                // Shortage (ঘাটতি)
                varianceLabel.textContent = "Shortage (ঘাটতি)";
                varianceFigure.textContent = "-৳ " + absDiff.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                varianceFigure.className = "mb-0 fw-bold text-danger";
                varianceBadge.innerHTML = `<span class="badge bg-danger-subtle text-danger px-3 py-2 rounded-pill"><i class="fa-solid fa-triangle-exclamation me-1"></i> Shortage</span>`;
                
                // Show required notes field
                notesContainer.classList.remove('d-none');
                closingNotes.setAttribute('required', 'required');
            } else {
                // Excess (বাড়তি)
                varianceLabel.textContent = "Excess (বাড়তি)";
                varianceFigure.textContent = "+৳ " + absDiff.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                varianceFigure.className = "mb-0 fw-bold text-info";
                varianceBadge.innerHTML = `<span class="badge bg-info-subtle text-info px-3 py-2 rounded-pill"><i class="fa-solid fa-arrow-trend-up me-1"></i> Excess</span>`;
                
                // Show required notes field
                notesContainer.classList.remove('d-none');
                closingNotes.setAttribute('required', 'required');
            }
        }

        actualInput.addEventListener('input', updateVariance);

        // Run on load in case of old input
        if (actualInput.value.trim() !== '') {
            updateVariance();
        }
    });
</script>
@endsection
