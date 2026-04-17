<?php

use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Manager\EmployeeController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;

Route::get('/', function () {
    return view('welcome');
})->name('home-page');


// Route::post('/notifications/read/{id}', function ($id) {
//     $notification = auth()->user()->notifications()->findOrFail($id);
//     $notification->markAsRead();
//     return response()->json(['success' => true]);
// });
Route::get('/notifications/{id}/mark-as-read', function ($id) {
    $notification = auth()->user()->notifications()->where('id', $id)->first();

    if ($notification) {
        $url = $notification->data['url']; // রিডাইরেক্ট ইউআরএল সেভ করে রাখা
        $notification->delete(); // ডাটাবেজ থেকে মুছে ফেলা
        return redirect($url); // নির্দিষ্ট পেজে পাঠিয়ে দেওয়া
    }

    return back();
})->name('notifications.markAndRedirect');


Route::get('/our/employee/{id}', [EmployeeController::class, 'see'])->name('relectric.employee');

// dashboards (protected)
Route::middleware(['auth'])->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboards');

    Route::get('/dashboard/user', function () {
        return view('pages.dashboard.user');
    })->name('dashboard.user')->middleware('role:user'); // user page - accessible by user (admin can also view if needed)

    Route::get('/dashboard/counter', function () {
        return view('pages.dashboard.counter');
    })->name('dashboard.counter_manager')->middleware('role:manager');

    Route::get('/dashboard/admin', function () {
        return view('pages.dashboard.admin');
    })->name('dashboard.admin')->middleware('role:admin');

    Route::get('/myprofile', [ProfileController::class, 'index'])->name('profile.index');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');

    // Update Profile Information
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');

    // Delete Account (optional, if you want to keep Breeze's delete)
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/settings', function () {
        return view('settings.mysettings');
    })->name('settings');

    Route::resource('customers', CustomerController::class);
});



require __DIR__ . '/auth.php';
require __DIR__ . '/manager.php';
require __DIR__ . '/admin.php';
require __DIR__ . '/manager.php';
require __DIR__ . '/sr.php';
require __DIR__ . '/channels.php';
