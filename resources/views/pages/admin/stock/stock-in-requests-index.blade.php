@extends('layouts.adminlayout')

@section('content')
<div class="manage-card">

  <div class="card-header">
    <h2>Stock-In Requests</h2>
    <p>Manage your all Stock-In Requests</p>
    @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif
  </div>

  <div class="table-wrapper">
    <table>
      <thead>
        <tr>
          <th>S.No</th>
          <th>Requested By</th>
          <th>Supplier</th>
          <th>Amount</th>
          <th>Status</th>
          <th>Date & Time</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody class="desktop-table">
        @forelse($requests as $request)
        <tr>
          <td scope="row">{{ $loop->iteration }}</td>
          <td class="name">{{ $request->requestedBy->branch->name }}</td>
          <td>{{ $request->supplier->company_name }}</td>
          <td>{{ number_format($request->net_total, 2) }} TK</td>
          <td>
            @if($request->status == "pending")

            <span class="status-inactive-badge">Pending...</span>
            @else
            <span class="status-active-badge">Approved</span>
            @endif
          </td>
          <td>{{ $request->created_at->format('d M Y, h:i A') }}</td>

          <td class="action-icons">
            <a href="{{ route('admin.stock.in.request.show', $request->id) }}" class="icon-btn view-icon">
              <i class="fa-solid fa-eye"></i>
            </a>
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="8" class="text-center text-muted">No requests found.</td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>
  <div class="manage-mobile-cards">
    @forelse($requests as $request)
    <div class="manage-card">

      <div class="card-body">
        <div><span>S.No</span>
          <p>{{ $loop->iteration }}</p>
        </div>
        <div><span>Requested By</span>
          <p>{{ $request->requestedBy->branch->name }}</p>
        </div>
        <div><span>Supplier</span>
          <p>{{ $request->supplier->company_name }}</p>
        </div>
        <div><span>Amount</span>
          <p>{{ number_format($request->net_total, 2) }} TK</p>
        </div>
        <div><span>Status</span>
          <p>
            @if($request->status == "pending")
            <span style="color:red;">Pending...</span>
            @else
            <span style="color:green;">● Approved</span>
            @endif
          </p>
        </div>
        <div><span>Data & Time</span>
          <p>{{ $request->created_at->format('d M Y, h:i A') }}</p>
        </div>
      </div>

      <div class="card-actions">
        <a href="{{ route('admin.stock.in.request.show', $request->id) }}" class="icon-btn view-icon">
          <i class="fa-solid fa-eye"></i>
        </a>

      </div>

    </div>
    @empty
    <p class="text-center text-muted">No requests found.</p>
    @endforelse
  </div>


</div>

@endsection