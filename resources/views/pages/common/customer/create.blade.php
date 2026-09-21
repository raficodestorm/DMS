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

            <div class="col-md-12">
                <label>Refer SR</label>

                @php $isSr = auth()->user()->role === 'sr'; @endphp

                <div style="position:relative;">
                    <input type="text" id="sr_search" class="input-form"
                        placeholder="Type at least 2 characters to search SR..."
                        autocomplete="off"
                        value="{{ $isSr ? (auth()->user()->fullname ?? auth()->user()->username) : '' }}"
                        {{ $isSr ? 'readonly' : 'required' }}>
                    <ul id="sr_dropdown" style="display:none; position:absolute; top:100%; left:0; right:0; background:var(--section-bg); border:1px solid var(--border-color); border-radius:6px; list-style:none; margin:2px 0 0; padding:4px 0; z-index:999; box-shadow:0 6px 20px rgba(0,0,0,.1); max-height:200px; overflow-y:auto;"></ul>
                </div>
                <input type="hidden" name="sr_id" id="sr_id" value="{{ $isSr ? auth()->id() : '' }}">
                @error('sr_id')<div class="error-text">{{ $message }}</div>@enderror
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
    // ---- Country / City logic ----
    let countriesData = {};
    try {
        countriesData = @json(file_exists(resource_path('data/countries.json')) ? json_decode(file_get_contents(resource_path('data/countries.json')), true) : []);
    } catch(e) { console.error("Failed to load country/cities data", e); }

    const countrySelect = document.getElementById('country');
    const citySelect    = document.getElementById('city');
    const oldCountry    = "{{ old('country') }}";
    const oldCity       = "{{ old('city') }}";

    function populateCities(country, selectedCity = '') {
        citySelect.innerHTML = '<option value="" disabled selected>Select City</option>';
        if (country && countriesData[country]) {
            countriesData[country].forEach(function (city) {
                const opt = document.createElement('option');
                opt.value = city; opt.textContent = city;
                if (city === selectedCity) opt.selected = true;
                citySelect.appendChild(opt);
            });
            citySelect.disabled = false;
        } else { citySelect.disabled = true; }
    }
    countrySelect.addEventListener('change', function () { populateCities(this.value); });
    if (oldCountry) populateCities(oldCountry, oldCity);

    // ---- SR Search logic ----
    const srs        = @json($srs);
    const srSearch   = document.getElementById('sr_search');
    const srDropdown = document.getElementById('sr_dropdown');
    const srIdInput  = document.getElementById('sr_id');

    if (!srSearch.readOnly) {
        srSearch.addEventListener('input', function () {
            const raw = this.value;
            const q   = raw.trim().toLowerCase();
            srDropdown.innerHTML = '';

            // 2+ spaces → show all branch SRs
            const showAll = raw.length >= 2 && q === '';

            if (!showAll && q.length < 2) { srDropdown.style.display = 'none'; return; }

            const matches = showAll ? srs : srs.filter(s =>
                (s.fullname && s.fullname.toLowerCase().includes(q)) ||
                (s.username && s.username.toLowerCase().includes(q))
            );

            if (!matches.length) { srDropdown.style.display = 'none'; return; }

            matches.forEach(s => {
                const li = document.createElement('li');
                li.textContent = s.fullname || s.username;
                li.style.cssText = 'padding:8px 12px; cursor:pointer; font-size:13.5px;';
                li.addEventListener('mouseenter', () => li.style.background = 'var(--primary-soft)');
                li.addEventListener('mouseleave', () => li.style.background = '');
                li.addEventListener('mousedown', () => {
                    srSearch.value  = s.fullname || s.username;
                    srIdInput.value = s.id;
                    srDropdown.style.display = 'none';
                });
                srDropdown.appendChild(li);
            });
            srDropdown.style.display = 'block';
        });

        // Clear sr_id if user clears the search text
        srSearch.addEventListener('blur', () => {
            setTimeout(() => { srDropdown.style.display = 'none'; }, 150);
            if (!srSearch.value.trim()) srIdInput.value = '';
        });
    }
});
</script>
@endsection