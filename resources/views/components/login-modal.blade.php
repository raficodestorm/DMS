<!-- Session Status -->
<x-auth-session-status class="mb-4" :status="session('status')" />

<style>
    .login-modal-overlay {
        position: fixed;
        inset: 0;
        width: 100vw;
        height: 100vh;
        background: rgba(10, 15, 30, 0.65);
        backdrop-filter: blur(8px);
        -webkit-backdrop-filter: blur(8px);
        z-index: 100000;
        display: none;
        justify-content: center;
        align-items: center;
        padding: 16px;
        opacity: 0;
        transition: opacity 0.25s ease;
    }

    .login-modal-overlay.show {
        display: flex;
        opacity: 1;
    }

    .login-modal-container {
        width: 100%;
        max-width: 450px;
        position: relative;
        transform: scale(0.95);
        transition: transform 0.25s cubic-bezier(0.16, 1, 0.3, 1);
    }

    .login-modal-overlay.show .login-modal-container {
        transform: scale(1);
    }

    .login-form-card-modal {
        background: var(--section-bg);
        border: 1px solid var(--border-color);
        border-radius: 20px;
        padding: 30px 26px 24px;
        box-shadow: 0 20px 45px rgba(0, 0, 0, 0.25);
        width: 100%;
        position: relative;
    }

    .login-modal-close {
        position: absolute;
        top: 16px;
        right: 16px;
        background: var(--primary-soft);
        border: none;
        color: var(--text-muted);
        cursor: pointer;
        width: 30px;
        height: 30px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        font-size: 13px;
        transition: all 0.2s ease;
    }

    .login-modal-close:hover {
        color: #ef4444;
        background: #fee2e2;
        transform: rotate(90deg);
    }

    .login-modal-header {
        text-align: center;
        margin-bottom: 20px;
    }

    .login-modal-header h2 {
        margin: 0;
        font-size: 30px;
        font-weight: 800;
        color: var(--text-main);
        letter-spacing: -0.3px;
    }

    .login-modal-header p {
        margin: 4px 0 0;
        font-size: 13px;
        color: var(--text-muted);
    }

    #loginFormModal {
        display: flex;
        flex-direction: column;
        gap: 14px;
    }

    .modal-field {
        display: flex;
        flex-direction: column;
        gap: 5px;
    }

    .modal-label {
        font-size: 12.5px;
        font-weight: 600;
        color: var(--text-main);
    }

    .modal-input-wrap {
        position: relative;
        display: flex;
        align-items: center;
    }

    .modal-input-wrap .input-icon-left {
        position: absolute;
        left: 14px;
        color: var(--text-muted);
        font-size: 14px;
        pointer-events: none;
    }

    .modal-input {
        width: 100%;
        height: 45px;
        background: var(--background);
        border: 1.5px solid var(--border-color);
        border-radius: 11px;
        padding: 10px 14px 10px 38px;
        font-size: 14px;
        color: var(--text-main);
        outline: none;
        transition: all 0.2s ease;
        font-family: inherit;
    }

    .modal-input:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 3px var(--primary-soft);
        background: var(--section-bg);
    }

    .modal-toggle-pwd {
        position: absolute;
        right: 12px;
        background: none;
        border: none;
        color: var(--text-muted);
        cursor: pointer;
        padding: 5px;
        font-size: 14px;
    }

    .modal-toggle-pwd:hover {
        color: var(--primary);
    }

    .modal-actions-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        font-size: 12.5px;
        margin-top: 1px;
    }

    .modal-remember {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        color: var(--text-muted);
        cursor: pointer;
    }

    .modal-remember input[type="checkbox"] {
        accent-color: var(--primary);
        cursor: pointer;
    }

    .modal-forgot-link {
        font-size: 12.5px;
        font-weight: 600;
        color: var(--primary);
        text-decoration: none;
    }

    .modal-forgot-link:hover {
        color: var(--accent);
        text-decoration: underline;
    }

    .btn-modal-submit {
        background: linear-gradient(135deg, var(--primary), var(--accent));
        color: #ffffff;
        font-weight: 700;
        border: none;
        border-radius: 11px;
        height: 46px;
        width: 100%;
        margin-top: 2px;
        cursor: pointer;
        transition: all 0.2s ease;
        font-size: 14.5px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        box-shadow: 0 4px 14px rgba(2, 2, 226, 0.2);
    }

    .btn-modal-submit:hover {
        transform: translateY(-1px);
        box-shadow: 0 6px 18px rgba(2, 2, 226, 0.3);
    }

    .modal-divider {
        display: flex;
        align-items: center;
        gap: 10px;
        margin: 4px 0;
    }

    .modal-divider span {
        flex: 1;
        height: 1px;
        background: var(--border-color);
    }

    .modal-divider small {
        color: var(--text-muted);
        font-size: 11.5px;
        text-transform: uppercase;
    }

    .modal-social-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 10px;
    }

    .btn-modal-social {
        background: var(--background);
        border: 1px solid var(--border-color);
        border-radius: 10px;
        height: 39px;
        color: var(--text-main);
        font-weight: 600;
        font-size: 12.5px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 7px;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .btn-modal-social:hover {
        background: var(--primary-soft);
        color: var(--primary);
        border-color: var(--primary-light);
    }

    .modal-footer-nav {
        text-align: center;
        font-size: 13px;
        color: var(--text-muted);
        padding-top: 12px;
        margin-top: 2px;
        border-top: 1px dashed var(--border-color);
    }

    .modal-footer-nav a {
        color: var(--accent);
        font-weight: 700;
        text-decoration: none;
        margin-left: 3px;
    }

    .modal-footer-nav a:hover {
        color: var(--primary);
        text-decoration: underline;
    }

    .modal-error-msg {
        font-size: 11.5px;
        color: #ef4444;
        font-weight: 500;
    }

    /* Mobile Screen Optimization */
    @media (max-width: 480px) {
        .login-modal-overlay {
            padding: 12px;
        }

        .login-modal-container {
            max-width: 100%;
        }

        .login-form-card-modal {
            padding: 20px 16px 16px;
            border-radius: 16px;
        }

        .login-modal-header h2 {
            font-size: 19px;
        }

        .modal-input {
            height: 38px;
            font-size: 13px;
        }

        .btn-modal-submit {
            height: 40px;
            font-size: 13.5px;
        }

        .btn-modal-social {
            height: 34px;
            font-size: 11.5px;
        }
    }
</style>

<div id="loginModal" class="login-modal-overlay" onclick="handleOutsideClick(event)">
    <div class="login-modal-container">
        <div class="login-form-card-modal" aria-labelledby="login-modal-title">
            <button class="login-modal-close" onclick="closeLoginModal()" aria-label="Close modal">
                <i class="fas fa-times"></i>
            </button>

            <div class="login-modal-header">
                <h2 id="login-modal-title">Welcome Back</h2>
                <p>Sign in to continue to your dashboard</p>
            </div>

            <form id="loginFormModal" method="POST" action="{{ route('login') }}">
                @csrf

                {{-- Username / Email --}}
                <div class="modal-field">
                    <label for="modal-login" class="modal-label">Username or Email</label>
                    <div class="modal-input-wrap">
                        <input class="modal-input @error('login') is-invalid @enderror" id="modal-login" name="login" value="{{ old('login') }}" placeholder="Username or email" required autocomplete="username" />
                        <span class="input-icon-left"><i class="far fa-envelope"></i></span>
                    </div>
                    @error('login')
                        <span class="modal-error-msg"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span>
                    @enderror
                </div>

                {{-- Password --}}
                <div class="modal-field">
                    <label for="modal-password" class="modal-label">Password</label>
                    <div class="modal-input-wrap">
                        <input class="modal-input @error('password') is-invalid @enderror" id="modal-password" type="password" name="password" placeholder="••••••••" required autocomplete="current-password" />
                        <span class="input-icon-left"><i class="fas fa-lock"></i></span>
                        <button type="button" class="modal-toggle-pwd" id="togglePwdModal" aria-label="Toggle password visibility" title="Show password">
                            <i class="far fa-eye"></i>
                        </button>
                    </div>
                    @error('password')
                        <span class="modal-error-msg"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span>
                    @enderror
                </div>

                {{-- Remember & Forgot --}}
                <div class="modal-actions-row">
                    <label class="modal-remember">
                        <input type="checkbox" id="remember-modal" name="remember" />
                        <span>Remember me</span>
                    </label>
                    <a href="{{ route('password.request') }}" class="modal-forgot-link">Forgot password?</a>
                </div>

                {{-- Submit Button --}}
                <button class="btn-modal-submit" type="submit">
                    <i class="fas fa-arrow-right-to-bracket"></i> Sign In
                </button>

                {{-- Social Divider --}}
                <div class="modal-divider">
                    <span></span>
                    <small>Or continue with</small>
                    <span></span>
                </div>

                {{-- Social Buttons --}}
                <div class="modal-social-grid">
                    <button type="button" class="btn-modal-social" aria-label="Sign in with Google">
                        <i class="fab fa-google" style="color: #ea4335;"></i> Google
                    </button>
                    <button type="button" class="btn-modal-social" aria-label="Sign in with Apple">
                        <i class="fab fa-apple"></i> Apple
                    </button>
                </div>

                {{-- Footer Register Nav --}}
                <div class="modal-footer-nav">
                    Don't have an account?
                    <a href="{{ route('register') }}">Create Account</a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function openLoginModal(event) {
        if (event) event.preventDefault();
        const modal = document.getElementById('loginModal');
        if (modal) {
            modal.style.display = 'flex';
            modal.offsetHeight;
            modal.classList.add('show');
            document.body.style.overflow = 'hidden';

            const usernameField = document.getElementById('modal-login');
            if (usernameField) {
                setTimeout(() => usernameField.focus(), 150);
            }
        }
    }

    function closeLoginModal() {
        const modal = document.getElementById('loginModal');
        if (modal) {
            modal.classList.remove('show');
            document.body.style.overflow = '';
            setTimeout(() => {
                modal.style.display = 'none';
            }, 250);
        }
    }

    function handleOutsideClick(event) {
        const container = document.querySelector('.login-modal-container');
        if (container && !container.contains(event.target)) {
            closeLoginModal();
        }
    }

    (function(){
        const pwd = document.getElementById('modal-password');
        const toggle = document.getElementById('togglePwdModal');
        if (pwd && toggle) {
            toggle.addEventListener('click', ()=>{
                const isPwd = pwd.type === 'password';
                pwd.type = isPwd ? 'text' : 'password';
                const icon = toggle.querySelector('i');
                if (icon) {
                    icon.className = isPwd ? 'far fa-eye-slash' : 'far fa-eye';
                }
                toggle.setAttribute('aria-label', isPwd ? 'Hide password' : 'Show password');
            });
        }
    })();

    document.addEventListener("DOMContentLoaded", function() {
        const urlParams = new URLSearchParams(window.location.search);
        const hasErrors = {{ $errors->has('login') || $errors->has('password') ? 'true' : 'false' }};
        const showLoginParam = urlParams.get('login') === 'show' || urlParams.get('show_login') === '1';

        if (hasErrors || showLoginParam) {
            openLoginModal();
        }
    });
</script>