@if(session('success'))
<div class="custom-alert success-alert">
  <i class="fas fa-check-circle"></i>
  <span>{{ session('success') }}</span>
</div>
@endif

@if(session('error'))
<div class="custom-alert error-alert">
  <i class="fas fa-times-circle"></i>
  <span>{{ session('error') }}</span>
</div>
@endif

@if($errors->any())
<div class="custom-alert error-alert">
  <i class="fas fa-exclamation-triangle"></i>
  <div>
    @foreach($errors->all() as $error)
    <div>{{ $error }}</div>
    @endforeach
  </div>
</div>
@endif


<style>
  .custom-alert {
    display: flex;
    align-items: flex-start;
    gap: 12px;
    padding: 14px 18px;
    border-radius: 12px;
    margin-bottom: 16px;
    font-size: 14px;
    font-weight: 500;
    border: 1px solid;
    animation: fadeIn .4s ease;
  }

  .custom-alert i {
    font-size: 18px;
    margin-top: 2px;
  }

  .success-alert {
    background: #01ff8837;
    color: #065f46;
    border-color: #01b4606d;
  }

  .error-alert {
    background: #ff101035;
    color: #991b1b;
    border-color: #fe040463;
  }

  @keyframes fadeIn {
    from {
      opacity: 0;
      transform: translateY(-8px);
    }

    to {
      opacity: 1;
      transform: translateY(0);
    }
  }
</style>