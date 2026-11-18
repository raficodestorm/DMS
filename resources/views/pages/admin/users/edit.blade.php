@extends('layouts.adminlayout')

@section('content')

<div class="container adduser-container justify-center">
  <div class="form-card">
    <h2>Edit User</h2>
    @include('components.alert')

    <form class="adduser-form" method="POST" action="{{ route('admin.users.update', $user->id) }}"
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
        <label>Role</label>
        <select class="input-form" name="role" required>
          <option value="">--Select Role--</option>
          <option value="editor" {{ old('role', $user->role) == 'editor' ? 'selected' : '' }}>Editor (Custom Access)</option>
          <option value="manager" {{ old('role', $user->role) == 'manager' ? 'selected' : '' }}>Manager</option>
          <option value="sr" {{ old('role', $user->role) == 'sr' ? 'selected' : '' }}>SR</option>
          <option value="dso" {{ old('role', $user->role) == 'dso' ? 'selected' : '' }}>DSO</option>
          <option value="customer" {{ old('role', $user->role) == 'customer' ? 'selected' : '' }}>Customer</option>
        </select>
        @error('role')<div class="error-text">{{ $message }}</div>@enderror
      </div>

      <div class="col-md-6">
        <label>Branch</label>
        <select class="input-form" name="branch_id">
          <option value="">--Select Branch--</option>

          @foreach($branches as $branch)
          <option value="{{ $branch->id }}" {{ old('branch_id', $user->branch_id) == $branch->id ? 'selected' : '' }}>
            {{ $branch->name }}
          </option>
          @endforeach

        </select>
      </div>
      <div class="col-md-6" id="employee_id_box">
        <label>Employee ID</label>
        <select class="input-form" name="employee_id">
          <option value="">--Select Employee ID--</option>

          @foreach($employees as $employee)
          <option value="{{ $employee->id }}" {{ old('employee_id', $user->employee_id) == $employee->id ? 'selected' : '' }}>
            BRE100{{$employee->id}} ({{$employee->name}})
          </option>
          @endforeach

        </select>
        @error('employee_id')<div class="error-text">{{ $message }}</div>@enderror
      </div>

      <div class="col-md-6" id="customer_id_box" style="display: none;">
        <label>Customer ID</label>
        <select class="input-form" name="customer_id">
          <option value="">--Select Customer ID--</option>

          @foreach($customers as $customer)
          <option value="{{ $customer->id }}" {{ old('customer_id', $user->customer_id) == $customer->id ? 'selected' : '' }}>
            BRC200{{$customer->id}} ({{$customer->shop_name}})
          </option>
          @endforeach

        </select>
        @error('customer_id')<div class="error-text">{{ $message }}</div>@enderror
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
            <img id="photoPreview"
              src="{{ str_starts_with($user->profile_photo_path, 'uploads/') ? asset($user->profile_photo_path) : asset('uploads/' . $user->profile_photo_path) }}"
              alt="Preview" style="display:block;">
            <i class="fa-solid fa-user" id="defaultIcon" style="display:none;"></i>
            @else
            <i class="fa-solid fa-user" id="defaultIcon"></i>
            <img id="photoPreview" src="" alt="Preview" style="display:none;">
            @endif

          </div>
        </div>
      </div>

      {{-- Editor Permissions Section --}}
      <div class="col-12 mt-3" id="editor_permissions_box" style="display: none;">
        <div class="editor-perm-card" style="background: var(--section-bg, #ffffff); border: 1px solid var(--border-color, #e2e8f0); border-radius: 12px; padding: 20px; box-shadow: 0 4px 16px rgba(0,0,0,0.04);">
          <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 pb-2 border-bottom">
            <div>
              <h5 style="margin: 0; font-weight: 700; color: var(--primary, #3131ff); display: inline-flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-user-shield"></i> Editor Permissions & Feature Access
              </h5>
              <p style="margin: 4px 0 0; font-size: 13px; color: #64748b;">
                Admin panel-e ei editor user kon kon features access korte parbe ta select korun.
              </p>
            </div>
            <div class="d-flex gap-2 mt-2 mt-md-0">
              <button type="button" class="btn btn-sm btn-outline-primary" id="btnSelectAllPerms" style="font-size: 12px; font-weight: 600; border-radius: 6px; padding: 4px 10px;">
                <i class="fa-solid fa-check-double me-1"></i> Select All
              </button>
              <button type="button" class="btn btn-sm btn-outline-secondary" id="btnUnselectAllPerms" style="font-size: 12px; font-weight: 600; border-radius: 6px; padding: 4px 10px;">
                <i class="fa-solid fa-xmark me-1"></i> Unselect All
              </button>
            </div>
          </div>

          <div class="row g-3">
            @php
              $currentPerms = old('permissions', is_array($user->permissions) ? $user->permissions : []);
            @endphp
            @foreach($permissionGroups as $groupTitle => $items)
            <div class="col-md-6 col-lg-4">
              <div class="perm-group-box" style="background: rgba(49, 49, 255, 0.02); border: 1px solid var(--border-color, #e2e8f0); border-radius: 8px; padding: 12px 14px; height: 100%;">
                <div class="d-flex justify-content-between align-items-center mb-2 pb-1 border-bottom">
                  <span style="font-weight: 700; font-size: 13px; color: #1e293b; text-transform: uppercase; letter-spacing: 0.5px;">
                    {{ $groupTitle }}
                  </span>
                  <button type="button" class="btn btn-link btn-sm p-0 toggle-group-perms" data-group="{{ Str::slug($groupTitle) }}" style="font-size: 11px; text-decoration: none; color: var(--primary, #3131ff); font-weight: 600;">
                    Toggle
                  </button>
                </div>

                <div class="perm-items-list" data-group-items="{{ Str::slug($groupTitle) }}">
                  @foreach($items as $permKey => $permMeta)
                  <label class="d-flex align-items-start gap-2 py-1 mb-1 perm-item-label" style="cursor: pointer; user-select: none;">
                    <input type="checkbox" name="permissions[]" value="{{ $permKey }}"
                           class="perm-checkbox form-check-input mt-1"
                           {{ in_array($permKey, $currentPerms, true) ? 'checked' : '' }}
                           style="cursor: pointer; min-width: 16px; min-height: 16px;">
                    <div style="line-height: 1.3;">
                      <div style="font-size: 13px; font-weight: 600; color: #334155;">{{ $permMeta['label'] }}</div>
                      <small style="font-size: 11px; color: #64748b; display: block;">{{ $permMeta['desc'] }}</small>
                    </div>
                  </label>
                  @endforeach
                </div>
              </div>
            </div>
            @endforeach
          </div>
        </div>
      </div>


      </div> <!-- Close row -->

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

// Dynamic toggle between Employee ID, Customer ID, and Editor Permissions based on selected role
const roleSelect = document.querySelector('select[name="role"]');
const employeeBox = document.getElementById('employee_id_box');
const customerBox = document.getElementById('customer_id_box');
const editorPermsBox = document.getElementById('editor_permissions_box');

function toggleRoleFields() {
    if (!roleSelect) return;
    const role = roleSelect.value;

    if (employeeBox) {
        employeeBox.style.display = (role === 'customer' || role === 'editor') ? 'none' : 'block';
    }

    if (customerBox) {
        customerBox.style.display = (role === 'customer') ? 'block' : 'none';
    }

    if (editorPermsBox) {
        editorPermsBox.style.display = (role === 'editor') ? 'block' : 'none';
    }
}

if (roleSelect) {
    roleSelect.addEventListener('change', toggleRoleFields);
    toggleRoleFields();
}

// Select All / Unselect All
document.getElementById('btnSelectAllPerms')?.addEventListener('click', function() {
    document.querySelectorAll('.perm-checkbox').forEach(cb => cb.checked = true);
});

document.getElementById('btnUnselectAllPerms')?.addEventListener('click', function() {
    document.querySelectorAll('.perm-checkbox').forEach(cb => cb.checked = false);
});

// Group Toggle
document.querySelectorAll('.toggle-group-perms').forEach(btn => {
    btn.addEventListener('click', function() {
        const groupSlug = this.dataset.group;
        const container = document.querySelector(`[data-group-items="${groupSlug}"]`);
        if (!container) return;
        const checkboxes = container.querySelectorAll('.perm-checkbox');
        const allChecked = Array.from(checkboxes).every(cb => cb.checked);
        checkboxes.forEach(cb => cb.checked = !allChecked);
    });
});
</script>
@endpush