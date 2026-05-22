<?php

use Illuminate\Support\Facades\Request;

if (!function_exists('isActive')) {
  function isActive($routes)
  {
    foreach ((array) $routes as $route) {
      if (Request::routeIs($route)) {
        return 'active-nav';
      }
    }
    return '';
  }
}

if (!function_exists('isOpen')) {
  function isOpen($routes)
  {
    foreach ((array) $routes as $route) {
      if (Request::routeIs($route)) {
        return 'display:block;';
      }
    }
    return '';
  }
}

if (!function_exists('getLayout')) {
  function getLayout()
  {
    $user = auth()->user();

    if (!$user) {
      return 'layouts.blank'; // guest fallback
    }

    return match ($user->role) {
      'admin' => 'layouts.adminlayout',
      'sr' => 'layouts.srlayout',
      'manager' => 'layouts.managerlayout',
      'customer' => 'layouts.customerlayout',
      default => 'layouts.userlayout',
    };
  }
}
