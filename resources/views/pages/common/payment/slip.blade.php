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
          <img src="{{ asset('image/relectric-logo.png') }}" alt="Logo" class="sidebar-logo img-fluid" style="width: 200px; height: 60x;">
        </div>
        <p>Payment Receipt</p>
      </div>
      <div class="receipt-status">
        <img src="{{ asset('image/paid.png') }}" alt="Paid" class="paid-stamp-img">
      </div>
    </div>

    <div class="receipt-body">
      <div class="amount-section">
        <span class="amount-label">Amount Paid</span>
        <h1 class="amount-value">৳ {{ number_format($payment->amount, 2) }}</h1>
      </div>

      <div class="divider"></div>

      <div class="receipt-details">
        <div class="receipt-row">
          <span class="label">Transaction ID</span>
          <span class="value">BRT00{{ $payment->id }}</span>
        </div>
        <div class="receipt-row">
          <span class="label">Payment Method</span>
          <span class="value">{{ ucfirst($payment->payment_method) ?? 'N/A' }}</span>
        </div>
        <div class="receipt-row">
          <span class="label">Date</span>
          <span class="value">{{ $payment->created_at->format('d M Y, h:i A') }}</span>
        </div>
        <div class="receipt-row">
          <span class="label">Customer / Shop</span>
          <span class="value">{{ $payment->customer->shop_name }}</span>
        </div>
        <div class="receipt-row">
          <span class="label">Collected By</span>
          <span class="value">{{ $payment->sr->fullname ?? 'Branch manager' }}</span>
        </div>
      </div>

      <div class="divider"></div>

      <div class="receipt-row">
        <span class="label">Due before payment</span>
        <span class="value">{{ number_format($payment->due_before_transaction, 2) }} TK</span>
      </div>
      <div class="receipt-row text-success">
        <span class="label">Due after payment</span>
        <span class="value"><strong>{{ number_format($payment->due_after_transaction, 2) }} TK</strong></span>
      </div>
    </div>

    <div class="receipt-footer">
      <div class="qr-section">
        <img
          src="https://api.qrserver.com/v1/create-qr-code/?size=80x80&data={{ route('payments.show.public', $payment->id) }}"
          alt="QR Code" class="qr-code">
        <p class="qr-text">Scan to verify transaction</p>
      </div>
      <div class="footer-note">
        <p>Thank you for choosing our service!</p>
        <p class="system-name">{{ config('app.name') }} Automated Billing</p>
      </div>
    </div>
  </div>
</div>

<div class="action-bar d-flex justify-content-center no-print mt-3">
  <button onclick="downloadPDF()" class="btn-smart btn-green me-3">
    <i class="fas fa-download me-1"></i> Download PDF
  </button>
  <button onclick="window.print()" class="btn-smart btn-blue">
    <i class="fas fa-print me-1"></i> Print Invoice
  </button>
</div>

<style>
  /* Professional Receipt Styling */
  .receipt-card {
    background: #fff !important;
    width: 100%;
    max-width: 450px;
    padding: 40px;
    border-radius: 15px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
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
    margin-bottom: 30px;
  }

  .brand-info h2 {
    color: #3131ff;
    font-weight: 800;
    margin: 0;
    text-transform: uppercase;
    letter-spacing: 1px;
  }

  .brand-info p {
    margin: 0;
    color: #777;
    font-size: 0.9rem;
  }

  .paid-stamp-img {
    width: 150px;
    height: auto;
    margin-right: -25px;
    opacity: 0.8;
    transform: rotate(-12deg);
    display: inline-block;
    filter: drop-shadow(0 2px 4px rgba(0,0,0,0.1));
  }

  .amount-section {
    text-align: center;
    margin: 35px 0;
  }

  .amount-label {
    font-size: 0.9rem;
    color: #777;
    font-weight: 500;
  }

  .amount-value {
    font-size: 2.3rem;
    font-weight: 600;
    color: #090766;
    margin-top: 5px;
  }

  .divider {
    border-top: 2px dashed #eee;
    margin: 20px 0;
  }

  .receipt-row {
    display: flex;
    justify-content: space-between;
    margin-bottom: 10px;
    font-size: 0.95rem;
  }

  .receipt-row .label {
    color: #666;
  }

  .receipt-row .value {
    color: #333;
    font-weight: 600;
    text-align: right;
  }

  .receipt-footer {
    text-align: center;
    margin-top: 40px;
  }

  .receipt-footer {
    text-align: center;
    margin-top: 40px;
  }

  .qr-code {
    background: #fff;
    padding: 8px;
    border: 1px solid #eee;
    border-radius: 12px;
    margin-bottom: 10px;
    width: 80px;
  }

  .qr-text {
    font-size: 0.75rem;
    color: #aaa;
    margin-bottom: 20px;
  }

  .footer-note p {
    margin: 0;
    font-size: 0.8rem;
    color: #777;
  }

  .system-name {
    font-weight: 700;
    color: #3131ff;
    margin-top: 5px !important;
  }


  @media print {

    /* 1. Browser-er default margin zero kora */
    @page {
      margin: 0;
      size: auto;
    }

    body {
      margin: 1.5cm;
      /* Print page-er charpashe koto tuku jayga thakbe */
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

    /* 2. Container-ke upore force kora */
    .container {
      padding-top: 0 !important;
      margin-top: 0 !important;
      display: block !important;
    }

    /* 3. Slip card-er position fix kora */
    .receipt-card {
      box-shadow: none;
      border: 1px solid #eee;
      /* Print-e choto border thakle sundor lage */
      width: 100%;
      max-width: 100%;
      padding: 20px;
      /* Padding ektu komiye deya jate kete na jay */
      margin: 0 auto;
      page-break-inside: avoid;
      /* Jate majhkhan diye kete na jay */
    }
  }
</style>

<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

<script>
  function downloadPDF() {
    const element = document.getElementById('printArea');
    const wrapper = document.getElementById("invoice-scale-target");

    // 1. PDF capture korar age temporary-vabe transform bondho kora
    const originalTransform = wrapper ? wrapper.style.transform : "none";
    if(wrapper) wrapper.style.transform = "none";

    const opt = {
        margin: [10, 5, 10, 5], // Top, Left, Bottom, Right margin
        filename: 'Invoice_BRT00{{ $payment->id }}.pdf',
        image: { type: 'jpeg', quality: 1.0 },
        html2canvas: { 
            scale: 2, 
            useCORS: true, 
            logging: false,
            letterRendering: true,
            // Scroll position fix kora jate cut na hoy
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

    // 3. Process start kora
    html2pdf().set(opt).from(element).toPdf().get('pdf').then(function (pdf) {
        // PDF generation sesh hole transform abar ager moto kore deya
        if(wrapper) wrapper.style.transform = originalTransform;
    }).save();
}
</script>
@endsection