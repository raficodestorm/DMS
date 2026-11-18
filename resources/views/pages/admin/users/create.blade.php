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
          <option value="">--Select Role--</option>
          <option value="editor" {{ old('role') == 'editor' ? 'selected' : '' }}>Editor (Custom Access)</option>
          <option value="manager" {{ old('role') == 'manager' ? 'selected' : '' }}>Manager</option>
          <option value="sr" {{ old('role') == 'sr' ? 'selected' : '' }}>SR</option>
          <option value="dso" {{ old('role') == 'dso' ? 'selected' : '' }}>DSO</option>
          <option value="customer" {{ old('role') == 'customer' ? 'selected' : '' }}>Customer</option>
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
              $oldPerms = old('permissions', []);
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
                           {{ in_array($permKey, $oldPerms, true) ? 'checked' : '' }}
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