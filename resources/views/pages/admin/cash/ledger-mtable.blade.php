@forelse($ledgers as $ledger)
@php
    $isInflow = in_array($ledger->type, ['cash-in', 'cash_transfer', 'bonus']);
@endphp

<div class="manage-card mb-3">
    <div class="card-body">
        <div>
            <span>S.No</span>
            <p>{{ $ledgers->firstItem() ? $ledgers->firstItem() + $loop->index : $loop->iteration }}</p>
        </div>

        <div>
            <span>Type</span>
            <p>
                @if($ledger->type == 'cash-in')
                    <span style="color: #15803d; font-weight: 700;">● Cash In</span>
                @elseif($ledger->type == 'cash-out')
                    <span style="color: #dc2626; font-weight: 700;">● Cash Out</span>
                @elseif($ledger->type == 'cash_transfer')
                    <span style="color: #1d4ed8; font-weight: 700;">● Branch Transfer</span>
                @elseif($ledger->type == 'supplier_payment')
                    <span style="color: #c2410c; font-weight: 700;">● Supplier Payment</span>
                @elseif($ledger->type == 'company_cost')
                    <span style="color: #b91c1c; font-weight: 700;">● Company Cost</span>
                @elseif($ledger->type == 'bonus')
                    <span style="color: #7e22ce; font-weight: 700;">● Bonus</span>
                @else
                    <span>● {{ ucfirst(str_replace('_', ' ', $ledger->type)) }}</span>
                @endif
            </p>
        </div>

        <div>
            <span>Amount</span>
            <p>
                @if($isInflow)
                    <strong style="color: #16a34a;">+ ৳ {{ number_format($ledger->amount, 2) }}</strong>
                @else
                    <strong style="color: #dc2626;">- ৳ {{ number_format($ledger->amount, 2) }}</strong>
                @endif
            </p>
        </div>

        <div>
            <span>Balance After</span>
            <p style="color: var(--primary, #3131ff); font-weight: 700;">
                ৳ {{ number_format($ledger->cash_after, 2) }}
            </p>
        </div>

        <div>
            <span>Method</span>
            <p style="text-transform: capitalize;">{{ $ledger->payment_method ?? 'Cash' }}</p>
        </div>

        <div>
            <span>Date</span>
            <p>{{ $ledger->created_at->format('d M Y, h:i A') }}</p>
        </div>
    </div>

    <div class="card-actions">
        <a href="{{ route('admin.cash.ledger.show', $ledger->id) }}" class="icon-btn view-icon" title="View Details">
            <i class="fa-solid fa-eye"></i>
        </a>
        @if($ledger->type == 'cash-in')
            <button type="button" class="icon-btn edit-icon btn-edit-cash-in" title="Edit Cash In"
                data-bs-toggle="modal"
                data-bs-target="#editCashInModal"
                data-id="{{ $ledger->id }}"
                data-amount="{{ $ledger->amount }}"
                data-payment-method="{{ $ledger->payment_method }}"
                data-note="{{ $ledger->note }}">
                <i class="fa-solid fa-pen-to-square"></i>
            </button>
        @elseif($ledger->type == 'cash-out')
            <button type="button" class="icon-btn edit-icon btn-edit-cash-out" title="Edit Cash Out"
                data-bs-toggle="modal"
                data-bs-target="#editCashOutModal"
                data-id="{{ $ledger->id }}"
                data-amount="{{ $ledger->amount }}"
                data-payment-method="{{ $ledger->payment_method }}"
                data-note="{{ $ledger->note }}">
                <i class="fa-solid fa-pen-to-square"></i>
            </button>
        @endif
    </div>
</div>
@empty
<p class="text-center text-muted py-5">No cash ledger entries found.</p>
@endforelse

