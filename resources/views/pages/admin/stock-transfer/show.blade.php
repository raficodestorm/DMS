@extends('layouts.adminlayout')

@section('content')
<div class="manage-card">
  <div class="card-header">
    <div class="header-left">
      <h2>Stock Transfer Request BRST{{ $transfer->id }}</h2>
      <p>Requested by: {{ $transfer->requestedBy->name }} ({{ $transfer->fromBranch->name }})</p>
    </div>
    <div class="header-right">
      @if($transfer->status == 'pending')
        <span class="status-pending-badge">Pending Approval</span>
      @elseif($transfer->status == 'approved')
        <span class="status-approved-badge" style="background: #fff3e0; color: #ef6c00;">Approved</span>
      @elseif($transfer->status == 'completed')
        <span class="status-approved-badge">Completed</span>
      @elseif($transfer->status == 'rejected')
        <span class="status-rejected-badge">Rejected</span>
      @endif
    </div>
  </div>

  @include('components.alert')

  <div class="transfer-info-grid" style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 30px; background: #f9f9f9; padding: 20px; border-radius: 8px;">
    <div>
      <h4 style="margin-bottom: 10px; color: #333;"><i class="fas fa-arrow-up"></i> Source Branch</h4>
      <p><strong>Branch:</strong> {{ $transfer->fromBranch->name }}</p>
      <p><strong>Date:</strong> {{ $transfer->created_at->format('d M Y, h:i A') }}</p>
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
          <th>Requested Quantity</th>
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

    @if($transfer->status == 'pending')
      <form action="{{ route('admin.stock-transfer.reject', $transfer->id) }}" method="POST" onsubmit="return confirm('Reject this transfer?')">
        @csrf
        <button type="submit" class="btn-smart btn-red" >
          <i class="fas fa-times-circle"></i> Reject
        </button>
      </form>

      <form action="{{ route('admin.stock-transfer.approve', $transfer->id) }}" method="POST" onsubmit="return confirm('Approve this transfer?')">
        @csrf
        <button type="submit" class="btn-smart btn-blue">
          <i class="fas fa-check-circle"></i> Approve Transfer
        </button>
      </form>
    @endif
  </div>
</div>


<div style="text-align: center; margin-top: 20px;">
  <a href="{{ route('admin.stock-transfer.index') }}" style="color: var(--text-muted); text-decoration: none;">
    <i class="fas fa-arrow-left"></i> Back to List
  </a>
</div>
@endsection
