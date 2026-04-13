@extends('layouts.managerlayout')

@section('content')
<div class="manage-card">

  <div class="card-header">
    @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    <h2>All SR-Users</h2>
    <p>Manage all registered SR-users</p>
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
        @forelse($srs as $sr)
        <tr>
          {{-- <td>{{ $manager->id }}</td> --}}
          <td scope="row">{{ $srs->firstItem() ? $srs->firstItem() + $loop->index : $loop->iteration }}</td>
          <td class="name">{{ $sr->fullname }}</td>
          <td>{{ $sr->username }}</td>
          <td>{{ $sr->branch->name ?? " " }}</td>

          <td class="action-icons">
            <a href="{{ route('manager.users.show', $sr) }}" class="icon-btn view-icon">
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
    @forelse($srs as $sr)
    <div class="manage-card">

      <div class="card-body">
        <div><span>S. No</span>
          <p>{{ $srs->firstItem() ? $srs->firstItem() + $loop->index : $loop->iteration }}</p>
        </div>
        <div><span>Full name</span>
          <p>{{ $sr->fullname }}</p>
        </div>
        <div><span>Username</span>
          <p>{{ $sr->username }}</p>
        </div>
        <div><span>Branch</span>
          <p>{{ $sr->branch->name ?? "-" }}</p>
        </div>
      </div>

      <div class="card-actions">
        <a href="{{ route('manager.users.show', $sr) }}" class="icon-btn view-icon">
          <i class="fa-solid fa-eye"></i>
        </a>

      </div>

    </div>
    @empty
    <p class="text-center text-muted">No records found.</p>
    @endforelse
  </div>


</div>

@endsection