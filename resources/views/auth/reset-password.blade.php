@extends('layouts.blank')

@section('content')
<style>
    /* Styling variables and custom animations */
    .reset-page-container {
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
        padding-right: 3rem; /* Space for the toggle button */
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

    .form-input[readonly] {
        background-color: var(--border-color);
        opacity: 0.7;
        cursor: not-allowed;
    }

    /* Toggle visibility button */
    .toggle-visibility-btn {
        position: absolute;
        right: 8px;
        height: 36px;
        width: 36px;
        border: none;
        background: transparent;
        color: var(--text-muted);
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        transition: all 0.2s;
    }

    .toggle-visibility-btn:hover {
        background-color: var(--glass);
        color: var(--text-main);
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

    /* Validation Errors */
    .validation-error-text {
        font-size: 0.825rem;
        color: #ef4444; /* Clean solid danger red instead of translucent */
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

<div class="reset-page-container">
    <!-- Orbs for aesthetic background glow -->
    <div class="ambient-glow-1"></div>
    <div class="ambient-glow-2"></div>

    <div class="auth-card">
        <!-- Brand Icon / Lock visual -->
        <div class="brand-logo-container">
            <div class="brand-icon">
                🔒
            </div>
        </div>

        <h2 class="card-title">Reset Password</h2>
        <p class="card-desc">Create a new secure password for your DMS account.</p>

        <form method="POST" action="{{ route('password.store') }}">
            @csrf

            <!-- Token -->
            <input type="hidden" name="token" value="{{ $request->route('token') }}">

            <!-- Email (Prefilled and Readonly for safety) -->
            <div class="form-group">
                <label for="email" class="form-label">Email Address</label>
                <div class="input-wrapper">
                    <input id="email" type="email" name="email" class="form-input" 
                        value="{{ old('email', $request->email) }}" required readonly autofocus autocomplete="username">
                </div>
                @if($errors->has('email'))
                    <span class="validation-error-text">⚠️ {{ $errors->first('email') }}</span>
                @endif
            </div>

            <!-- New Password -->
            <div class="form-group">
                <label for="password" class="form-label">New Password</label>
                <div class="input-wrapper">
                    <input id="password" type="password" name="password" class="form-input" required autocomplete="new-password" placeholder="••••••••">
                    <button type="button" class="toggle-visibility-btn" data-target="password" aria-label="Toggle password visibility">
                        👁️
                    </button>
                </div>
                @if($errors->has('password'))
                    <span class="validation-error-text">⚠️ {{ $errors->first('password') }}</span>
                @endif
            </div>

            <!-- Confirm Password -->
            <div class="form-group">
                <label for="password_confirmation" class="form-label">Confirm Password</label>
                <div class="input-wrapper">
                    <input id="password_confirmation" type="password" name="password_confirmation" class="form-input" required autocomplete="new-password" placeholder="••••••••">
                    <button type="button" class="toggle-visibility-btn" data-target="password_confirmation" aria-label="Toggle password visibility">
                        👁️
                    </button>
                </div>
                @if($errors->has('password_confirmation'))
                    <span class="validation-error-text">⚠️ {{ $errors->first('password_confirmation') }}</span>
                @endif
            </div>

            <!-- Submit -->
            <button type="submit" class="btn-submit">
                <span>Save New Password</span> ➔
            </button>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        // Password visibility toggles
        const toggles = document.querySelectorAll('.toggle-visibility-btn');
        toggles.forEach(toggle => {
            toggle.addEventListener('click', () => {
                const targetId = toggle.getAttribute('data-target');
                const input = document.getElementById(targetId);
                if (input) {
                    const isPassword = input.type === 'password';
                    input.type = isPassword ? 'text' : 'password';
                    toggle.textContent = isPassword ? '🙈' : '👁️';
                }
            });
        });
    });
</script>
@endsection