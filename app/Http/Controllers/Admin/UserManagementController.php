<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Employee;
use App\Models\User;
use App\Traits\UploadHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class UserManagementController extends Controller
{
  use UploadHelper;

  public function __construct()
  {
    $this->middleware(['auth', 'role:admin']);
  }

  public function admins()
  {
    $admins = User::where('role', 'admin')->orderBy('created_at', 'desc')->paginate(10);
    return view('pages.admin.users.index-admin', [
      'admins' => $admins,
      'roleTitle' => 'Admins'
    ]);
  }

  public function managers()
  {
    $managers = User::with('branch')->where('role', 'manager')->orderBy('created_at', 'desc')->paginate(10);
    return view('pages.admin.users.index-managers', [
      'managers' => $managers,
      'roleTitle' => 'Branch Managers'
    ]);
  }

  public function sr()
  {
    $srs = User::with('branch')->where('role', 'sr')->orderBy('created_at', 'desc')->paginate(10);
    return view('pages.admin.users.index-sr', [
      'srs' => $srs,
      'roleTitle' => 'sr'
    ]);
  }

  public function customer()
  {
    $customers = User::with('branch')->where('role', 'customer')->orderBy('created_at', 'desc')->paginate(10);
    return view('pages.admin.users.index-customer', [
      'customers' => $customers,
      'roleTitle' => 'customers'
    ]);
  }

  public function normalUsers()
  {
    $users = User::where('role', 'user')->orderBy('created_at', 'desc')->paginate(10);
    return view('pages.admin.users.index-users', [
      'users' => $users,
      'roleTitle' => 'Users'
    ]);
  }

  public function show(User $user)
  {
    $user->load('branch');
    return view('pages.admin.users.show', compact('user'));
  }

  public function create()
  {
    $branches = Branch::select('id', 'name')->orderBy('name')->get();
    $employees = Employee::select('id', 'name')->latest()->get();
    return view('pages.admin.users.create', compact('branches', 'employees'));
  }

  public function store(Request $request)
  {
    $request->validate([
      'fullname' => ['required', 'string', 'max:255'],
      'username' => ['required', 'string', 'max:50', 'alpha_dash', 'unique:users,username'],
      'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
      'password' => ['required', 'confirmed', Rules\Password::defaults()],
      'role' => ['required', 'in:admin,manager,sr,customer,user'],
      'branch_id' => ['nullable', 'integer'],
      'employee_id' => ['nullable', 'integer'],
      'profile_photo' => ['nullable', 'image', 'max:2048'],
    ]);

    $profilePath = null;
    if ($request->hasFile('profile_photo')) {
      $profilePath = $this->uploadFile($request->file('profile_photo'), 'profile_photos');
    }

    $user = User::create([
      'fullname' => $request->fullname,
      'username' => $request->username,
      'email' => $request->email,
      'password' => Hash::make($request->password),
      'role' => $request->role,
      'branch_id' => $request->branch_id,
      'employee_id' => $request->employee_id,
      'profile_photo_path' => $profilePath,
    ]);

    // return redirect()->route('admin.users.index')->with('success', 'User created successfully.');
    return redirect()->route('dashboard.admin')->with('success', 'User created successfully.');
  }

  public function edit(User $user)
  {
    $branches = Branch::orderBy('name', 'asc')->get();
    $employees = Employee::orderBy('name', 'asc')->get();
    return view('pages.admin.users.edit', compact('user', 'branches', 'employees'));
  }

  public function update(Request $request, User $user)
  {
    $request->validate([
      'fullname' => ['required', 'string', 'max:255'],
      'username' => ['required', 'string', 'max:50', 'alpha_dash', 'unique:users,username,' . $user->id],
      'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
      'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
      'role' => ['required', 'in:user,customer,sr,manager,admin'],
      'branch_id' => ['nullable', 'integer'],
      'employee_id' => ['nullable', 'integer'],
      'profile_photo' => ['nullable', 'image', 'max:2048'],
      'status' => ['required', 'in:active,inactive'],
    ]);

    if ($request->hasFile('profile_photo')) {
      $this->deleteFile($user->profile_photo_path);
      $user->profile_photo_path = $this->uploadFile($request->file('profile_photo'), 'profile_photos');
    }

    $user->fullname = $request->fullname;
    $user->username = $request->username;
    $user->email = $request->email;
    if ($request->filled('password')) {
      $user->password = Hash::make($request->password);
    }
    $user->role = $request->role;
    $user->branch_id = $request->branch_id;
    $user->employee_id = $request->employee_id;
    $user->status = $request->status;

    $user->save();

    return redirect()->route('dashboards')->with('success', 'User updated.');
  }

  public function destroy(User $user)
  {
    $this->deleteFile($user->profile_photo_path);
    $user->delete();
    return redirect()->route('dashboards')
      ->with('success', 'User deleted successfully');
  }
}
