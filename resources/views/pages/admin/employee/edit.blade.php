@extends(getLayout())

@section('content')

<div class="container justify-center">
    <div class="form-card">
        <h2>Edit Employee</h2>
        <h4>ID : BRE100{{ $employee->id }}</h4>
        @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form class="adduser-form" method="POST" action="{{ route('admin.employees.update', $employee->id) }}"
            enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div>
                <label>Full name</label>
                <input class="input-form" name="name" value="{{ old('name', $employee->name) }}" required>
                @error('name')<div class="error-text">{{ $message }}</div>@enderror
            </div>

            <div>
                <label>Rank</label>
                <select class="input-form" name="rank">
                    <option value="SR" {{ old('rank', $employee->rank) == 'SR' ? 'selected' : '' }}>SR</option>
                    <option value="TSM" {{ old('rank', $employee->rank) == 'TSM' ? 'selected' : '' }}>TSM</option>
                    <option value="Manager" {{ old('rank', $employee->rank) == 'Manager' ? 'selected' : '' }}>Manager
                    </option>
                    <option value="DSO" {{ old('rank', $employee->rank) == 'DSO' ? 'selected' : '' }}>DSO</option>
                    <option value="Cooperator" {{ old('rank', $employee->rank) == 'Cooperator' ? 'selected' : ''
                        }}>Cooperator</option>
                </select>
                @error('rank')<div class="error-text">{{ $message }}</div>@enderror
            </div>

            <div>
                <label>Branch</label>
                <select class="input-form" name="branch_id">
                    @foreach($branches as $branch)
                    <option value="{{ $branch->id }}" {{ old('branch_id', $employee->branch_id) == $branch->id ?
                        'selected' : '' }}>
                        {{ $branch->name }}
                    </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label>Father name</label>
                <input class="input-form" name="father" value="{{ old('father', $employee->father) }}">
                @error('father')<div class="error-text">{{ $message }}</div>@enderror
            </div>

            <div>
                <label>Phone</label>
                <input class="input-form" name="phone" value="{{ old('phone', $employee->phone) }}" required>
                @error('phone')<div class="error-text">{{ $message }}</div>@enderror
            </div>

            <div>
                <label>Email</label>
                <input class="input-form" name="email" type="email" value="{{ old('email', $employee->email) }}">
                @error('email')<div class="error-text">{{ $message }}</div>@enderror
            </div>

            <div class="photo-upload">
                <div class="upload-left">
                    <label>Profile Picture</label>
                    <input class="input-form" type="file" name="photo" id="photoInput">
                    @error('photo')<div class="error-text">{{ $message }}</div>@enderror
                </div>

                <div class="upload-right">
                    <div class="preview-box">

                        @if($employee->photo)
                        <img id="photoPreview" src="{{ asset('storage/' . $employee->photo) }}" alt="Preview"
                            style="display:block;">
                        <i class="fa-solid fa-user" id="defaultIcon" style="display:none;"></i>
                        @else
                        <i class="fa-solid fa-user" id="defaultIcon"></i>
                        <img id="photoPreview" src="" alt="Preview" style="display:none;">
                        @endif

                    </div>
                </div>
            </div>

            <div>
                <label>Address</label>
                <textarea class="input-form" name="address">{{ old('address', $employee->address) }}</textarea>
                @error('address')<div class="error-text">{{ $message }}</div>@enderror
            </div>

            <div>
                <button class="btn-submit" type="submit">Update</button>
            </div>
        </form>
        <a href="{{ route('admin.employees.index') }}" class="btn btn-secondary px-4 mt-4">Back</a>
    </div>
</div>
@endsection
@push('scripts')
<script>
    document.getElementById('photoInput').addEventListener('change', function (e) {
    const file = e.target.files[0];
    const preview = document.getElementById('photoPreview');
    const icon = document.getElementById('defaultIcon');

    if (file && file.type.startsWith('image/')) {
        const reader = new FileReader();

        reader.onload = function (e) {
            preview.src = e.target.result;
            preview.style.display = 'block';
            icon.style.display = 'none';
        }

        reader.readAsDataURL(file);
    }
});
</script>
@endpush