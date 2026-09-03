@extends('layouts.adminlayout')

@section('content')
<div class="manage-card">

  <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
    <div>
      <h2 class="mb-0">Stock Transfer Management</h2>
      <p class="text-muted mb-0">Review and approve stock transfer requests between branches</p>
    </div>
    
  </div>

  @include('components.alert')

  {{-- Smart Filter Bar --}}
  <div style="margin: 15px 0; background: var(--section-bg, #fff); padding: 15px; border-radius: 12px; border: 1px solid var(--border-color, #e2e8f0);">
    <form method="GET" action="{{ route('admin.stock-transfer.index') }}" id="filterForm">
      <div class="row g-2 align-items-end">

        {{-- Search Input (ID & From Branch / To Branch) --}}
        <div class="col-12 col-md-4 col-lg-3">
          <label style="font-size: 0.8rem; font-weight: 600; color: var(--text-muted); margin-bottom: 4px; display: block;">Search</label>
          <div style="position: relative;">
            <input type="text" 
                   name="search" 
                   id="searchInput" 
                   class="input-form" 
                   placeholder="Search by ID or Branch..." 
                   value="{{ request('search') }}"
                   style="margin-bottom: 0; padding-left: 35px; height: 42px;">
            <i class="fas fa-search" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: var(--text-muted);"></i>
          </div>
        </div>

        {{-- From Branch Filter --}}
        <div class="col-6 col-md-3 col-lg-2">
          <label style="font-size: 0.8rem; font-weight: 600; color: var(--text-muted); margin-bottom: 4px; display: block;">From Branch</label>
          <select name="from_branch_id" id="fromBranchFilter" class="input-form" style="margin-bottom: 0; height: 42px; padding: 5px;">
            <option value="">-- All Branches --</option>
            @foreach($branches as $b)
            <option value="{{ $b->id }}" {{ request('from_branch_id') == $b->id ? 'selected' : '' }}>{{ $b->name }}</option>
            @endforeach
          </select>
        </div>

        {{-- Status Filter --}}
        <div class="col-6 col-md-2 col-lg-2">
          <label style="font-size: 0.8rem; font-weight: 600; color: var(--text-muted); margin-bottom: 4px; display: block;">Status</label>
          <select name="status" id="statusFilter" class="input-form" style="margin-bottom: 0; height: 42px; padding: 5px;">
            <option value="">-- All Statuses --</option>
            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
            <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
            <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
            <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
          </select>
        </div>

        {{-- From Date --}}
        <div class="col-6 col-md-3 col-lg-2">
          <label style="font-size: 0.8rem; font-weight: 600; color: var(--text-muted); margin-bottom: 4px; display: block;">From Date</label>
          <input type="date" 
                 name="from_date" 
                 id="fromDate" 
                 class="input-form" 
                 value="{{ request('from_date') }}"
                 style="margin-bottom: 0; height: 42px; padding: 5px 10px;">
        </div>

        {{-- To Date --}}
        <div class="col-6 col-md-3 col-lg-2">
          <label style="font-size: 0.8rem; font-weight: 600; color: var(--text-muted); margin-bottom: 4px; display: block;">To Date</label>
          <input type="date" 
                 name="to_date" 
                 id="toDate" 
                 class="input-form" 
                 value="{{ request('to_date') }}"
                 style="margin-bottom: 0; height: 42px; padding: 5px 10px;">
        </div>

        {{-- Buttons --}}
        <div class="col-12 col-md-2 col-lg-1 d-flex gap-1">
          <button type="submit" class="btn btn-primary w-100" style="height: 42px; display: inline-flex; align-items: center; justify-content: center;" title="Filter">
            <i class="fas fa-filter"></i>
          </button>
          @if(request('search') || request('from_branch_id') || request('status') || request('from_date') || request('to_date'))
          <a href="{{ route('admin.stock-transfer.index') }}" class="btn btn-outline-secondary w-100" style="height: 42px; display: inline-flex; align-items: center; justify-content: center;" title="Reset">
            <i class="fas fa-undo"></i>
          </a>
          @endif
        </div>

      </div>
    </form>
  </div>

  <div class="table-wrapper">
    <table>
      <thead>
        <tr>
          <th>ID</th>
          <th>From Branch</th>
          <th>To Branch</th>
          <th>Status</th>
          <th>Date</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody class="desktop-table">
        @forelse($transfers as $transfer)
        <tr>
          <td><strong>BRST{{ $transfer->id }}</strong></td>
          <td>{{ $transfer->fromBranch->name ?? 'N/A' }}</td>
          <td>{{ $transfer->toBranch->name ?? 'N/A' }}</td>
          
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
            <a href="{{ route('admin.stock-transfer.show', $transfer->id) }}" class="icon-btn view-icon">
              <i class="fa-solid fa-eye"></i>
            </a>
            @if($transfer->status != 'completed')
              <form action="{{ route('admin.stock-transfer.destroy', $transfer->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this transfer request?')">
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
          <td colspan="7" class="text-center text-muted">No stock transfers found.</td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <div class="manage-mobile-cards">
    @forelse($transfers as $transfer)
    <div class="manage-card">
      <div class="card-body">
        <div><span>ID</span><p>BRST{{ $transfer->id }}</p></div>
        <div><span>From</span><p>{{ $transfer->fromBranch->name ?? 'N/A' }}</p></div>
        <div><span>To</span><p>{{ $transfer->toBranch->name ?? 'N/A' }}</p></div>
        
        <div><span>Status</span>
          <p>
            @if($transfer->status == 'pending')
              <span style="color: #ffc107;">⏳ Pending</span>
            @elseif($transfer->status == 'approved')
              <span style="color: #fd7e14;">● Approved</span>
            @elseif($transfer->status == 'completed')
              <span style="color: #28a745;">✓ Completed</span>
            @else
              <span style="color: #dc3545;">✖ Rejected</span>
            @endif
          </p>
        </div>
        <div><span>Date</span><p>{{ $transfer->created_at->format('d M Y') }}</p></div>
      </div>
      <div class="card-actions">
        <a href="{{ route('admin.stock-transfer.show', $transfer->id) }}" class="icon-btn view-icon">
          <i class="fa-solid fa-eye"></i>
        </a>
        @if($transfer->status != 'completed')
          <form action="{{ route('admin.stock-transfer.destroy', $transfer->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure?')">
            @csrf
            @method('DELETE')
            <button type="submit" class="icon-btn delete-icon">
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

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const filterForm = document.getElementById('filterForm');

    // Auto-submit on select change
    ['fromBranchFilter', 'statusFilter'].forEach(function(id) {
        const el = document.getElementById(id);
        if (el) {
            el.addEventListener('change', function () {
                filterForm.submit();
            });
        }
    });

    // Auto-submit on date change when both filled or single date selected
    ['fromDate', 'toDate'].forEach(function(id) {
        const el = document.getElementById(id);
        if (el) {
            el.addEventListener('change', function () {
                filterForm.submit();
            });
        }
    });

    // Debounce live search
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        let timer;
        searchInput.addEventListener('keyup', function () {
            clearTimeout(timer);
            timer = setTimeout(function () {
                filterForm.submit();
            }, 450);
        });
    }
});
</script>
@endpush

@endsection
