@extends('layouts.adminlayout')

@section('content')
<div class="manage-card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <div>
            <h2 class="mb-0">Bonus Management</h2>
            <p class="text-muted mb-0">Track additional income, incentives, and cashback.</p>
        </div>
        <a href="{{ route('admin.bonuses.create') }}" class="btn-smart btn-blue">
            <i class="fas fa-plus me-1"></i> Add Bonus
        </a>
    </div>

    <!-- Summary & Filters -->
    <div class="row mt-4 g-3">
        <div class="col-md-4">
            <div class="p-3 border rounded bg-light shadow-sm">
                <small class="text-muted d-block mb-1">Total Bonus (Selected Period)</small>
                <h3 class="mb-0 text-primary fw-bold">{{ number_format($totalBonus, 2) }} ৳</h3>
            </div>
        </div>
        <div class="col-md-8">
            <form action="{{ route('admin.bonuses.index') }}" method="GET" class="row g-2 align-items-end">
                <div class="col-md-4">
                    <label class="small text-muted">Month</label>
                    <select name="month" class="input-form py-1" onchange="this.form.submit()">
                        @for($m=1; $m<=12; $m++)
                            <option value="{{ sprintf('%02d', $m) }}" {{ $month == sprintf('%02d', $m) ? 'selected' : '' }}>
                                {{ date('F', mktime(0, 0, 0, $m, 1)) }}
                            </option>
                        @endfor
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="small text-muted">Year</label>
                    <select name="year" class="input-form py-1" onchange="this.form.submit()">
                        @for($y=date('Y'); $y>=2020; $y--)
                            <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                </div>
                <div class="col-md-4">
                    <a href="{{ route('admin.bonuses.index') }}" class="btn-smart btn-blue w-100 text-center">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <div class="table-wrapper mt-4">
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Title</th>
                    <th>Type</th>
                    <th>Amount</th>
                    <th>Created By</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($bonuses as $bonus)
                <tr>
                    <td>{{ $bonus->bonus_date->format('d M Y') }}</td>
                    <td class="fw-bold">{{ $bonus->title }}</td>
                    <td>
                        @php
                            $badgeClass = match($bonus->type) {
                                'incentive' => 'bg-info',
                                'cashback' => 'bg-success',
                                'special' => 'bg-warning',
                                default => 'bg-secondary',
                            };
                        @endphp
                        <span class="badge {{ $badgeClass }}">{{ ucfirst($bonus->type) }}</span>
                    </td>
                    <td class="fw-bold text-success">{{ number_format($bonus->amount, 2) }} ৳</td>
                    <td>{{ $bonus->creator->username ?? 'N/A' }}</td>
                    <td class="text-end">
                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('admin.bonuses.show', $bonus->id) }}" class="btn-sm-smart btn-blue" title="View">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('admin.bonuses.edit', $bonus->id) }}" class="btn-sm-smart btn-green" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('admin.bonuses.destroy', $bonus->id) }}" method="POST" onsubmit="return confirm('Delete this bonus entry?')">
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
                    <td colspan="6" class="text-center py-4">No bonus entries found for this period.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $bonuses->links() }}
    </div>
</div>
@endsection
