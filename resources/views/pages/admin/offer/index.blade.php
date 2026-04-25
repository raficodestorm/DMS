@extends('layouts.adminlayout')

@section('content')
<div class="manage-card">

    <div class="card-header">
        <h2>All Offers</h2>
        <p>Manage product discounts and promotional offers</p>
        @include('components.alert')
    </div>

    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>S.No</th>
                    <th>Offer Name</th>
                    <th>Product</th>
                    <th>Discount</th>
                    <th>Duration</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody class="desktop-table">
                @forelse($offers as $offer)
                <tr>
                    <td scope="row">
                        {{ $offers->firstItem() ? $offers->firstItem() + $loop->index : $loop->iteration }}
                    </td>
                    <td class="name">{{ $offer->name }}</td>
                    <td>{{ $offer->product->name ?? 'N/A' }}</td>
                    <td>
                        <strong>
                            {{ $offer->type == 'percentage' ? $offer->discount_amount . '%' :
                            number_format($offer->discount_amount, 2) . ' TK' }}
                        </strong>
                    </td>
                    <td style="font-size: 0.85rem;">
                        {{ \Carbon\Carbon::parse($offer->start_date)->format('d M') }} -
                        {{ \Carbon\Carbon::parse($offer->end_date)->format('d M Y') }}
                    </td>
                    <td>
                        @if($offer->status == 1)
                        <span class="status-active-badge">● Active</span>
                        @else
                        <span class="status-inactive-badge">● Inactive</span>
                        @endif
                    </td>

                    <td class="action-icons">
                        <a href="{{ route('admin.offers.show', $offer) }}" class="icon-btn view-icon">
                            <i class="fa-solid fa-eye"></i>
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center text-muted">No offers found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Mobile View Cards --}}
    <div class="manage-mobile-cards">
        @forelse($offers as $offer)
        <div class="manage-card">
            <div class="card-body">
                <div><span>S.No</span>
                    <p>{{ $offers->firstItem() ? $offers->firstItem() + $loop->index : $loop->iteration }}</p>
                </div>
                <div><span>Offer</span>
                    <p><strong>{{ $offer->name }}</strong></p>
                </div>
                <div><span>Product</span>
                    <p>{{ $offer->product->name ?? 'N/A' }}</p>
                </div>
                <div><span>Discount</span>
                    <p>{{ $offer->type == 'percentage' ? $offer->discount_amount . '%' :
                        number_format($offer->discount_amount, 2) . ' TK' }}</p>
                </div>
                <div><span>Status</span>
                    <p>
                        @if($offer->status == 1)
                        <span style="color:green;">● Active</span>
                        @else
                        <span style="color:red;">● Inactive</span>
                        @endif
                    </p>
                </div>
            </div>

            <div class="card-actions">
                <a href="{{ route('admin.offers.show', $offer) }}" class="icon-btn view-icon">
                    <i class="fa-solid fa-eye"></i>
                </a>
            </div>
        </div>
        @empty
        <p class="text-center text-muted">No offers found.</p>
        @endforelse
    </div>
</div>

<div class="d-flex justify-content-center mt-3">
    {{ $offers->links() }}
</div>
@endsection