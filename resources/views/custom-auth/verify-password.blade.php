@extends(getLayout())

@section('content')
<div class="auth-container">
  <div class="auth-card">
    <h2>Verify Account</h2>

    @if($errors->has('error'))
    <div class="alert-error">{{ $errors->first('error') }}</div>
    @endif

    <form method="POST" action="{{ route('password.verify') }}">
      @csrf

      <div class="input-box">
        <input type="email" name="email" required placeholder="Email">
      </div>

      <div class="input-box">
        <input type="password" name="password" required placeholder="Old Password">
      </div>

      <button type="submit" class="btn-primary">Verify</button>
    </form>
  </div>
</div>
@endsection