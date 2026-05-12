@extends('layouts.adminlayout')

@section('content')
<div class="manage-card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <div>
            <h2 class="mb-0">Company Cost Dashboard</h2>
            <p class="text-muted mb-0">Complete overview of global and branch-level expenses.</p>
        </div>
        <div class="d-flex gap-2">
            <form action="{{ route('admin.costs.dashboard') }}" method="GET" class="row g-2 align-items-end">
                <div class="col-auto">
                    <select name="month" class="input-form py-1" onchange="this.form.submit()">
                        @for($m=1; $m<=12; $m++)
                            <option value="{{ sprintf('%02d', $m) }}" {{ $month == sprintf('%02d', $m) ? 'selected' : '' }}>
                                {{ date('F', mktime(0, 0, 0, $m, 1)) }}
                            </option>
                        @endfor
                    </select>
                </div>
                <div class="col-auto">
                    <select name="year" class="input-form py-1" onchange="this.form.submit()">
                        @for($y=date('Y'); $y>=2020; $y--)
                            <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                </div>
            </form>
        </div>
    </div>

    <!-- Summary Widgets -->
    <div class="row mt-4 g-3">
        <div class="col-6 col-md-4">
            <div class="p-3 p-md-4 border rounded bg-white shadow-sm text-center h-100">
                <div class="mb-2 text-primary fs-4"><i class="fas fa-globe"></i></div>
                <small class="text-muted d-block mb-1">Global Costs</small>
                <h4 class="mb-0 fw-bold">{{ number_format($globalTotal, 2) }} ৳</h4>
                <a href="{{ route('admin.company_costs.index', ['month' => $month, 'year' => $year]) }}" class="text-decoration-none small">View Details</a>
            </div>
        </div>
        <div class="col-6 col-md-4">
            <div class="p-3 p-md-4 border rounded bg-white shadow-sm text-center h-100">
                <div class="mb-2 text-info fs-4"><i class="fas fa-store-alt"></i></div>
                <small class="text-muted d-block mb-1">Branch Costs</small>
                <h4 class="mb-0 fw-bold">{{ number_format($branchesTotal, 2) }} ৳</h4>
                <span class="small text-muted d-none d-md-inline">Across {{ $branches->count() }} branches</span>
            </div>
        </div>
        <div class="col-12 col-md-4">
            <div class="p-3 p-md-4 border rounded bg-primary text-white shadow-sm text-center h-100">
                <div class="mb-2 fs-4"><i class="fas fa-chart-pie"></i></div>
                <small class="d-block mb-1 opacity-75">Grand Total Company Cost</small>
                <h3 class="mb-0 fw-bold">{{ number_format($grandTotal, 2) }} ৳</h3>
                <span class="small opacity-75">For {{ date('F', mktime(0, 0, 0, (int)$month, 1)) }} {{ $year }}</span>
            </div>
        </div>
    </div>

    <h4 class="mt-5 mb-4 border-left-primary ps-3">Branch-wise Expense Breakdown</h4>

    <!-- Desktop Table -->
    <div class="table-wrapper d-none d-md-block">
        <table>
            <thead>
                <tr>
                    <th>Branch Name</th>
                    <th>Manager</th>
                    <th>Transactions</th>
                    <th>Total Expense</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($branches as $branch)
                <tr>
                    <td class="fw-bold">{{ $branch->name }}</td>
                    <td>{{ $branch->manager ?? 'N/A' }}</td>
                    <td><span class="badge bg-light text-dark">{{ $branch->branchCosts->count() }} records</span></td>
                    <td class="fw-bold text-danger">{{ number_format($branch->total_cost, 2) }} ৳</td>
                    <td class="text-end">
                        <a href="{{ route('admin.costs.branch', ['id' => $branch->id, 'month' => $month, 'year' => $year]) }}" class="btn-sm-smart btn-blue">
                            <i class="fas fa-list me-1"></i> View
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Mobile Cards -->
    <div class="d-md-none mt-3">
        @foreach($branches as $branch)
        <div class="p-3 border rounded mb-3 bg-white shadow-sm">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h6 class="mb-0 fw-bold text-primary">{{ $branch->name }}</h6>
                <span class="badge bg-light text-dark small">{{ $branch->branchCosts->count() }} records</span>
            </div>
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <small class="text-muted d-block">Total Cost</small>
                    <span class="fw-bold text-danger">{{ number_format($branch->total_cost, 2) }} ৳</span>
                </div>
                <a href="{{ route('admin.costs.branch', ['id' => $branch->id, 'month' => $month, 'year' => $year]) }}" class="btn-sm-smart btn-blue py-1">
                    <i class="fas fa-list"></i> View
                </a>
            </div>
        </div>
        @endforeach
    </div>
</div>

<style>
    .border-left-primary {
        border-left: 5px solid var(--primary);
    }
</style>
@endsection
