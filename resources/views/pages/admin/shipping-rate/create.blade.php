@extends('layouts.adminlayout')

@section('content')

<div class="container justify-center">

    <div class="form-card">

        <h2>Add Shipping Rate</h2>
        <p class="text-muted mb-4">Set up location-based shipping charges for orders.</p>

        @include('components.alert')

        <form class="adduser-form" method="POST" action="{{ route('admin.shipping-rates.store') }}">
            @csrf

            <div class="row">

                {{-- Rate Name --}}
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Rate Name <span style="color: #dc2626; display: inline; margin-left: 2px;">*</span></label>
                    <input
                        type="text"
                        name="name"
                        class="input-form @error('name') is-invalid @enderror"
                        placeholder="e.g. Inside Dhaka Standard Delivery"
                        value="{{ old('name') }}"
                        required>
                    @error('name')
                        <div class="error-text text-danger mt-1">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Base Rate Amount --}}
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Base Shipping Rate (৳) <span style="color: #dc2626; display: inline; margin-left: 2px;">*</span></label>
                    <input
                        type="number"
                        step="0.01"
                        min="0"
                        name="base_rate"
                        class="input-form @error('base_rate') is-invalid @enderror"
                        placeholder="e.g. 60.00"
                        value="{{ old('base_rate') }}"
                        required>
                    @error('base_rate')
                        <div class="error-text text-danger mt-1">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Country Select --}}
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Country <span style="color: #dc2626; display: inline; margin-left: 2px;">*</span></label>
                    <select
                        name="country"
                        id="country"
                        class="input-form @error('country') is-invalid @enderror"
                        required>
                        <option value="" disabled {{ old('country') ? '' : 'selected' }}>Select Country</option>
                        @foreach(array_keys($countriesData) as $countryName)
                            <option value="{{ $countryName }}" {{ old('country') === $countryName ? 'selected' : '' }}>
                                {{ $countryName }}
                            </option>
                        @endforeach
                    </select>
                    @error('country')
                        <div class="error-text text-danger mt-1">{{ $message }}</div>
                    @enderror
                </div>

                {{-- City Select --}}
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">City <span style="color: #dc2626; display: inline; margin-left: 2px;">*</span></label>
                    <select
                        name="city"
                        id="city"
                        class="input-form @error('city') is-invalid @enderror"
                        required
                        {{ old('country') ? '' : 'disabled' }}>
                        <option value="" disabled {{ old('city') ? '' : 'selected' }}>Select City</option>
                    </select>
                    @error('city')
                        <div class="error-text text-danger mt-1">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Status --}}
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Status <span style="color: #dc2626; display: inline; margin-left: 2px;">*</span></label>
                    <select name="status" class="input-form @error('status') is-invalid @enderror" required>
                        <option value="1" {{ old('status', '1') == '1' ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ old('status') === '0' ? 'selected' : '' }}>Inactive</option>
                    </select>
                    @error('status')
                        <div class="error-text text-danger mt-1">{{ $message }}</div>
                    @enderror
                </div>

            </div> <!-- end row -->

            <div class="d-flex align-items-center gap-3 mt-4">
                <button class="btn-submit" type="submit">
                    <i class="fas fa-save me-1"></i> Save Shipping Rate
                </button>
                <a href="{{ route('admin.shipping-rates.index') }}" class="btn btn-secondary px-4">
                    Cancel
                </a>
            </div>

        </form>

    </div>

</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    let countriesData = {};

    try {
        countriesData = @json($countriesData);
    } catch(e) {
        console.error("Failed to load countries data", e);
    }

    const countrySelect = document.getElementById('country');
    const citySelect = document.getElementById('city');

    const oldCountry = @json(old('country', ''));
    const oldCity = @json(old('city', ''));

    function populateCities(country, selectedCity = '') {
        citySelect.innerHTML = '<option value="" disabled selected>Select City</option>';

        if (country && countriesData[country]) {
            const cities = countriesData[country];

            cities.forEach(function (city) {
                const option = document.createElement('option');
                option.value = city;
                option.textContent = city;
                if (city === selectedCity) {
                    option.selected = true;
                }
                citySelect.appendChild(option);
            });

            citySelect.disabled = false;
        } else {
            citySelect.disabled = true;
        }
    }

    countrySelect.addEventListener('change', function () {
        populateCities(this.value);
    });

    if (oldCountry) {
        populateCities(oldCountry, oldCity);
    }
});
</script>
@endpush
