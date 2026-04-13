@extends(getLayout())

@section('content')
<div class="auth-container">
  <div class="auth-card">

    <h2>Reset Password</h2>

    @if(session('success'))
    <div class="alert-success">{{ session('success') }}</div>
    @endif
    @if ($errors->any())
    <div class="alert-error">
      <ul>
        @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
    @endif

    <div class="user-info">
      <p><strong>User name:</strong> {{ auth()->user()->username }}</p>
      <p><strong>Email:</strong> {{ auth()->user()->email }}</p>
    </div>

    <form method="POST" action="{{ route('password.update') }}">
      @csrf

      <div class="input-box password-box">
        <input type="password" name="password" id="password" placeholder="New Password" required>
        <i class="fa-solid fa-eye toggle-pass" data-target="password"></i>
      </div>

      <div class="input-box password-box">
        <input type="password" name="password_confirmation" id="confirm" placeholder="Confirm Password" required>
        <i class="fa-solid fa-eye toggle-pass" data-target="confirm"></i>
      </div>

      <button class="btn-primary">Update Password</button>
    </form>
  </div>
</div>

<script>
  document.querySelectorAll('.toggle-pass').forEach(icon => {
  icon.addEventListener('click', function () {

    const input = document.getElementById(this.dataset.target);

    if (input.type === "password") {
      input.type = "text";
      this.classList.remove('fa-eye');
      this.classList.add('fa-eye-slash');
    } else {
      input.type = "password";
      this.classList.remove('fa-eye-slash');
      this.classList.add('fa-eye');
    }

  });
});
</script>
@endsection