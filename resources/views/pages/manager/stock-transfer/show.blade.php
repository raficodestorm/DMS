@extends('layouts.managerlayout')

@section('content')
<div class="manage-card">
  <div class="card-header">
    <div class="header-left">
      <h2>Stock Transfer Details BRST{{ $transfer->id }}</h2>
      <p>Request Date: {{ $transfer->created_at->format('d M Y, h:i A') }}</p>
    </div>
    <div class="header-right">
      @if($transfer->status == 'pending')
        <span class="status-pending-badge">Pending Approval</span>
      @elseif($transfer->status == 'approved')
        <span class="status-approved-badge" style="background: #fff3e0; color: #ef6c00;">Awaiting Receipt</span>
      @elseif($transfer->status == 'completed')
        <span class="status-approved-badge">Completed</span>
      @elseif($transfer->status == 'rejected')
        <span class="status-rejected-badge">Rejected</span>
      @endif
    </div>
  </div>

  @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif
  @if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
  @endif

  <div class="transfer-info-grid" style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 30px; background: #f9f9f9; padding: 20px; border-radius: 8px;">
    <div>
      <h4 style="margin-bottom: 10px; color: #333;"><i class="fas fa-arrow-up"></i> Source Branch</h4>
      <p><strong>Branch:</strong> {{ $transfer->fromBranch->name }}</p>
      <p><strong>Requested By:</strong> {{ $transfer->requestedBy->name }}</p>
    </div>
    <div>
      <h4 style="margin-bottom: 10px; color: #333;"><i class="fas fa-arrow-down"></i> Destination Branch</h4>
      <p><strong>Branch:</strong> {{ $transfer->toBranch->name }}</p>
      <p><strong>Note:</strong> {{ $transfer->note ?? 'N/A' }}</p>
    </div>
  </div>

  <div class="table-wrapper">
    <table class="table">
      <thead>
        <tr>
          <th>S.No</th>
          <th>Product Name</th>
          <th>Quantity</th>
          <th>Item Note</th>
        </tr>
      </thead>
      <tbody>
        @foreach($transfer->items as $item)
        <tr>
          <td>{{ $loop->iteration }}</td>
          <td>{{ $item->product->name }}</td>
          <td>{{ $item->quantity }}</td>
          <td>{{ $item->note ?? '-' }}</td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>

  <div class="action-footer" style="margin-top: 30px; display: flex; justify-content: flex-end; gap: 15px;">
    
    @if($transfer->status == 'approved' && $transfer->to_branch_id == auth()->user()->branch_id)
      <form action="{{ route('manager.stock-transfer.receive', $transfer->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to receive this stock? This will update inventory for both branches.')">
        @csrf
        <button type="submit" class="btn-smart btn-green">
          <i class="fas fa-check-circle"></i> Receive Stock
        </button>
      </form>
    @endif

    {{-- Delete Button for Requesting Manager --}}
    @if($transfer->status == 'pending' && $transfer->from_branch_id == auth()->user()->branch_id)
      <form action="{{ route('manager.stock-transfer.destroy', $transfer->id) }}" method="POST" onsubmit="return confirm('Delete this request?')">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn-smart btn-red" >
          <i class="fas fa-trash"></i> Delete
        </button>
      </form>
    @endif
  </div>
</div>

<div style="text-align: center; margin-top: 20px;">
  <a href="{{ route('manager.stock-transfer.index') }}" style="color: var(--text-muted); text-decoration: none;">
    <i class="fas fa-arrow-left"></i> Back to List
  </a>
</div>
@endsection
