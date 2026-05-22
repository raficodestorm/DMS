<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Traits\UploadHelper;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Support\Str;

class RegisteredUserController extends Controller
{
    use UploadHelper;

    public function create()
    {
        return view('auth.register'); // users register view (only general users)
    }

    public function store(Request $request)
    {
        $request->validate([
            'fullname'      => ['required', 'string', 'max:255'],
            'username'      => ['required', 'string', 'max:50', 'alpha_dash', 'unique:users,username'],
            'email'         => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password'      => ['required', 'confirmed', Rules\Password::defaults()],
            'phone'         => ['nullable', 'string', 'max:30'],
            'address'       => ['nullable', 'string'],
            'profile_photo' => ['nullable', 'image', 'max:2048'],
        ]);

        $profilePath = null;
        if ($request->hasFile('profile_photo')) {
            $profilePath = $this->uploadFile($request->file('profile_photo'), 'profile_photos');
        }

        $user = User::create([
            'fullname'          => $request->fullname,
            'username'          => $request->username,
            'email'             => $request->email,
            'password'          => Hash::make($request->password),
            'profile_photo_path' => $profilePath,
            'role'              => 'user',
        ]);

        event(new Registered($user));

        auth()->login($user);

        // redirect based on role (user)
        return redirect()->route('dashboard.user');
    }
}
