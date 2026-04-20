@extends('layouts.adminlayout')

@section('content')
<div class="manage-card">

    <div class="card-header">
        <h2>All Suppliers</h2>
        <p>Manage all registered Suppliers</p>
        @include('components.alert')
    </div>

    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>S.No</th>
                    <th>Name</th>
                    <th>Company Name</th>
                    <th>phone</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody class="desktop-table">
                @forelse($suppliers as $supplier)
                <tr>
                    {{-- <td>{{ $user->id }}</td> --}}
                    <td scope="row">{{ $suppliers->firstItem() ? $suppliers->firstItem() + $loop->index :
                        $loop->iteration
                        }}</td>
                    <td class="name">{{ $supplier->name }}</td>
                    <td>{{ $supplier->company_name }}</td>
                    <td>{{ $supplier->phone }}</td>

                    <td class="action-icons">
                        <a href="{{ route('admin.suppliers.show', $supplier) }}" class="icon-btn view-icon">
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
        @forelse($suppliers as $supplier)
        <div class="manage-card">

            <div class="card-body">
                <div><span>S.No</span>
                    <p>{{ $suppliers->firstItem() ? $suppliers->firstItem() + $loop->index : $loop->iteration }}</p>
                </div>
                <div><span>Name</span>
                    <p>{{ $supplier->name }}</p>
                </div>
                <div><span>Company Name</span>
                    <p>{{ $supplier->company_name }}</p>
                </div>
                <div><span>Phone</span>
                    <p>{{ $supplier->phone }}</p>
                </div>
            </div>

            <div class="card-actions">
                <a href="{{ route('admin.suppliers.show', $supplier) }}" class="icon-btn view-icon">
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
    {{ $suppliers->links() }}
</div>

@endsection