@extends('layouts.userlayout')
@section('content')

<style>
    :root {
        --main-color: #ff0000;
        --second-color: #780116;
        --section-bg: #fffffc;
        --muted: #6b6b6b;
        --card-shadow: 0 8px 30px rgba(15, 15, 15, 0.12);
        --radius-lg: 18px;
        --radius-md: 10px;
        font-family: Inter, ui-sans-serif, system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial;
    }

        {
        padding: 0;
    }

    .register-form-card {
        background: #fff;
        border-radius: var(--radius-lg);
        padding: 28px;
        box-shadow: var(--card-shadow);
        width: 50%;
        max-width: 700px;
        margin: 40px auto;
        animation: enter .6s cubic-bezier(.2, .9, .2, 1) both;
    }

    p.lead {
        color: var(--muted);
        margin-bottom: 18px;
    }

    .form-regi {
        display: flex;
        flex-direction: column;
        gap: 14px;
    }

    .field {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    label {
        font-size: 13px;
        color: var(--muted);
    }

    input,
    textarea {
        height: 48px;
        border-radius: var(--radius-md);
        border: 1px solid rgba(16, 16, 16, 0.06);
        padding: 12px 14px;
        font-size: 15px;
        outline: none;
        background: #fff;
        transition: box-shadow .18s, border-color .18s;
    }

    textarea {
        height: 90px;
        resize: vertical;
    }

    input:focus,
    textarea:focus {
        box-shadow: 0 6px 18px rgba(120, 1, 22, 0.06);
        border-color: rgba(120, 1, 22, 0.2);
    }

    /* .btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        height: 48px;
        padding: 0 20px;
        border-radius: 12px;
        border: none;
        cursor: pointer;
        font-weight: 600;
        font-size: 15px;
        transition: transform 0.1s ease-in-out;
    } */

    .btn:hover {
        transform: translateY(-1px);
    }

    .btn-primary {
        background: linear-gradient(90deg, var(--main-color), var(--second-color));
        color: #fff;
        box-shadow: 0 8px 22px rgba(120, 1, 22, 0.12);
    }

    .error {
        color: #b32;
        font-size: 13px;
    }

    .meta {
        margin-top: 10px;
        text-align: center;
        font-size: 14px;
    }

    .meta a {
        color: var(--main-color);
        text-decoration: none;
        font-weight: 600;
    }

    @keyframes enter {
        from {
            transform: translateY(6px);
            opacity: 0;
        }

        to {
            transform: none;
            opacity: 1;
        }
    }

    @media (max-width: 768px) {
        .register-form-card {
            padding: 20px;
            width: 100%;
            max-width: 700px;
            margin: 30px auto;
        }
    }
</style>

<div class="register-form-card">
    <h2 class="text-center">Join With Runstar</h2>
    <p class="lead text-center">Create your account to start using the dashboard</p>

    <form class="form-regi" method="POST" action="{{ route('register') }}" enctype="multipart/form-data">
        @csrf

        <div class="field">
            <label>Fullname</label>
            <input name="fullname" value="{{ old('fullname') }}" required>
            @error('fullname')<div class="error">{{ $message }}</div>@enderror
        </div>

        <div class="field">
            <label>Username</label>
            <input name="username" value="{{ old('username') }}" required>
            @error('username')<div class="error">{{ $message }}</div>@enderror
        </div>

        <div class="field">
            <label>Email</label>
            <input type="email" name="email" value="{{ old('email') }}" required>
            @error('email')<div class="error">{{ $message }}</div>@enderror
        </div>

        <div class="field">
            <label>Password</label>
            <input type="password" name="password" required>
            @error('password')<div class="error">{{ $message }}</div>@enderror
        </div>

        <div class="field">
            <label>Confirm Password</label>
            <input type="password" name="password_confirmation" required>
        </div>

        <div class="field">
            <label>Phone</label>
            <input name="phone" value="{{ old('phone') }}">
        </div>

        <div class="field">
            <label>Address</label>
            <textarea name="address">{{ old('address') }}</textarea>
        </div>

        <div class="field">
            <label>Profile Picture</label>
            <input type="file" name="profile_photo" accept="image/*">
        </div>

        <button type="submit" class="btn btn-primary">Create Account</button>

        <div class="meta">
            Already have an account?
            <a href="{{ route('login') }}">Sign in</a>
        </div>
    </form>
</div>
@endsection