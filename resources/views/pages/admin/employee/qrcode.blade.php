@extends('layouts.adminlayout')

@section('content')

<style>
  /* Card */
  .show-card {
    max-width: 420px;
    margin: 30px auto;
    background: var(--section-bg);
    border-radius: 16px;
    padding: 20px;
    text-align: center;
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.05);
  }

  /* Name */
  .show-name {
    font-size: 22px;
    font-weight: 700;
    color: #6e0178;
    margin-bottom: 10px;
  }



  /* QR */
  .qr-placeholder {
    margin: 20px 0;
  }

  /* Download Button */
  .download-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: var(--primary);
    color: #fff;
    padding: 8px 14px;
    border-radius: 8px;
    font-size: 14px;
    text-decoration: none;
    transition: 0.2s ease;
  }

  .download-btn:hover {
    background: var(--accent);
    transform: translateY(-2px);
  }

  .download-btn:active {
    transform: scale(0.95);
  }

  /* Statement */
  .statement-text {
    font-size: 13px;
    color: #444;
    margin-top: 15px;
  }

  /* Mobile */
  @media (max-width: 480px) {
    .show-card {
      margin: 15px;
      padding: 15px;
    }
  }
</style>

<div class="show-card">
  @include('components.alert')
  <div class="content-area">
    <h1 class="show-name">{{ $employee->name }}</h1>

    <div class="rank-pill">BRE100{{ $employee->id }}</div>
    <div class="rank-pill">{{ $employee->rank }}</div>

    <div class="qr-placeholder">
      {!! QrCode::size(150)->generate(url('/our/employee/' . $employee->id)) !!}
    </div>

    <!-- ✅ Download Button -->
    <a href="{{ route('admin.employees.qr.download', $employee) }}" class="download-btn">
      <i class="fa-solid fa-download"></i> Download QR
    </a>

    <div class="statement">
      <p class="statement-text">
        "This pass certifies that <strong>{{ $employee->name }}</strong> is a verified professional of
        <strong>{{ config('app.name') }}</strong>."
      </p>
    </div>
  </div>

  <div class="card-footer-actions">
    <a href="{{ route('admin.employees.index') }}" class="btn">
      ← Back
    </a>
  </div>
</div>

@endsection