@extends('layouts.managerlayout')

@section('content')

<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

<style>
  :root {
    --main-color: #3131ff;
    --danger-color: #dc2626;
    --text-color: #222;
    --muted-color: #666;
    --border-color: #e5e5e5;
    --bg-light: #f8f9fa;
  }

  /* =========================
   CORE FIX (IMPORTANT)
========================= */

  body {
    margin: 0;
    padding: 0;
    overflow-x: hidden;
  }

  /* OUTER WRAPPER (ONLY SCALE HERE) */
  .invoice-wrapper {
    width: 100%;
    max-width: 800px;
    /* 🔥 reduce from 900 */
    margin: auto;
  }

  .invoice-box {
    width: 100%;
    background: #fff;
    border: 1px solid var(--border-color);
    box-shadow: 0 0 10px rgba(0, 0, 0, .08);
    padding: 20px;
    /* 🔥 reduce padding */
    box-sizing: border-box;
  }

  /* =========================
   FIXED LAYOUT (NO WRAP EVER)
========================= */

  .fixed-row {
    display: flex;
    gap: 15px;
  }

  .fixed-col-6 {
    width: 50%;
  }

  .fixed-col-5 {
    width: 41.66%;
  }

  .fixed-col-7 {
    width: 58.33%;
  }

  /* =========================
   HEADER
========================= */

  .header {
    text-align: center;
    border-bottom: 2px solid var(--main-color);
    padding-bottom: 12px;
    margin-bottom: 25px;
  }

  .header h1 {
    margin: 0;
    color: var(--main-color);
    font-size: 32px;
    font-weight: 700;
    text-transform: uppercase;
  }

  /* =========================
   INFO CARD
========================= */

  .info-card {
    background: var(--bg-light);
    border: 1px solid var(--border-color);
    border-radius: 8px;
    padding: 15px;
    font-size: 14px;
    line-height: 1.7;
  }

  /* =========================
   TABLE FIX (MOST IMPORTANT)
========================= */

  .table-responsive {
    width: 100%;
    overflow: hidden;
    /* IMPORTANT */
  }

  .invoice-table {
    width: 100%;
    border-collapse: collapse;
    table-layout: fixed;
  }

  .invoice-table th,
  .invoice-table td {
    border: 1px solid var(--border-color);
    padding: 8px;
    font-size: 13px;
    word-break: break-word;
    white-space: normal;
  }

  .invoice-table thead th {
    background: #f2f2f2;
  }

  /* =========================
   SUMMARY
========================= */

  .summary-card {
    border: 1px dashed var(--border-color);
    border-radius: 8px;
    padding: 15px;
  }

  .summary-row {
    display: flex;
    justify-content: space-between;
    padding: 10px 0;
    border-bottom: 1px solid #eee;
  }

  .total-payable {
    font-size: 22px;
    font-weight: 700;
    color: var(--main-color);
    border-top: 2px solid #ddd;
  }

  /* =========================
   SIGNATURE
========================= */

  .signature-box {
    border-top: 1px solid #333;
    text-align: center;
    width: 180px;
    padding-top: 6px;
    margin-top: 50px;
  }

  /* =========================
   PRINT SAFE
========================= */

  @media print {

    .no-print,
    .sidebar-overlay,
    .sidebar,
    .custom-navbar {
      display: none !important;
    }

    aside {
      display: none !important;
    }

    nav {
      display: none !important;
    }

    body {
      background: #fff;
    }

    .invoice-wrapper {
      transform: none !important;
    }
  }
</style>

<div class="container-fluid py-3" id="wrapper-outer">

  {{-- Buttons --}}
  <div class="action-bar d-flex justify-content-end no-print mb-3">
    <button onclick="downloadPDF()" class="btn btn-success me-2">
      <i class="fas fa-download me-1"></i> Download PDF
    </button>
    <button onclick="printInvoice()" class="btn btn-primary">
      <i class="fas fa-print me-1"></i> Print Invoice
    </button>
  </div>

  <div class="invoice-wrapper" id="invoice-scale-target">

    <div class="invoice-box" id="printArea">

      <div class="header">
        <h1>R Electric</h1>
        <p>Double Mooring, Chattogram, Bangladesh</p>
        <p>Phone: +8801XXXXXXXXX | Email: info@relectric.com</p>
      </div>


      <div class="fixed-row">
        <div class="fixed-col-6">
          <div class="info-card">
            <b>Customer Details:</b><br>
            Name: {{ $customerData['details']->shop_name }}<br>
            Address: {{ $customerData['details']->address ?? 'Chattogram' }}<br>
            Phone: {{ $customerData['details']->phone }}
          </div>
        </div>

        <div class="fixed-col-6">
          <div class="info-card text-end" style="text-align: right;">
            <b>Invoice Info:</b><br>
            Order ID: #ORD-{{ $order->id }}<br>
            Date: {{ $order->created_at->format('d M Y, h:i A') }}<br>
            Reference (SR): {{ $order->sr->name ?? 'N/A' }}
          </div>
        </div>
      </div>

      <div class="table-responsive mb-4">
        <table class="invoice-table">
          <colgroup>
            <col style="width: 8%;">
            <col style="width: 45%;">
            <col style="width: 15%;">
            <col style="width: 10%;">
            <col style="width: 22%;">
          </colgroup>
          <thead>
            <tr>
              <th>S.No</th> {{-- style="width: 8%;" --}}
              <th>Product</th>
              <th>Rate</th>
              <th>Qty</th>
              <th style="text-align: right;">Subtotal</th>
            </tr>
          </thead>
          <tbody>
            @php $sl = 1; @endphp
            @foreach($groupedItems as $category => $items)
            <tr class="category-row">
              <td colspan="5">{{ $category }}</td>
            </tr>
            @foreach($items as $item)
            <tr>
              <td>{{ $sl++ }}</td>
              <td>{{ $item->product->name }}</td>
              <td>{{ number_format($item->price,2) }} ৳</td>
              <td>{{ $item->quantity }}</td>
              <td style="text-align: right;">{{ number_format($item->net_total,2) }} ৳</td>
            </tr>
            @endforeach
            @endforeach
          </tbody>
        </table>
      </div>

      <div class="fixed-row">
        <div class="fixed-col-5">
          <div class="summary-card">
            <h5 class="text-danger">Payment Status</h5>
            <div class="mb-2">
              Previous Due:
              <strong>{{ number_format($customerData['previous_due'],2) }} ৳</strong>
            </div>
            <div>
              Current Total Due:
              <strong>{{ number_format($customerData['details']->due,2) }} ৳</strong>
            </div>
          </div>
        </div>

        <div class="fixed-col-7">
          <div class="summary-card">
            <div class="summary-table">
              <div class="summary-row">
                <span>Total Amount:</span>
                <span>{{ number_format($order->net_total + $order->total_discount,2) }} ৳</span>
              </div>
              <div class="summary-row" style="color: var(--danger-color);">
                <span>Discount:</span>
                <span>- {{ number_format($order->total_discount,2) }} ৳</span>
              </div>
              <div class="summary-row total-payable">
                <span>Net Payable:</span>
                <span>{{ number_format($order->net_total,2) }} ৳</span>
              </div>
            </div>
          </div>
        </div>
      </div>


      <div style="display: flex; justify-content: space-between; margin-top: 50px;">
        <div class="signature-box">Customer Signature</div>
        <div class="signature-box">Manager Signature</div>
      </div>


      <div class="footer-note">
        This is a computer generated invoice and requires no physical signature.
      </div>

    </div>
  </div>
</div>
@endsection
@push('scripts')
<script>
  function scaleInvoice() {
    const wrapper = document.getElementById("invoice-scale-target");

    const baseWidth = 900;
    const screenWidth = window.innerWidth;

    let scale = screenWidth / baseWidth;

    if (scale > 1) scale = 1; // prevent zoom-in

    wrapper.style.transform = `scale(${scale})`;
    wrapper.style.transformOrigin = "top left";
  }

  window.addEventListener("load", scaleInvoice);
  window.addEventListener("resize", scaleInvoice);

  function printInvoice() {
    window.print();
  }

  function downloadPDF() {
    const element = document.getElementById('printArea');

    html2pdf().set({
      margin: 10,
      filename: 'Invoice.pdf',
      image: {
        type: 'jpeg',
        quality: 1
      },
      html2canvas: {
        scale: 2
      },
      jsPDF: {
        format: 'a4',
        orientation: 'portrait'
      }
    }).from(element).save();
  }
</script>
@endpush