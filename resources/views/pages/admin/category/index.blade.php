@extends('layouts.adminlayout')

@section('content')
<div class="manage-card">

    <div class="card-header">
        <h2>All Category</h2>
        <p>Manage all registered Categories</p>
        @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
        @endif
    </div>

    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>S.No</th>
                    <th>Category Name</th>
                    <th>Description</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody class="desktop-table">
                @forelse($categories as $category)
                <tr>
                    {{-- <td>{{ $user->id }}</td> --}}
                    <td scope="row">{{ $categories->firstItem() ? $categories->firstItem() + $loop->index :
                        $loop->iteration
                        }}</td>
                    <td class="name">{{ $category->name }}</td>
                    <td>{{ $category->description }}</td>

                    <td class="action-icons">
                        <a href="{{ route('admin.categories.show', $category) }}" class="icon-btn view-icon">
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
        @forelse($categories as $category)
        <div class="manage-card">

            <div class="card-body">
                <div><span>S.No</span>
                    <p>{{ $categories->firstItem() ? $categories->firstItem() + $loop->index : $loop->iteration }}</p>
                </div>
                <div><span>category Name</span>
                    <p>{{ $category->name }}</p>
                </div>
                <div><span>Description</span>
                    <p>{{ $category->description }}</p>
                </div>
            </div>

            <div class="card-actions">
                <a href="{{ route('admin.categories.show', $category) }}" class="icon-btn view-icon">
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
    {{ $categories->links() }}
</div>

@endsection