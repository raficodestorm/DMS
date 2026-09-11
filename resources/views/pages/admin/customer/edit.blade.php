@extends(getLayout())

@section('content')

<div class="container justify-center">
    <div class="form-card">
        <h2>Edit Customer</h2>
        <h4>ID : BRC200{{ $customer->id }}</h4>
        @include('components.alert')

        <form class="adduser-form" method="POST" action="{{ route('admin.customers.update', $customer->id) }}">
            @csrf
            @method('PUT')
            <div class="row">
            <div class="col-6">
                <label>Shop Name</label>
                <input type="text" class="input-form" name="shop_name"
                    value="{{ old('shop_name', $customer->shop_name) }}" required>
                @error('shop_name')<div class="error-text">{{ $message }}</div>@enderror
            </div>

            <div class="col-md-6">
                <label>Manager</label>
                <input type="text" class="input-form" name="manager" value="{{ old('manager', $customer->manager) }}"
                    required>
                @error('manager')<div class="error-text">{{ $message }}</div>@enderror
            </div>

            <div class="col-md-6">
                <label>Phone</label>
                <input type="text" class="input-form" name="phone" value="{{ old('phone', $customer->phone) }}"
                    required>
                @error('phone')<div class="error-text">{{ $message }}</div>@enderror
            </div>

            <div class="col-md-6">
                <label>Zone</label>
                <select class="input-form" name="branch_id">
                    <option value="">--Select Zone--</option>

                    @foreach($branches as $branch)
                    <option value="{{ $branch->id }}" {{ old('branch_id', $customer->branch_id) == $branch->id ?
                        'selected'
                        : '' }}>
                        {{ $branch->name }}
                    </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-6"> 
                <label>Country</label> 
                <select class="input-form" name="country" id="country" required> 
                    <option value="" disabled>Select Country</option> 
                    @foreach ($countries as $c) 
                    <option value="{{ $c }}" {{ old('country', $customer->country) === $c ? 'selected' : '' }}> 
                        {{ $c }} 
                    </option> 
                    @endforeach 
                </select> 
                @error('country') <div class="error-text">{{ $message }}</div> @enderror 
            </div> 
            
            <div class="col-md-6"> 
                <label>City</label> 
                <select class="input-form" name="city" id="city" required> 
                    <option value="" disabled>Select City</option> 
                </select> 
                @error('city') <div class="error-text">{{ $message }}</div> @enderror 
            </div>


            <div class="col-12">
                <label>Address</label>
                <textarea class="input-form" name="address">{{ old('address', $customer->address) }}</textarea>
                @error('address')<div class="error-text">{{ $message }}</div>@enderror
            </div>

            

            <div class="col-md-6">
                <label>Due</label>
                <input class="input-form" name="due" value="{{ old('due', $customer->due) }}"
                    required>
                @error('due')<div class="error-text">{{ $message }}</div>@enderror
            </div>

                    </div>


            <div>
                <button class="btn-submit" type="submit">Update</button>
            </div>
        </form>
        <a href="{{ route('admin.customers.index') }}" class="btn btn-secondary px-4 mt-4">Back</a>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {

    // Load country/city JSON directly from Laravel
    let countriesData = {};

    try {
        countriesData = @json(
            file_exists(resource_path('data/countries.json'))
                ? json_decode(file_get_contents(resource_path('data/countries.json')), true)
                : []
        );
    } catch (e) {
        console.error('Failed to load country/cities data', e);
    }

    const countrySelect = document.getElementById('country');
    const citySelect = document.getElementById('city');

    // Existing database values + old input after validation error
    const selectedCountry = @json(old('country', $customer->country));
    const selectedCity = @json(old('city', $customer->city));

    function populateCities(country, selectedCity = '') {

        // Reset city dropdown
        citySelect.innerHTML =
            '<option value="" disabled>Select City</option>';

        if (country && countriesData[country]) {

            const cities = countriesData[country];

            cities.forEach(function (city) {

                const option = document.createElement('option');

                option.value = city;
                option.textContent = city;

                // Select existing/old city
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

    // Country changed manually
    countrySelect.addEventListener('change', function () {

        // When country changes, don't keep old city
        populateCities(this.value);
    });

    // Load existing city on edit page
    populateCities(selectedCountry, selectedCity);

});
</script>

@endsection