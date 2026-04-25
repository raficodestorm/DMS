@extends('layouts.managerlayout')

@section('content')

<div class="container justify-center">
  <div class="form-card">
    <h2>New Payment Request</h2>
    <p style="color: #666; margin-bottom: 20px; font-size: 0.9rem;">
      Submit a payment collection request for manager approval.
    </p>

    {{-- Success/Error Alert Component --}}
    @include('components.alert')

    <form class="adduser-form" method="POST" action="{{ route('manager.payments.store') }}">
      @csrf

      {{-- Customer Selection --}}
      <div>
        <label>Select Customer / Shop</label>
        <select class="input-form" name="customer_id" required>
          <option value="">--Select Customer--</option>
          @foreach($customers as $customer)
          <option value="{{ $customer->id }}" {{ old('customer_id')==$customer->id ? 'selected' : '' }}>
            {{ $customer->shop_name }} ({{ $customer->name }}) - Due: {{ number_format($customer->due, 2) }}
          </option>
          @endforeach
        </select>
        @error('customer_id')<div class="error-text">{{ $message }}</div>@enderror
      </div>

      {{-- Payment Amount --}}
      <div class="input-box">
        <label>Collection Amount (TK)</label>
        <input type="number" step="0.01" class="input-form" name="amount" placeholder="Enter collected amount" required
          value="{{ old('amount') }}">
        @error('amount')<div class="error-text">{{ $message }}</div>@enderror
      </div>

      {{-- Note / Remarks --}}
      <div class="input-box">
        <label>Note / Remarks (Optional)</label>
        <textarea class="input-form" name="note" rows="3"
          placeholder="Any specific info about this payment...">{{ old('note') }}</textarea>
        @error('note')<div class="error-text">{{ $message }}</div>@enderror
      </div>

      {{-- Info Box for SR --}}
      <div
        style="background: #f8f9fa; padding: 10px; border-radius: 5px; margin-bottom: 20px; border-left: 4px solid #3131ff;">
        <small style="color: #555;">
          <i class="fa-solid fa-circle-info"></i>
          After submitting this payment will be <strong>completed</strong> automatically &
          Customer due will be updated.
        </small>
      </div>

      <div>
        <button class="btn-submit" type="submit">
          <i class="fa-solid fa-paper-plane"></i> Send Request
        </button>
      </div>
    </form>
  </div>
</div>

{{-- Back Button --}}
<div class="container justify-center" style="margin-top: 15px;">
  <a href="{{ route('sr.payments.index') }}" style="text-decoration: none; color: #666;">
    ← Back to Payment List
  </a>
</div>

@endsection