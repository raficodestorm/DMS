@extends('layouts.managerlayout')

@section('content')
<div class="manage-card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <div>
            <h2 class="mb-0">Branch Cost Management</h2>
            <p class="text-muted mb-0">Record and track all branch-level expenses.</p>
        </div>
        <a href="{{ route('manager.costs.create') }}" class="btn-smart btn-blue">
            <i class="fas fa-plus me-1"></i> Record New Cost
        </a>
    </div>

    <!-- Summary & Filters -->
    <div class="row mt-4 g-3">
        <div class="col-md-4">
            <div class="p-3 border rounded bg-light shadow-sm">
                <small class="text-muted d-block mb-1">Total Expenses (Selected Period)</small>
                <h3 class="mb-0 text-danger fw-bold">{{ number_format($totalCost, 2) }} ৳</h3>
            </div>
        </div>
        <div class="col-md-8">
            <form action="{{ route('manager.costs.index') }}" method="GET" class="row g-2 align-items-end">
                <div class="col-md-3">
                    <label class="small text-muted">Month</label>
                    <select name="month" class="input-form py-1" onchange="this.form.submit()">
                        @for($m=1; $m<=12; $m++)
                            <option value="{{ sprintf('%02d', $m) }}" {{ $month == sprintf('%02d', $m) ? 'selected' : '' }}>
                                {{ date('F', mktime(0, 0, 0, $m, 1)) }}
                            </option>
                        @endfor
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="small text-muted">Year</label>
                    <select name="year" class="input-form py-1" onchange="this.form.submit()">
                        @for($y=date('Y'); $y>=2020; $y--)
                            <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="small text-muted">Category</label>
                    <select name="category" class="input-form py-1" onchange="this.form.submit()">
                        <option value="">All Categories</option>
                        @foreach(['office', 'transport', 'salary', 'maintenance', 'product', 'utility', 'marketing', 'miscellaneous'] as $cat)
                            <option value="{{ $cat }}" {{ request('category') == $cat ? 'selected' : '' }}>{{ ucfirst($cat) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <a href="{{ route('manager.costs.index') }}" class="btn-smart btn-blue w-100 text-center">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <div class="table-wrapper mt-4">
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Category</th>
                    <th>Description</th>
                    <th>Amount</th>
                    <th>Recorded By</th>
                    <th class="text-end">Actions</th>
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
                    <td class="text-end">
                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('manager.costs.show', $cost->id) }}" class="btn-sm-smart btn-blue" title="View">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('manager.costs.edit', $cost->id) }}" class="btn-sm-smart btn-green" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('manager.costs.destroy', $cost->id) }}" method="POST" onsubmit="return confirm('Delete this cost record?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn-sm-smart btn-red" title="Delete">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-4">No expense records found for this period.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $costs->appends(request()->query())->links() }}
    </div>
</div>
@endsection
