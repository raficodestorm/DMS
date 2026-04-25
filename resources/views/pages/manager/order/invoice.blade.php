@extends(getLayout())

@section('content')

<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

<style>
  .invoice-wrapper {
    width: 100%;
    max-width: 800px;
    margin: auto;
  }

  .invoice-box {
    width: 100%;
    background: #fff;
    padding: 20px;
    box-sizing: border-box;
  }


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

  .header {
    text-align: center;
    border-bottom: 2px solid var(--primary);
    padding-bottom: 12px;
    margin-bottom: 15px;
  }

  .header h1 {
    margin: 0;
    color: var(--primary);
    font-size: 32px;
    font-weight: 700;
    text-transform: uppercase;
  }

  .info-card {
    border: 1px solid var(--border-color);
    border-radius: 8px;
    padding: 10px;
    font-size: 12px;
    line-height: 1.7;
  }

  .table-responsive {
    width: 100%;
    overflow: hidden;
  }

  .invoice-table {
    width: 100%;
    border-collapse: collapse;
    table-layout: fixed;
  }

  .invoice-table th,
  .invoice-table td {
    border: 1px solid #08111b;
    padding: 8px;
    font-size: 13px;
    word-break: break-word;
    white-space: normal;
  }

  .invoice-table td {
    color: #08111b;
  }

  .invoice-table thead th {
    background: #f2f2f2;
  }

  .summary-card {
    border: 1px dashed var(--border-color);
    border-radius: 8px;
    padding: 10px;
  }

  .summary-row {
    display: flex;
    justify-content: space-between;
    padding: 6px 0;
    border-bottom: 1px solid #eee;
  }

  .total-payable {
    font-size: 20px;
    font-weight: 700;
    color: var(--primary);
    border-top: 2px solid #ddd;
  }

  .signature-box {
    border-top: 1px solid #333;
    text-align: center;
    width: 180px;
    padding-top: 6px;
    margin-top: 40px;
  }

  .footer-note {
    margin-top: 50px;
    text-align: center;
    font-size: 10px;
    color: #666;
    border-top: 1px solid #ddd;
    padding-top: 10px;
    line-height: 1.6;
  }

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

  #printArea {
    height: auto !important;
    overflow: visible !important;
    display: block !important;
  }

  /* PDF generate korar somoy jate background color thik thake */
  .invoice-box {
    background: #ffffff !important;
    -webkit-print-color-adjust: exact;
  }
</style>

<div class="container-fluid" id="wrapper-outer">
  <div class="invoice-wrapper" id="invoice-scale-target">

    <div class="invoice-box" id="printArea">

      <div class="header">
        <h1>{{ config('app.name') }}</h1>
        <p>Double Mooring, Chattogram, Bangladesh</p>

      </div>


      <div class="fixed-row">
        <div class="fixed-col-6">
          <div class="info-card">
            <b>Customer Details:</b><br>
            Shop Name: {{ $customerData['details']->shop_name }}<br>
            Address: {{ $customerData['details']->address ?? 'Chattogram' }}<br>
            Phone: {{ $customerData['details']->phone }}
          </div>
        </div>

        <div class="fixed-col-6">
          <div class="info-card text-end" style="text-align: right;">
            <b>Invoice Info:</b><br>
            Order ID: BRS{{ $order->id }}<br>
            Date: {{ $order->created_at->timezone(auth()->user()->timezone)->format('d M Y, h:i A') }}<br>
            Reference (SR): {{ $order->sr->fullname ?? 'N/A' }}
          </div>
        </div>
      </div>

      <div class="table-responsive mb-4 mt-3">
        <table class="invoice-table">
          <colgroup>
            @if($hasDiscount)
            <col style="width: 8%;">
            <col style="width: 17%;">
            <col style="width: 31%;">
            <col style="width: 12%;">
            <col style="width: 9%;">
            <col style="width: 10%;">
            <col style="width: 13%;">
            @else
            <col style="width: 8%;">
            <col style="width: 19%;">
            <col style="width: 32%;">
            <col style="width: 15%;">
            <col style="width: 10%;">
            <col style="width: 16%;">
            @endif
          </colgroup>
          <thead>
            <tr>
              <th>S.No</th> {{-- style="width: 8%;" --}}
              <th>Category</th>
              <th>Product</th>
              <th>Rate</th>
              <th>Qty</th>
              @if($hasDiscount)
              <th>Discount</th>
              @endif
              <th style="text-align: right;">Total</th>
            </tr>
          </thead>
          <tbody>
            @php $sl = 1; @endphp
            @foreach($items as $item)
            <tr>
              <td>{{ $sl++ }}</td>
              <td>{{ $item->product->category->name }}</td>
              <td>{{ $item->product->name }}</td>
              <td>{{ number_format($item->price,2) }} ৳</td>
              <td>{{ $item->quantity }}</td>
              @if($hasDiscount)
              <td style="color: #dc2626;">
                {{ $item->discount_amount > 0 ? number_format($item->discount_amount,2).' ৳' : '-' }}
              </td>
              @endif
              <td style="text-align: right;">{{ number_format($item->net_total,2) }} ৳</td>
            </tr>
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
                <span>{{ number_format($order->net_total + $order->discount_amount,2) }} ৳</span>
              </div>
              <div class="summary-row" style="color: #dc2626;">
                <span>Discount:</span>
                <span>- {{ number_format($order->discount_amount,2) }} ৳</span>
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
        www.relectric.com </br>
        Software Developed & Maintained by S A Rafi | Contact: 01877100096
      </div>

    </div>
  </div>
  {{-- Buttons --}}
  <div class="action-bar d-flex justify-content-center no-print mt-3">
    <button onclick="downloadPDF()" class="btn-smart btn-green me-3">
      <i class="fas fa-download me-1"></i> Download PDF
    </button>
    <button onclick="printInvoice()" class="btn-smart btn-blue">
      <i class="fas fa-print me-1"></i> Print Invoice
    </button>
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
    const wrapper = document.getElementById("invoice-scale-target");

    // 1. PDF capture korar age temporary-vabe transform bondho kora
    const originalTransform = wrapper.style.transform;
    wrapper.style.transform = "none";

    const opt = {
        margin: [2, 1, 2, 3],
        filename: 'Invoice_BRS{{ $order->id }}.pdf',
        image: { type: 'jpeg', quality: 0.98 },
        html2canvas: { 
            scale: 2, 
            useCORS: true, 
            logging: false,
            letterRendering: true,
            scrollY: 0 
        },
        jsPDF: { 
            unit: 'mm', 
            format: 'a4', 
            orientation: 'portrait' 
        }
    };

    // 2. PDF generate kora
    html2pdf().set(opt).from(element).toPdf().get('pdf').then(function (pdf) {
        // PDF generation sesh hole transform abar ager moto kore deya
        wrapper.style.transform = originalTransform;
    }).save();
}
</script>
@endpush