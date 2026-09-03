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

    @if($user->role !== 'customer')

    <div 
    style="
        padding: 20px;
        background: #fff8e1;
        border: 1px solid #f6c343;
        color: #7a5b00;
        border-radius: 10px;
        text-align: center;
        margin-top: 20px;
    "
>
    <i class="fa-solid fa-triangle-exclamation me-2"></i>

<strong>শুধুমাত্র Customer Account এডিট করা যাবে</strong>

<p style="margin: 8px 0 0;">
    এই পেজ থেকে শুধুমাত্র Customer Account এডিট করা যাবে।
    অন্যান্য Account শুধুমাত্র Admin এডিট করতে পারবেন।
</p>

</div>


@else
    
    <form class="adduser-form" method="POST" action="{{ route('manager.users.update', $user->id) }}"
      enctype="multipart/form-data">
      @csrf
      @method('PUT')
      <div class="row">
      <div class="col-md-6">
        <label>Fullname</label>
        <input class="input-form" name="fullname" value="{{ old('fullname', $user->fullname) }}" required>
        @error('fullname')<div class="error-text">{{ $message }}</div>@enderror
      </div>

      <div class="col-md-6">
        <label>Username</label>
        <input class="input-form" name="username" value="{{ old('username', $user->username) }}" required>
      </div>

      <div class="col-md-6">
        <label>Email</label>
        <input class="input-form" name="email" type="email" value="{{ old('email', $user->email) }}" required>
      </div>


<div class="col-md-6">
        <label>Customer ID</label>
        <select class="input-form" name="customer_id">
          <option value="">--Select Customer ID--</option>

          @foreach($customers as $customer)
          <option value="{{ $customer->id }}" {{ old('customer_id', $user->customer_id) == $customer->id ? 'selected' :
            '' }}>
            BRC200{{ $customer->id }} ({{$customer->shop_name}})
          </option>
          @endforeach

        </select>
      </div>

      <div class="col-md-6">
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
            <img id="photoPreview" src="{{ str_starts_with($user->profile_photo_path, 'uploads/') ? asset($user->profile_photo_path) : asset('uploads/' . $user->profile_photo_path) }}" alt="Preview"
              style="display:block;">
            <i class="fa-solid fa-user" id="defaultIcon" style="display:none;"></i>
            @else
            <i class="fa-solid fa-user" id="defaultIcon"></i>
            <img id="photoPreview" src="" alt="Preview" style="display:none;">
            @endif

          </div>
        </div>
      </div>


      </div> <!-- end row -->

      <div>
        <button class="btn-submit" type="submit">Update</button>
      </div>

    </form>
    @endif
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