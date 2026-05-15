<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class UserManagementController extends Controller
{
  public function __construct()
  {
    $this->middleware(['auth', 'role:manager']);
  }

  public function sr()
  {
    $branchId = auth()->user()->branch_id;

    $srs = User::with('branch')
      ->where('role', 'sr')
      ->where('branch_id', $branchId)
      ->latest() // cleaner than orderBy
      ->paginate(20);

    return view('pages.manager.users.index-sr', [
      'srs' => $srs,
      'roleTitle' => 'SR'
    ]);
  }

  public function customer()
  {
    $branchId = auth()->user()->branch_id;

    $customers = User::with('branch')
      ->where('role', 'customer')
      ->where('branch_id', $branchId)
      ->latest() // cleaner than orderBy
      ->paginate(20);

    return view('pages.manager.users.index-cuatomer', [
      'customers' => $customers,
      'roleTitle' => 'Customer'
    ]);
  }

  public function show(User $user)
  {
    $user->load('branch');
    return view('pages.manager.users.show', compact('user'));
  }

  public function create()
  {
    $employees = Employee::select('id', 'name')->where('branch_id', auth()->user()->branch_id)->latest()->get();
    return view('pages.manager.users.create', compact('employees'));
  }

  public function store(Request $request)
  {
    $request->validate([
      'fullname' => ['required', 'string', 'max:255'],
      'username' => ['required', 'string', 'max:50', 'alpha_dash', 'unique:users,username'],
      'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
      'password' => ['required', 'confirmed', Rules\Password::defaults()],
      'employee_id' => ['nullable', 'integer'],
      'profile_photo' => ['nullable', 'image', 'max:2048'],
    ]);

    $profilePath = null;
    if ($request->hasFile('profile_photo')) {
      $profilePath = $request->file('profile_photo')->store('profile_photos', 'public');
    }

    $user = User::create([
      'fullname' => $request->fullname,
      'username' => $request->username,
      'email' => $request->email,
      'password' => Hash::make($request->password),
      'role' => 'sr',
      'branch_id' => auth()->user()->branch_id,
      'employee_id' => $request->employee_id,
      'profile_photo_path' => $profilePath,
    ]);

    // return redirect()->route('admin.users.index')->with('success', 'User created successfully.');
    return redirect()->route('dashboards')->with('success', 'User created successfully.');
  }

  public function edit(User $user)
  {
    $employees = Employee::select('id', 'name')->where('branch_id', auth()->user()->branch_id)->latest()->get();
    $branches = Branch::orderBy('name', 'asc')->get();
    return view('pages.manager.users.edit', compact('user', 'branches', 'employees'));
  }

  public function update(Request $request, User $user)
  {
    $request->validate([
      'fullname' => ['required', 'string', 'max:255'],
      'username' => ['required', 'string', 'max:50', 'alpha_dash', 'unique:users,username,' . $user->id],
      'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
      'branch_id' => ['nullable', 'integer'],
      'employee_id' => ['nullable', 'integer'],
      'profile_photo' => ['nullable', 'image', 'max:2048'],
      'status' => ['required', 'in:active,inactive'],
    ]);

    if ($request->hasFile('profile_photo')) {
      $user->profile_photo_path = $request->file('profile_photo')->store('profile_photos', 'public');
    }

    $user->fullname = $request->fullname;
    $user->username = $request->username;
    $user->email = $request->email;
    $user->branch_id = $request->branch_id;
    $user->employee_id = $request->employee_id;
    $user->status = $request->status;

    $user->save();

    return redirect()->route('dashboards')->with('success', 'User updated successfully.');
  }

  public function destroy(User $user)
  {
    $user->delete();
    return back()->with('success', 'User deleted.');
  }
}
