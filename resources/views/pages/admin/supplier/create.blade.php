@extends('layouts.adminlayout')

@section('content')

<div class="container justify-center">
  <div class="form-card">
    <h2>Add Supplier</h2>
    @include('components.alert')
    <form class="adduser-form" method="POST" action="{{ route('admin.suppliers.store') }}" enctype="multipart/form-data">
      @csrf
      <div class="row">

      <div class="col-md-6">
          <label>Company Name</label>
          <input class="input-form" name="company_name" value="{{ old('company_name') }}" placeholder="Company Name" required>
          @error('company_name')<div class="error-text">{{ $message }}</div>@enderror
        </div>
        
        <div class="col-md-6">
          <label>Contact Person Name</label>
          <input class="input-form" name="name" value="{{ old('name') }}" placeholder="Supplier / Contact Name" required>
          @error('name')<div class="error-text">{{ $message }}</div>@enderror
        </div>

        <div class="col-md-6">
          <label>Phone</label>
          <input class="input-form" name="phone" value="{{ old('phone') }}" placeholder="Phone Number" required>
          @error('phone')<div class="error-text">{{ $message }}</div>@enderror
        </div>

        <div class="col-md-6">
          <label>Email</label>
          <input class="input-form" name="email" type="email" value="{{ old('email') }}" placeholder="Email Address">
          @error('email')<div class="error-text">{{ $message }}</div>@enderror
        </div>

        <div class="col-md-6">
          <label>Initial Due Amount (TK)</label>
          <input class="input-form" type="number" step="0.01" name="due" value="{{ old('due', '0.00') }}" placeholder="0.00">
          @error('due')<div class="error-text">{{ $message }}</div>@enderror
        </div>

        <div class="col-md-6">
          <label>Address</label>
          <input class="input-form" name="address" value="{{ old('address') }}" placeholder="Supplier Address">
          @error('address')<div class="error-text">{{ $message }}</div>@enderror
        </div>

        <div class="photo-upload col-12">
          <div class="upload-left">
            <label>Supplier Photo / Logo</label>
            <input class="input-form" type="file" name="image" id="photoInput" accept="image/*">
            @error('image')<div class="error-text">{{ $message }}</div>@enderror
          </div>

          <div class="upload-right" style="width: 180px; height: 90px;">
            <div class="preview-box" style="width: 100%; height: 100%;">
              <i class="fa-solid fa-building-user" id="defaultIcon"></i>
              <img id="photoPreview" src="" alt="Preview" style="display: none; object-fit: contain; padding: 4px;">
            </div>
          </div>
        </div>

      </div> <!-- Close row -->

      <div>
        <button class="btn-submit" type="submit">Create Supplier</button>
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
            if (icon) icon.style.display = 'none';
        }

        reader.readAsDataURL(file);
    }
});
</script>
@endpush