@extends('layouts.adminlayout')

@section('content')
<div class="manage-card">

  <div class="card-header">
    <h2>Stock Cut History</h2>
    <p>Manage all stock cuts recorded in the system</p>
    @include('components.alert')
  </div>

  <div class="mb-3" style="margin: 15px 0;">
    <a href="{{ route('admin.stock.cut.create') }}" class="btn-submit" style="text-decoration: none; display: inline-block; width: auto; padding: 10px 20px;">
      <i class="fas fa-plus-circle"></i> Create New Stock Cut
    </a>
  </div>

  <div class="table-wrapper">
    <table>
      <thead>
        <tr>
          <th>S.No</th>
          <th>Date</th>
          <th>Supplier</th>
          <th>Requested By</th>
          <th>Total Amount</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody class="desktop-table">
        @forelse($stockCuts as $cut)
        <tr>
          <td>{{ $loop->iteration }}</td>
          <td>{{ $cut->created_at->format('d M Y, h:i A') }}</td>
          <td>{{ $cut->supplier->company_name ?? 'N/A' }}</td>
          <td>{{ $cut->requestedBy->fullname ?? 'N/A' }}</td>
          <td>{{ number_format($cut->net_total, 2) }} TK</td>
          <td class="action-icons">
            <div style="display: flex; gap: 5px;">
              <a href="{{ route('admin.stock.cut.cut.show', $cut->id) }}" class="icon-btn view-icon" title="View Detail">
                <i class="fas fa-eye"></i>
              </a>
              <a href="{{ route('admin.stock.cut.cut.edit', $cut->id) }}" class="icon-btn edit-icon" title="Edit Record">
                <i class="fas fa-edit"></i>
              </a>
              <form action="{{ route('admin.stock.cut.cut.destroy', $cut->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this record? Stock will be restored.')" style="margin: 0;">
                @csrf
                @method('DELETE')
                <button type="submit" class="icon-btn delete-icon" style="border: none; cursor: pointer;" title="Delete Record">
                  <i class="fas fa-trash"></i>
                </button>
              </form>
            </div>
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="6" class="text-center text-muted">No stock cuts found.</td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <div class="manage-mobile-cards">
    @forelse($stockCuts as $cut)
    <div class="manage-card">
      <div class="card-body">
        <div><span>S.No</span><p>{{ $loop->iteration }}</p></div>
        <div><span>Date</span><p>{{ $cut->created_at->format('d M Y, h:i A') }}</p></div>
        <div><span>Supplier</span><p>{{ $cut->supplier->company_name ?? 'N/A' }}</p></div>
        <div><span>Requested By</span><p>{{ $cut->requestedBy->fullname ?? 'N/A' }}</p></div>
        <div><span>Amount</span><p>{{ number_format($cut->net_total, 2) }} TK</p></div>
      </div>
      <div class="card-actions">
        <a href="{{ route('admin.stock.cut.cut.show', $cut->id) }}" class="icon-btn view-icon">
          <i class="fas fa-eye"></i>
        </a>
        <a href="{{ route('admin.stock.cut.cut.edit', $cut->id) }}" class="icon-btn edit-icon">
          <i class="fas fa-edit"></i>
        </a>
        <form action="{{ route('admin.stock.cut.cut.destroy', $cut->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this record? Stock will be restored.')" style="display:inline;">
          @csrf
          @method('DELETE')
          <button type="submit" class="icon-btn delete-icon" style="border: none; cursor: pointer;">
            <i class="fas fa-trash"></i>
          </button>
        </form>
      </div>
    </div>
    @empty
    <p class="text-center text-muted">No stock cuts found.</p>
    @endforelse
  </div>
</div>
@endsection