@extends('layouts.managerlayout')

@section('content')
<style>
  .request-header-box {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    border-bottom: 2px solid var(--background);
    padding-bottom: 15px;
  }

  .info-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 15px;
    margin-bottom: 25px;
    background: var(--section-bg, #f9f9f9);
    padding: 20px;
    border-radius: 8px;
    border: 1px solid var(--border-color, #e2e8f0);
  }

  .info-item label {
    color: var(--text-muted);
    font-size: 0.8rem;
    display: block;
    margin-bottom: 4px;
  }

  .info-item p {
    font-weight: 600;
    color: var(--text-main);
    margin: 0;
  }

  .action-bar {
    display: flex;
    gap: 10px;
    margin-top: 30px;
    justify-content: flex-end;
    flex-wrap: wrap;
  }

  .btn-smart {
    padding: 10px 20px;
    border-radius: 8px;
    font-weight: 600;
    text-decoration: none;
    border: none;
    cursor: pointer;
    transition: 0.3s;
    display: flex;
    align-items: center;
    gap: 5px;
    font-size: 14px;
  }

  .btn-green {
    background: #dcfce7;
    color: #16a34a;
    border: 1px solid #86efac;
  }

  .btn-green:hover {
    background: #16a34a;
    color: white;
  }

  .btn-red {
    background: #fee2e2;
    color: #dc2626;
    border: 1px solid #fca5a5;
  }

  .btn-red:hover {
    background: #dc2626;
    color: white;
  }

  .request-status-badge {
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: bold;
    text-transform: uppercase;
  }

  @media (max-width: 600px) {
    .info-grid {
      grid-template-columns: 1fr;
    }

    .action-bar {
      flex-direction: column;
    }

    .action-bar form,
    .btn-smart {
      width: 100%;
      justify-content: center;
    }
  }
</style>

<div class="manage-card">
  <div class="card-header">
    <div class="request-header-box">
      <div>
        <h3 style="margin:0; color:var(--primary);">Stock Transfer Details (BRST{{ $transfer->id }})</h3>
        <p class="text-muted mb-0" style="font-size:0.85rem; margin-top:4px;">Request Date: <strong>{{ $transfer->created_at->format('d M Y, h:i A') }}</strong></p>
      </div>

      <div>
        @if($transfer->status == 'pending')
          <span class="request-status-badge" style="background: #fffbeb; color: #d97706;">Pending Approval</span>
        @elseif($transfer->status == 'approved')
          <span class="request-status-badge" style="background: #fff3e0; color: #ef6c00;">Awaiting Receipt</span>
        @elseif($transfer->status == 'completed')
          <span class="request-status-badge" style="background: #dcfce7; color: #16a34a;">Completed</span>
        @elseif($transfer->status == 'rejected')
          <span class="request-status-badge" style="background: #fee2e2; color: #dc2626;">Rejected</span>
        @endif
      </div>
    </div>

    @include('components.alert')
  </div>

  <div class="info-grid">
    <div>
      <h4 style="margin-bottom: 10px; color: var(--primary);"><i class="fas fa-arrow-up"></i> Source Branch</h4>
      <div class="info-item mb-2">
        <label>Branch Name</label>
        <p>{{ $transfer->fromBranch->name ?? 'N/A' }}</p>
      </div>
      <div class="info-item">
        <label>Requested By</label>
        <p>{{ $transfer->requestedBy->name ?? 'N/A' }}</p>
      </div>
    </div>

    <div>
      <h4 style="margin-bottom: 10px; color: var(--primary);"><i class="fas fa-arrow-down"></i> Destination Branch</h4>
      <div class="info-item mb-2">
        <label>Branch Name</label>
        <p>{{ $transfer->toBranch->name ?? 'N/A' }}</p>
      </div>
      <div class="info-item">
        <label>Note</label>
        <p>{{ $transfer->note ?? 'N/A' }}</p>
      </div>
    </div>
  </div>

  <h4 style="color:var(--text-muted); border-left: 4px solid var(--primary); padding-left: 10px; margin-bottom: 15px;">
    Transfer Items</h4>

  <div class="table-wrapper">
    <table>
      <thead>
        <tr>
          <th>S.No</th>
          <th>Product Name</th>
          <th>Quantity</th>
          <th>Item Note</th>
        </tr>
      </thead>
      <tbody class="desktop-table">
        @forelse($transfer->items as $item)
        <tr>
          <td>{{ $loop->iteration }}</td>
          <td><strong>{{ $item->product->name }}</strong></td>
          <td>{{ $item->quantity }} Pcs</td>
          <td>{{ $item->note ?? '-' }}</td>
        </tr>
        @empty
        <tr>
          <td colspan="4" class="text-center text-muted">No items found.</td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <div class="manage-mobile-cards">
    @foreach($transfer->items as $item)
    <div class="manage-card" style="margin-bottom: 10px; padding: 10px; border: 1px solid var(--border-color);">
      <div class="card-body">
        <div><span>Product</span>
          <p>{{ $item->product->name }}</p>
        </div>
        <div><span>Qty</span>
          <p>{{ $item->quantity }} Pcs</p>
        </div>
        <div><span>Item Note</span>
          <p>{{ $item->note ?? '-' }}</p>
        </div>
      </div>
    </div>
    @endforeach
  </div>

  <div class="action-bar">
    @if($transfer->status == 'approved' && $transfer->to_branch_id == auth()->user()->branch_id)
      <form action="{{ route('manager.stock-transfer.receive', $transfer->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to receive this stock? This will update inventory for both branches.')">
        @csrf
        <button type="submit" class="btn-smart btn-green">
          <i class="fas fa-check-circle"></i> Receive Stock
        </button>
      </form>
    @endif

    @if($transfer->status == 'pending' && $transfer->from_branch_id == auth()->user()->branch_id)
      <form action="{{ route('manager.stock-transfer.destroy', $transfer->id) }}" method="POST" onsubmit="return confirm('Delete this request?')">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn-smart btn-red">
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
