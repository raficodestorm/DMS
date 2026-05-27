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
          <td scope="row">{{ $loop->iteration }}</td>
          <td class="name">{{ $manager->fullname }}</td>
          <td>{{ $manager->username }}</td>
          <td>{{ $manager->branch->name ?? " " }}</td>

          <td class="action-icons">
            <a href="{{ route('admin.users.show', $manager) }}" class="icon-btn view-icon">
              <i class="fa-solid fa-eye"></i>
            </a>

            <a href="{{ route('admin.users.edit', $manager) }}" class="icon-btn edit-icon">
              <i class="fa-solid fa-pen"></i>
            </a>

            <form action="{{ route('admin.users.destroy', $manager) }}" method="POST" class="d-inline"
              onsubmit="return confirm('Are you sure?')">
              @csrf
              @method('DELETE')
              <button type="submit" class="icon-btn delete-icon" style="border: none;">
                <i class="fa-solid fa-trash"></i>
              </button>
            </form>
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
        <div><span>ID</span>
          <p>{{ $manager->id }}</p>
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

        <a href="{{ route('admin.users.edit', $manager) }}" class="icon-btn edit-icon">
          <i class="fa-solid fa-pen"></i>
        </a>

        <form action="{{ route('admin.users.destroy', $manager) }}" method="POST"
          onsubmit="return confirm('Are you sure?')">
          @csrf
          @method('DELETE')
          <button type="submit" class="icon-btn delete-icon " style="border: none;">
            <i class="fa-solid fa-trash"></i>
          </button>
        </form>
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