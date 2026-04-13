@extends('layouts.userlayout')
<!-- Session Status -->
<x-auth-session-status class="mb-4" :status="session('status')" />
@section('content')

<style>
    .login-main {
        width: 100%;
        height: 100vh;
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .login-form-card {
        background: var(--section-bg);
        border-radius: 15px;
        padding: 28px;
        box-shadow: var(--text-muted);
        width: 40%;
    }

    .login-form-card h2 {
        margin: 0 0 6px 0;
        font-size: 38px;
        font-weight: bold;
        color: var(--primary)
    }

    .login-form-card p.lead {
        margin: 0 0 18px 0;
        font-size: 15px;
        color: var(--text-muted)
    }

    #loginForm {
        display: flex;
        flex-direction: column;
        gap: 14px
    }

    .field {
        display: flex;
        flex-direction: column;
        gap: 8px
    }

    .label-log {
        font-size: 13px;
        color: var(--text-muted)
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
        gap: 10px
    }

    .remember {
        display: flex;
        gap: 8px;
        align-items: center;
        font-size: 14px;
        color: var(--text-muted)
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

    .alt-actions {
        display: flex;
        gap: 10px;
        margin-top: 6px
    }

    .divider-log {
        display: flex;
        align-items: center;
        gap: 12px;
        margin: 6px 0
    }

    .divider-log span {
        flex: 1;
        height: 1px;
        background: rgba(16, 16, 16, 0.04)
    }

    .divider-log small {
        color: var(--text-muted);
        font-size: 13px;
        padding: 0 6px
    }

    .socials-log {
        display: flex;
        gap: 8px
    }

    .socials-log button {
        flex: 1;
        height: 44px
    }

    .meta {
        display: flex;
        justify-content: space-between;
        gap: 10px;
        margin-top: 8px;
        font-size: 14px
    }

    .meta a {
        color: var(--primary);
        text-decoration: none;
        font-weight: 600
    }

    .note {
        font-size: 13px;
        color: var(--text-muted)
    }

    /* small screen adjustments */


    @media (max-width:420px) {
        .benefits {
            grid-template-columns: 1fr
        }

        .logo {
            width: 56px;
            height: 56px
        }
    }

    /* subtle entrance animation */
    .enter {
        transform: translateY(6px);
        opacity: 0;
        animation: enter .6s cubic-bezier(.2, .9, .2, 1) both
    }

    @keyframes enter {
        to {
            transform: none;
            opacity: 1
        }
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .login-form-card {
            width: 90%;
            padding: 22px;
        }

        .login-form-card p.lead {
            margin: 0 0 18px 0;
            font-size: 15px
        }
    }

    @media (max-width: 480px) {
        .login-main {
            padding: 16px;
            height: auto;
            align-items: flex-start;
        }

        .login-form-card {
            width: 100%;
            padding: 18px;
            border-radius: 14px;
            margin-top: 40px;
        }

        .login-form-card h2 {
            font-size: 32px;
        }

        .login-form-card p.lead {
            font-size: 14px;
        }

        .input-log {
            height: 44px;
            font-size: 14px;
        }

        .btn-primary {
            height: 46px;
            font-size: 15px;
        }

        .actions {
            flex-direction: column;
            align-items: flex-start;
            gap: 6px;
        }

        .socials-log {
            flex-direction: column;
        }

        .socials-log button {
            width: 100%;
        }

        .meta {
            flex-direction: column;
            gap: 8px;
            text-align: left;
        }
    }
</style>
<div class="login-main">
    <div class="mt-5 login-form-card enter" aria-labelledby="login-title">
        <div style="display:flex; align-items:center; justify-content:space-between; gap:12px">
            <div>
                <h2 id="login-title">Welcome back</h2>
                <p class="lead">Sign in to continue to your dashboard</p>
            </div>
            <div style="text-align:right">
                <small class="note">Not a member?</small>
                {{-- <div><a href="{{ route('register') }}" class="note"
                        style="font-weight:700; color:var(--accent); text-decoration:none">Create
                        account</a></div> --}}
            </div>
        </div>

        <form id="loginForm" method="POST" action="{{ route('login') }}">
            @csrf
            <div class="field">
                <label for="email" class="label-log">Userename or Email</label>
                <input class="input-log" name="login" value="{{ old('login') }}" required />
                @error('login')<div class="text-danger">{{ $message }}</div>@enderror
            </div>

            <div class="field">
                <label for="password" class="label-log">Password</label>
                <div style="position:relative; display:flex; align-items:center">
                    <input class="input-log" id="password" type="password" name="password" placeholder="••••••••"
                        required />
                    <button type="button" id="togglePwd" aria-label="Show password" title="Show password"
                        style="position:absolute; right:8px; height:34px; padding:0 8px; border-radius:8px; border:none; background:transparent; cursor:pointer">👁️</button>
                </div>
                @error('password')<div>{{ $message }}</div>@enderror
            </div>

            <div class="actions">
                <label class="remember label-log"><input type="checkbox" id="remember" /> Remember me</label>

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
    (function(){
      const pwd = document.getElementById('password');
      const toggle = document.getElementById('togglePwd');

      toggle.addEventListener('click', ()=>{
        const type = pwd.type === 'password' ? 'text' : 'password';
        pwd.type = type;
        toggle.textContent = type === 'password' ? '👁️' : '🙈';
        toggle.setAttribute('aria-label', type === 'password' ? 'Show password' : 'Hide password');
      });
    })();
</script>
@endsection