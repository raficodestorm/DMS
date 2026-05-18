@extends('layouts.adminlayout')

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
  }

  .info-item label {
    color: var(--text-muted);
    font-size: 0.8rem;
    display: block;
  }

  .info-item p {
    font-weight: 700;
    color: var(--text-main);
    margin: 0;
  }

  .action-bar {
    display: flex;
    gap: 10px;
    margin-top: 30px;
    justify-content: flex-end;
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
  }

  .btn-edit {
    background: var(--primary-soft);
    color: var(--primary);
    border: 1px solid var(--primary);
  }

  .btn-edit:hover {
    background: var(--primary);
    color: white;
  }

  .btn-delete {
    background: #fee2e2;
    color: #dc2626;
    border: 1px solid #fca5a5;
  }

  .btn-delete:hover {
    background: #dc2626;
    color: white;
  }

  .request-status-badge {
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: bold;
  }

  @media (max-width: 600px) {
    .info-grid {
      grid-template-columns: 1fr;
    }

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
      <h3 style="margin:0; color:var(--primary);">Stock Cut Detail</h3>
      <span class="request-status-badge" style="background: #fee2e2; color: #dc2626;">
        Stock Cut
      </span>
    </div>

    @include('components.alert')
  </div>

  <div class="info-grid">
    <div class="info-item">
      <label>Supplier Name</label>
      <p>{{ $stockCut->supplier->company_name }}</p>
    </div>
    <div class="info-item">
      <label>Requested By</label>
      <p>{{ $stockCut->requestedBy->fullname }}</p>
    </div>
    <div class="info-item">
      <label>Branch</label>
      <p>{{ $stockCut->requestedBy->branch->name ?? 'Head Office' }}</p>
    </div>
    <div class="info-item">
      <label>Date</label>
      <p>{{ $stockCut->created_at->format('d M Y, h:i A') }}</p>
    </div>
  </div>

  <h4 style="color:var(--text-muted); border-left: 4px solid var(--primary); padding-left: 10px;">Stock Cut Products</h4>

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
          <td>{{ $item->product->name }}</td>
          <td>{{ number_format($item->price, 2) }} TK</td>
          <td>{{ $item->quantity }}</td>
          <td>{{ number_format($item->price * $item->quantity, 2) }} TK</td>
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
        <div><span>Product</span><p>{{ $item->product->name }}</p></div>
        <div><span>Rate</span><p>{{ number_format($item->price, 2) }} TK</p></div>
        <div><span>Quantity</span><p>{{ $item->quantity }}</p></div>
        <div><span>Subtotal</span><p>{{ number_format($item->price * $item->quantity, 2) }} TK</p></div>
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
    <label style="color: #d97706; font-size: 0.8rem; display: block; font-weight: bold;">Note:</label>
    <p style="margin: 0; color: #92400e;">{{ $stockCut->note }}</p>
  </div>
  @endif

  <div class="action-bar">
    <a href="{{ route('admin.stock.cut.cut.edit', $stockCut->id) }}" class="btn-smart btn-edit">
      <i class="fas fa-edit"></i> Edit Record
    </a>

    <form action="{{ route('admin.stock.cut.cut.destroy', $stockCut->id) }}" method="POST"
      onsubmit="return confirm('Are you sure you want to delete this record? Stock will be restored.')">
      @csrf
      @method('DELETE')
      <button type="submit" class="btn-smart btn-delete">
        <i class="fas fa-trash"></i> Delete Record
      </button>
    </form>
    
    <button onclick="window.print()" class="btn-smart" style="background: var(--background); color: var(--text-main); border: 1px solid var(--border);">
      <i class="fas fa-print"></i> Print
    </button>
  </div>

</div>

<div style="text-align: center; margin-top: 20px;">
  <a href="{{ route('admin.stock.cut.cuts.index') }}" style="color: var(--text-muted); text-decoration: none;">
    <i class="fas fa-arrow-left"></i> Back to List
  </a>
</div>

@endsection
