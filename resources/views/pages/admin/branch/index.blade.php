@extends('layouts.adminlayout')

@section('content')
<div class="manage-card">

    <div class="card-header">
        <h2>All Branches</h2>
        <p>Manage all registered Branches</p>
        @include('components.alert')
    </div>

    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>S.No</th>
                    <th>Branch Name</th>
                    <th>Manager Name</th>
                    <th>Address</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody class="desktop-table">
                @forelse($branches as $branch)
                <tr>
                    {{-- <td>{{ $user->id }}</td> --}}
                    <td scope="row">{{ $branches->firstItem() ? $branches->firstItem() + $loop->index : $loop->iteration
                        }}</td>
                    <td class="name">{{ $branch->name }}</td>
                    <td>{{ $branch->manager }}</td>
                    <td>{{ $branch->address }}</td>

                    <td class="action-icons">
                        <a href="{{ route('admin.branches.show', $branch) }}" class="icon-btn view-icon">
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
        @forelse($branches as $branch)
        <div class="manage-card">

            <div class="card-body">
                <div><span>S.No</span>
                    <p>{{ $branches->firstItem() ? $branches->firstItem() + $loop->index : $loop->iteration }}</p>
                </div>
                <div><span>Branch Name</span>
                    <p>{{ $branch->name }}</p>
                </div>
                <div><span>Manager name</span>
                    <p>{{ $branch->manager }}</p>
                </div>
                <div><span>Address</span>
                    <p>{{ $branch->address }}</p>
                </div>
            </div>

            <div class="card-actions">
                <a href="{{ route('admin.branches.show', $branch) }}" class="icon-btn view-icon">
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
    {{ $branches->links() }}
</div>

@endsection