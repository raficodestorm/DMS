@extends('layouts.adminlayout')

@section('content')
<div class="manage-card">

  <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
    <div>
      <h2 class="mb-0">Stock-In Requests</h2>
      <p class="text-muted mb-0">Manage your all Stock-In Requests</p>
    </div>
  </div>

  @include('components.alert')

  {{-- Smart Filter Bar --}}
  <div style="margin: 15px 0; background: var(--section-bg, #fff); padding: 15px; border-radius: 12px; border: 1px solid var(--border-color, #e2e8f0);">
    <form method="GET" action="{{ route('admin.stock.in.requests.index') }}" id="filterForm">
      <div class="row g-2 align-items-end">

        {{-- Branch Filter --}}
        <div class="col-6 col-md-3 col-lg-3">
          <label style="font-size: 0.8rem; font-weight: 600; color: var(--text-muted); margin-bottom: 4px; display: block;">Branch</label>
          <select name="branch_id" id="branchFilter" class="input-form" style="margin-bottom: 0; height: 42px; padding: 5px;">
            <option value="">-- All Branches --</option>
            @foreach($branches as $b)
            <option value="{{ $b->id }}" {{ request('branch_id') == $b->id ? 'selected' : '' }}>{{ $b->name }}</option>
            @endforeach
          </select>
        </div>

        {{-- Status Filter --}}
        <div class="col-6 col-md-2 col-lg-2">
          <label style="font-size: 0.8rem; font-weight: 600; color: var(--text-muted); margin-bottom: 4px; display: block;">Status</label>
          <select name="status" id="statusFilter" class="input-form" style="margin-bottom: 0; height: 42px; padding: 5px;">
            <option value="">-- All --</option>
            <option value="pending"  {{ request('status') == 'pending'  ? 'selected' : '' }}>Pending</option>
            <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
            <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
          </select>
        </div>

        {{-- From Date --}}
        <div class="col-6 col-md-3 col-lg-3">
          <label style="font-size: 0.8rem; font-weight: 600; color: var(--text-muted); margin-bottom: 4px; display: block;">From Date</label>
          <input type="date" name="from_date" id="fromDate" class="input-form"
            value="{{ request('from_date') }}"
            style="margin-bottom: 0; height: 42px; padding: 5px 10px;">
        </div>

        {{-- To Date --}}
        <div class="col-6 col-md-3 col-lg-2">
          <label style="font-size: 0.8rem; font-weight: 600; color: var(--text-muted); margin-bottom: 4px; display: block;">To Date</label>
          <input type="date" name="to_date" id="toDate" class="input-form"
            value="{{ request('to_date') }}"
            style="margin-bottom: 0; height: 42px; padding: 5px 10px;">
        </div>

        {{-- Buttons --}}
        <div class="col-12 col-md-1 col-lg-2 d-flex gap-2">
          <button type="submit" class="btn btn-primary" style="height: 42px; flex: 1;">
            <i class="fas fa-filter"></i> Filter
          </button>
          <a href="{{ route('admin.stock.in.requests.index') }}" class="btn btn-outline-secondary" style="height: 42px; display: inline-flex; align-items: center; justify-content: center; padding: 0 12px;" title="Reset">
            <i class="fas fa-undo"></i>
          </a>
        </div>
      </div>
    </form>
  </div>

  <div class="table-wrapper">
    <table>
      <thead>
        <tr>
          <th>S.No</th>
          <th>Branch</th>
          <th>Supplier</th>
          <th>Amount</th>
          <th>Status</th>
          <th>Date & Time</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody class="desktop-table">
        @forelse($requests as $request)
        <tr>
          <td scope="row">{{ $loop->iteration }}</td>
          <td class="name">{{ $request->requestedBy->branch->name ?? 'N/A' }}</td>
          <td>{{ $request->supplier->company_name }}</td>
          <td>{{ number_format($request->net_total, 2) }} TK</td>
          <td>
            @if($request->status == "pending")
            <span class="status-pending-badge">Pending...</span>
            @elseif($request->status == 'rejected')
            <span class="status-rejected-badge">Rejected</span>
            @else
            <span class="status-approved-badge">Approved</span>
            @endif
          </td>
          <td>{{ $request->created_at->timezone(auth()->user()->timezone)->format('d M Y, h:i A') }}</td>
          <td class="action-icons">
            <a href="{{ route('admin.stock.in.request.show', $request->id) }}" class="icon-btn view-icon">
              <i class="fa-solid fa-eye"></i>
            </a>
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="7" class="text-center text-muted">No requests found.</td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <div class="manage-mobile-cards">
    @forelse($requests as $request)
    <div class="manage-card">

      <div class="card-body">
        <div><span>S.No</span>
          <p>{{ $loop->iteration }}</p>
        </div>
        <div><span>Branch</span>
          <p>{{ $request->requestedBy->branch->name ?? 'N/A' }}</p>
        </div>
        <div><span>Supplier</span>
          <p>{{ $request->supplier->company_name }}</p>
        </div>
        <div><span>Amount</span>
          <p>{{ number_format($request->net_total, 2) }} TK</p>
        </div>
        <div><span>Status</span>
          <p>
            @if($request->status == "pending")
            <span style="color:#d39e00;">⏳ Pending...</span>
            @elseif($request->status == "rejected")
            <span style="color:#dc3545;">● Rejected</span>
            @else
            <span style="color:#28a745;">✓ Approved</span>
            @endif
          </p>
        </div>
        <div><span>Date & Time</span>
          <p>{{ $request->created_at->timezone(auth()->user()->timezone)->format('d M Y, h:i A') }}</p>
        </div>
      </div>

      <div class="card-actions">
        <a href="{{ route('admin.stock.in.request.show', $request->id) }}" class="icon-btn view-icon">
          <i class="fa-solid fa-eye"></i>
        </a>
      </div>

    </div>
    @empty
    <p class="text-center text-muted">No requests found.</p>
    @endforelse
  </div>

</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Auto-submit on dropdown change
    ['branchFilter', 'statusFilter'].forEach(function(id) {
        const el = document.getElementById(id);
        if (el) el.addEventListener('change', function () {
            document.getElementById('filterForm').submit();
        });
    });

    // Auto-submit when both date fields are filled
    ['fromDate', 'toDate'].forEach(function(id) {
        const el = document.getElementById(id);
        if (el) el.addEventListener('change', function () {
            const from = document.getElementById('fromDate').value;
            const to   = document.getElementById('toDate').value;
            if (from && to) {
                document.getElementById('filterForm').submit();
            }
        });
    });
});
</script>
@endpush

@endsection