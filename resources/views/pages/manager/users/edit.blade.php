@extends('layouts.managerlayout')

@section('content')

<div class="container adduser-container justify-center">
  <div class="form-card">
    <h2>Edit User</h2>
    @if ($errors->any())
    <div class="alert alert-danger">
      <ul class="mb-0">
        @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
    @endif

    <form class="adduser-form" method="POST" action="{{ route('manager.users.update', $user->id) }}"
      enctype="multipart/form-data">
      @csrf
      @method('PUT')

      <div class="input-box">
        <label>Fullname</label>
        <input class="input-form" name="fullname" value="{{ old('fullname', $user->fullname) }}" required>
        @error('fullname')<div class="error-text">{{ $message }}</div>@enderror
      </div>

      <div class="input-box">
        <label>Username</label>
        <input class="input-form" name="username" value="{{ old('username', $user->username) }}" required>
      </div>

      <div class="input-box">
        <label>Email</label>
        <input class="input-form" name="email" type="email" value="{{ old('email', $user->email) }}" required>
      </div>

      <div class="input-box">
        <label>Branch</label>
        <select class="input-form" name="branch_id">
          <option value="">--Select Branch--</option>
          @foreach($branches as $branch)
          <option value="{{ old('branch_id', $user->branch_id) == $branch->id ? 'selected' : '' }}">
            {{ $branch->name }}
          </option>
          @endforeach
        </select>
      </div>

      <div class="input-box">
        <label>Status</label>
        <select class="input-form" name="status">
          <option value="active" {{ old('status', $user->status) == 'active' ? 'selected' : '' }}>active</option>
          <option value="inactive" {{ old('status', $user->status) == 'inactive' ? 'selected' : '' }}>inactive</option>
        </select>
      </div>

      <div class="photo-upload">
        <div class="upload-left">
          <label>Profile Picture</label>
          <input class="input-form" type="file" name="profile_photo" id="photoInput">
          @error('profile_photo')<div class="error-text">{{ $message }}</div>@enderror
        </div>

        <div class="upload-right">
          <div class="preview-box">

            @if($user->profile_photo_path)
            <img id="photoPreview" src="{{ asset('storage/' . $user->profile_photo_path) }}" alt="Preview"
              style="display:block;">
            <i class="fa-solid fa-user" id="defaultIcon" style="display:none;"></i>
            @else
            <i class="fa-solid fa-user" id="defaultIcon"></i>
            <img id="photoPreview" src="" alt="Preview" style="display:none;">
            @endif

          </div>
        </div>
      </div>


      <div class="input-box">
        <label>Employee ID</label>
        <select class="input-form" name="employee_id">
          <option value="">--Select Employee ID--</option>

          @foreach($employees as $employee)
          <option value="{{ $employee->id }}" {{ old('employee_id', $user->employee_id) == $employee->id ? 'selected' :
            '' }}>
            BRE100{{ $employee->id }}
          </option>
          @endforeach

        </select>
      </div>

      <div>
        <button class="btn-submit" type="submit">Update</button>
      </div>

    </form>
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