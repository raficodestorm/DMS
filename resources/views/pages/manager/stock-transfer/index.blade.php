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
        <tr>
          <td>BRST{{ $transfer->id }}</td>
          <td>
            @if($transfer->from_branch_id == auth()->user()->branch_id)
              <span class="badge" style="background: #e3f2fd; color: #1976d2; padding: 4px 8px; border-radius: 4px;">Outgoing</span>
            @else
              <span class="badge" style="background: #f1f8e9; color: #388e3c; padding: 4px 8px; border-radius: 4px;">Incoming</span>
            @endif
          </td>
          <td>
            @if($transfer->from_branch_id == auth()->user()->branch_id)
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
            @if($transfer->status == 'pending' && $transfer->from_branch_id == auth()->user()->branch_id)
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
    <div class="manage-card">
      <div class="card-body">
        <div><span>ID</span><p>BRST{{ $transfer->id }}</p></div>
        <div><span>Type</span>
          <p>
            @if($transfer->from_branch_id == auth()->user()->branch_id)
              <span style="color: #1976d2;">Outgoing</span>
            @else
              <span style="color: #388e3c;">Incoming</span>
            @endif
          </p>
        </div>
        <div><span>Branch</span>
          <p>
            @if($transfer->from_branch_id == auth()->user()->branch_id)
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
        @if($transfer->status == 'pending' && $transfer->from_branch_id == auth()->user()->branch_id)
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
