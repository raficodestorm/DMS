@extends('layouts.adminlayout')

@section('content')
<div class="manage-card">

  <div class="card-header">
    @include('components.alert')
    <h2>All Customer-Users</h2>
    <p>Manage all registered customers</p>
  </div>

  <div class="table-wrapper">
    <table>
      <thead>
        <tr>
          <th>S.No</th>
          <th>Full Name</th>
          <th>Username</th>
          <th>Branch</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody class="desktop-table">
        @forelse($customers as $customer)
        <tr>
          {{-- <td>{{ $customer->id }}</td> --}}
          <td scope="row">{{ $customers->firstItem() ? $customers->firstItem() + $loop->index : $loop->iteration }}</td>
          <td class="name">{{ $customer->fullname }}</td>
          <td>{{ $customer->username }}</td>
          <td>{{ $customer->branch->name ?? " " }}</td>

          <td class="action-icons">
            <a href="{{ route('admin.users.show', $customer) }}" class="icon-btn view-icon">
              <i class="fa-solid fa-eye"></i>
            </a>
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="8" class="text-center text-muted">No records found.</td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>
  <div class="manage-mobile-cards">
    @forelse($customers as $customer)
    <div class="manage-card">

      <div class="card-body">
        <div><span>S. No</span>
          <p>{{ $customers->firstItem() ? $customers->firstItem() + $loop->index : $loop->iteration }}</p>
        </div>
        <div><span>Full name</span>
          <p>{{ $customer->fullname }}</p>
        </div>
        <div><span>Username</span>
          <p>{{ $customer->username }}</p>
        </div>
        <div><span>Branch</span>
          <p>{{ $customer->branch->name ?? "-" }}</p>
        </div>
      </div>

      <div class="card-actions">
        <a href="{{ route('admin.users.show', $customer) }}" class="icon-btn view-icon">
          <i class="fa-solid fa-eye"></i>
        </a>
      </div>

    </div>
    @empty
    <p class="text-center text-muted">No records found.</p>
    @endforelse
  </div>


</div>
<div class="d-flex justify-content-center mt-3">
  {{ $customers->links() }}
</div>

@endsection