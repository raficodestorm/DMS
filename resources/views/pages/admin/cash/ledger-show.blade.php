@extends('layouts.adminlayout')

@section('content')

@php
    $isInflow = in_array($ledger->type, ['cash-in', 'cash_transfer']);
@endphp

<p class="show-head">
    Admin Cash Ledger Entry Details
</p>

<div class="show-card">
    <div class="content-area">
        <p style="color: gray; font-style: italic; text-align: center; margin-bottom: 12px;">
            Central Treasury Movement Audit Record
        </p>

        <div class="rank-pill">
            ACL00{{ $ledger->id }}
        </div>

        <div class="info-list">

            <div class="info-group">
                <span class="i-label">Movement Type</span>
                <span class="i-value">
                    @if($ledger->type == 'cash-in')
                        <span style="color:#15803d; font-weight:700; background:#dcfce7; padding:3px 12px; border-radius:12px; font-size:0.85rem;">
                            <i class="fa-solid fa-arrow-down-long me-1"></i> Cash In
                        </span>
                    @elseif($ledger->type == 'cash_transfer')
                        <span style="color:#1d4ed8; font-weight:700; background:#dbeafe; padding:3px 12px; border-radius:12px; font-size:0.85rem;">
                            <i class="fa-solid fa-code-branch me-1"></i> Branch Cash Transfer
                        </span>
                    @elseif($ledger->type == 'supplier_payment')
                        <span style="color:#c2410c; font-weight:700; background:#ffedd5; padding:3px 12px; border-radius:12px; font-size:0.85rem;">
                            <i class="fa-solid fa-truck me-1"></i> Supplier Payment
                        </span>
                    @elseif($ledger->type == 'company_cost')
                        <span style="color:#b91c1c; font-weight:700; background:#fee2e2; padding:3px 12px; border-radius:12px; font-size:0.85rem;">
                            <i class="fa-solid fa-file-invoice-dollar me-1"></i> Company Expense
                        </span>
                    @else
                        <span style="color:#475569; font-weight:700; background:#f1f5f9; padding:3px 12px; border-radius:12px; font-size:0.85rem;">
                            {{ ucfirst(str_replace('_', ' ', $ledger->type)) }}
                        </span>
                    @endif
                </span>
            </div>

            <div class="info-group">
                <span class="i-label">Cash Flow</span>
                <span class="i-value">
                    @if($isInflow)
                        <strong style="color: #16a34a; font-size: 1.15rem;">
                            + ৳ {{ number_format($ledger->amount, 2) }}
                        </strong>
                        <small class="badge bg-success ms-2">Inflow (Added)</small>
                    @else
                        <strong style="color: #dc2626; font-size: 1.15rem;">
                            - ৳ {{ number_format($ledger->amount, 2) }}
                        </strong>
                        <small class="badge bg-danger ms-2">Outflow (Deducted)</small>
                    @endif
                </span>
            </div>

            <div class="info-group">
                <span class="i-label">Payment / Deposit Method</span>
                <span class="i-value" style="text-transform: capitalize;">
                    <strong>{{ $ledger->payment_method ?? 'Cash' }}</strong>
                </span>
            </div>

            <div class="info-group">
                <span class="i-label">Admin Cash Before</span>
                <span class="i-value text-muted">
                    ৳ {{ number_format($ledger->cash_before, 2) }}
                </span>
            </div>

            <div class="info-group">
                <span class="i-label">Admin Cash After</span>
                <span class="i-value" style="color: var(--primary, #3131ff); font-size: 1.1rem;">
                    <strong>৳ {{ number_format($ledger->cash_after, 2) }}</strong>
                </span>
            </div>

            <div class="info-group">
                <span class="i-label">Recorded By Admin</span>
                <span class="i-value">
                    <strong>{{ $ledger->admin->name ?? 'Admin' }}</strong>
                    @if(!empty($ledger->admin?->email))
                        <small class="text-muted">({{ $ledger->admin->email }})</small>
                    @endif
                </span>
            </div>

            <div class="info-group">
                <span class="i-label">Date & Time</span>
                <span class="i-value">
                    {{ $ledger->created_at->format('d M Y, h:i:s A') }}
                </span>
            </div>

            @if($ledger->note)
            <div class="info-group">
                <span class="i-label">Note / Reference</span>
                <span class="i-value" style="white-space: pre-wrap;">
                    {{ $ledger->note }}
                </span>
            </div>
            @endif

            @if($ledger->reference_type && $ledger->reference_id)
            <div class="info-group">
                <span class="i-label">System Source</span>
                <span class="i-value">
                    <code>{{ class_basename($ledger->reference_type) }} #{{ $ledger->reference_id }}</code>
                </span>
            </div>
            @endif

        </div>

        <div class="text-center mt-4">
            <a href="{{ route('admin.cash.ledger') }}" class="btn-smart btn-blue">
                <i class="fa-solid fa-arrow-left me-1"></i> Back to Admin Cash Ledger
            </a>
            <a href="{{ route('admin.cash.index') }}" class="btn-smart btn-gray ms-2">
                <i class="fa-solid fa-vault me-1"></i> Cash Dashboard
            </a>
        </div>
    </div>
</div>

@endsection

