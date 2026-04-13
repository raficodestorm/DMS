<x-guest-layout>
    <style>
        /* Container styling to center the card */
        .reset-password-wrapper {
            background-color: var(--background);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
        }

        /* The Card Design */
        .auth-card {
            background-color: var(--section-bg);
            border: 1px solid var(--border-color);
            border-radius: 1rem;
            width: 100%;
            max-width: 450px;
            padding: 2.5rem;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05);
        }

        /* Input Styling */
        .form-label {
            color: var(--text-main);
            font-weight: 500;
            margin-bottom: 0.5rem;
        }

        .form-control {
            background-color: var(--background);
            border: 1px solid var(--border-color);
            color: var(--text-main);
            padding: 0.75rem 1rem;
            border-radius: 0.5rem;
            transition: all 0.2s ease-in-out;
        }

        .form-control:focus {
            background-color: var(--background);
            color: var(--text-main);
            border-color: var(--primary);
            box-shadow: 0 0 0 0.25rem var(--glass);
            outline: none;
        }

        /* Button Styling */
        .btn-reset {
            background-color: var(--primary);
            color: #ffffff;
            border: none;
            padding: 0.8rem;
            font-weight: 600;
            border-radius: 0.5rem;
            transition: transform 0.1s ease, background-color 0.2s ease;
        }

        .btn-reset:hover {
            background-color: var(--primary-light);
            color: #ffffff;
        }

        .btn-reset:active {
            transform: scale(0.98);
        }

        /* Error Text */
        .error-msg {
            color: var(--danger);
            font-size: 0.875rem;
            margin-top: 0.25rem;
        }

        .text-muted-custom {
            color: var(--text-muted);
        }
    </style>

    <div class="reset-password-wrapper">
        <div class="auth-card">
            <div class="text-center mb-4">
                <h2 class="h4 fw-bold" style="color: var(--text-main);">Reset Password</h2>
                <p class="text-muted-custom small">Enter your details to create a new password.</p>
            </div>

            <form method="POST" action="{{ route('password.store') }}">
                @csrf

                <input type="hidden" name="token" value="{{ $request->route('token') }}">

                <div class="mb-3">
                    <label for="email" class="form-label">{{ __('Email') }}</label>
                    <input id="email" type="email" name="email" class="form-control"
                        value="{{ old('email', $request->email) }}" required autofocus autocomplete="username">
                    <x-input-error :messages="$errors->get('email')" class="error-msg" />
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">{{ __('New Password') }}</label>
                    <input id="password" type="password" name="password" class="form-control" required
                        autocomplete="new-password">
                    <x-input-error :messages="$errors->get('password')" class="error-msg" />
                </div>

                <div class="mb-4">
                    <label for="password_confirmation" class="form-label">{{ __('Confirm Password') }}</label>
                    <input id="password_confirmation" type="password" name="password_confirmation" class="form-control"
                        required autocomplete="new-password">
                    <x-input-error :messages="$errors->get('password_confirmation')" class="error-msg" />
                </div>

                <div class="d-grid mt-4">
                    <button type="submit" class="btn btn-reset">
                        {{ __('Reset Password') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-guest-layout>