@extends(getLayout())

@section('content')
<div style="max-width:800px;margin:auto;padding:30px;background:#fff;">
  <h2>Invoice #{{ $invoice->invoice_number }}</h2>

  <p>Customer: {{ $invoice->order->customer->name }}</p>

  <table width="100%">
    <tr>
      <th>Product</th>
      <th>Qty</th>
      <th>Total</th>
    </tr>

    @foreach($invoice->order->items as $item)
    <tr>
      <td>{{ $item->product->name }}</td>
      <td>{{ $item->quantity }}</td>
      <td>{{ $item->total }}</td>
    </tr>
    @endforeach
  </table>

  <h3>Total: {{ $invoice->order->final_amount }}</h3>
</div>
@endsection
@push('scripts')

@endpush