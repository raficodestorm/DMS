@extends('layouts.adminlayout')

@section('content')
<style>
  .detail-card {
    background: var(--section-bg);
    border-radius: 20px;
    border: 1px solid var(--border-color);
    box-shadow: 0 15px 35px var(--glass);
    max-width: 1000px;
    margin: auto;
    overflow: hidden;
  }

  /* Header Section */
  .detail-header {
    padding: 30px;
    background: var(--glass);
    border-bottom: 1px solid var(--border-color);
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
  }

  .info-group h4 {
    color: var(--text-muted);
    font-size: 0.8rem;
    text-transform: uppercase;
    margin-bottom: 5px;
  }

  .info-group p {
    color: var(--text-main);
    font-weight: 700;
    font-size: 1.1rem;
    margin: 0;
  }

  .status-pill {
    padding: 6px 16px;
    border-radius: 30px;
    font-weight: 800;
    font-size: 0.85rem;
  }

  /* Table Section */
  .item-section {
    padding: 30px;
  }

  .item-table {
    width: 100%;
    border-collapse: collapse;
  }

  .item-table th {
    text-align: left;
    padding: 12px;
    color: var(--text-muted);
    border-bottom: 2px solid var(--border-color);
  }

  .item-table td {
    padding: 15px 12px;
    border-bottom: 1px solid var(--border-color);
    color: var(--text-main);
  }

  .product-name {
    font-weight: 700;
    color: var(--primary);
  }

  /* Footer / Summary */
  .detail-footer {
    padding: 30px;
    background: var(--background);
    display: flex;
    justify-content: space-between;
    align-items: center;
  }

  .total-box h2 {
    color: var(--primary);
    font-weight: 900;
    font-size: 2rem;
    margin: 0;
  }

  /* Action Buttons */
  .action-btns {
    display: flex;
    gap: 15px;
  }

  .btn-action {
    padding: 12px 25px;
    border-radius: 12px;
    font-weight: 700;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 8px;
    transition: 0.3s;
    border: none;
    text-decoration: none;
  }

  .btn-approve {
    background: #16a34a;
    color: white;
  }

  .btn-approve:hover {
    background: #15803d;
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(22, 163, 74, 0.4);
  }

  .btn-reject {
    background: #dc2626;
    color: white;
  }

  .btn-reject:hover {
    background: #b91c1c;
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(220, 38, 38, 0.4);
  }

  .btn-print {
    background: var(--glass);
    border: 1px solid var(--border-color);
    color: var(--text-main);
  }

  /* Mobile Responsive */
  @media (max-width: 768px) {
    .detail-header {
      flex-direction: column;
      gap: 20px;
    }

    .detail-footer {
      flex-direction: column;
      gap: 25px;
      text-align: center;
    }

    .action-btns {
      width: 100%;
      flex-direction: column;
    }

    .btn-action {
      width: 100%;
      justify-content: center;
    }

    .item-table thead {
      display: none;
    }

    .item-table tr {
      display: block;
      margin-bottom: 15px;
      border: 1px solid var(--border-color);
      border-radius: 10px;
      padding: 10px;
    }

    .item-table td {
      display: flex;
      justify-content: space-between;
      border: none;
      padding: 5px;
    }

    .item-table td::before {
      content: attr(data-label);
      font-weight: 600;
      color: var(--text-muted);
    }
  }
</style>

<div class="detail-card">
  <div class="detail-header">
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 40px;">
      <div class="info-group">
        <h4>Supplier</h4>
        <p>{{ $stockRequest->supplier->company_name }}</p>
      </div>
      <div class="info-group">
        <h4>Requested By</h4>
        <p>{{ $stockRequest->requestedBy->name }}</p>
      </div>
      <div class="info-group">
        <h4>Request Date</h4>
        <p>{{ $stockRequest->created_at->format('d M, Y') }}</p>
      </div>
      <div class="info-group">
        <h4>Status</h4>
        <span class="status-pill"
          style="background: {{ $stockRequest->status == 'pending' ? '#fef3c7' : ($stockRequest->status == 'approved' ? '#dcfce7' : '#fee2e2') }}; color: {{ $stockRequest->status == 'pending' ? '#d97706' : ($stockRequest->status == 'approved' ? '#16a34a' : '#dc2626') }};">
          {{ strtoupper($stockRequest->status) }}
        </span>
      </div>
    </div>

    <button onclick="window.print()" class="btn-action btn-print">
      <i class="fas fa-print"></i> Print
    </button>
  </div>

  <div class="item-section">
    <table class="item-table">
      <thead>
        <tr>
          <th>Product Name</th>
          <th>Rate</th>
          <th>Quantity</th>
          <th style="text-align: right;">Subtotal</th>
        </tr>
      </thead>
      <tbody>
        @foreach($stockRequest->items as $item)
        <tr>
          <td data-label="Product" class="product-name">{{ $item->product->name }}</td>
          <td data-label="Rate">{{ number_format($item->cost_price, 2) }} TK</td>
          <td data-label="Qty">{{ $item->quantity }} Units</td>
          <td data-label="Subtotal" style="text-align: right; font-weight: 700;">
            {{ number_format($item->cost_price * $item->quantity, 2) }} TK
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>

  <div class="detail-footer">
    <div class="total-box">
      <span>Net Total Amount</span>
      <h2>{{ number_format($stockRequest->net_total, 2) }} TK</h2>
    </div>

    @if($stockRequest->status == 'pending')
    <div class="action-btns">
      <form action="{{ route('admin.stock.in.reject', $stockRequest->id) }}" method="POST"
        onsubmit="return confirm('Reject this request?')">
        @csrf
        <button type="submit" class="btn-action btn-reject">
          <i class="fas fa-times-circle"></i> Reject Request
        </button>
      </form>

      <form action="{{ route('admin.stock.in.approve', $stockRequest->id) }}" method="POST"
        onsubmit="return confirm('Approve and update stock?')">
        @csrf
        <button type="submit" class="btn-action btn-approve">
          <i class="fas fa-check-circle"></i> Approve & Stock-In
        </button>
      </form>
    </div>
    @else
    <div class="info-group" style="text-align: right;">
      <h4>Processed By</h4>
      <p>{{ $stockRequest->approvedBy->name ?? 'N/A' }}</p>
    </div>
    @endif
  </div>
</div>

<div style="margin-top: 20px; text-align: center;">
  <a href="{{ route('admin.stock.in.requests.index') }}"
    style="color: var(--text-muted); text-decoration: none; font-weight: 600;">
    <i class="fas fa-arrow-left"></i> Back to List
  </a>
</div>
@endsection