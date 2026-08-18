<?php

use App\Http\Controllers\PaymentController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Manager\EmployeeController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home-page');
Route::get('/about', function () {
    return view('about');
})->name('about');
Route::get('/contact', function () {
    return view('contact');
})->name('contact');


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

Route::get('/payments/proof/{payment}', [PaymentController::class, 'publicShow'])->name('payments.show.public');

// dashboards (protected)
Route::middleware(['auth'])->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboards');

    Route::get('/dashboard/user', [DashboardController::class, 'index'])->name('dashboard.user')->middleware('role:user');

    Route::get('/dashboard/counter', [DashboardController::class, 'index'])->name('dashboard.counter_manager')->middleware('role:manager');

    Route::get('/dashboard/admin', [DashboardController::class, 'index'])->name('dashboard.admin')->middleware('role:admin');

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

    Route::get('/payments/{payment}/slip', [PaymentController::class, 'viewSlip'])->name('payments.slip');

    Route::get('/notifications/poll', function () {
        $user = auth()->user();
        if (!$user) {
            return response()->json([], 401);
        }

        $notifications = $user->unreadNotifications->map(function ($notification) {
            $message = $notification->data['message'] ?? '';
            $messageHtml = '';
            if (is_array($message)) {
                $text = $message['text'] ?? '';
                $from = $message['from'] ?? '';
                $messageHtml = e($text);
                if ($from) {
                    $messageHtml .= ' <span class="text-primary fw-bold">' . e($from) . '</span>';
                }
            } else {
                $messageHtml = e($message);
            }

            return [
                'id' => $notification->id,
                'title' => $notification->data['title'] ?? 'System Alert',
                'message_html' => $messageHtml,
                'url' => route('notifications.markAndRedirect', $notification->id),
                'created_at' => $notification->created_at->toIso8601String(),
                'diffForHumans' => $notification->created_at->diffForHumans(),
            ];
        });

        return response()->json($notifications);
    })->name('notifications.poll');
});



require __DIR__ . '/auth.php';
require __DIR__ . '/manager.php';
require __DIR__ . '/admin.php';
require __DIR__ . '/sr.php';
require __DIR__ . '/customer.php';
require __DIR__ . '/channels.php';
