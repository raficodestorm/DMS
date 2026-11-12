@forelse($ledgers as $ledger)
@php
    $isInflow = in_array($ledger->type, ['cash-in', 'cash_transfer']);
@endphp
<tr>
    <td>
        {{ $ledgers->firstItem() ? $ledgers->firstItem() + $loop->index : $loop->iteration }}
    </td>

    <td>
        <strong>ACL00{{ $ledger->id }}</strong>
    </td>

    <td>
        @if($ledger->type == 'cash-in')
            <span style="display: inline-flex; align-items: center; gap: 5px; background: #ecfdf5; color: #15803d; font-weight: 700; font-size: 0.8rem; padding: 3px 10px; border-radius: 20px; border: 1px solid #bbf7d0;">
                <i class="fa-solid fa-arrow-down-long"></i> Cash In
            </span>
        @elseif($ledger->type == 'cash_transfer')
            <span style="display: inline-flex; align-items: center; gap: 5px; background: #eff6ff; color: #1d4ed8; font-weight: 700; font-size: 0.8rem; padding: 3px 10px; border-radius: 20px; border: 1px solid #bfdbfe;">
                <i class="fa-solid fa-code-branch"></i> Branch Transfer
            </span>
        @elseif($ledger->type == 'supplier_payment')
            <span style="display: inline-flex; align-items: center; gap: 5px; background: #fff7ed; color: #c2410c; font-weight: 700; font-size: 0.8rem; padding: 3px 10px; border-radius: 20px; border: 1px solid #fed7aa;">
                <i class="fa-solid fa-truck"></i> Supplier Payment
            </span>
        @elseif($ledger->type == 'company_cost')
            <span style="display: inline-flex; align-items: center; gap: 5px; background: #fef2f2; color: #b91c1c; font-weight: 700; font-size: 0.8rem; padding: 3px 10px; border-radius: 20px; border: 1px solid #fecaca;">
                <i class="fa-solid fa-file-invoice-dollar"></i> Company Cost
            </span>
        @else
            <span style="background: #f1f5f9; color: #475569; font-weight: 600; font-size: 0.8rem; padding: 3px 10px; border-radius: 20px;">
                {{ ucfirst(str_replace('_', ' ', $ledger->type)) }}
            </span>
        @endif
    </td>

    <td>
        @if($isInflow)
            <span style="color: #16a34a; font-weight: 800; font-size: 0.95rem;">
                + ৳ {{ number_format($ledger->amount, 2) }}
            </span>
        @else
            <span style="color: #dc2626; font-weight: 800; font-size: 0.95rem;">
                - ৳ {{ number_format($ledger->amount, 2) }}
            </span>
        @endif
    </td>

    <td class="text-muted small">
        ৳ {{ number_format($ledger->cash_before, 2) }}
    </td>

    <td>
        <strong style="color: var(--primary, #3131ff); font-size: 0.92rem;">
            ৳ {{ number_format($ledger->cash_after, 2) }}
        </strong>
    </td>

    <td>
        <span class="badge bg-light text-dark" style="text-transform: capitalize; font-weight: 600; font-size: 0.78rem; border: 1px solid #e2e8f0;">
            {{ $ledger->payment_method ?? 'Cash' }}
        </span>
    </td>

    <td>
        <div style="font-weight: 600; font-size: 0.85rem; color: #1e293b;">
            {{ $ledger->admin->name ?? 'Admin' }}
        </div>
        @if($ledger->note)
            <small class="text-muted d-block text-truncate" style="max-width: 180px;" title="{{ $ledger->note }}">
                {{ $ledger->note }}
            </small>
        @endif
    </td>

    <td>
        <div style="font-size: 0.82rem; font-weight: 600; color: #1e293b;">
            {{ $ledger->created_at->format('d M Y') }}
        </div>
        <small class="text-muted">
            {{ $ledger->created_at->format('h:i A') }}
        </small>
    </td>

    <td>
        <a href="{{ route('admin.cash.ledger.show', $ledger->id) }}" class="icon-btn view-icon" title="View Details">
            <i class="fa-solid fa-eye"></i>
        </a>
    </td>
</tr>
@empty
<tr>
    <td colspan="10" class="text-center py-5 text-muted">
        <i class="fa-solid fa-folder-open mb-2" style="font-size: 2rem; display: block; opacity: 0.4;"></i>
        No cash ledger entries found.
    </td>
</tr>
@endforelse

