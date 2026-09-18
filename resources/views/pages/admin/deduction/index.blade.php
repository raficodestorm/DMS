@extends('layouts.adminlayout')

@section('content')
<div class="manage-card">

    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <h2 class="mb-0">All Deductions</h2>
            <p class="text-muted mb-0">Manage all registered Deductions</p>
        </div>
        <a href="{{ route('admin.deductions.create') }}" class="btn-smart btn-blue">
            <i class="fas fa-plus me-1"></i> Add New Deduction
        </a>
    </div>
@include('components.alert')
    

    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>S.No</th>
                    <th>Supplier</th>
                    <th>Type</th>
                    <th>Cust-Deduction</th>
                    <th>Retail-Deduction</th>
                    <th>Own-Deduction</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody class="desktop-table">
                @forelse($deductions as $deduction)
                <tr>
                    <td scope="row">
                        {{ $deductions->firstItem() ? $deductions->firstItem() + $loop->index : $loop->iteration }}
                    </td>
                    <td>{{ $deduction->supplier->company_name ?? 'N/A' }}</td>
                    <td>{{ $deduction->type }}</td>
                    <td>{{ $deduction->customer_deduction }}</td>
                    <td>{{ $deduction->retail_deduction }}</td>
                    <td>{{ $deduction->my_deduction }}</td>
                    

                    <td class="action-icons">
                        <a href="{{ route('admin.deductions.show', $deduction) }}" class="icon-btn view-icon">
                            <i class="fa-solid fa-eye"></i>
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center text-muted">No deductions found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Mobile View Cards --}}
    <div class="manage-mobile-cards">
        @forelse($deductions as $deduction)
        <div class="manage-card">
            <div class="card-body">
                <div><span>S.No</span>
                    <p>{{ $deductions->firstItem() ? $deductions->firstItem() + $loop->index : $loop->iteration }}</p>
                </div>

                <div><span>Supplier</span>
                    <p>{{ $deduction->supplier->company_name ?? 'N/A' }}</p>
                </div>
                <div><span>Type</span>
                    <p><strong>{{ $deduction->type }}</strong></p>
                </div>
                <div><span>Cust-Deduction</span>
                    <p>{{ $deduction->customer_deduction }}</p>
                </div>
                <div><span>Retail-Deduction</span>
                    <p>{{ $deduction->retail_deduction }}</p>
                </div>
                <div><span>Own-Deduction</span>
                    <p>{{ $deduction->my_deduction }}</p>
                </div>
               

            </div>

            <div class="card-actions">
                <a href="{{ route('admin.deductions.show', $deduction) }}" class="icon-btn view-icon">
                    <i class="fa-solid fa-eye"></i>
                </a>
            </div>
        </div>
        @empty
        <p class="text-center text-muted">No deductions found.</p>
        @endforelse
    </div>
</div>

<div class="d-flex justify-content-center mt-3">
    {{ $deductions->links() }}
</div>
@endsection