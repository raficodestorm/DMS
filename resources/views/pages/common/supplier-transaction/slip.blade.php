@extends(getLayout())

@section('content')

<div class="container d-flex flex-column align-items-center" id="invoice-scale-target">
  {{-- The Slip Card --}}
  <div class="receipt-card" id="printArea">

    <!-- Watermark Logo -->
    <img src="{{ asset('image/relectric-logo.png') }}" class="watermark-logo" alt="Watermark">

    <div class="receipt-header">
      <div class="brand-info">
        <div>
          <img src="{{ asset('image/relectric-logo.png') }}" alt="Logo" class="sidebar-logo img-fluid" style="width: 200px; height: auto;">
        </div>
        <p>Supplier Payment Voucher</p>
      </div>
      <div class="receipt-status">
        <img src="{{ asset('image/paid.png') }}" alt="Paid" class="paid-stamp-img">
      </div>
    </div>

    <div class="receipt-body">
      <div class="amount-section">
        <span class="amount-label">Amount Paid</span>
        <h1 class="amount-value">৳ {{ number_format($transaction->amount, 2) }}</h1>
      </div>

      <div class="divider"></div>

      <div class="receipt-details">
        <div class="receipt-row">
          <span class="label">Voucher / Txn ID</span>
          <span class="value">BRST00{{ $transaction->id }}</span>
        </div>
        <div class="receipt-row">
          <span class="label">Payment Method</span>
          <span class="value" style="text-transform: capitalize;">{{ $transaction->payment_method ?? 'Cash' }}</span>
        </div>
        <div class="receipt-row">
          <span class="label">Date &amp; Time</span>
          <span class="value">{{ $transaction->created_at->format('d M Y, h:i A') }}</span>
        </div>
        <div class="receipt-row">
          <span class="label">Supplier Company</span>
          <span class="value">{{ $transaction->supplier->company_name ?? 'N/A' }}</span>
        </div>
        <div class="receipt-row">
          <span class="label">Contact Person</span>
          <span class="value">{{ $transaction->supplier->name ?? 'N/A' }}</span>
        </div>
        <div class="receipt-row">
          <span class="label">Reference Branch</span>
          <span class="value">{{ $transaction->branch->name ?? 'Main / Head Office' }}</span>
        </div>
        @if($transaction->note)
        <div class="receipt-row">
          <span class="label">Note / Remarks</span>
          <span class="value" style="font-weight: 500; font-size: 0.88rem;">{{ $transaction->note }}</span>
        </div>
        @endif
      </div>

      <div class="divider"></div>

      <div class="receipt-row">
        <span class="label">Due before payment</span>
        <span class="value">{{ number_format($transaction->due_before_transaction, 2) }} TK</span>
      </div>
      <div class="receipt-row text-success">
        <span class="label">Due after payment</span>
        <span class="value"><strong>{{ number_format($transaction->due_after_transaction, 2) }} TK</strong></span>
      </div>
    </div>

    <div class="receipt-footer">
      <div class="qr-section">
        <img
          src="https://api.qrserver.com/v1/create-qr-code/?size=100x100&data={{ urlencode(route('supplier-transactions.show.public', $transaction->id)) }}"
          alt="QR Code" class="qr-code">
        <p class="qr-text">Scan to verify voucher</p>
      </div>
      <div class="footer-note">
        <p>Thank you for partnering with us!</p>
        <p class="system-name">{{ config('app.name') }} Automated Billing</p>
      </div>
    </div>
  </div>
</div>

<div class="action-bar d-flex justify-content-center no-print mt-3 mb-4">
  <button onclick="downloadPDF()" class="btn-smart btn-green me-3">
    <i class="fas fa-download me-1"></i> Download PDF
  </button>
  <button onclick="window.print()" class="btn-smart btn-blue me-3">
    <i class="fas fa-print me-1"></i> Print Voucher
  </button>
  <a href="{{ url()->previous() }}" class="btn-smart" style="background:#64748b; color:#fff;">
    <i class="fas fa-arrow-left me-1"></i> Back
  </a>
</div>

<style>
  /* Professional Receipt Styling */
  .receipt-card {
    background: #fff !important;
    width: 100%;
    max-width: 460px;
    padding: 40px;
    border-radius: 15px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.06);
    position: relative;
    overflow: hidden;
    border: 1px solid #eee;
    z-index: 1;
    -webkit-print-color-adjust: exact;
  }

  .receipt-card > *:not(.watermark-logo) {
    position: relative;
    z-index: 2;
  }

  .watermark-logo {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 70%;
    max-width: 300px;
    opacity: 0.08;
    z-index: 0;
    pointer-events: none;
    user-select: none;
    -webkit-print-color-adjust: exact;
    print-color-adjust: exact;
  }

  .receipt-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 25px;
  }

  .brand-info p {
    margin: 4px 0 0 0;
    color: #64748b;
    font-size: 0.88rem;
    font-weight: 600;
  }

  .paid-stamp-img {
    width: 140px;
    height: auto;
    margin-right: -20px;
    opacity: 0.85;
    transform: rotate(-12deg);
    display: inline-block;
    filter: drop-shadow(0 2px 4px rgba(0,0,0,0.1));
  }

  .amount-section {
    text-align: center;
    margin: 25px 0;
  }

  .amount-label {
    font-size: 0.85rem;
    color: #64748b;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
  }

  .amount-value {
    font-size: 2.2rem;
    font-weight: 700;
    color: #090766;
    margin-top: 4px;
    letter-spacing: -0.5px;
  }

  .divider {
    border-top: 2px dashed #e2e8f0;
    margin: 18px 0;
  }

  .receipt-row {
    display: flex;
    justify-content: space-between;
    margin-bottom: 10px;
    font-size: 0.92rem;
  }

  .receipt-row .label {
    color: #64748b;
  }

  .receipt-row .value {
    color: #1e293b;
    font-weight: 600;
    text-align: right;
  }

  .receipt-footer {
    text-align: center;
    margin-top: 30px;
  }

  .qr-code {
    background: #fff;
    padding: 6px;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    margin-bottom: 8px;
    width: 88px;
    height: 88px;
  }

  .qr-text {
    font-size: 0.75rem;
    color: #94a3b8;
    margin-bottom: 16px;
  }

  .footer-note p {
    margin: 0;
    font-size: 0.8rem;
    color: #64748b;
  }

  .system-name {
    font-weight: 700;
    color: #3131ff;
    margin-top: 4px !important;
  }

  @media print {
    @page {
      margin: 0;
      size: auto;
    }

    body {
      margin: 1.5cm;
      background: #fff !important;
    }

    .no-print,
    .action-bar,
    .back-btn,
    header,
    nav,
    .sidebar {
      display: none !important;
    }

    .container {
      padding-top: 0 !important;
      margin-top: 0 !important;
      display: block !important;
    }

    .receipt-card {
      box-shadow: none;
      border: 1px solid #eee;
      width: 100%;
      max-width: 100%;
      padding: 20px;
      margin: 0 auto;
      page-break-inside: avoid;
    }
  }
</style>

<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

<script>
  function downloadPDF() {
    const element = document.getElementById('printArea');
    const wrapper = document.getElementById("invoice-scale-target");

    const originalTransform = wrapper ? wrapper.style.transform : "none";
    if(wrapper) wrapper.style.transform = "none";

    const opt = {
        margin: [10, 5, 10, 5],
        filename: 'Supplier_Voucher_BRST00{{ $transaction->id }}.pdf',
        image: { type: 'jpeg', quality: 1.0 },
        html2canvas: { 
            scale: 2, 
            useCORS: true, 
            logging: false,
            letterRendering: true,
            scrollY: 0,
            scrollX: 0,
            windowWidth: document.documentElement.offsetWidth,
            windowHeight: document.documentElement.offsetHeight
        },
        jsPDF: { 
            unit: 'mm', 
            format: 'a4', 
            orientation: 'portrait' 
        }
    };

    html2pdf().set(opt).from(element).toPdf().get('pdf').then(function (pdf) {
        if(wrapper) wrapper.style.transform = originalTransform;
    }).save();
}
</script>
@endsection
