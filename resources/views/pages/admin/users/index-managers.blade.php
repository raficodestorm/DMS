@extends('layouts.adminlayout')

@section('content')
<div class="manage-card">

  <div class="card-header">
    @include('components.alert')
    <h2>All Users</h2>
    <p>Manage all registered admins & editors</p>
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
        @forelse($managers as $manager)
        <tr>
          {{-- <td>{{ $manager->id }}</td> --}}
          <td scope="row">{{ $managers->firstItem() ? $managers->firstItem() + $loop->index : $loop->iteration }}</td>
          <td class="name">{{ $manager->fullname }}</td>
          <td>{{ $manager->username }}</td>
          <td>{{ $manager->branch->name ?? " " }}</td>

          <td class="action-icons">
            <a href="{{ route('admin.users.show', $manager) }}" class="icon-btn view-icon">
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
    @forelse($managers as $manager)
    <div class="manage-card">

      <div class="card-body">
        <div><span>S. No</span>
          <p>{{ $managers->firstItem() ? $managers->firstItem() + $loop->index : $loop->iteration }}</p>
        </div>
        <div><span>Full name</span>
          <p>{{ $manager->fullname }}</p>
        </div>
        <div><span>Username</span>
          <p>{{ $manager->username }}</p>
        </div>
        <div><span>Branch</span>
          <p>{{ $manager->branch->name ?? "-" }}</p>
        </div>
      </div>

      <div class="card-actions">
        <a href="{{ route('admin.users.show', $manager) }}" class="icon-btn view-icon">
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
  {{ $managers->links() }}
</div>

@endsection