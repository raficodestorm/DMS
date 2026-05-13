@extends('layouts.srlayout')

@section('content')
<style>
  .quick-access-card {
    border-radius: 15px;
    padding: 15px 10px;
    
  }

  .icon-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 40px 10px;
  }

  .icon-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    text-decoration: none;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    group;
  }

  .icon-item:hover {
    transform: translateY(-5px);
  }

  .icon-circle {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    margin-bottom: 10px;
    transition: all 0.3s ease;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    position: relative;
    overflow: hidden;
  }

  .icon-circle::after {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(255, 255, 255, 0.1);
    opacity: 0;
    transition: opacity 0.3s ease;
  }

  .icon-item:hover .icon-circle::after {
    opacity: 1;
  }

  .icon-item span {
    font-size: 12px;
    font-weight: 600;
    color: var(--text-main);
    text-align: center;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    font-family: 'Inter', sans-serif;
  }

  /* Specific Icon Colors with Gradients */
  .bg-order { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; }
  .bg-customer { background: linear-gradient(135deg, #0ba360 0%, #3cba92 100%); color: white; }
  .bg-account { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white; }
  .bg-payment { background: linear-gradient(135deg, #f6d365 0%, #fda085 100%); color: white; }
  .bg-return { background: linear-gradient(135deg, #ff0844 0%, #ffb199 100%); color: white; }
  .bg-all-orders { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); color: white; }

  @media (max-width: 576px) {
    .icon-circle {
      width: 50px;
      height: 50px;
      font-size: 20px;
    }
    .icon-item span {
      font-size: 10px;
    }
    .quick-access-card {
      padding: 20px 10px;
    }
  }
</style>

<div class="d-flex justify-content-between align-items-center mb-4 animate__animated animate__fadeIn">
  <h2 style="font-size: 22px; font-family: 'Cinzel', serif; font-weight: 700; color: var(--primary);">Overview</h2>
  <span class="badge p-2 px-3" style="letter-spacing: 1px; color: var(--text-muted); background: var(--section-bg); border-radius: 50px; border: 1px solid var(--border-color);">
    <i class="far fa-calendar-alt me-1"></i> {{ date('d M, Y') }}
  </span>
</div>

<div class="quick-access-card animate__animated animate__fadeInUp">
  <h6 class="mb-4" style="font-size: 14px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 1px;">Quick Actions</h6>
  <div class="icon-grid">
    <!-- Row 1 -->
    <a href="{{ route('sr.order.create') }}" class="icon-item">
      <div class="icon-circle bg-order">
        <i class="fas fa-cart-plus"></i>
      </div>
      <span>Order</span>
    </a>

    <a href="{{ route('customers.create') }}" class="icon-item">
      <div class="icon-circle bg-customer">
        <i class="fas fa-user-plus"></i>
      </div>
      <span>Customer</span>
    </a>

    <a href="{{ route('sr.users.create') }}" class="icon-item">
      <div class="icon-circle bg-account">
        <i class="fas fa-user-gear"></i>
      </div>
      <span>Account</span>
    </a>

    <!-- Row 2 -->
    <a href="{{ route('sr.payments.create') }}" class="icon-item">
      <div class="icon-circle bg-payment">
        <i class="fas fa-money-bill-wave"></i>
      </div>
      <span>Payment</span>
    </a>

    <a href="{{ route('sr.return.create') }}" class="icon-item">
      <div class="icon-circle bg-return">
        <i class="fas fa-rotate-left"></i>
      </div>
      <span>Return</span>
    </a>

    <a href="{{ route('sr.order.all') }}" class="icon-item">
      <div class="icon-circle bg-all-orders">
        <i class="fas fa-list-check"></i>
      </div>
      <span>Orders</span>
    </a>
  </div>
</div>
@endsection

@if(session('success'))
@push('scripts')

@endpush
@endif