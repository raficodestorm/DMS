@extends('layouts.userlayout')

@section('content')
<style>
    .register-page-wrapper {
        min-height: calc(100vh - 120px);
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 40px 16px;
        margin-top: 5rem;
        background: var(--background);
    }

    .register-card {
        background: var(--section-bg);
        border: 1px solid var(--border-color);
        border-radius: 20px;
        padding: 36px 32px;
        width: 100%;
        max-width: 820px;
        box-shadow: 0 16px 40px -10px rgba(0, 0, 0, 0.08);
        animation: regEnter 0.5s cubic-bezier(0.16, 1, 0.3, 1) both;
    }

    @keyframes regEnter {
        from {
            opacity: 0;
            transform: translateY(16px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .register-header {
        text-align: center;
        margin-bottom: 28px;
    }

    .register-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 5px 14px;
        background: var(--primary-soft);
        color: var(--primary);
        border-radius: 30px;
        font-size: 0.78rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 12px;
    }

    .register-header h2 {
        font-size: 28px;
        font-weight: 800;
        color: var(--primary);
        margin: 0 0 6px 0;
        letter-spacing: -0.5px;
    }

    .register-header p {
        color: var(--text-muted);
        font-size: 14.5px;
        margin: 0;
    }

    .register-form {
        display: flex;
        flex-direction: column;
        gap: 18px;
    }

    .form-grid-2 {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 16px;
    }

    .form-full {
        grid-column: span 2;
    }

    .reg-field {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }

    .reg-label {
        font-size: 13px;
        font-weight: 600;
        color: var(--text-main);
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .reg-label i {
        color: var(--primary);
        font-size: 12px;
    }

    .reg-label .optional {
        color: var(--text-muted);
        font-weight: 400;
        font-size: 11.5px;
        margin-left: auto;
    }

    .reg-input-wrap {
        position: relative;
        display: flex;
        align-items: center;
    }

    .reg-input {
        width: 100%;
        height: 46px;
        background: var(--background);
        border: 1.5px solid var(--border-color);
        border-radius: 10px;
        padding: 10px 14px;
        font-size: 14.5px;
        color: var(--text-main);
        outline: none;
        transition: all 0.2s ease;
        font-family: inherit;
    }

    .reg-input:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 3.5px var(--primary-soft);
        background: var(--section-bg);
    }

    .reg-input.is-invalid {
        border-color: #ef4444;
        background: #fef2f2;
    }

    [data-theme="dark"] .reg-input.is-invalid {
        background: #2a1215;
    }

    .reg-textarea {
        height: 80px;
        resize: vertical;
        padding-top: 10px;
    }

    .reg-toggle-pwd {
        position: absolute;
        right: 12px;
        background: none;
        border: none;
        color: var(--text-muted);
        cursor: pointer;
        padding: 4px;
        font-size: 14px;
        transition: color 0.15s;
    }

    .reg-toggle-pwd:hover {
        color: var(--primary);
    }

    .reg-error {
        color: #ef4444;
        font-size: 12px;
        font-weight: 500;
        display: flex;
        align-items: center;
        gap: 4px;
    }

    /* Photo Upload Box */
    .photo-upload-wrap {
        display: flex;
        align-items: center;
        gap: 14px;
        background: var(--background);
        border: 1.5px dashed var(--border-color);
        border-radius: 10px;
        padding: 8px 12px;
        cursor: pointer;
        transition: border-color 0.2s;
    }

    .photo-upload-wrap:hover {
        border-color: var(--primary);
    }

    .photo-preview-thumb {
        width: 44px;
        height: 44px;
        border-radius: 8px;
        background: var(--primary-soft);
        color: var(--primary);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        overflow: hidden;
        flex-shrink: 0;
    }

    .photo-preview-thumb img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .photo-upload-text {
        flex: 1;
        min-width: 0;
    }

    .photo-upload-text span {
        display: block;
        font-size: 13px;
        font-weight: 600;
        color: var(--text-main);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .photo-upload-text small {
        display: block;
        font-size: 11px;
        color: var(--text-muted);
    }

    /* Submit Button */
    .btn-register {
        background: linear-gradient(135deg, var(--primary), var(--accent));
        color: #ffffff;
        font-weight: 700;
        font-size: 15.5px;
        letter-spacing: 0.3px;
        border: none;
        border-radius: 10px;
        height: 50px;
        width: 100%;
        margin-top: 8px;
        cursor: pointer;
        transition: all 0.25s ease;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        box-shadow: 0 4px 16px rgba(2, 2, 226, 0.25);
    }

    .btn-register:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 24px rgba(2, 2, 226, 0.35);
        background: linear-gradient(135deg, var(--accent), var(--primary-light));
    }

    .btn-register:active {
        transform: translateY(0);
    }

    .register-footer {
        text-align: center;
        margin-top: 10px;
        font-size: 14px;
        color: var(--text-muted);
    }

    .register-footer a {
        color: var(--primary);
        font-weight: 700;
        text-decoration: none;
        margin-left: 4px;
        transition: color 0.15s;
    }

    .register-footer a:hover {
        color: var(--accent);
        text-decoration: underline;
    }

    /* Mobile Responsive */
    @media (max-width: 768px) {
        .register-page-wrapper {
            margin-top: 4rem;
            padding: 20px 12px;
        }

        .register-card {
            padding: 24px 18px;
            border-radius: 16px;
        }

        .register-header h2 {
            font-size: 24px;
        }

        .form-grid-2 {
            grid-template-columns: 1fr;
            gap: 14px;
        }

        .form-full {
            grid-column: span 1;
        }

        .reg-input {
            height: 44px;
            font-size: 14px;
        }

        .btn-register {
            height: 46px;
            font-size: 14.5px;
        }
    }
</style>

<div class="register-page-wrapper">
    <div class="register-card">
        <div class="register-header">
            <span class="register-badge">
                <i class="fas fa-user-plus"></i> Retail Customer Registration
            </span>
            <h2>Create Your Account</h2>
            <p>Join us to order products, track live shipments, and receive retail discounts.</p>
        </div>

        @if(session('status'))
            <div style="background: var(--success); color: var(--green); padding: 10px 16px; border-radius: 8px; margin-bottom: 16px; font-size: 14px;">
                {{ session('status') }}
            </div>
        @endif

        <form class="register-form" method="POST" action="{{ route('register') }}" enctype="multipart/form-data">
            @csrf

            <div class="form-grid-2">
                {{-- Fullname --}}
                <div class="reg-field">
                    <label class="reg-label">
                        <i class="far fa-user"></i> Full Name <span class="text-danger">*</span>
                    </label>
                    <div class="reg-input-wrap">
                        <input type="text" name="fullname" class="reg-input @error('fullname') is-invalid @enderror" value="{{ old('fullname') }}" placeholder="e.g. S A Rafi" required autofocus>
                    </div>
                    @error('fullname')
                        <span class="reg-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span>
                    @enderror
                </div>

                {{-- Shop / Business Name (Optional) --}}
                <div class="reg-field">
                    <label class="reg-label">
                        <i class="fas fa-store"></i> Shop / Business Name <span class="optional">(Optional)</span>
                    </label>
                    <div class="reg-input-wrap">
                        <input type="text" name="shop_name" class="reg-input @error('shop_name') is-invalid @enderror" value="{{ old('shop_name') }}" placeholder="e.g. Rafi Electric Store">
                    </div>
                    @error('shop_name')
                        <span class="reg-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span>
                    @enderror
                </div>

                {{-- Username --}}
                <div class="reg-field">
                    <label class="reg-label">
                        <i class="fas fa-at"></i> Username <span class="text-danger">*</span>
                    </label>
                    <div class="reg-input-wrap">
                        <input type="text" name="username" class="reg-input @error('username') is-invalid @enderror" value="{{ old('username') }}" placeholder="e.g. rafi123" required>
                    </div>
                    @error('username')
                        <span class="reg-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span>
                    @enderror
                </div>

                {{-- Email --}}
                <div class="reg-field">
                    <label class="reg-label">
                        <i class="far fa-envelope"></i> Email Address <span class="text-danger">*</span>
                    </label>
                    <div class="reg-input-wrap">
                        <input type="email" name="email" class="reg-input @error('email') is-invalid @enderror" value="{{ old('email') }}" placeholder="e.g. rafi@example.com" required>
                    </div>
                    @error('email')
                        <span class="reg-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span>
                    @enderror
                </div>

                {{-- Phone --}}
                <div class="reg-field">
                    <label class="reg-label">
                        <i class="fas fa-phone-alt"></i> Phone Number <span class="text-danger">*</span>
                    </label>
                    <div class="reg-input-wrap">
                        <input type="tel" name="phone" class="reg-input @error('phone') is-invalid @enderror" value="{{ old('phone') }}" placeholder="e.g. 01700000000" required>
                    </div>
                    @error('phone')
                        <span class="reg-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span>
                    @enderror
                </div>

                {{-- Country --}}
                <div class="reg-field">
                    <label class="reg-label">
                        <i class="fas fa-globe"></i> Country <span class="text-danger">*</span>
                    </label>
                    <div class="reg-input-wrap">
                        <select name="country" id="reg_country" class="reg-input @error('country') is-invalid @enderror" required>
                            <option value="" disabled {{ old('country') ? '' : 'selected' }}>Select Country</option>
                            @foreach ($countries as $c)
                                <option value="{{ $c }}" {{ old('country') === $c ? 'selected' : ($c === 'Bangladesh' && !old('country') ? 'selected' : '') }}>
                                    {{ $c }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    @error('country')
                        <span class="reg-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span>
                    @enderror
                </div>

                {{-- City --}}
                <div class="reg-field">
                    <label class="reg-label">
                        <i class="fas fa-city"></i> City / District <span class="text-danger">*</span>
                    </label>
                    <div class="reg-input-wrap">
                        <select name="city" id="reg_city" class="reg-input @error('city') is-invalid @enderror" required>
                            <option value="" disabled selected>Select City</option>
                        </select>
                    </div>
                    @error('city')
                        <span class="reg-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span>
                    @enderror
                </div>

                {{-- Profile Photo --}}
                <div class="reg-field">
                    <label class="reg-label">
                        <i class="fas fa-image"></i> Profile Photo <span class="optional">(Optional)</span>
                    </label>
                    <label for="profile_photo_input" class="photo-upload-wrap">
                        <div class="photo-preview-thumb" id="photo_preview_thumb">
                            <i class="fas fa-camera"></i>
                        </div>
                        <div class="photo-upload-text">
                            <span id="photo_file_name">Choose an image</span>
                            <small>PNG, JPG, JPEG up to 2MB</small>
                        </div>
                    </label>
                    <input type="file" id="profile_photo_input" name="profile_photo" accept="image/*" style="display: none;">
                    @error('profile_photo')
                        <span class="reg-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span>
                    @enderror
                </div>

                {{-- Address --}}
                <div class="reg-field form-full">
                    <label class="reg-label">
                        <i class="fas fa-map-marker-alt"></i> Detailed Address <span class="text-danger">*</span>
                    </label>
                    <div class="reg-input-wrap">
                        <textarea name="address" class="reg-input reg-textarea @error('address') is-invalid @enderror" placeholder="Road, House, Area / Village details..." required>{{ old('address') }}</textarea>
                    </div>
                    @error('address')
                        <span class="reg-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span>
                    @enderror
                </div>

                {{-- Password --}}
                <div class="reg-field">
                    <label class="reg-label">
                        <i class="fas fa-lock"></i> Password <span class="text-danger">*</span>
                    </label>
                    <div class="reg-input-wrap">
                        <input type="password" name="password" id="reg_password" class="reg-input @error('password') is-invalid @enderror" placeholder="••••••••" required>
                        <button type="button" class="reg-toggle-pwd" onclick="togglePasswordVisibility('reg_password', this)">
                            <i class="far fa-eye"></i>
                        </button>
                    </div>
                    @error('password')
                        <span class="reg-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span>
                    @enderror
                </div>

                {{-- Confirm Password --}}
                <div class="reg-field">
                    <label class="reg-label">
                        <i class="fas fa-shield-alt"></i> Confirm Password <span class="text-danger">*</span>
                    </label>
                    <div class="reg-input-wrap">
                        <input type="password" name="password_confirmation" id="reg_password_confirmation" class="reg-input" placeholder="••••••••" required>
                        <button type="button" class="reg-toggle-pwd" onclick="togglePasswordVisibility('reg_password_confirmation', this)">
                            <i class="far fa-eye"></i>
                        </button>
                    </div>
                </div>
            </div>

            <button type="submit" class="btn-register">
                <i class="fas fa-user-check"></i> Complete Registration
            </button>

            <div class="register-footer">
                Already have an account?
                <a href="{{ route('login') }}">Sign In here</a>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    let countriesData = {};

    try {
        countriesData = @json($countriesData ?? (file_exists(resource_path('data/countries.json')) ? json_decode(file_get_contents(resource_path('data/countries.json')), true) : []));
    } catch(e) {
        console.error("Failed to parse country data", e);
    }

    const countrySelect = document.getElementById('reg_country');
    const citySelect = document.getElementById('reg_city');

    const oldCountry = "{{ old('country', 'Bangladesh') }}";
    const oldCity = "{{ old('city') }}";

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

    if (countrySelect) {
        countrySelect.addEventListener('change', function () {
            populateCities(this.value);
        });

        // Initialize on load
        if (countrySelect.value || oldCountry) {
            const initCountry = countrySelect.value || oldCountry;
            populateCities(initCountry, oldCity);
        }
    }

    // Live Photo Preview
    const photoInput = document.getElementById('profile_photo_input');
    const photoThumb = document.getElementById('photo_preview_thumb');
    const photoName  = document.getElementById('photo_file_name');

    if (photoInput) {
        photoInput.addEventListener('change', function (e) {
            const file = e.target.files[0];
            if (file) {
                photoName.textContent = file.name;
                const reader = new FileReader();
                reader.onload = function (event) {
                    photoThumb.innerHTML = `<img src="${event.target.result}" alt="Preview">`;
                };
                reader.readAsDataURL(file);
            } else {
                photoName.textContent = 'Choose an image';
                photoThumb.innerHTML = '<i class="fas fa-camera"></i>';
            }
        });
    }
});

function togglePasswordVisibility(inputId, btn) {
    const input = document.getElementById(inputId);
    const icon = btn.querySelector('i');
    if (input) {
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    }
}
</script>
@endsection