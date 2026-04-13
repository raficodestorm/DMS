<?php

namespace App\Models;

use App\Models\Branch;
use App\Models\Employee;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'fullname',
        'username',
        'email',
        'password',
        'branch_id',
        'customer_id',
        'employee_id',
        'profile_photo_path',
        'role',
        'status',

    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    // helper
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isManager(): bool
    {
        return $this->role === 'manager';
    }

    public function isSr(): bool
    {
        return $this->role === 'sr';
    }

    public function isCustomer(): bool
    {
        return $this->role === 'customer';
    }

    public function isUser(): bool
    {
        return $this->role === 'user';
    }

    public function employees()
    {
        return $this->hasMany(Employee::class, 'added_by');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    // 🔹 SR → Orders
    public function srOrders()
    {
        return $this->hasMany(Order::class, 'sr_id');
    }

    // 🔹 Manager → Orders
    public function managerOrders()
    {
        return $this->hasMany(Order::class, 'manager_id');
    }

    // 🔹 Customer → Orders
    public function customerOrders()
    {
        return $this->hasMany(Order::class, 'customer_id');
    }
}
