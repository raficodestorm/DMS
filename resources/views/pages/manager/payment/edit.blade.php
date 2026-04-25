@extends('layouts.managerlayout')

@section('content')

<div class="container justify-center">
  <div class="form-card">
    <h2>Edit Payment Request</h2>
    <p style="color: #666; margin-bottom: 20px; font-size: 0.9rem;">
      Update your pending payment collection details.
    </p>

    {{-- Success/Error Alert Component --}}
    @include('components.alert')

    {{-- Security Check: Only Pending payments can be edited --}}
    @if($payment->status === 'complete')
    <div
      style="padding: 20px; background: #fff5f5; border: 1px solid #feb2b2; color: #c53030; border-radius: 8px; text-align: center;">
      <i class="fa-solid fa-circle-exclamation"></i>
      This payment has already been <strong>Approved</strong> and cannot be edited.
    </div>
    @else
    <form class="adduser-form" method="POST" action="{{ route('sr.payments.update', $payment->id) }}">
      @csrf
      @method('PUT')

      {{-- Customer Selection (Disabled in Edit is often better, but keeping it as per your requirement) --}}
      <div>
        <label>Select Customer / Shop</label>
        <select class="input-form" name="customer_id" required>
          <option value="">--Select Customer--</option>
          @foreach($customers as $customer)
          <option value="{{ $customer->id }}" {{ old('customer_id', $payment->customer_id) == $customer->id ? 'selected'
            : '' }}>
            {{ $customer->shop_name }} (Due: {{ number_format($customer->due, 2) }})
          </option>
          @endforeach
        </select>
        @error('customer_id')<div class="error-text">{{ $message }}</div>@enderror
      </div>

      {{-- Payment Amount --}}
      <div class="input-box">
        <label>Collection Amount (TK)</label>
        <input type="number" step="0.01" class="input-form" name="amount" placeholder="Enter collected amount" required
          value="{{ old('amount', $payment->amount) }}">
        @error('amount')<div class="error-text">{{ $message }}</div>@enderror
      </div>

      {{-- Note / Remarks --}}
      <div class="input-box">
        <label>Note / Remarks (Optional)</label>
        <textarea class="input-form" name="note" rows="3"
          placeholder="Any specific info about this payment...">{{ old('note', $payment->note) }}</textarea>
        @error('note')<div class="error-text">{{ $message }}</div>@enderror
      </div>

      <div
        style="background: #fffaf0; padding: 10px; border-radius: 5px; margin-bottom: 20px; border-left: 4px solid #ed8936;">
        <small style="color: #744210;">
          <i class="fa-solid fa-triangle-exclamation"></i>
          Changing the amount will require a new review carefully.
        </small>
      </div>

      <div>
        <button class="btn-submit" type="submit">
          <i class="fa-solid fa-rotate"></i> Update Request
        </button>
      </div>
    </form>
    @endif
  </div>
</div>

{{-- Back Button --}}
<div class="container justify-center" style="margin-top: 15px;">
  <a href="{{ route('sr.payments.index') }}" style="text-decoration: none; color: #666;">
    ← Back to Payment List
  </a>
</div>

@endsection