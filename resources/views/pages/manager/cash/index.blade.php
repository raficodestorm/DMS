@extends('layouts.managerlayout')

@section('content')
<div class="manager-cash-wrapper py-2">

    @if(!$branch)
        <div class="empty-branch-card p-4 text-center rounded-3 border">
            <i class="fa-solid fa-store-slash fs-2 text-muted mb-3 opacity-50"></i>
            <h5 class="fw-bold mb-1">No Branch Linked</h5>
            <p class="text-muted small mb-0">Your account is not linked to any branch. Please contact the administrator.</p>
        </div>
    @else

        {{-- Top Minimal Header --}}
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
            <div>
                <h4 class="fw-bold mb-1 text-main">Branch Cash</h4>
                <p class="text-muted mb-0 small">
                    Overview for <span class="fw-semibold text-primary">{{ $branch->name }}</span>
                </p>
            </div>
            
            <div class="d-flex align-items-center gap-2">
                <span class="badge-date">
                    <i class="fa-regular fa-calendar me-1 text-muted"></i> {{ now()->format('d M, Y') }}
                </span>
                <button type="button" onclick="window.location.reload();" class="btn-icon-reload" title="Refresh">
                    <i class="fa-solid fa-rotate-right"></i>
                </button>
            </div>
        </div>

        {{-- Center Smart Cash Card --}}
        <div class="row justify-content-center">
            <div class="col-12 col-md-8 col-lg-6">
                <div class="smart-cash-card border rounded-4 text-center p-4 p-md-5">

                    {{-- Live Indicator & Branch Tag --}}
                    <div class="d-flex justify-content-center align-items-center gap-2 mb-3">
                        @if($todayClosing)
                            <span class="closed-dot"></span>
                            <span class="status-pill-closed">Today's Register Closed</span>
                        @else
                            <span class="live-dot"></span>
                            <span class="branch-pill">{{ $branch->name }}</span>
                        @endif
                    </div>

                    {{-- Main Cash Number --}}
                    <span class="cash-label text-muted text-uppercase fw-semibold">Current Cash</span>
                    <div class="cash-amount-display my-2">
                        <span class="curr-symbol">৳</span>
                        <span class="amount-val">{{ number_format($branch->cash ?? 0, 2) }}</span>
                    </div>

                    {{-- Branch Quick Meta --}}
                    <div class="d-flex justify-content-center align-items-center gap-3 text-muted x-small mt-3 pt-3 border-top">
                        <span><i class="fa-solid fa-user-tie me-1"></i> {{ $branch->manager ?? auth()->user()->fullname ?? auth()->user()->username }}</span>
                        @if(!empty($branch->address))
                            <span>•</span>
                            <span class="text-truncate" style="max-width: 220px;" title="{{ $branch->address }}">
                                <i class="fa-solid fa-location-dot me-1"></i> {{ $branch->address }}
                            </span>
                        @endif
                    </div>

                    {{-- Close For Today Action Section --}}
                    <div class="mt-4 pt-2">
                        @if($todayClosing)
                            {{-- Already Closed State --}}
                            <div class="already-closed-box p-3 rounded-3 border mb-2">
                                <div class="d-flex align-items-center justify-content-center gap-2 text-success fw-semibold small mb-1">
                                    <i class="fa-solid fa-circle-check fs-5"></i>
                                    <span>Daily Closing Completed</span>
                                </div>
                                <span class="text-muted xx-small d-block">
                                    Sealed Actual Cash: <strong>৳ {{ number_format($todayClosing->actual_cash, 2) }}</strong> 
                                    @if($todayClosing->cash_variance != 0)
                                        ({{ $todayClosing->cash_variance > 0 ? '+' : '' }}৳ {{ number_format($todayClosing->cash_variance, 2) }})
                                    @endif
                                    • at {{ \Carbon\Carbon::parse($todayClosing->closed_at)->format('h:i A') }}
                                </span>
                            </div>

                            <button type="button" class="btn-smart-closed" onclick="alert('Daily cash closing for today ({{ now()->format('d M, Y') }}) has already been completed.')">
                                <i class="fa-solid fa-lock me-2"></i> Closed for Today
                            </button>
                        @else
                            {{-- Open / Active State --}}
                            <a href="{{ route('manager.cash.closing.create') }}" class="btn-smart-close text-decoration-none">
                                <i class="fa-solid fa-calculator me-2"></i> Close for Today
                            </a>
                        @endif
                    </div>

                </div>
            </div>
        </div>

    @endif

</div>

<style>
    .manager-cash-wrapper {
        max-width: 100%;
    }

    .text-main {
        color: var(--text-main);
    }

    .x-small {
        font-size: 0.8rem;
    }

    .xx-small {
        font-size: 0.73rem;
    }

    .badge-date {
        background: var(--section-bg);
        border: 1px solid var(--border-color);
        color: var(--text-main);
        font-size: 0.8rem;
        font-weight: 500;
        padding: 6px 14px;
        border-radius: 20px;
    }

    .btn-icon-reload {
        width: 34px;
        height: 34px;
        border-radius: 10px;
        border: 1px solid var(--border-color);
        background: var(--section-bg);
        color: var(--text-muted);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .btn-icon-reload:hover {
        color: var(--primary);
        border-color: var(--primary);
    }

    /* Smart Minimal Cash Card */
    .smart-cash-card {
        background: var(--section-bg);
        border-color: var(--border-color) !important;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
        transition: box-shadow 0.2s ease;
    }

    .smart-cash-card:hover {
        box-shadow: 0 8px 26px rgba(0, 0, 0, 0.06);
    }

    .live-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background-color: #10b981;
        display: inline-block;
    }

    .closed-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background-color: #64748b;
        display: inline-block;
    }

    .branch-pill {
        background: var(--primary-soft);
        color: var(--primary);
        font-size: 0.78rem;
        font-weight: 600;
        padding: 4px 12px;
        border-radius: 20px;
    }

    .status-pill-closed {
        background: rgba(100, 116, 139, 0.12);
        color: var(--text-muted);
        font-size: 0.78rem;
        font-weight: 600;
        padding: 4px 12px;
        border-radius: 20px;
    }

    .cash-label {
        font-size: 0.75rem;
        letter-spacing: 1px;
    }

    .cash-amount-display {
        font-family: 'Inter', 'Roboto', sans-serif;
        color: var(--text-main);
        font-weight: 800;
        line-height: 1.1;
    }

    .amount-val {
        font-size: 3.2rem;
        letter-spacing: -1px;
    }

    .curr-symbol {
        font-size: 2.2rem;
        color: var(--primary);
        font-weight: 700;
        margin-right: 2px;
    }

    /* Smart Action Buttons */
    .btn-smart-close {
        background: var(--primary);
        color: #ffffff;
        border: none;
        font-weight: 600;
        font-size: 0.95rem;
        padding: 0.75rem 2.2rem;
        border-radius: 12px;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s ease;
        box-shadow: 0 4px 14px rgba(2, 2, 226, 0.2);
    }

    .btn-smart-close:hover {
        background: var(--primary-light);
        box-shadow: 0 6px 18px rgba(2, 2, 226, 0.3);
        transform: translateY(-1px);
        color: #ffffff;
    }

    .btn-smart-closed {
        background: var(--background);
        color: var(--text-muted);
        border: 1px solid var(--border-color);
        font-weight: 600;
        font-size: 0.95rem;
        padding: 0.75rem 2.2rem;
        border-radius: 12px;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s ease;
    }

    .btn-smart-closed:hover {
        background: var(--section-bg);
        color: var(--text-main);
    }

    .already-closed-box {
        background: var(--background);
        border-color: var(--border-color) !important;
    }

    .empty-branch-card {
        background: var(--section-bg);
        border-color: var(--border-color) !important;
    }

    @media (max-width: 576px) {
        .amount-val {
            font-size: 2.4rem;
        }
        .curr-symbol {
            font-size: 1.7rem;
        }
        .btn-smart-close, .btn-smart-closed {
            width: 100%;
        }
    }
</style>
@endsection
