<?php

use Illuminate\Support\Facades\Route;


Route::prefix('customer')->name('customer.')->middleware(['auth', 'role:customer'])->group(function () {});
