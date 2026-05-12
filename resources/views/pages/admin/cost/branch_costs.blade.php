@extends('layouts.adminlayout')

@section('content')
<div class="manage-card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <div>
            <h2 class="mb-0"><i class="fas fa-store-alt text-primary me-2"></i> {{ $branch->name }} - Expense History</h2>
            <p class="text-muted mb-0">Detailed view of costs recorded by this branch.</p>
        </div>
        <a href="{{ route('admin.costs.dashboard', ['month' => $month, 'year' => $year]) }}" class="btn-smart btn-blue">
            <i class="fas fa-arrow-left me-1"></i> Back to Dashboard
        </a>
    </div>

    <!-- Summary & Filters -->
    <div class="row mt-4 g-3">
        <div class="col-12 col-md-4">
            <div class="p-3 border rounded bg-white shadow-sm h-100 d-flex flex-column justify-content-center">
                <small class="text-muted d-block mb-1">Branch Total (Selected Period)</small>
                <h3 class="mb-0 text-danger fw-bold">{{ number_format($totalCost, 2) }} ৳</h3>
            </div>
        </div>
        <div class="col-12 col-md-8">
            <form action="{{ route('admin.costs.branch', $branch->id) }}" method="GET" class="row g-2">
                <div class="col-6 col-md-3">
                    <label class="small text-muted">Month</label>
                    <select name="month" class="input-form py-1" onchange="this.form.submit()">
                        @for($m=1; $m<=12; $m++)
                            <option value="{{ sprintf('%02d', $m) }}" {{ $month == sprintf('%02d', $m) ? 'selected' : '' }}>
                                {{ date('F', mktime(0, 0, 0, $m, 1)) }}
                            </option>
                        @endfor
                    </select>
                </div>
                <div class="col-6 col-md-3">
                    <label class="small text-muted">Year</label>
                    <select name="year" class="input-form py-1" onchange="this.form.submit()">
                        @for($y=date('Y'); $y>=2020; $y--)
                            <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                </div>
                <div class="col-12 col-md-3">
                    <label class="small text-muted">Category</label>
                    <select name="category" class="input-form py-1" onchange="this.form.submit()">
                        <option value="">All Categories</option>
                        @foreach(['office', 'transport', 'staff', 'maintenance', 'product', 'utility', 'marketing', 'miscellaneous'] as $cat)
                            <option value="{{ $cat }}" {{ request('category') == $cat ? 'selected' : '' }}>{{ ucfirst($cat) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-md-3 d-flex align-items-end">
                    <a href="{{ route('admin.costs.branch', $branch->id) }}" class="btn-smart btn-blue w-100 text-center py-2">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Desktop Table -->
    <div class="table-wrapper mt-4 d-none d-md-block">
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Category</th>
                    <th>Description</th>
                    <th>Amount</th>
                    <th>Recorded By</th>
                    <th class="text-end">Record ID</th>
                </tr>
            </thead>
            <tbody>
                @forelse($costs as $cost)
                <tr>
                    <td>{{ $cost->cost_date->format('d M Y') }}</td>
                    <td>
                        <span class="badge bg-secondary">{{ ucfirst($cost->category) }}</span>
                    </td>
                    <td class="fw-bold">{{ Str::limit($cost->description, 40) }}</td>
                    <td class="fw-bold text-danger">{{ number_format($cost->amount, 2) }} ৳</td>
                    <td>{{ $cost->creator->username ?? 'N/A' }}</td>
                    <td class="text-end text-muted small italic">#BC-{{ $cost->id }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-4">No expense records found for this branch.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Mobile Cards -->
    <div class="d-md-none mt-3">
        @forelse($costs as $cost)
        <div class="p-3 border rounded mb-3 bg-white shadow-sm">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="badge bg-secondary small">{{ ucfirst($cost->category) }}</span>
                <small class="text-muted">{{ $cost->cost_date->format('d M Y') }}</small>
            </div>
            <h6 class="fw-bold mb-2">{{ $cost->description }}</h6>
            <div class="d-flex justify-content-between align-items-end">
                <div>
                    <small class="text-muted d-block">Recorded By: {{ $cost->creator->username ?? 'N/A' }}</small>
                    <span class="fw-bold text-danger">{{ number_format($cost->amount, 2) }} ৳</span>
                </div>
                <small class="text-muted italic">#BC-{{ $cost->id }}</small>
            </div>
        </div>
        @empty
        <div class="text-center py-4 text-muted">No records found.</div>
        @endforelse
    </div>

    <div class="mt-4">
        {{ $costs->appends(request()->query())->links() }}
    </div>
</div>
@endsection
