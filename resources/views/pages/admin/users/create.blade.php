@extends('layouts.adminlayout')

@section('content')

<div class="container justify-center">
  <div class="form-card">
    <h2>Add Users</h2>
    @include('components.alert')
    <form class="adduser-form" method="POST" action="{{ route('admin.users.store') }}" enctype="multipart/form-data">
      @csrf
    <div class="row">
      <div class="col-md-6">
        <label>Full name</label>
        <input class="input-form" name="fullname" required>
        @error('fullname')<div class="error-text">{{ $message }}</div>@enderror
      </div>

      <div class="col-md-6">
        <label>Username</label>
        <input class="input-form" name="username" required>
        @error('username')<div class="error-text">{{ $message }}</div>@enderror
      </div>

      <div class="col-md-6">
        <label>Email</label>
        <input class="input-form" name="email" type="email" required>
        @error('email')<div class="error-text">{{ $message }}</div>@enderror
      </div>

      <div class="col-md-6">
        <label for="">Branch</label>
        <select class="input-form" name="branch_id">
          <option value="">--Select Branch--</option>
          @foreach($branches as $branch)
          <option value="{{$branch->id}}">{{$branch->name}}</option>
          @endforeach
        </select>
      </div>

      <div class="password-box col-md-6">
        <label>Password</label>
        <input type="password" class="input-form" name="password" id="password" placeholder="New Password" required>
        <i class="fa-solid fa-eye toggle-pass" data-target="password"></i>
      </div>

      <div class="password-box col-md-6">
        <label>Confirm Password</label>
        <input type="password" class="input-form" name="password_confirmation" id="confirm"
          placeholder="Confirm Password" required>
        <i class="fa-solid fa-eye toggle-pass" data-target="confirm"></i>
      </div>

      <div class="col-md-6">
        <label>Role</label>
        <select class="input-form" name="role" required>
          <option value="manager">Manager</option>
          <option value="sr">SR</option>
          <option value="customer">Customer</option>
        </select>
        @error('role')<div class="error-text">{{ $message }}</div>@enderror
      </div>

      

      <div class="col-md-6" id="employee_id_box">
        <label>Employee ID</label>
        <select class="input-form" name="employee_id">
          <option value="">--Select Employee ID--</option>
          @foreach($employees as $employee)
          <option value="{{$employee->id}}" {{ old('employee_id') == $employee->id ? 'selected' : '' }}>BRE100{{$employee->id}} ({{$employee->name}})</option>
          @endforeach
        </select>
        @error('employee_id')<div class="error-text">{{ $message }}</div>@enderror
      </div>

      <div class="col-md-6" id="customer_id_box" style="display: none;">
        <label>Customer ID</label>
        <select class="input-form" name="customer_id">
          <option value="">--Select Customer ID--</option>
          @foreach($customers as $customer)
          <option value="{{$customer->id}}" {{ old('customer_id') == $customer->id ? 'selected' : '' }}>BRC200{{$customer->id}} ({{$customer->shop_name}})</option>
          @endforeach
        </select>
        @error('customer_id')<div class="error-text">{{ $message }}</div>@enderror
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

      </div> <!-- Close row -->

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

// Dynamic toggle between Employee ID and Customer ID based on selected role
const roleSelect = document.querySelector('select[name="role"]');
const employeeBox = document.getElementById('employee_id_box');
const customerBox = document.getElementById('customer_id_box');

function toggleRoleFields() {
    if (!roleSelect || !employeeBox || !customerBox) return;
    if (roleSelect.value === 'customer') {
        employeeBox.style.display = 'none';
        customerBox.style.display = 'block';
    } else {
        employeeBox.style.display = 'block';
        customerBox.style.display = 'none';
    }
}

if (roleSelect) {
    roleSelect.addEventListener('change', toggleRoleFields);
    toggleRoleFields();
}
</script>
@endpush