<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class PasswordChangeController extends Controller
{
  public function showVerifyForm()
  {
    return view('custom-auth.verify-password');
  }

  public function verifyUser(Request $request)
  {
    $request->validate([
      'email' => 'required|email',
      'password' => 'required'
    ]);

    $user = auth()->user();

    if ($user->email !== $request->email || !Hash::check($request->password, $user->password)) {
      return back()->withErrors([
        'error' => 'Email and password does not match'
      ]);
    }

    Session::put('password_verified', true);

    return redirect()->route('password.reset.form');
  }

  public function showResetForm()
  {
    if (!Session::get('password_verified')) {
      return redirect()->route('password.verify.form');
    }

    return view('custom-auth.reset-password');
  }

  public function updatePassword(Request $request)
  {
    if (!Session::get('password_verified')) {
      return redirect()->route('password.verify.form');
    }

    $request->validate([
      'password' => 'required|min:6|confirmed'
    ]);

    $user = auth()->user();
    $user->update([
      'password' => Hash::make($request->password)
    ]);

    Session::forget('password_verified');

    return redirect()->route('dashboards')->with('success', 'Password updated successfully');
  }
}
