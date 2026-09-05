@extends('layouts.adminlayout')

@section('content')
<div class="manage-card">

    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <h2 class="mb-0">All Suppliers</h2>
            <p class="text-muted mb-0">Manage all registered Suppliers</p>
        </div>
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <div style="background: rgba(49, 49, 255, 0.08); color: var(--primary); padding: 8px 16px; border-radius: 20px; font-weight: 700; font-size: 0.9rem; border: 1px solid rgba(49, 49, 255, 0.2);">
                <i class="fas fa-truck me-1"></i> Total Suppliers: <span id="totalSupplierCount">{{ $suppliers->total() }}</span>
            </div>
            <a href="{{ route('admin.suppliers.create') }}" class="btn-smart btn-blue">
                <i class="fas fa-plus me-1"></i> Add New Supplier
            </a>
        </div>
    </div>

    @include('components.alert')

    {{-- Smart Filter Bar --}}
    <div class="smart-filter-wrapper">
        <div class="smart-filter-grid-2">

            {{-- Search --}}
            <div>
                <label style="font-size: 0.8rem; font-weight: 600; color: var(--text-muted); margin-bottom: 4px; display: block;">Search</label>
                <div style="position: relative;">
                    <input type="text" id="liveSearch" class="input-form" placeholder="Search by Supplier Name, Company, Phone..." style="padding-left: 32px; margin-bottom: 0;">
                    <i class="fas fa-search" style="position: absolute; left: 10px; top: 50%; transform: translateY(-50%); color: var(--text-muted); font-size: 0.8rem;"></i>
                </div>
            </div>

            {{-- Reset Button --}}
            <div>
                <label style="font-size: 0.8rem; font-weight: 600; color: transparent; margin-bottom: 4px; display: block;">Reset</label>
                <button type="button" id="resetBtn" class="btn btn-outline-secondary" title="Reset Filters" style="height: 36px; width: 100%; padding: 0; display: inline-flex; align-items: center; justify-content: center; font-size: 0.85rem;">
                    <i class="fas fa-undo"></i>
                </button>
            </div>

        </div>
    </div>

    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>S.No</th>
                    <th>Image</th>
                    <th>Name</th>
                    <th>Company Name</th>
                    <th>Phone</th>
                    <th>Due</th>
                    <th class="text-center">Action</th>
                </tr>
            </thead>
            <tbody class="desktop-table" id="supplierTableBody">
                @forelse($suppliers as $supplier)
                <tr class="searchable-row" data-search="{{ strtolower($supplier->name) }} {{ strtolower($supplier->company_name) }} {{ strtolower($supplier->phone) }}">
                    <td scope="row">{{ $suppliers->firstItem() ? $suppliers->firstItem() + $loop->index : $loop->iteration }}</td>
                    <td>
                        @if($supplier->image)
                        <img src="{{ asset($supplier->image) }}" alt="{{ $supplier->name }}" style="width: 78px; height: 38px; object-fit: cover; border-radius: 6px; border: 1px solid var(--border-color, #cbd5e1);">
                        @else
                        <div style="width: 48px; height: 38px; border-radius: 6px; background: rgba(49, 49, 255, 0.06); border: 1px solid rgba(49, 49, 255, 0.15); display: flex; align-items: center; justify-content: center; color: var(--primary);">
                            <i class="fa-solid fa-building"></i>
                        </div>
                        @endif
                    </td>
                    <td class="name">{{ $supplier->name }}</td>
                    <td>{{ $supplier->company_name }}</td>
                    <td>{{ $supplier->phone }}</td>
                    <td style="color: #dc3545; font-weight: 600;">{{ number_format($supplier->due, 2) }} TK</td>
                    <td class="text-center">
                        <div class="d-inline-flex gap-2">
                            <a href="{{ route('admin.suppliers.show', $supplier) }}" class="icon-btn view-icon" title="View">
                                <i class="fa-solid fa-eye"></i>
                            </a>
                            <a href="{{ route('admin.suppliers.edit', $supplier) }}" class="icon-btn edit-icon" title="Edit">
                                <i class="fa-solid fa-pen"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center text-muted py-4">No suppliers found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="manage-mobile-cards" id="mobileCardsWrapper">
        @forelse($suppliers as $supplier)
        <div class="manage-card searchable-card" data-search="{{ strtolower($supplier->name) }} {{ strtolower($supplier->company_name) }} {{ strtolower($supplier->phone) }}">
            <div class="card-body">
                <div><span>S.No</span>
                    <p>{{ $suppliers->firstItem() ? $suppliers->firstItem() + $loop->index : $loop->iteration }}</p>
                </div>
                <div><span>Image</span>
                    <p>
                        @if($supplier->image)
                        <img src="{{ asset($supplier->image) }}" alt="{{ $supplier->name }}" style="width: 48px; height: 38px; object-fit: cover; border-radius: 6px; border: 1px solid var(--border-color, #cbd5e1);">
                        @else
                        <span style="color: var(--text-muted);">N/A</span>
                        @endif
                    </p>
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
                <div><span>Due</span>
                    <p style="color: #dc3545; font-weight: 600;">{{ number_format($supplier->due, 2) }} TK</p>
                </div>
            </div>

            <div class="card-actions">
                <a href="{{ route('admin.suppliers.show', $supplier) }}" class="icon-btn view-icon">
                    <i class="fa-solid fa-eye"></i>
                </a>
                <a href="{{ route('admin.suppliers.edit', $supplier) }}" class="icon-btn edit-icon">
                    <i class="fa-solid fa-pen"></i>
                </a>
            </div>
        </div>
        @empty
        <p class="text-center text-muted">No suppliers found.</p>
        @endforelse
    </div>

</div>

<div class="d-flex justify-content-center mt-3">
    {{ $suppliers->links() }}
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const liveSearch   = document.getElementById('liveSearch');
    const resetBtn     = document.getElementById('resetBtn');
    const visibleCount = document.getElementById('totalSupplierCount');
    const totalRows    = document.querySelectorAll('.searchable-row').length;

    function filterSuppliers() {
        const q = liveSearch ? liveSearch.value.toLowerCase().trim() : '';
        let count = 0;

        document.querySelectorAll('.searchable-row').forEach(row => {
            const text = row.getAttribute('data-search') || '';
            const match = !q || text.includes(q);
            row.style.display = match ? '' : 'none';
            if (match) count++;
        });

        document.querySelectorAll('.searchable-card').forEach(card => {
            const text = card.getAttribute('data-search') || '';
            card.style.display = (!q || text.includes(q)) ? '' : 'none';
        });

        if (visibleCount) {
            visibleCount.innerText = q ? count : totalRows;
        }
    }

    if (liveSearch) liveSearch.addEventListener('input', filterSuppliers);

    if (resetBtn) {
        resetBtn.addEventListener('click', function () {
            if (liveSearch) liveSearch.value = '';
            filterSuppliers();
        });
    }
});
</script>
@endpush