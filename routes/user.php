<?php

use Illuminate\Support\Facades\Route;


Route::prefix('user')->name('user.')->middleware(['auth', 'role:user'])->group(function () {});
