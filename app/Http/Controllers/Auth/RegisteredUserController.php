<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\User;
use App\Traits\UploadHelper;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class RegisteredUserController extends Controller
{
    use UploadHelper;

    public function create()
    {
        $countriesData = [];
        $countriesPath = resource_path('data/countries.json');

        if (file_exists($countriesPath)) {
            $countriesData = json_decode(file_get_contents($countriesPath), true) ?: [];
        }

        $countries = array_keys($countriesData);

        return view('auth.register', compact('countries', 'countriesData'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'fullname'      => ['required', 'string', 'max:255'],
            'username'      => ['required', 'string', 'max:50', 'alpha_dash', 'unique:users,username'],
            'email'         => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'phone'         => ['required', 'string', 'max:30'],
            'country'       => ['required', 'string', 'max:100'],
            'city'          => ['required', 'string', 'max:100'],
            'address'       => ['required', 'string', 'max:500'],
            'shop_name'     => ['nullable', 'string', 'max:150'],
            'password'      => ['required', 'confirmed', Rules\Password::defaults()],
            'profile_photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
        ]);

        $profilePath = null;
        if ($request->hasFile('profile_photo')) {
            $profilePath = $this->uploadFile($request->file('profile_photo'), 'profile_photos');
        }

        $user = DB::transaction(function () use ($request, $profilePath) {
            $customer = Customer::create([
                'shop_name'      => $request->filled('shop_name') ? $request->shop_name : $request->fullname,
                'manager'        => $request->fullname,
                'phone'          => $request->phone,
                'country'        => $request->country,
                'city'           => $request->city,
                'address'        => $request->address,
                'branch_id'      => null,
                'due'            => 0,
                'customer_type'  => 'retail',
                'customer_group' => 'retail',

            ]);

            $user = User::create([
                'fullname'           => $request->fullname,
                'username'           => $request->username,
                'email'              => $request->email,
                'password'           => Hash::make($request->password),
                'customer_id'        => $customer->id,
                'branch_id'          => null,
                'profile_photo_path' => $profilePath,
                'role'               => 'customer',
                'status'             => 'active',
            ]);

            return $user;
        });

        event(new Registered($user));

        auth()->login($user);

        return redirect()->route('dashboards');
    }
}
