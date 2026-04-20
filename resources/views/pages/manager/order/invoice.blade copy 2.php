@extends('layouts.managerlayout')

@section('content')
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
<style>
  :root {
    --primary-color: #2c3e50;
    --secondary-color: #34495e;
    --accent-color: #e74c3c;
    --border-color: #dee2e6;
  }

  #wrapper-outer {
    background: #f8f9fa;
    min-height: 100vh;
    padding: 20px;
  }

  /* Fixed Layout Container */
  .invoice-wrapper {
    width: 800px;
    /* Fixed width for consistency */
    margin: 0 auto;
    background: white;
    box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
    padding: 40px;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    color: #333;
  }

  .header {
    border-bottom: 2px solid var(--primary-color);
    padding-bottom: 20px;
    margin-bottom: 30px;
    text-align: center;
  }

  .header h1 {
    color: var(--primary-color);
    font-weight: 800;
    text-transform: uppercase;
    margin: 0;
  }

  /* Grid System for Fixed Layout */
  .fixed-row {
    display: flex;
    width: 100%;
    margin-bottom: 30px;
  }

  .fixed-col-6 {
    width: 50%;
  }

  .fixed-col-5 {
    width: 42%;
  }

  .fixed-col-7 {
    width: 58%;
  }

  .info-card b {
    color: var(--primary-color);
    display: block;
    margin-bottom: 5px;
    font-size: 1.1rem;
  }

  /* Table Styling */
  .invoice-table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 30px;
  }

  .invoice-table th {
    background-color: var(--primary-color);
    color: white;
    padding: 12px 10px;
    text-align: left;
  }

  .invoice-table td {
    padding: 10px;
    border-bottom: 1px solid var(--border-color);
  }

  .category-row {
    background-color: #f1f1f1;
    font-weight: bold;
  }

  /* Summary Styling */
  .summary-card {
    padding: 15px;
    background: #f9f9f9;
    border-radius: 5px;
  }

  .summary-row {
    display: flex;
    justify-content: space-between;
    padding: 5px 0;
    font-size: 1rem;
  }

  .total-payable {
    border-top: 2px solid var(--primary-color);
    margin-top: 10px;
    padding-top: 10px;
    font-weight: bold;
    font-size: 1.2rem;
    color: var(--primary-color);
  }

  .signature-box {
    width: 200px;
    border-top: 1px solid #333;
    text-align: center;
    padding-top: 10px;
    font-weight: bold;
    margin-top: 80px;
  }

  .footer-note {
    text-align: center;
    margin-top: 50px;
    font-size: 12px;
    color: #777;
    font-style: italic;
  }

  /* Print Specific Settings */
  @media print {
    body * {
      visibility: hidden;
    }

    #printArea,
    #printArea * {
      visibility: visible;
    }

    #printArea {
      position: absolute;
      left: 0;
      top: 0;
      width: 100%;
      margin: 0;
      padding: 20px;
      box-shadow: none;
    }

    .no-print {
      display: none !important;
    }

    @page {
      size: A4;
      margin: 10mm;
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

      {{-- Header --}}
      <div class="header">
        <h1>R Electric</h1>
        <p>Double Mooring, Chattogram, Bangladesh</p>
        <p>Phone: +8801XXXXXXXXX | Email: info@relectric.com</p>
      </div>

      {{-- Customer + Invoice Info (Fixed Row) --}}
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

      {{-- Table --}}
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

      {{-- Summary (Fixed Row) --}}
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

      {{-- Signature --}}
      <div style="display: flex; justify-content: space-between; margin-top: 50px;">
        <div class="signature-box">Customer Signature</div>
        <div class="signature-box">Manager Signature</div>
      </div>

      {{-- Footer --}}
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
    const container = document.getElementById("wrapper-outer");
    
    const baseWidth = 840; // Invoice width + some margin
    const screenWidth = window.innerWidth;
    
    if (screenWidth < baseWidth) {
        let scale = screenWidth / baseWidth;
        wrapper.style.transform = `scale(${scale})`;
        wrapper.style.transformOrigin = "top center";
        // Adjust container height to prevent white space
        container.style.height = (wrapper.offsetHeight * scale) + "px";
    } else {
        wrapper.style.transform = "scale(1)";
        container.style.height = "auto";
    }
}

window.addEventListener("load", scaleInvoice);
window.addEventListener("resize", scaleInvoice);

function printInvoice() {
    window.print();
}

function downloadPDF() {
    const element = document.getElementById('printArea');
    const opt = {
        margin:       [10, 10, 10, 10],
        filename:     'Invoice-{{ $order->id }}.pdf',
        image:        { type: 'jpeg', quality: 0.98 },
        html2canvas:  { scale: 3, useCORS: true }, // High scale for clear text
        jsPDF:        { unit: 'mm', format: 'a4', orientation: 'portrait' }
    };

    html2pdf().set(opt).from(element).save();
}
</script>
@endpush