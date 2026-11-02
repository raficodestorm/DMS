@extends('layouts.adminlayout')

@section('content')
<div class="manage-card">

  {{-- Top Navigation & Header --}}
  <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4 pb-3 border-bottom">
    <div class="d-flex align-items-center gap-3">
      <a href="{{ route('admin.cash-transfers.index') }}" class="btn-smart btn-outline-secondary" style="border: 1px solid var(--border-color);" title="Back to Transfer List">
        <i class="fas fa-arrow-left me-1"></i> Back
      </a>
      <div>
        <h3 class="mb-0 fw-bold" style="color: var(--primary);">
          Cash Transfer #TR-{{ str_pad($transfer->id, 5, '0', STR_PAD_LEFT) }}
        </h3>
        <p class="text-muted mb-0 small">Branch to Central Admin Cash Transfer Request</p>
      </div>
    </div>

    <div>
      @if($transfer->status === 'pending')
        <span class="status-pending-badge" style="font-size: 13px; padding: 6px 18px;">Pending Review...</span>
      @elseif($transfer->status === 'approved')
        <span class="status-approved-badge" style="font-size: 13px; padding: 6px 18px;">● Approved & Settled</span>
      @elseif($transfer->status === 'rejected')
        <span class="status-rejected-badge" style="font-size: 13px; padding: 6px 18px;">● Rejected</span>
      @endif
    </div>
  </div>

  @include('components.alert')

  {{-- Transfer Amount Hero Card --}}
  <div class="p-4 mb-4 rounded-4" style="background: var(--background); border: 1px solid var(--border-color);">
    <div class="row g-3 align-items-center">
      <div class="col-12 {{ $transfer->status === 'pending' ? 'col-md-5' : 'col-md-7' }}">
        <span class="text-muted small text-uppercase fw-semibold d-block">
          {{ $transfer->status === 'approved' ? 'Transferred Amount' : 'Requested Transfer Amount' }}
        </span>
        <div class="d-flex align-items-baseline gap-1 my-1">
          <span style="font-size: 1.8rem; font-weight: 700; color: var(--primary);">৳</span>
          <span style="font-size: 2.6rem; font-weight: 800; color: var(--text-main); line-height: 1;">
            {{ number_format($transfer->amount, 2) }}
          </span>
        </div>
        <span class="text-muted x-small">
          Submitted on {{ $transfer->created_at->format('d M Y, h:i A') }} ({{ $transfer->created_at->diffForHumans() }})
        </span>
      </div>

      @if($transfer->status === 'pending')
      {{-- Only show live balances when reviewing a pending transfer --}}
      <div class="col-12 col-md-4">
        <div class="p-3 rounded-3" style="background: var(--section-bg); border: 1px solid var(--border-color);">
          <span class="text-muted small d-block mb-1">Branch Current Cash Balance</span>
          <h4 class="fw-bold mb-1 text-main">৳ {{ number_format($transfer->branch->cash ?? 0, 2) }}</h4>
          @if(($transfer->branch->cash ?? 0) >= $transfer->amount)
            <span class="text-success small fw-semibold">
              <i class="fa-solid fa-circle-check me-1"></i> Sufficient branch cash balance
            </span>
          @else
            <span class="text-danger small fw-semibold">
              <i class="fa-solid fa-triangle-exclamation me-1"></i> Warning: Branch cash is currently less than transfer amount!
            </span>
          @endif
        </div>
      </div>

      <div class="col-12 col-md-3">
        <div class="p-3 rounded-3" style="background: var(--section-bg); border: 1px solid var(--border-color);">
          <span class="text-muted small d-block mb-1">Central Admin Cash</span>
          <h4 class="fw-bold mb-1 text-primary">৳ {{ number_format($totalAdminCash, 2) }}</h4>
          <span class="text-muted x-small">
            Will become: <strong>৳ {{ number_format($totalAdminCash + $transfer->amount, 2) }}</strong>
          </span>
        </div>
      </div>
      @elseif($transfer->status === 'approved')
      {{-- When approved, show clean settlement badge instead of live balances --}}
      <div class="col-12 col-md-5">
        <div class="p-3 rounded-3 text-md-end" style="background: var(--section-bg); border: 1px solid var(--border-color);">
          <span class="text-muted small d-block mb-1">Settlement Status</span>
          <h5 class="fw-bold text-success mb-1">
            <i class="fa-solid fa-circle-check me-1"></i> Successfully Transferred to Admin
          </h5>
          <span class="text-muted x-small">
            Approved on {{ $transfer->updated_at->format('d M Y, h:i A') }}
          </span>
        </div>
      </div>
      @endif
    </div>
  </div>

  {{-- Two Column Detail Information --}}
  <div class="row g-4 mb-4">

    {{-- Left: Branch & Request Details --}}
    <div class="col-12 col-md-6">
      <div class="p-3 rounded-3 h-100" style="background: var(--section-bg); border: 1px solid var(--border-color);">
        <h5 class="fw-bold mb-3 pb-2 border-bottom text-main" style="font-size: 0.95rem;">
          <i class="fa-solid fa-shop text-primary me-2"></i> Branch Information
        </h5>

        <div class="detail-row">
          <span class="detail-label">Branch Name:</span>
          <span class="detail-value">{{ $transfer->branch->name ?? 'N/A' }}</span>
        </div>

        <div class="detail-row">
          <span class="detail-label">Branch Manager:</span>
          <span class="detail-value">{{ $transfer->branch->manager ?? 'N/A' }}</span>
        </div>

        @if(!empty($transfer->branch->address))
        <div class="detail-row">
          <span class="detail-label">Branch Address:</span>
          <span class="detail-value text-truncate" style="max-width: 250px;" title="{{ $transfer->branch->address }}">
            {{ $transfer->branch->address }}
          </span>
        </div>
        @endif

        <div class="detail-row">
          <span class="detail-label">Requested By User:</span>
          <span class="detail-value">
            {{ $transfer->requestedBy->fullname ?? $transfer->requestedBy->username ?? 'N/A' }}
            @if(!empty($transfer->requestedBy->username))
              <span class="text-muted small">({{ $transfer->requestedBy->username }})</span>
            @endif
          </span>
        </div>

        <div class="detail-row border-bottom-0">
          <span class="detail-label">Request Time:</span>
          <span class="detail-value">{{ $transfer->created_at->format('d M Y, h:i A') }}</span>
        </div>
      </div>
    </div>

    {{-- Right: Balance Impact & Manager Notes --}}
    <div class="col-12 col-md-6">
      <div class="p-3 rounded-3 h-100" style="background: var(--section-bg); border: 1px solid var(--border-color);">
        <h5 class="fw-bold mb-3 pb-2 border-bottom text-main" style="font-size: 0.95rem;">
          <i class="fa-solid fa-calculator text-primary me-2"></i> Transfer Audit & Ledger Impact
        </h5>

        <div class="detail-row">
          <span class="detail-label">Transfer Amount:</span>
          <span class="detail-value fw-bold text-main">৳ {{ number_format($transfer->amount, 2) }}</span>
        </div>

        <div class="detail-row">
          <span class="detail-label">Branch Cash Before Transfer:</span>
          <span class="detail-value text-muted">৳ {{ number_format($transfer->cash_before_transfer, 2) }}</span>
        </div>

        <div class="detail-row">
          <span class="detail-label">Branch Cash After Transfer:</span>
          <span class="detail-value text-muted">৳ {{ number_format($transfer->cash_after_transfer, 2) }}</span>
        </div>

        <div class="pt-3">
          <span class="detail-label d-block mb-1">Branch Manager Note / Reference:</span>
          <div class="p-3 rounded-3 text-muted" style="background: var(--background); font-size: 0.88rem; min-height: 60px;">
            {{ $transfer->note ?: 'No note or reference was provided with this request.' }}
          </div>
        </div>
      </div>
    </div>

  </div>

  {{-- Status Alerts & Action Buttons --}}
  <div class="mt-4 pt-3 border-top">
    @if($transfer->status === 'pending')
      <div class="d-flex flex-column flex-sm-row justify-content-between align-items-center gap-3">
        <div class="text-muted small">
          <i class="fa-solid fa-circle-info text-primary me-1"></i>
          Approving this request will immediately deduct <strong>৳ {{ number_format($transfer->amount, 2) }}</strong> from <strong>{{ $transfer->branch->name }}</strong> and credit it to <strong>Admin Central Cash</strong>.
        </div>

        <div class="d-flex align-items-center gap-2 w-100 w-sm-auto justify-content-end">
          {{-- Reject Form --}}
          <form action="{{ route('admin.cash-transfers.reject', $transfer->id) }}" method="POST" id="rejectTransferForm" onsubmit="return confirmReject(event, this)">
            @csrf
            <button type="submit" class="btn-smart btn-red px-4" title="Reject Request">
              <i class="fas fa-circle-xmark me-1"></i> Reject
            </button>
          </form>

          {{-- Approve Form --}}
          <form action="{{ route('admin.cash-transfers.approve', $transfer->id) }}" method="POST" id="approveTransferForm" onsubmit="return confirmApprove(event, this, {{ $transfer->amount }}, '{{ addslashes($transfer->branch->name ?? '') }}')">
            @csrf
            <button type="submit" class="btn-smart btn-green px-4" title="Approve & Transfer Cash">
              <i class="fas fa-circle-check me-1"></i> Approve Transfer
            </button>
          </form>
        </div>
      </div>
    @elseif($transfer->status === 'approved')
      <div class="alert alert-success d-flex align-items-center gap-3 mb-0" style="border-radius: 12px;">
        <i class="fa-solid fa-circle-check fs-4"></i>
        <div>
          <h6 class="mb-0 fw-bold">Transfer Settled & Approved</h6>
          <small>
            ৳ {{ number_format($transfer->amount, 2) }} was successfully deducted from {{ $transfer->branch->name }} and credited to Central Admin Cash on {{ $transfer->updated_at->format('d M Y, h:i A') }}.
          </small>
        </div>
      </div>
    @elseif($transfer->status === 'rejected')
      <div class="alert alert-danger d-flex align-items-center gap-3 mb-0" style="border-radius: 12px;">
        <i class="fa-solid fa-circle-xmark fs-4"></i>
        <div>
          <h6 class="mb-0 fw-bold">Transfer Request Rejected</h6>
          <small>
            This transfer request was rejected on {{ $transfer->updated_at->format('d M Y, h:i A') }}. No funds were moved.
          </small>
        </div>
      </div>
    @endif
  </div>

</div>

<style>
  .detail-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 9px 0;
    border-bottom: 1px dashed var(--border-color);
    font-size: 0.88rem;
  }
  .detail-label {
    color: var(--text-muted);
    font-weight: 500;
  }
  .detail-value {
    color: var(--text-main);
    font-weight: 600;
  }
  .x-small {
    font-size: 0.78rem;
  }
</style>
@endsection

@push('scripts')
<script>
function confirmApprove(e, form, amount, branchName) {
    e.preventDefault();
    const formattedAmount = Number(amount).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

    if (typeof Swal !== 'undefined') {
        Swal.fire({
            title: 'Approve Cash Transfer?',
            html: `
                <p class="mb-2">Are you sure you want to approve this cash transfer?</p>
                <div class="p-3 my-2 text-start rounded-3" style="background: rgba(49, 49, 255, 0.05); border: 1px solid rgba(49, 49, 255, 0.2);">
                    <div class="d-flex justify-content-between mb-1">
                        <span class="text-muted small">Branch:</span>
                        <strong class="text-main">${branchName}</strong>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted small">Transfer Amount:</span>
                        <strong class="text-primary">৳ ${formattedAmount}</strong>
                    </div>
                </div>
                <small class="text-muted">This will deduct ৳ ${formattedAmount} from branch cash and add it to Admin Cash.</small>
            `,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#16a34a',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '<i class="fas fa-check me-1"></i> Yes, Approve & Transfer',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    } else if (confirm(`Approve transfer of ৳ ${formattedAmount} from ${branchName} to Admin Cash?`)) {
        form.submit();
    }
    return false;
}

function confirmReject(e, form) {
    e.preventDefault();
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            title: 'Reject Transfer Request?',
            text: 'Are you sure you want to reject this cash transfer request? No cash balance will be moved.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '<i class="fas fa-times me-1"></i> Yes, Reject Request',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    } else if (confirm('Are you sure you want to reject this cash transfer request?')) {
        form.submit();
    }
    return false;
}
</script>
@endpush
