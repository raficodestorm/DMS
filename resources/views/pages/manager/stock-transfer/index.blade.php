@extends('layouts.managerlayout')

@section('content')
<div class="manage-card">

  <div class="card-header">
    <div class="header-left">
      <h2>Stock Transfer Requests</h2>
      <p>Manage your branch's incoming and outgoing stock transfers</p>
    </div>
    <div class="header-right mb-4">
      <a href="{{ route('manager.stock-transfer.create') }}" class="btn-submit" style="text-decoration: none; padding: 10px 20px;">
        <i class="fas fa-plus"></i> New Transfer Request
      </a>
    </div>
  </div>

  @include('components.alert')

  {{-- Search and Filter Controls --}}
  <form method="GET" action="{{ route('manager.stock-transfer.index') }}" class="mb-4" id="transferFilterForm">
    <div style="display: flex; gap: 12px; flex-wrap: wrap; align-items: flex-end; margin-bottom: 20px; background: var(--background); padding: 15px; border-radius: 12px; border: 1px solid var(--border-color);">
      
      {{-- Search Bar (ID or Branch) --}}
      <div style="flex: 1; min-width: 180px;">
        <label style="font-size: 0.8rem; font-weight: 600; color: var(--text-muted); margin-bottom: 4px; display: block;">Search</label>
        <div style="position: relative;">
          <input type="text" 
                 name="search" 
                 id="transferSearchInput"
                 class="input-form" 
                 placeholder="ID or Branch name..." 
                 value="{{ request('search') }}"
                 style="margin-bottom: 0; padding-left: 36px; height: 42px;">
          <i class="fas fa-search" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: var(--text-muted);"></i>
        </div>
      </div>

      {{-- Transfer Type Filter --}}
      <div style="min-width: 140px;">
        <label style="font-size: 0.8rem; font-weight: 600; color: var(--text-muted); margin-bottom: 4px; display: block;">Transfer Type</label>
        <select name="transfer_type" id="typeFilter" class="input-form" style="padding: 5px; margin-bottom: 0; height: 42px;">
          <option value="">-- All Types --</option>
          <option value="outgoing" {{ request('transfer_type') == 'outgoing' ? 'selected' : '' }}>Outgoing</option>
          <option value="incoming" {{ request('transfer_type') == 'incoming' ? 'selected' : '' }}>Incoming</option>
        </select>
      </div>

      {{-- Status Filter --}}
      <div style="min-width: 140px;">
        <label style="font-size: 0.8rem; font-weight: 600; color: var(--text-muted); margin-bottom: 4px; display: block;">Filter Status</label>
        <select name="status" id="statusFilter" class="input-form" style="padding: 5px; margin-bottom: 0; height: 42px;">
          <option value="">-- All Statuses --</option>
          <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
          <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
          <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
          <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
        </select>
      </div>

      {{-- From Date --}}
      <div style="min-width: 135px;">
        <label style="font-size: 0.8rem; font-weight: 600; color: var(--text-muted); margin-bottom: 4px; display: block;">From Date</label>
        <input type="date" 
               name="from_date" 
               id="fromDate" 
               class="input-form" 
               value="{{ request('from_date') }}"
               style="margin-bottom: 0; height: 42px;">
      </div>

      {{-- To Date --}}
      <div style="min-width: 135px;">
        <label style="font-size: 0.8rem; font-weight: 600; color: var(--text-muted); margin-bottom: 4px; display: block;">To Date</label>
        <input type="date" 
               name="to_date" 
               id="toDate" 
               class="input-form" 
               value="{{ request('to_date') }}"
               style="margin-bottom: 0; height: 42px;">
      </div>

      {{-- Action Buttons --}}
      <div style="display: flex; gap: 8px;">
        <button type="submit" class="btn-submit" style="padding: 0 1.2rem; height: 42px; font-size: 0.85rem; border: none; cursor: pointer; display: inline-flex; align-items: center; gap: 6px; border-radius: 8px;">
          <i class="fas fa-filter"></i> Filter
        </button>
        @if(request('search') || request('transfer_type') || request('status') || request('from_date') || request('to_date'))
        <a href="{{ route('manager.stock-transfer.index') }}" class="btn-submit" style="padding: 0 1rem; height: 42px; font-size: 0.85rem; text-decoration: none; display: inline-flex; align-items: center; gap: 6px; background: #6c757d; color: #fff; border-radius: 8px;">
          <i class="fas fa-undo"></i> Reset
        </a>
        @endif
      </div>

    </div>
  </form>

  <div class="table-wrapper">
    <table>
      <thead>
        <tr>
          <th>ID</th>
          <th>Type</th>
          <th>Branch</th>
          <th>Status</th>
          <th>Date</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody class="desktop-table">
        @forelse($transfers as $transfer)
        @php
          $isOutgoing = $transfer->from_branch_id == auth()->user()->branch_id;
          $targetBranch = $isOutgoing ? $transfer->toBranch->name : $transfer->fromBranch->name;
          $typeVal = $isOutgoing ? 'outgoing' : 'incoming';
        @endphp
        <tr class="transfer-item-row"
            data-date="{{ $transfer->created_at->format('Y-m-d') }}"
            data-type="{{ $typeVal }}"
            data-status="{{ strtolower($transfer->status) }}"
            data-search="brst{{ $transfer->id }} {{ strtolower($targetBranch) }}">
          <td>BRST{{ $transfer->id }}</td>
          <td>
            @if($isOutgoing)
              <span class="badge" style="background: #e3f2fd; color: #1976d2; padding: 4px 8px; border-radius: 4px;">Outgoing</span>
            @else
              <span class="badge" style="background: #f1f8e9; color: #388e3c; padding: 4px 8px; border-radius: 4px;">Incoming</span>
            @endif
          </td>
          <td>
            @if($isOutgoing)
              To: {{ $transfer->toBranch->name }}
            @else
              From: {{ $transfer->fromBranch->name }}
            @endif
          </td>
          <td>
            @if($transfer->status == 'pending')
              <span class="status-pending-badge">Pending</span>
            @elseif($transfer->status == 'approved')
              <span class="status-approved-badge" style="background: #fff3e0; color: #ef6c00;">Approved</span>
            @elseif($transfer->status == 'completed')
              <span class="status-approved-badge">Completed</span>
            @elseif($transfer->status == 'rejected')
              <span class="status-rejected-badge">Rejected</span>
            @endif
          </td>
          <td>{{ $transfer->created_at->format('d M Y') }}</td>
          <td class="action-icons">
            <a href="{{ route('manager.stock-transfer.show', $transfer->id) }}" class="icon-btn view-icon">
              <i class="fa-solid fa-eye"></i>
            </a>
            @if($transfer->status == 'pending' && $isOutgoing)
              <a href="{{ route('manager.stock-transfer.edit', $transfer->id) }}" class="icon-btn edit-icon">
                <i class="fa-solid fa-pen-to-square"></i>
              </a>
              <form action="{{ route('manager.stock-transfer.destroy', $transfer->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this request?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="icon-btn delete-icon">
                  <i class="fa-solid fa-trash"></i>
                </button>
              </form>
            @endif
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="6" class="text-center text-muted">No stock transfers found.</td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <div class="manage-mobile-cards">
    @forelse($transfers as $transfer)
    @php
      $isOutgoing = $transfer->from_branch_id == auth()->user()->branch_id;
      $targetBranch = $isOutgoing ? $transfer->toBranch->name : $transfer->fromBranch->name;
      $typeVal = $isOutgoing ? 'outgoing' : 'incoming';
    @endphp
    <div class="manage-card transfer-item-card"
         data-date="{{ $transfer->created_at->format('Y-m-d') }}"
         data-type="{{ $typeVal }}"
         data-status="{{ strtolower($transfer->status) }}"
         data-search="brst{{ $transfer->id }} {{ strtolower($targetBranch) }}">
      <div class="card-body">
        <div><span>ID</span><p>BRST{{ $transfer->id }}</p></div>
        <div><span>Type</span>
          <p>
            @if($isOutgoing)
              <span style="color: #1976d2;">Outgoing</span>
            @else
              <span style="color: #388e3c;">Incoming</span>
            @endif
          </p>
        </div>
        <div><span>Branch</span>
          <p>
            @if($isOutgoing)
              To: {{ $transfer->toBranch->name }}
            @else
              From: {{ $transfer->fromBranch->name }}
            @endif
          </p>
        </div>
        <div><span>Status</span>
          <p>
            @if($transfer->status == 'pending')
              <span style="color: #ffc107;">Pending</span>
            @elseif($transfer->status == 'approved')
              <span style="color: #fd7e14;">Approved</span>
            @elseif($transfer->status == 'completed')
              <span style="color: #28a745;">Completed</span>
            @else
              <span style="color: #dc3545;">Rejected</span>
            @endif
          </p>
        </div>
        <div><span>Date</span><p>{{ $transfer->created_at->format('d M Y') }}</p></div>
      </div>
      <div class="card-actions">
        <a href="{{ route('manager.stock-transfer.show', $transfer->id) }}" class="icon-btn view-icon">
          <i class="fa-solid fa-eye"></i>
        </a>
        @if($transfer->status == 'pending' && $isOutgoing)
          <a href="{{ route('manager.stock-transfer.edit', $transfer->id) }}" class="icon-btn edit-icon">
            <i class="fa-solid fa-pen-to-square"></i>
          </a>
          <form action="{{ route('manager.stock-transfer.destroy', $transfer->id) }}" method="POST" style="display:inline;">
            @csrf
            @method('DELETE')
            <button type="submit" class="icon-btn delete-icon" onclick="return confirm('Are you sure?')">
              <i class="fa-solid fa-trash"></i>
            </button>
          </form>
        @endif
      </div>
    </div>
    @empty
    <p class="text-center text-muted">No stock transfers found.</p>
    @endforelse
  </div>

  <div class="pagination-wrapper">
    {{ $transfers->links() }}
  </div>

</div>
@endsection

@push('scripts')
<script type="module">
  $(document).ready(function () {
    function liveFilterTransfers() {
      const searchVal = $('#transferSearchInput').val().toLowerCase().trim();
      const typeVal   = $('#typeFilter').val().toLowerCase();
      const statusVal = $('#statusFilter').val().toLowerCase();
      const fromDate  = $('#fromDate').val();
      const toDate    = $('#toDate').val();

      $('.transfer-item-row, .transfer-item-card').each(function () {
        const $el        = $(this);
        const rowDate    = $el.data('date');     // YYYY-MM-DD
        const rowType    = ($el.data('type') || '').toString().toLowerCase();
        const rowStatus  = ($el.data('status') || '').toString().toLowerCase();
        const searchData = ($el.data('search') || '').toString().toLowerCase();

        let matchesDate = true;
        if (fromDate && rowDate < fromDate) matchesDate = false;
        if (toDate && rowDate > toDate) matchesDate = false;

        let matchesType   = !typeVal || rowType === typeVal;
        let matchesStatus = !statusVal || rowStatus === statusVal;
        let matchesSearch = !searchVal || searchData.includes(searchVal);

        if (matchesDate && matchesType && matchesStatus && matchesSearch) {
          $el.show();
        } else {
          $el.hide();
        }
      });
    }

    // Live JS filter as user types or picks inputs on current page
    $('#transferSearchInput').on('keyup input', liveFilterTransfers);
    $('#typeFilter, #statusFilter, #fromDate, #toDate').on('change', liveFilterTransfers);
  });
</script>
@endpush
