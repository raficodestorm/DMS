<?php

namespace App\Http\Controllers\Sr;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Customer;
use App\Models\User;
use App\Traits\UploadHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\Rule;

class UserManagementController extends Controller
{
  use UploadHelper;

  public function __construct()
  {
    $this->middleware(['auth', 'role:sr']);
  }


  public function customer()
  {
    $branchId = auth()->user()->branch_id;

    $customers = User::with('branch')
      ->where('role', 'customer')
      ->where('branch_id', $branchId)
      ->latest()
      ->paginate(20);

    return view('pages.sr.users.index-customer', [
      'customers' => $customers,
      'roleTitle' => 'Customer'
    ]);
  }

  public function show(User $user)
  {
    $user->load('branch');
    return view('pages.sr.users.show', compact('user'));
  }

  public function create()
  {
    // form to create customer 
    $customers = Customer::orderBy('id', 'desc')->get();
    return view('pages.sr.users.create', compact('customers'));
  }

  public function store(Request $request)
  {
    $request->validate([
      'fullname' => ['required', 'string', 'max:255'],
      'username' => ['required', 'string', 'max:50', 'alpha_dash', 'unique:users,username'],
      'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
      'password' => ['required', 'confirmed', Rules\Password::defaults()],
      'customer_id' => ['required', 'integer', Rule::unique('users', 'customer_id'),],
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
      'role' => 'customer',
      'branch_id' => auth()->user()->branch_id,
      'customer_id' => $request->customer_id,
      'profile_photo_path' => $profilePath,
    ]);

    // return redirect()->route('admin.users.index')->with('success', 'User created successfully.');
    return redirect()->route('dashboards')->with('success', 'User created successfully.');
  }

  public function edit(User $user)
  {
    $branches = Branch::orderBy('name', 'asc')->get();
    return view('pages.sr.users.edit', compact('user', 'branches'));
  }

  public function update(Request $request, User $user)
  {
    $request->validate([
      'fullname' => ['required', 'string', 'max:255'],
      'username' => ['required', 'string', 'max:50', 'alpha_dash', 'unique:users,username,' . $user->id],
      'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
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
    $user->status = $request->status;

    $user->save();

    return redirect()->route('dashboards')->with('success', 'User updated successfully.');
  }

  public function destroy(User $user)
  {
    $this->deleteFile($user->profile_photo_path);
    $user->delete();
    return back()->with('success', 'User deleted.');
  }
}
