@extends('layouts.adminlayout')

@section('content')
<div class="manage-card">

    <div class="card-header">
        <h2>Your Deductions</h2>
        <p>Manage your all deductions</p>
        @include('components.alert')
    </div>

    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>S.No</th>
                    <th>Type</th>
                    <th>Cust-Deduction</th>
                    <th>Own Deduction</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody class="desktop-table">
                @forelse($deductions as $deduction)
                <tr>
                    <td scope="row">
                        {{ $deductions->firstItem() ? $deductions->firstItem() + $loop->index : $loop->iteration }}
                    </td>
                    <td>{{ $deduction->type }}</td>
                    <td>{{ $deduction->customer_deduction }}</td>
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
                <div><span>Type</span>
                    <p><strong>{{ $deduction->type }}</strong></p>
                </div>
                <div><span>Cust-Deduction</span>
                    <p>{{ $deduction->customer_deduction }}</p>
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