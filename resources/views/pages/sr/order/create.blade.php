@extends('layouts.srlayout')
@section('content')
<form method="POST" action="{{ route('orders.store') }}">
  @csrf

  <select name="customer_id">
    @foreach($customers as $c)
    <option value="{{ $c->id }}">{{ $c->name }}</option>
    @endforeach
  </select>

  <div>
    <input name="products[0][product_id]" value="1">
    <input name="products[0][qty]" type="number">
  </div>

  <button>Submit Order</button>
</form>
@endsection
@push('scripts')
<script>

</script>
@endpush