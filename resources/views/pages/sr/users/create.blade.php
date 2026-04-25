@extends('layouts.srlayout')

@section('content')

<div class="container justify-center">
  <div class="form-card">
    <h2>Add Users</h2>
    @include('components.alert')
    <form class="adduser-form" method="POST" action="{{ route('sr.users.store') }}" enctype="multipart/form-data">
      @csrf

      <div>
        <label>Full name</label>
        <input class="input-form" name="fullname" required>
        @error('fullname')<div class="error-text">{{ $message }}</div>@enderror
      </div>

      <div>
        <label>Username</label>
        <input class="input-form" name="username" required>
        @error('username')<div class="error-text">{{ $message }}</div>@enderror
      </div>

      <div>
        <label>Email</label>
        <input class="input-form" name="email" type="email" required>
        @error('email')<div class="error-text">{{ $message }}</div>@enderror
      </div>

      <div class="password-box">
        <label>Password</label>
        <input type="password" class="input-form" name="password" id="password" placeholder="New Password" required>
        <i class="fa-solid fa-eye toggle-pass" data-target="password"></i>
      </div>

      <div class="password-box">
        <label>Confirm Password</label>
        <input type="password" class="input-form" name="password_confirmation" id="confirm"
          placeholder="Confirm Password" required>
        <i class="fa-solid fa-eye toggle-pass" data-target="confirm"></i>
      </div>

      <div class="photo-upload">
        <div class="upload-left">
          <label>Profile Picture</label>
          <input class="input-form" type="file" name="profile_photo" id="photoInput">
        </div>

        <div class="upload-right">
          <div class="preview-box">
            <i class="fa-solid fa-user" id="defaultIcon"></i>
            <img id="photoPreview" src="" alt="Preview">
          </div>
        </div>
      </div>

      <div>
        <label>Customer ID</label>
        <select class="input-form" name="customer_id">
          <option value="">--Select Customer ID--</option>
          @foreach($customers as $customer)
          <option value="{{$customer->id}}">BRC200{{$customer->id}} ( {{
            $customer->shop_name }} )</option>
          @endforeach
        </select>
      </div>

      <div>
        <button class="btn-submit" type="submit">Create</button>
      </div>
    </form>
  </div>
</div>
@endsection
@push('scripts')
<script>
  document.querySelectorAll('.toggle-pass').forEach(icon => {
  icon.addEventListener('click', function () {

    const input = document.getElementById(this.dataset.target);

    if (input.type === "password") {
      input.type = "text";
      this.classList.remove('fa-eye');
      this.classList.add('fa-eye-slash');
    } else {
      input.type = "password";
      this.classList.remove('fa-eye-slash');
      this.classList.add('fa-eye');
    }
 
  });
});


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