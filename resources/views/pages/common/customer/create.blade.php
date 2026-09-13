@extends(getLayout())

@section('content')

<div class="container justify-center">
    <div class="form-card">
        <h2>Add New Customer</h2>
        @include('components.alert')

        <form class="adduser-form" method="POST" action="{{ route('customers.store') }}" enctype="multipart/form-data">
            @csrf
            <div class="row">
  
            <div class="col-12">
                <label>Shop name</label>
                <input type="text" class="input-form" name="shop_name" required>
                @error('shop_name')<div class="error-text">{{ $message }}</div>@enderror
            </div>

            <div class="col-md-6">
                <label>Manager</label>
                <input type="text" class="input-form" name="manager" required>
                @error('manager')<div class="error-text">{{ $message }}</div>@enderror
            </div>

            <div class="col-md-6">
                <label>Phone</label>
                <input type="text" class="input-form" name="phone" required>
                @error('phone')<div class="error-text">{{ $message }}</div>@enderror
            </div>

            <div class="col-md-6">
                <label>Country</label>
                <select class="input-form" name="country" id="country" required>
                <option value="" disabled selected>Select Country</option>
                    @foreach ($countries as $c)
                    <option value="{{ $c }}" {{ old('country') === $c ? 'selected' : '' }}>{{ $c }}</option>
                    @endforeach
                </select>

                @error('country')
                    <div class="error-text">{{ $message }}</div>
                @enderror
            </div>


            <div class="col-md-6">
                <label>City</label>
                <select class="input-form" name="city" id="city" required disabled>
                    <option value="" disabled selected>Select City</option>
                </select>
                @error('city')
                <div class="error">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-12">
                <label>Address</label>
                <textarea class="input-form" name="address"></textarea>
                @error('address')<div class="error-text">{{ $message }}</div>@enderror
            </div>

            </div> <!-- end row  -->

            <div>
                <button class="btn-submit" type="submit">Submit</button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // Read JSON data source directly
    let countriesData = {};
    
    try {
        // Embed the loaded JSON directly from disk safely
        countriesData = @json(file_exists(resource_path('data/countries.json')) ? json_decode(file_get_contents(resource_path('data/countries.json')), true) : []);
    } catch(e) {
        console.error("Failed to load country/cities data", e);
    }

    const countrySelect = document.getElementById('country');
    const citySelect = document.getElementById('city');

    // Retrieve old values from Laravel
    const oldCountry = "{{ old('country') }}";
    const oldCity = "{{ old('city') }}";

    function populateCities(country, selectedCity = '') {
        // Clear previous options
        citySelect.innerHTML = '<option value="" disabled selected>Select City</option>';
        
        if (country && countriesData[country]) {
            const cities = countriesData[country];
            
            // Populate select input element
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

    // Bind change listener for country input
    countrySelect.addEventListener('change', function () {
        populateCities(this.value);
    });

    // Handle old input preservation on validation redirects
    if (oldCountry) {
        populateCities(oldCountry, oldCity);
    }

    
});
</script>
@endsection