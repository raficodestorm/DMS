@extends('layouts.managerlayout')

@section('content')
<div class="manage-card">

  <div class="card-header">
    <h2>Stock-In Requests</h2>
    <p>Your all Stock-In Requests</p>
    @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif
  </div>

  {{-- Search and Filter Controls --}}
  <form method="GET" action="{{ route('manager.stock.in.requests.index') }}" class="mb-4" id="requestFilterForm" onsubmit="return false;">
    <div style="display: flex; gap: 12px; flex-wrap: wrap; align-items: flex-end; margin-bottom: 20px; background: var(--background); padding: 15px; border-radius: 12px; border: 1px solid var(--border-color);">
      
      {{-- Supplier Search --}}
      <div style="flex: 1; min-width: 200px;">
        <label style="font-size: 0.8rem; font-weight: 600; color: var(--text-muted); margin-bottom: 4px; display: block;">Search Supplier</label>
        <div style="position: relative;">
          <input type="text" 
                 name="search" 
                 id="supplierSearchInput"
                 class="input-form" 
                 placeholder="Search supplier name..." 
                 value="{{ request('search') }}"
                 style="margin-bottom: 0; padding-left: 36px; height: 42px;">
          <i class="fas fa-search" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: var(--text-muted);"></i>
        </div>
      </div>

      {{-- Status Filter --}}
      <div style="min-width: 160px;">
        <label style="font-size: 0.8rem; font-weight: 600; color: var(--text-muted); margin-bottom: 4px; display: block;">Filter Status</label>
        <select name="status" id="statusFilter" class="input-form" style=" padding: 5px; margin-bottom: 0; height: 42px;">
          <option value="">-- All Statuses --</option>
          <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
          <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
          <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
        </select>
      </div>

      {{-- From Date --}}
      <div style="min-width: 150px;">
        <label style="font-size: 0.8rem; font-weight: 600; color: var(--text-muted); margin-bottom: 4px; display: block;">From Date</label>
        <input type="date" 
               name="from_date" 
               id="fromDate" 
               class="input-form" 
               value="{{ request('from_date') }}"
               style="margin-bottom: 0; height: 42px;">
      </div>

      {{-- To Date --}}
      <div style="min-width: 150px;">
        <label style="font-size: 0.8rem; font-weight: 600; color: var(--text-muted); margin-bottom: 4px; display: block;">To Date</label>
        <input type="date" 
               name="to_date" 
               id="toDate" 
               class="input-form" 
               value="{{ request('to_date') }}"
               style="margin-bottom: 0; height: 42px;">
      </div>

      {{-- Reset Button --}}
      <div id="resetBtnWrapper" style="display: {{ (request('search') || request('status') || request('from_date') || request('to_date')) ? 'block' : 'none' }};">
        <button type="button" id="resetRequestFilterBtn" class="btn-submit" style="padding: 0 1rem; height: 42px; font-size: 0.85rem; border: none; cursor: pointer; display: inline-flex; align-items: center; gap: 6px; background: #6c757d; color: #fff; border-radius: 8px;">
          <i class="fas fa-undo"></i> Reset
        </button>
      </div>

    </div>
  </form>

  <div class="table-wrapper">
    <table>
      <thead>
        <tr>
          <th>S.No</th>
          <th>Supplier</th>
          <th>Amount</th>
          <th>Status</th>
          <th>Date & Time</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody class="desktop-table">
        @forelse($requests as $request)
        <tr class="request-item-row"
            data-date="{{ $request->created_at->format('Y-m-d') }}"
            data-supplier="{{ strtolower($request->supplier->company_name) }}"
            data-status="{{ strtolower($request->status) }}">
          <td scope="row">{{ $loop->iteration }}</td>
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
            <a href="{{ route('manager.stock.in.request.show', $request->id) }}" class="icon-btn view-icon">
              <i class="fa-solid fa-eye"></i>
            </a>
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="8" class="text-center text-muted">No requests found.</td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>
  <div class="manage-mobile-cards">
    @forelse($requests as $request)
    <div class="manage-card request-item-card"
         data-date="{{ $request->created_at->format('Y-m-d') }}"
         data-supplier="{{ strtolower($request->supplier->company_name) }}"
         data-status="{{ strtolower($request->status) }}">

      <div class="card-body">
        <div><span>S.No</span>
          <p>{{ $loop->iteration }}</p>
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
            <span style="color:#d39e00;">Pending...</span>
            @elseif($request->status == "rejected")
            <span style="color:#dc3545;">● Rejected</span>
            @else
            <span style="color:#28a745;">● Approved</span>
            @endif
          </p>
        </div>
        <div><span>Data & Time</span>
          <p>{{ $request->created_at->timezone(auth()->user()->timezone)->format('d M Y, h:i A') }}</p>
        </div>
      </div>

      <div class="card-actions">
        <a href="{{ route('manager.stock.in.request.show', $request->id) }}" class="icon-btn view-icon">
          <i class="fa-solid fa-eye"></i>
        </a>

      </div>

    </div>
    @empty
    <p class="text-center text-muted">No requests found.</p>
    @endforelse
  </div>

</div>
@endsection

@push('scripts')
<script type="module">
  $(document).ready(function () {
    function filterRequestTable() {
      const searchVal = $('#supplierSearchInput').val().toLowerCase().trim();
      const statusVal = $('#statusFilter').val().toLowerCase();
      const fromDate  = $('#fromDate').val();
      const toDate    = $('#toDate').val();

      if (searchVal || statusVal || fromDate || toDate) {
        $('#resetBtnWrapper').show();
      } else {
        $('#resetBtnWrapper').hide();
      }

      $('.request-item-row, .request-item-card').each(function () {
        const $el       = $(this);
        const rowDate   = $el.data('date');     // YYYY-MM-DD
        const rowStatus = ($el.data('status') || '').toString().toLowerCase();
        const supplier  = ($el.data('supplier') || '').toString().toLowerCase();

        let matchesDate = true;
        if (fromDate && rowDate < fromDate) matchesDate = false;
        if (toDate && rowDate > toDate) matchesDate = false;

        let matchesStatus = !statusVal || rowStatus === statusVal;
        let matchesSearch = !searchVal || supplier.includes(searchVal);

        if (matchesDate && matchesStatus && matchesSearch) {
          $el.show();
        } else {
          $el.hide();
        }
      });
    }

    // Initial filter execution
    filterRequestTable();

    // Event listeners
    $('#supplierSearchInput').on('keyup input', filterRequestTable);
    $('#statusFilter, #fromDate, #toDate').on('change', filterRequestTable);

    // Instant Reset
    $('#resetRequestFilterBtn').on('click', function () {
      $('#supplierSearchInput').val('');
      $('#statusFilter').val('');
      $('#fromDate').val('');
      $('#toDate').val('');
      filterRequestTable();
      if (window.history.pushState) {
        window.history.pushState(null, '', '{{ route("manager.stock.in.requests.index") }}');
      }
    });
  });
</script>
@endpush