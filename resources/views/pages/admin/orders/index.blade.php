@extends('layouts.adminlayout')
@section('content')
@foreach($orders as $order)
<div class="card">
  <h4>Order #{{ $order->id }}</h4>
  <p>Status: {{ $order->status }}</p>

  <a href="{{ route('orders.approve',$order->id) }}">Approve</a>
</div>
@endforeach
@endsection
@push('scripts')
<script>

</script>
@endpush