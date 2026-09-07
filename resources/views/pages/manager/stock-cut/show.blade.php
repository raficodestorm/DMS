@extends('layouts.managerlayout')

@section('content')

<style>
  .request-header-box {
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-bottom: 2px solid var(--border);
    padding-bottom: 12px;
    margin-bottom: 15px;
  }

  .request-status-badge {
    padding: 6px 14px;
    border-radius: 20px;
    font-size: 0.85rem;
    font-weight: 700;
    letter-spacing: 0.5px;
    text-transform: uppercase;
  }

  .info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 15px;
    background: var(--background);
    padding: 15px;
    border-radius: 8px;
    margin-bottom: 20px;
    border: 1px solid var(--border);
  }

  .info-item label {
    font-size: 0.75rem;
    color: var(--text-muted);
    margin-bottom: 3px;
    display: block;
    text-transform: uppercase;
    font-weight: 700;
  }

  .info-item p {
    font-size: 0.95rem;
    font-weight: 600;
    margin: 0;
    color: var(--text-main);
  }

  .action-bar {
    display: flex;
    gap: 10px;
    justify-content: flex-end;
    margin-top: 25px;
    border-top: 1px solid var(--border);
    padding-top: 15px;
  }

  @media (max-width: 768px) {
    .action-bar {
      flex-direction: column;
    }

    .btn-smart {
      width: 100%;
      justify-content: center;
    }
  }
</style>

<div class="manage-card">

  <div class="card-header">
    <div class="request-header-box">
      <h3 style="margin:0; color:var(--primary);"><i class="fas fa-scissors me-2"></i>Stock Return Request Details</h3>
      <div>
        @if($stockCut->status === 'approved')
          <span class="request-status-badge" style="background: #dcfce7; color: #15803d;">
            <i class="fas fa-check-circle me-1"></i> Approved
          </span>
        @elseif($stockCut->status === 'rejected')
          <span class="request-status-badge" style="background: #fee2e2; color: #b91c1c;">
            <i class="fas fa-times-circle me-1"></i> Rejected
          </span>
        @else
          <span class="request-status-badge" style="background: #fef9c3; color: #a16207;">
            <i class="fas fa-clock me-1"></i> Pending Approval
          </span>
        @endif
      </div>
    </div>

    @include('components.alert')
  </div>

  <div class="info-grid">
    <div class="info-item">
      <label>Supplier Name</label>
      <p>{{ $stockCut->supplier->company_name ?? 'N/A' }}</p>
    </div>
    <div class="info-item">
      <label>Requested By</label>
      <p>{{ $stockCut->requestedBy->fullname ?? $stockCut->requestedBy->username ?? 'N/A' }}</p>
    </div>
    <div class="info-item">
      <label>Branch</label>
      <p>{{ $stockCut->branch->name ?? 'Head Office' }}</p>
    </div>
    <div class="info-item">
      <label>Date</label>
      <p>{{ $stockCut->created_at ? $stockCut->created_at->timezone(auth()->user()->timezone)->format('d M Y, h:i A') : 'N/A' }}</p>
    </div>
  </div>

  <h4 style="color:var(--text-muted); border-left: 4px solid var(--primary); padding-left: 10px; margin-bottom: 12px;">Returned Products</h4>

  <div class="table-wrapper">
    <table>
      <thead>
        <tr>
          <th>S.No</th>
          <th>Product</th>
          <th>Rate</th>
          <th>Quantity</th>
          <th>Subtotal</th>
        </tr>
      </thead>
      <tbody class="desktop-table">
        @forelse($stockCut->items as $item)
        <tr>
          <td>{{ $loop->iteration }}</td>
          <td>{{ $item->product->name ?? 'N/A' }}</td>
          <td>{{ number_format($item->price, 2) }} TK</td>
          <td>{{ $item->quantity }}</td>
          <td>{{ number_format($item->total ?? ($item->price * $item->quantity), 2) }} TK</td>
        </tr>
        @empty
        <tr>
          <td colspan="5" class="text-center text-muted">No items found.</td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <div class="manage-mobile-cards">
    @forelse($stockCut->items as $item)
    <div class="manage-card">
      <div class="card-body">
        <div><span>S.No</span><p>{{ $loop->iteration }}</p></div>
        <div><span>Product</span><p>{{ $item->product->name ?? 'N/A' }}</p></div>
        <div><span>Rate</span><p>{{ number_format($item->price, 2) }} TK</p></div>
        <div><span>Quantity</span><p>{{ $item->quantity }}</p></div>
        <div><span>Subtotal</span><p>{{ number_format($item->total ?? ($item->price * $item->quantity), 2) }} TK</p></div>
      </div>
    </div>
    @empty
    <p class="text-center text-muted">No items found.</p>
    @endforelse
  </div>

  <div style="text-align: right; margin-top: 20px;">
    <h3 style="color: var(--primary);">Net Total: {{ number_format($stockCut->net_total, 2) }} TK</h3>
  </div>

  @if($stockCut->note)
  <div style="margin-top: 20px; padding: 15px; background: #fffbeb; border-radius: 8px; border-left: 4px solid #d97706;">
    <label style="color: #d97706; font-size: 0.8rem; display: block; font-weight: bold;">Note / Reason:</label>
    <p style="margin: 0; color: #92400e;">{{ $stockCut->note }}</p>
  </div>
  @endif

  <div class="action-bar">
    @if($stockCut->status === 'pending')
    <a href="{{ route('manager.stock.cut.edit', $stockCut->id) }}" class="btn-smart btn-blue">
      <i class="fas fa-edit"></i> Edit Request
    </a>

    <form action="{{ route('manager.stock.cut.destroy', $stockCut->id) }}" method="POST"
      onsubmit="return confirm('Are you sure you want to delete this pending return request?')" style="display:inline;">
      @csrf
      @method('DELETE')
      <button type="submit" class="btn-smart btn-red">
        <i class="fas fa-trash"></i> Delete Request
      </button>
    </form>
    @endif
  </div>

</div>

<div style="text-align: center; margin-top: 20px;">
  <a href="{{ route('manager.stock.cut.index') }}" style="color: var(--text-muted); text-decoration: none;">
    <i class="fas fa-arrow-left"></i> Back to Return Requests
  </a>
</div>

@endsection
