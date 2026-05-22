<!-- Session Status -->
<x-auth-session-status class="mb-4" :status="session('status')" />

<style>
    .login-modal-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100vw;
        height: 100vh;
        background: rgba(15, 23, 42, 0.6);
        /* Slate color overlay */
        backdrop-filter: blur(8px);
        -webkit-backdrop-filter: blur(8px);
        z-index: 100000;
        /* Extremely high z-index to show above everything */
        display: none;
        /* starts with display none */
        justify-content: center;
        align-items: center;
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .login-modal-overlay.show {
        display: flex;
        opacity: 1;
    }

    .login-modal-container {
        width: 100%;
        max-width: 550px;
        padding: 20px;
        position: relative;
        transform: translateY(-30px);
        transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
    }

    .login-modal-overlay.show .login-modal-container {
        transform: translateY(0);
    }

    .login-modal-close {
        position: absolute;
        top: 15px;
        right: 15px;
        background: transparent;
        border: none;
        font-size: 26px;
        font-weight: bold;
        color: var(--text-muted);
        cursor: pointer;
        transition: all 0.2s ease;
        z-index: 10;
        line-height: 1;
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
    }

    .login-modal-close:hover {
        color: var(--primary);
        background: rgba(0, 0, 0, 0.05);
        transform: rotate(90deg);
    }

    .login-form-card-modal {
        background: var(--section-bg);
        border-radius: 15px;
        padding: 28px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.12);
        width: 100%;
        position: relative;
    }

    .login-form-card-modal h2 {
        margin: 0 0 6px 0;
        font-size: 38px;
        font-weight: bold;
        color: var(--primary);
    }

    .login-form-card-modal p.lead {
        margin: 0 0 18px 0;
        font-size: 15px;
        color: var(--text-muted);
    }

    #loginFormModal {
        display: flex;
        flex-direction: column;
        gap: 14px;
    }

    .field {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .label-log {
        font-size: 13px;
        color: var(--text-muted);
    }

    .input-log {
        height: 48px;
        border-radius: 10px;
        border: .5px solid var(--primary-light);
        padding: 12px 14px;
        font-size: 15px;
        outline: none;
        transition: box-shadow .18s, border-color .18s, transform .06s;
        background: var(--background);
    }

    .input-log:focus {
        box-shadow: 0 6px 18px rgba(1, 84, 120, 0.06);
        border-color: var(--primary-light);
    }

    .actions {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
    }

    .remember {
        display: flex;
        gap: 8px;
        align-items: center;
        font-size: 14px;
        color: var(--text-muted);
    }

    .btn-submit {
        background: linear-gradient(90deg, var(--primary), var(--accent));
        color: white;
        font-weight: 600;
        border: none;
        border-radius: 10px;
        padding: 0.7rem;
        width: 100%;
        margin-top: 0.8rem;
        cursor: pointer;
        transition: all 0.3s ease;
        font-size: 1rem;
        letter-spacing: 1px;
        box-shadow: 0 4px 15px rgba(49, 49, 255, 0.3);
    }

    .btn-submit:hover {
        transform: translateY(-2px);
        background: linear-gradient(90deg, var(--accent), var(--primary-light));
    }

    .btn-submit:active {
        transform: translateY(1px);
        background: #39aff8;
        border: 1px solid #e81efa;
    }

    .btn-outline {
        background: transparent;
        border: 1px solid rgba(16, 16, 16, 0.06);
    }

    .divider-log {
        display: flex;
        align-items: center;
        gap: 12px;
        margin: 6px 0;
    }

    .divider-log span {
        flex: 1;
        height: 1px;
        background: rgba(16, 16, 16, 0.04);
    }

    .divider-log small {
        color: var(--text-muted);
        font-size: 13px;
        padding: 0 6px;
    }

    .socials-log {
        display: flex;
        gap: 8px;
    }

    .socials-log button {
        flex: 1;
        height: 44px;
    }

    .meta {
        display: flex;
        justify-content: space-between;
        gap: 10px;
        margin-top: 8px;
        font-size: 14px;
    }

    .meta a {
        color: var(--primary);
        text-decoration: none;
        font-weight: 600;
    }

    .note {
        font-size: 13px;
        color: var(--text-muted);
    }

    /* Small screens adjustments */
    @media (max-width: 767px) {
        .login-modal-container {
            padding: 15px;
            width: 80%;
            height: 80vh;
        }

        .login-form-card-modal {
            padding: 20px;
            border-radius: 12px;
        }

        .login-form-card-modal h2 {
            font-size: 30px;
        }

        .input-log {
            height: 44px;
            font-size: 14px;
        }

        .actions {
            flex-direction: column;
            align-items: flex-start;
            gap: 8px;
        }

        .socials-log {
            flex-direction: column;
        }

        .meta {
            flex-direction: column;
            gap: 8px;
        }
    }
</style>

<div id="loginModal" class="login-modal-overlay" onclick="handleOutsideClick(event)">
    <div class="login-modal-container">
        <div class="login-form-card-modal" aria-labelledby="login-modal-title">
            <button class="login-modal-close" onclick="closeLoginModal()" aria-label="Close modal">&times;</button>
            <div style="display:flex; align-items:center; justify-content:space-between; gap:12px">
                <div>
                    <h2 id="login-modal-title">Welcome back</h2>
                    <p class="lead">Sign in to continue to your dashboard</p>
                </div>
                <div style="text-align:right">
                    <small class="note">Not a member?</small>
                </div>
            </div>

            <form id="loginFormModal" method="POST" action="{{ route('login') }}">
                @csrf
                <div class="field">
                    <label for="modal-login" class="label-log">Username or Email</label>
                    <input class="input-log" id="modal-login" name="login" value="{{ old('login') }}" required
                        autocomplete="username" />
                    @error('login')<div class="text-danger" style="font-size: 13px; color: #dc3545; margin-top: 4px;">{{
                        $message }}</div>@enderror
                </div>

                <div class="field">
                    <label for="modal-password" class="label-log">Password</label>
                    <div style="position:relative; display:flex; align-items:center">
                        <input class="input-log" style="width: 100%;" id="modal-password" type="password"
                            name="password" placeholder="••••••••" required autocomplete="current-password" />
                        <button type="button" id="togglePwdModal" aria-label="Show password" title="Show password"
                            style="position:absolute; right:8px; height:34px; padding:0 8px; border-radius:8px; border:none; background:transparent; cursor:pointer">👁️</button>
                    </div>
                    @error('password')<div class="text-danger"
                        style="font-size: 13px; color: #dc3545; margin-top: 4px;">{{ $message }}</div>@enderror
                </div>

                <div class="actions">
                    <label class="remember label-log"><input type="checkbox" id="remember-modal" name="remember" />
                        Remember me</label>
                </div>

                <button class="btn btn-submit" type="submit">Sign in</button>
                <div class="actions">
                    <a href="{{ route('password.request') }}" class="note">Forgot password?</a>
                </div>

                <div class="divider-log"><span></span><small>or continue with</small><span></span></div>

                <div class="socials-log">
                    <button type="button" class="btn btn-outline" aria-label="Sign in with Google">Google</button>
                    <button type="button" class="btn btn-outline" aria-label="Sign in with Apple">Apple</button>
                </div>

                <div class="meta">
                    <div class="note">By signing in you accept our <a href="#">Terms</a></div>
                    <div style="text-align:right"><a href="#">Need help?</a></div>
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
            // Trigger reflow for transition
            modal.offsetHeight;
            modal.classList.add('show');
            document.body.style.overflow = 'hidden'; // Prevent scrolling
            
            // Focus the username field
            const usernameField = document.getElementById('modal-login');
            if (usernameField) {
                setTimeout(() => usernameField.focus(), 100);
            }
        }
    }

    function closeLoginModal() {
        const modal = document.getElementById('loginModal');
        if (modal) {
            modal.classList.remove('show');
            document.body.style.overflow = ''; // Restore scrolling
            setTimeout(() => {
                modal.style.display = 'none';
            }, 300); // match transition speed
        }
    }

    function handleOutsideClick(event) {
        const container = document.querySelector('.login-modal-container');
        if (container && !container.contains(event.target)) {
            closeLoginModal();
        }
    }

    // Toggle password visibility
    (function(){
        const pwd = document.getElementById('modal-password');
        const toggle = document.getElementById('togglePwdModal');
        if (pwd && toggle) {
            toggle.addEventListener('click', ()=>{
                const type = pwd.type === 'password' ? 'text' : 'password';
                pwd.type = type;
                toggle.textContent = type === 'password' ? '👁️' : '🙈';
                toggle.setAttribute('aria-label', type === 'password' ? 'Show password' : 'Hide password');
            });
        }
    })();

    // Auto open if login page requested or validation errors are present
    document.addEventListener("DOMContentLoaded", function() {
        const urlParams = new URLSearchParams(window.location.search);
        
        // Check for error elements or query parameters
        const hasErrors = {{ $errors->has('login') || $errors->has('password') ? 'true' : 'false' }};
        const showLoginParam = urlParams.get('login') === 'show' || urlParams.get('show_login') === '1';

        if (hasErrors || showLoginParam) {
            openLoginModal();
        }
    });
</script>