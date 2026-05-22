@extends('layouts.blank')

@section('content')
<style>
    /* Styling variables and custom animations */
    .forgot-page-container {
        position: relative;
        min-height: 100vh;
        width: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: var(--background);
        overflow: hidden;
        padding: 2rem 1rem;
    }

    /* Ambient glowing background circles for premium feel */
    .ambient-glow-1 {
        position: absolute;
        top: -10%;
        left: -10%;
        width: 40vw;
        height: 40vw;
        background: radial-gradient(circle, rgba(49, 49, 255, 0.15) 0%, rgba(0, 0, 0, 0) 70%);
        border-radius: 50%;
        filter: blur(80px);
        pointer-events: none;
        z-index: 1;
    }

    .ambient-glow-2 {
        position: absolute;
        bottom: -10%;
        right: -10%;
        width: 45vw;
        height: 45vw;
        background: radial-gradient(circle, rgba(174, 4, 241, 0.12) 0%, rgba(0, 0, 0, 0) 70%);
        border-radius: 50%;
        filter: blur(100px);
        pointer-events: none;
        z-index: 1;
    }

    /* Glassmorphic card design */
    .auth-card {
        position: relative;
        width: 100%;
        max-width: 480px;
        background-color: var(--section-bg);
        border: 1px solid var(--border-color);
        box-shadow: 0 25px 50px -12px var(--glass);
        backdrop-filter: blur(16px);
        -webkit-backdrop-filter: blur(16px);
        border-radius: 24px;
        padding: 3rem 2.5rem;
        z-index: 10;
        transform: translateY(20px);
        opacity: 0;
        animation: cardAppear 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards;
    }

    @keyframes cardAppear {
        to {
            transform: translateY(0);
            opacity: 1;
        }
    }

    /* Typography & Header */
    .brand-logo-container {
        display: flex;
        justify-content: center;
        margin-bottom: 1.5rem;
    }

    .brand-icon {
        width: 54px;
        height: 54px;
        background: linear-gradient(135deg, var(--primary), var(--accent));
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #ffffff;
        font-size: 24px;
        font-weight: bold;
        box-shadow: 0 8px 20px rgba(49, 49, 255, 0.2);
    }

    .card-title {
        font-size: 1.75rem;
        font-weight: 700;
        color: var(--text-main);
        text-align: center;
        margin-bottom: 0.5rem;
        letter-spacing: -0.5px;
    }

    .card-desc {
        color: var(--text-muted);
        font-size: 0.925rem;
        text-align: center;
        margin-bottom: 2rem;
        line-height: 1.5;
    }

    /* Form Fields */
    .form-group {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
        margin-bottom: 1.5rem;
    }

    .form-label {
        font-size: 0.85rem;
        font-weight: 600;
        color: var(--text-muted);
        letter-spacing: 0.3px;
        text-transform: uppercase;
    }

    .input-wrapper {
        position: relative;
        display: flex;
        align-items: center;
    }

    .form-input {
        width: 100%;
        height: 50px;
        background-color: var(--background);
        border: 1px solid var(--border-color);
        color: var(--text-main);
        padding: 0 1rem;
        border-radius: 12px;
        font-size: 0.95rem;
        font-family: inherit;
        outline: none;
        transition: all 0.25s ease;
    }

    .form-input:focus {
        border-color: var(--primary-light);
        box-shadow: 0 0 0 4px var(--primary-soft);
    }

    /* Submit Button */
    .btn-submit {
        width: 100%;
        height: 52px;
        background: linear-gradient(90deg, var(--primary), var(--accent));
        color: #ffffff;
        font-size: 1rem;
        font-weight: 600;
        border: none;
        border-radius: 12px;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(49, 49, 255, 0.2);
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        margin-top: 1rem;
    }

    .btn-submit:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(49, 49, 255, 0.3);
        filter: brightness(1.05);
    }

    .btn-submit:active {
        transform: translateY(1px);
    }

    /* Footer / Bottom Actions */
    .auth-footer {
        text-align: center;
        margin-top: 2rem;
        font-size: 0.9rem;
        color: var(--text-muted);
    }

    .auth-footer a {
        color: var(--primary);
        text-decoration: none;
        font-weight: 600;
        transition: color 0.2s;
    }

    .auth-footer a:hover {
        color: var(--primary-light);
        text-decoration: underline;
    }

    /* Validation & Status Alert */
    .status-alert {
        background-color: rgba(34, 197, 94, 0.12);
        border: 1px solid rgba(34, 197, 94, 0.5);
        color: #16a34a;
        padding: 1rem;
        border-radius: 12px;
        font-size: 0.9rem;
        margin-bottom: 1.5rem;
        display: flex;
        align-items: center;
        gap: 8px;
        font-weight: 500;
    }

    .validation-error-text {
        font-size: 0.825rem;
        color: #ef4444;
        font-weight: 500;
        margin-top: 0.25rem;
        display: flex;
        align-items: center;
        gap: 4px;
    }

    /* Responsive adjustments */
    @media (max-width: 480px) {
        .auth-card {
            padding: 2.25rem 1.5rem;
            border-radius: 20px;
        }

        .card-title {
            font-size: 1.5rem;
        }

        .form-input {
            height: 46px;
        }

        .btn-submit {
            height: 48px;
        }
    }
</style>

<div class="forgot-page-container">
    <!-- Orbs for aesthetic background glow -->
    <div class="ambient-glow-1"></div>
    <div class="ambient-glow-2"></div>

    <div class="auth-card">
        <!-- Brand Icon / Key visual -->
        <div class="brand-logo-container">
            <div class="brand-icon">
                🔑
            </div>
        </div>

        <h2 class="card-title">Forgot Password?</h2>
        <p class="card-desc">No worries! Enter your registered email address below and we'll send you a link to reset your password.</p>

        <!-- Session Status Alert -->
        @if (session('status'))
            <div class="status-alert">
                <span>📧</span>
                <div>{{ session('status') }}</div>
            </div>
        @endif

        <form method="POST" action="{{ route('password.email') }}">
            @csrf

            <!-- Email -->
            <div class="form-group">
                <label for="email" class="form-label">Email Address</label>
                <div class="input-wrapper">
                    <input id="email" type="email" name="email" class="form-input" 
                        value="{{ old('email') }}" required autofocus placeholder="name@example.com" autocomplete="email">
                </div>
                @if($errors->has('email'))
                    <span class="validation-error-text">⚠️ {{ $errors->first('email') }}</span>
                @endif
            </div>

            <!-- Submit -->
            <button type="submit" class="btn-submit">
                <span>Send Reset Link</span> ➔
            </button>
        </form>

        <!-- Footer -->
        <div class="auth-footer">
            Remembered your password? 
            <a href="{{ route('login') }}">Back to Login</a>
        </div>
    </div>
</div>
@endsection