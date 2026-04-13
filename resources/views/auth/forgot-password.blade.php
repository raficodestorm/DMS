<x-guest-layout>
    <style>
        .auth-wrapper {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--background);
            padding: 20px;
        }

        .auth-card {
            width: 100%;
            max-width: 420px;
            background: var(--section-bg);
            border-radius: 16px;
            padding: 30px 25px;
            box-shadow: 0 10px 30px var(--glass);
            border: 1px solid var(--border-color);
        }

        .auth-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--text-main);
            margin-bottom: 10px;
        }

        .auth-desc {
            font-size: 0.9rem;
            color: var(--text-muted);
            margin-bottom: 20px;
            line-height: 1.5;
        }

        .auth-input {
            width: 100%;
            padding: 12px 14px;
            border-radius: 10px;
            border: 1px solid var(--border-color);
            background: transparent;
            color: var(--text-main);
            transition: 0.3s;
        }

        .auth-input:focus {
            border-color: var(--primary);
            outline: none;
            box-shadow: 0 0 0 3px var(--primary-soft);
        }

        .auth-btn {
            width: 100%;
            padding: 12px;
            border-radius: 10px;
            border: none;
            background: var(--primary);
            color: #fff;
            font-weight: 500;
            transition: 0.3s;
        }

        .auth-btn:hover {
            background: var(--primary-light);
        }

        .auth-footer {
            text-align: center;
            margin-top: 15px;
            font-size: 0.85rem;
            color: var(--text-muted);
        }

        @media (max-width: 480px) {
            .auth-card {
                padding: 20px 15px;
            }
        }
    </style>

    <div class="auth-wrapper">
        <div class="auth-card">

            <!-- Title -->
            <div class="auth-title">
                Forgot Password 🔐
            </div>

            <div class="auth-desc">
                Enter your email and we’ll send you a password reset link.
            </div>

            <!-- Session Status -->
            <x-auth-session-status class="mb-4 text-green-600" :status="session('status')" />

            <!-- Form -->
            <form method="POST" action="{{ route('password.email') }}">
                @csrf

                <!-- Email -->
                <div class="mb-3">
                    <x-input-label for="email" :value="__('Email')" />
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                        class="auth-input mt-1" placeholder="Enter your email">
                    <x-input-error :messages="$errors->get('email')" class="mt-2 text-red-500" />
                </div>

                <!-- Button -->
                <div class="mt-4">
                    <button type="submit" class="auth-btn">
                        Email Password Reset Link
                    </button>
                </div>
            </form>

            <!-- Footer -->
            <div class="auth-footer">
                Remember your password?
                <a href="{{ route('login') }}" style="color: var(--primary); font-weight: 500;">
                    Back to Login
                </a>
            </div>

        </div>
    </div>
</x-guest-layout>