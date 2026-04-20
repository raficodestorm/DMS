@extends(getLayout())
@section('content')
<style>
    .container-profile {
        max-width: 700px;
        margin: auto;
        padding: 0 15px;
    }

    /* Glassmorphism Card */
    .edit-card {
        background: var(--section-bg);
        border: 1px solid var(--border-color);
        border-radius: 24px;
        padding: 40px;
        box-shadow: 0 20px 40px var(--glass);
        backdrop-filter: blur(10px);
        position: relative;
    }

    .form-title {
        font-family: 'Cinzel', serif;
        font-size: 24px;
        color: var(--text-main);
        margin-bottom: 30px;
        text-align: center;
        font-weight: 700;
    }

    /* FB Style Photo Upload */
    .profile-pic-wrapper {
        position: relative;
        width: 130px;
        height: 130px;
        margin: 0 auto 35px;
    }

    .img-edit {
        width: 130px;
        height: 130px;
        border-radius: 50%;
        object-fit: cover;
        border: 4px solid var(--section-bg);
        box-shadow: 0 4px 15px var(--glass);
        background: var(--background);
    }

    .camera-icon-label {
        position: absolute;
        bottom: 5px;
        right: 5px;
        background: var(--primary);
        color: white;
        width: 38px;
        height: 38px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        border: 3px solid var(--section-bg);
        transition: all 0.3s ease;
    }

    .camera-icon-label:hover {
        background: var(--primary-light);
        transform: scale(1.1);
    }

    /* Input Styling */
    .form-group {
        margin-bottom: 22px;
        position: relative;
    }

    .form-group label {
        display: block;
        font-size: 12px;
        font-weight: 700;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 1px;
        margin-bottom: 8px;
    }

    .edit-input {
        width: 100%;
        padding: 14px 18px;
        background: var(--background);
        border: 1px solid var(--border-color);
        border-radius: 14px;
        color: var(--text-main);
        font-size: 15px;
        transition: all 0.3s ease;
    }

    .edit-input:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 4px var(--primary-soft);
    }

    .edit-input[readonly] {
        background: var(--glass);
        cursor: not-allowed;
        opacity: 0.6;
    }

    /* "Edited Just Now" Badge */
    .edited-badge {
        display: none;
        font-size: 11px;
        color: var(--success);
        font-weight: 600;
        margin-top: 5px;
        animation: fadeIn 0.4s ease;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(-5px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Buttons */
    .profile-actions {
        display: flex;
        gap: 15px;
        margin-top: 10px;
    }

    .profile-edit-btn {
        flex: 2;
        background: var(--primary);
        color: #ffffff;
        border: none;
        padding: 14px;
        border-radius: 14px;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .profile-edit-btn:hover {
        background: var(--primary-light);
        transform: translateY(-2px);
    }

    .btn-cancel {
        flex: 1;
        background: var(--background);
        color: var(--text-main);
        border: 1px solid var(--border-color);
        padding: 14px;
        border-radius: 14px;
        text-decoration: none;
        text-align: center;
        font-weight: 600;
    }

    @media (max-width: 576px) {
        .edit-card {
            padding: 30px 20px;
        }

        .profile-actions {
            flex-direction: column;
        }
    }
</style>

<div class="container-profile">
    <div class="edit-card">
        <h2 class="form-title">Edit Profile</h2>

        @include('components.alert')

        <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
            @csrf
            @method('PATCH')

            <div class="profile-pic-wrapper">
                <img id="previewImg" class="img-edit"
                    src="{{ $user->profile_photo_path ? asset('storage/'.$user->profile_photo_path) : 'https://ui-avatars.com/api/?name='.urlencode($user->fullname).'&background=3131ff&color=fff' }}"
                    alt="Profile">

                <label for="profile_photo" class="camera-icon-label shadow">
                    <i class="fas fa-camera"></i>
                    <input type="file" id="profile_photo" name="profile_photo" hidden onchange="previewFile(this)">
                </label>
            </div>

            <div class="row">
                <div class="col-12 form-group">
                    <label>Full Name</label>
                    <input class="edit-input" name="fullname" value="{{ old('fullname', $user->fullname) }}" readonly>
                </div>

                <div class="col-md-6 form-group">
                    <label>Username</label>
                    <input class="edit-input monitor-change" name="username"
                        value="{{ old('username', $user->username) }}" required>
                    <div class="edited-badge"><i class="mdi mdi-pencil-circle"></i> This field has edited just now</div>
                    @error('username') <div style="color:var(--danger); font-size:12px;">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-6 form-group">
                    <label>Email Address</label>
                    <input class="edit-input monitor-change" name="email" type="email"
                        value="{{ old('email', $user->email) }}" required>
                    <div class="edited-badge"><i class="mdi mdi-pencil-circle"></i> This field has edited just now</div>
                    @error('email') <div style="color:var(--danger); font-size:12px;">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="profile-actions">
                <button class="profile-edit-btn shadow-sm" type="submit">Update Profile</button>
                <a href="{{ route('profile.index') }}" class="btn-cancel">Cancel</a>
            </div>
        </form>
    </div>
</div>

{{-- @if(in_array($user->role, ['admin', 'vendor', 'manager']))
<div>
    <label>Father Name</label>
    <input class="edit-intput" name="father_name" value="{{ old('father_name', $user->father_name) }}">
</div>

<div>
    <label>NID No</label>
    <input class="edit-intput" name="nid_no" value="{{ old('nid_no', $user->nid_no) }}">
</div>
@endif --}}
@endsection
@push('scripts')
<script>
    // 1. Image Preview Logic
    function previewFile(input) {
        const file = input.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('previewImg').setAttribute('src', e.target.result);
            }
            reader.readAsDataURL(file);
        }
    }

    // 2. "Edited Just Now" Logic
    document.querySelectorAll('.monitor-change').forEach(input => {
        const originalValue = input.value;
        
        input.addEventListener('input', function() {
            const badge = this.parentElement.querySelector('.edited-badge');
            if (this.value !== originalValue) {
                badge.style.display = 'block';
            } else {
                badge.style.display = 'none';
            }
        });
    });
</script>
@endpush