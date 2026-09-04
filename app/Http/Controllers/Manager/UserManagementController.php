<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Customer;
use App\Models\Employee;
use App\Models\User;
use App\Traits\UploadHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;

class UserManagementController extends Controller
{
  use UploadHelper;

  public function __construct()
  {
    $this->middleware(['auth', 'role:manager']);
  }

  

  /**
   * Load UI page for Customer Accounts.
   */
  public function customer(Request $request)
  {
    return view('pages.manager.users.index-customer', [
      'roleTitle' => 'Customer',
    ]);
  }

  /**
   * Fetch Customer Accounts data via AJAX.
   */
  public function fetchCustomersData(Request $request)
  {
    $branchId = auth()->user()->branch_id;
    $query = User::with('branch')
      ->where('role', 'customer')
      ->where('branch_id', $branchId);

    if ($request->filled('search')) {
      $cleanSearch = trim($request->search);
      $idSearch = preg_replace('/^brc(200)?/i', '', $cleanSearch);

      $query->where(function ($q) use ($cleanSearch, $idSearch) {
        $q->where('fullname', 'like', "%{$cleanSearch}%")
          ->orWhere('username', 'like', "%{$cleanSearch}%")
          ->orWhere('customer_id', 'like', "%{$cleanSearch}%");

        if ($idSearch !== '' && is_numeric($idSearch)) {
          $q->orWhere('customer_id', (int) $idSearch);
        }
      });
    }

    $customers = $query->latest()->paginate(20)->withQueryString();

    return response()->json([
      'table'      => view('pages.manager.users.customer-table', compact('customers'))->render(),
      'mobile'     => view('pages.manager.users.customer-mtable', compact('customers'))->render(),
      'total'      => $customers->total(),
      'pagination' => (string) $customers->links(),
    ]);
  }

  public function show(User $user)
  {
    $user->load('branch');
    return view('pages.manager.users.show', compact('user'));
  }

  public function create()
  {
    $customers = Customer::select('id', 'shop_name')->where('branch_id', auth()->user()->branch_id)->orderBy('id', 'desc')->get();
    return view('pages.manager.users.create', compact('customers'));
  }

  public function store(Request $request)
  {
    $request->validate([
      'fullname' => ['required', 'string', 'max:255'],
      'username' => ['required', 'string', 'max:50', 'alpha_dash', 'unique:users,username'],
      'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
      'password' => ['required', 'confirmed', Rules\Password::defaults()],
      'customer_id' => ['required', 'integer', Rule::unique('users', 'customer_id')],
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

    return redirect()->route('manager.index.customers')->with('success', 'User created successfully.');
  }

  public function edit(User $user)
  {
    $customers = Customer::select('id', 'shop_name')->where('branch_id', auth()->user()->branch_id)->orderBy('id', 'desc')->get();
    $branches = Branch::orderBy('name', 'asc')->get();
    return view('pages.manager.users.edit', compact('user', 'branches', 'customers'));
  }

  public function update(Request $request, User $user)
  {
    $request->validate([
      'fullname' => ['required', 'string', 'max:255'],
      'username' => ['required', 'string', 'max:50', 'alpha_dash', 'unique:users,username,' . $user->id],
      'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
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
    $user->customer_id = $request->customer_id;
    $user->status = $request->status;

    $user->save();

    return redirect()->route('dashboards')->with('success', 'User updated successfully.');
  }

  // public function destroy(User $user)
  // {
  //   $this->deleteFile($user->profile_photo_path);
  //   $user->delete();
  //   return back()->with('success', 'User deleted.');
  // }
}
