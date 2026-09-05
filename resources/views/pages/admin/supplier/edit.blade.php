@extends('layouts.adminlayout')

@section('content')

<div class="container justify-center">
  <div class="form-card">
    <h2>Edit Supplier</h2>
    @include('components.alert')

    <form class="adduser-form" method="POST" action="{{ route('admin.suppliers.update', $supplier->id) }}" enctype="multipart/form-data">
      @csrf
      @method('PUT')

      <div class="row">
        <div class="col-md-6">
          <label>Contact Person Name</label>
          <input class="input-form" name="name" value="{{ old('name', $supplier->name) }}" required>
          @error('name')<div class="error-text">{{ $message }}</div>@enderror
        </div>

        <div class="col-md-6">
          <label>Company Name</label>
          <input class="input-form" name="company_name" value="{{ old('company_name', $supplier->company_name) }}" required>
          @error('company_name')<div class="error-text">{{ $message }}</div>@enderror
        </div>

        <div class="col-md-6">
          <label>Phone</label>
          <input class="input-form" name="phone" value="{{ old('phone', $supplier->phone) }}" required>
          @error('phone')<div class="error-text">{{ $message }}</div>@enderror
        </div>

        <div class="col-md-6">
          <label>Email</label>
          <input class="input-form" name="email" type="email" value="{{ old('email', $supplier->email) }}">
          @error('email')<div class="error-text">{{ $message }}</div>@enderror
        </div>

        <div class="col-md-6">
          <label>Due Amount (TK)</label>
          <input class="input-form" type="number" step="0.01" name="due" value="{{ old('due', number_format($supplier->due, 2, '.', '')) }}">
          @error('due')<div class="error-text">{{ $message }}</div>@enderror
        </div>

        <div class="col-md-6">
          <label>Address</label>
          <input class="input-form" name="address" value="{{ old('address', $supplier->address) }}">
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
              @if($supplier->image)
                <img id="photoPreview" src="{{ asset($supplier->image) }}" alt="Preview" style="display: block; object-fit: contain; padding: 4px;">
                <i class="fa-solid fa-building-user" id="defaultIcon" style="display: none;"></i>
              @else
                <i class="fa-solid fa-building-user" id="defaultIcon"></i>
                <img id="photoPreview" src="" alt="Preview" style="display: none; object-fit: contain; padding: 4px;">
              @endif
            </div>
          </div>
        </div>

      </div>

      <div>
        <button class="btn-submit" type="submit">Update Supplier</button>
      </div>
    </form>
    <a href="{{ route('admin.suppliers.index') }}" class="btn btn-secondary px-4 mt-3">Back</a>
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