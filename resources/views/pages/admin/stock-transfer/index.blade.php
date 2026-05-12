@extends('layouts.adminlayout')

@section('content')
<div class="manage-card">

  <div class="card-header">
    <h2>Stock Transfer Management</h2>
    <p>Review and approve stock transfer requests between branches</p>
    @include('components.alert')
  </div>

  <div class="table-wrapper">
    <table>
      <thead>
        <tr>
          <th>ID</th>
          <th>From Branch</th>
          <th>To Branch</th>
          <th>Requested By</th>
          <th>Status</th>
          <th>Date</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody class="desktop-table">
        @forelse($transfers as $transfer)
        <tr>
          <td>BRST{{ $transfer->id }}</td>
          <td>{{ $transfer->fromBranch->name }}</td>
          <td>{{ $transfer->toBranch->name }}</td>
          <td>{{ $transfer->requestedBy->name }}</td>
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
              <form action="{{ route('admin.stock-transfer.destroy', $transfer->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure?')">
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
        <div><span>From</span><p>{{ $transfer->fromBranch->name }}</p></div>
        <div><span>To</span><p>{{ $transfer->toBranch->name }}</p></div>
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
      </div>
      <div class="card-actions">
        <a href="{{ route('admin.stock-transfer.show', $transfer->id) }}" class="icon-btn view-icon">
          <i class="fa-solid fa-eye"></i>
        </a>
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
