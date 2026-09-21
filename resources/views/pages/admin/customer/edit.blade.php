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
                <select class="input-form" name="branch_id" id="branch_select">
                    <option value="">--Select Zone--</option>
                    @foreach($branches as $branch)
                    <option value="{{ $branch->id }}" {{ old('branch_id', $customer->branch_id) == $branch->id ? 'selected' : '' }}>
                        {{ $branch->name }}
                    </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-6">
                <label>Refer SR</label>
                <div style="position:relative;">
                    <input type="text" id="sr_search" class="input-form"
                        placeholder="Select a branch first, then type or press space twice..."
                        autocomplete="off">
                    <ul id="sr_dropdown" style="display:none; position:absolute; top:100%; left:0; right:0; background:var(--section-bg); border:1px solid var(--border-color); border-radius:6px; list-style:none; margin:2px 0 0; padding:4px 0; z-index:999; box-shadow:0 6px 20px rgba(0,0,0,.1); max-height:200px; overflow-y:auto;"></ul>
                </div>
                <input type="hidden" name="sr_id" id="sr_id" value="{{ old('sr_id', $customer->sr_id ?? '') }}">
                @error('sr_id')<div class="error-text">{{ $message }}</div>@enderror
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
                <input class="input-form" name="due" value="{{ old('due', $customer->due) }}" required>
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

    // ---- Country / City ----
    let countriesData = {};
    try {
        countriesData = @json(
            file_exists(resource_path('data/countries.json'))
                ? json_decode(file_get_contents(resource_path('data/countries.json')), true)
                : []
        );
    } catch (e) {}

    const countrySelect   = document.getElementById('country');
    const citySelect      = document.getElementById('city');
    const selectedCountry = @json(old('country', $customer->country));
    const selectedCity    = @json(old('city', $customer->city));

    function populateCities(country, sel = '') {
        citySelect.innerHTML = '<option value="" disabled>Select City</option>';
        if (country && countriesData[country]) {
            countriesData[country].forEach(city => {
                const opt = document.createElement('option');
                opt.value = city; opt.textContent = city;
                if (city === sel) opt.selected = true;
                citySelect.appendChild(opt);
            });
            citySelect.disabled = false;
        } else { citySelect.disabled = true; }
    }
    countrySelect.addEventListener('change', function () { populateCities(this.value); });
    populateCities(selectedCountry, selectedCity);

    // ---- SR Search (dynamic by branch) ----
    const allSrs     = @json($allSrs);
    const branchSel  = document.getElementById('branch_select');
    const srSearch   = document.getElementById('sr_search');
    const srDropdown = document.getElementById('sr_dropdown');
    const srIdInput  = document.getElementById('sr_id');

    let branchSrs = [];

    // Pre-fill existing SR name
    const existingSrId = {{ $customer->sr_id ?? 'null' }};
    if (existingSrId) {
        const existingSr = allSrs.find(s => s.id == existingSrId);
        if (existingSr) srSearch.value = existingSr.fullname || existingSr.username;
    }

    function updateSrField(branchId, keepExisting = false) {
        branchSrs = allSrs.filter(s => s.branch_id == branchId);
        if (!keepExisting) {
            srSearch.value  = '';
            srIdInput.value = '';
        }
        srDropdown.style.display = 'none';
        srSearch.disabled = branchSrs.length === 0;
        srSearch.placeholder = branchSrs.length
            ? 'Type name or press 2 spaces to see all SRs...'
            : 'No SRs in this branch';
    }

    // Init with current branch
    const currentBranch = "{{ old('branch_id', $customer->branch_id) }}";
    if (currentBranch) updateSrField(currentBranch, true);

    branchSel.addEventListener('change', function () { updateSrField(this.value); });

    function showDropdown(matches) {
        srDropdown.innerHTML = '';
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
    }

    srSearch.addEventListener('input', function () {
        const raw = this.value;
        const q   = raw.trim().toLowerCase();
        const showAll = raw.length >= 2 && q === '';
        if (!showAll && q.length < 2) { srDropdown.style.display = 'none'; return; }
        const matches = showAll ? branchSrs : branchSrs.filter(s =>
            (s.fullname && s.fullname.toLowerCase().includes(q)) ||
            (s.username && s.username.toLowerCase().includes(q))
        );
        showDropdown(matches);
    });

    srSearch.addEventListener('blur', () => {
        setTimeout(() => { srDropdown.style.display = 'none'; }, 150);
        if (!srSearch.value.trim()) srIdInput.value = '';
    });
});
</script>

@endsection