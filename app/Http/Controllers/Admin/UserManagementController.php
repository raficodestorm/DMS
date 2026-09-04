<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Customer;
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

  /**
   * Load UI page only for Admin Users (no heavy query on page load).
   */
  public function users()
  {
    $branches = Branch::select('id', 'name')->orderBy('name', 'asc')->get();
    return view('pages.admin.users.index', compact('branches'));
  }

  /**
   * Fetch Users data via AJAX.
   */
  public function fetchUsersIndexData(Request $request)
  {
    $query = User::with('branch');

    if ($request->filled('search')) {
      $search = trim($request->search);
      $query->where(function ($q) use ($search) {
        $q->where('fullname', 'like', "%{$search}%")
          ->orWhere('username', 'like', "%{$search}%");
      });
    }

    if ($request->filled('role')) {
      $query->where('role', $request->role);
    }

    if ($request->filled('branch_id')) {
      $query->where('branch_id', $request->branch_id);
    }

    $users = $query->orderBy('created_at', 'desc')->paginate(20)->withQueryString();

    return response()->json([
      'table'      => view('pages.admin.users.table', compact('users'))->render(),
      'mobile'     => view('pages.admin.users.mtable', compact('users'))->render(),
      'total'      => $users->total(),
      'pagination' => (string) $users->links(),
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
    $customers = Customer::select('id', 'shop_name')->orderBy('id', 'desc')->get();
    return view('pages.admin.users.create', compact('branches', 'employees', 'customers'));
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
      'customer_id' => ['nullable', 'integer'],
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
      'employee_id' => $request->role === 'customer' ? null : $request->employee_id,
      'customer_id' => $request->role === 'customer' ? $request->customer_id : null,
      'profile_photo_path' => $profilePath,
    ]);

    return redirect()->route('admin.index.users')->with('success', 'User created successfully.');
    
  }

  public function edit(User $user)
  {
    $branches = Branch::orderBy('name', 'asc')->get();
    $employees = Employee::orderBy('id', 'desc')->get();
    $customers = Customer::orderBy('id', 'desc')->get();
    return view('pages.admin.users.edit', compact('user', 'branches', 'employees', 'customers'));
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
      'customer_id' => ['nullable', 'integer'],
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
    $user->employee_id = $request->role === 'customer' ? null : $request->employee_id;
    $user->customer_id = $request->role === 'customer' ? $request->customer_id : null;
    $user->status = $request->status;

    $user->save();

    return redirect()->route('admin.index.users')->with('success', 'User updated.');
  }

  public function destroy(User $user)
{
    if ($user->hasRelatedRecords()) {
        return redirect()
            ->back()
            ->with(
                'error',
                'This user cannot be deleted because related records exist.'
            );
    }

    $profilePhoto = $user->profile_photo_path;

    $user->delete();

    $this->deleteFile($profilePhoto);

    return redirect()
        ->route('admin.index.users')
        ->with('success', 'User deleted successfully');
}
}
