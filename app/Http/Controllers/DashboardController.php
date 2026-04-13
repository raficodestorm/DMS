<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;


class DashboardController extends Controller
{
  public function index(Request $request)
  {
    $user = $request->user();
    $role = $user->role;

    // Role-based dashboard view
    $view = "pages.dashboard.{$role}";
    if (!view()->exists($view)) {
      $view = "pages.dashboard.user";
    }

    return view($view);
  }
}
