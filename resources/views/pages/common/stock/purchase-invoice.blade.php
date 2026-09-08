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
    padding: 24px;
    box-sizing: border-box;
    position: relative;
    border-radius: 8px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.05);
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
    position: relative;
  }

  .header h1 {
    margin: 0;
    color: var(--primary);
    font-size: 28px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
  }

  .invoice-type-pill {
    display: inline-block;
    background: #0f172a;
    color: #fff;
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 1.5px;
    padding: 4px 14px;
    border-radius: 20px;
    margin-top: 6px;
    margin-bottom: 6px;
    box-shadow: 0 2px 5px rgba(0,0,0,0.15);
  }

  .info-card {
    border: 1px solid var(--border-color);
    border-radius: 8px;
    padding: 10px 14px;
    font-size: 12px;
    line-height: 1.75;
    background: #fafafa;
  }

  .info-card b {
    color: var(--primary);
    font-size: 13px;
    display: inline-block;
    margin-bottom: 4px;
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
    border: 1px solid #cbd5e1;
    padding: 8px 10px;
    font-size: 13px;
    word-break: break-word;
    white-space: normal;
  }

  .invoice-table td {
    color: #08111b;
  }

  .invoice-table thead th {
    background: #f1f5f9;
    color: #0f172a;
    font-weight: 700;
  }

  .summary-card {
    border: 1px dashed var(--border-color);
    border-radius: 8px;
    padding: 12px;
    background: #fafafa;
  }

  .summary-row {
    display: flex;
    justify-content: space-between;
    padding: 6px 0;
    border-bottom: 1px solid #e2e8f0;
    font-size: 13px;
  }

  .total-payable {
    font-size: 18px;
    font-weight: 700;
    color: var(--primary);
    border-top: 2px solid #cbd5e1;
    margin-top: 4px;
    padding-top: 8px;
  }

  .signature-box {
    border-top: 1px solid #475569;
    text-align: center;
    width: 180px;
    padding-top: 6px;
    margin-top: 45px;
    font-size: 12px;
    color: #334155;
    font-weight: 600;
  }

  .footer-note {
    margin-top: 40px;
    text-align: center;
    font-size: 11px;
    color: #64748b;
    border-top: 1px solid #e2e8f0;
    padding-top: 10px;
    line-height: 1.6;
  }

  @media print {
    .no-print,
    .sidebar-overlay,
    .sidebar,
    .custom-navbar,
    aside,
    nav {
      display: none !important;
    }

    body {
      background: #fff;
    }

    .invoice-wrapper {
      transform: none !important;
      max-width: 100% !important;
      margin: 0 !important;
    }

    .invoice-box {
      box-shadow: none !important;
      padding: 10px !important;
    }
  }

  #printArea {
    height: auto !important;
    overflow: visible !important;
    display: block !important;
    background: #ffffff !important;
    -webkit-print-color-adjust: exact;
    print-color-adjust: exact;
    position: relative;
    z-index: 1;
  }

  .invoice-box > *:not(.watermark-logo) {
    position: relative;
    z-index: 2;
  }

  .watermark-logo {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 60%;
    max-width: 500px;
    opacity: 0.06;
    z-index: 0;
    pointer-events: none;
    user-select: none;
    -webkit-print-color-adjust: exact;
    print-color-adjust: exact;
  }
</style>

<div class="container-fluid py-3" id="wrapper-outer">
  <div class="invoice-wrapper" id="invoice-scale-target">

    <div class="invoice-box" id="printArea">
      
      <!-- Watermark Logo -->
      <img src="{{ $invoiceHeader['company_logo'] }}" class="watermark-logo" alt="Watermark">

      <div class="header">
        <div>
          <img src="{{ $invoiceHeader['company_logo'] }}" alt="Logo" class="img-fluid" style="max-width: 220px; max-height: 65px; object-fit: contain;">
        </div>
        <div>
          <span class="invoice-type-pill"><i class="fas fa-box-open me-1"></i> PURCHASE INVOICE / স্টক-ইন ক্রয় চালান</span>
        </div>
        <!-- <p style="margin-bottom: 2px; font-size: 13px;"><strong>{{ config('app.name', 'R Electric') }}</strong> | {{ $invoiceHeader['subtitle'] }}</p> -->
        <p style="margin-bottom: 0; font-size: 11.5px; color: #475569;">Double Mooring, Chattogram, Bangladesh | Contact: 01871923000</p>
      </div>

      <div class="fixed-row">
        <!-- Supplier Details (Vendor) -->
        <div class="fixed-col-6">
          <div class="info-card">
            <b><i class="fas fa-truck-loading me-1"></i> Supplier / Vendor Details:</b><br>
            <strong>Company:</strong> {{ $stockInRequest->supplier->company_name ?? 'N/A' }}<br>
            <strong>Contact Person:</strong> {{ $stockInRequest->supplier->name ?? 'N/A' }}<br>
            <strong>Phone:</strong> {{ $stockInRequest->supplier->phone ?? 'N/A' }}<br>
            <strong>Address:</strong> {{ $stockInRequest->supplier->address ?? 'N/A' }}
          </div>
        </div>

        <!-- Invoice & Branch Receiving Details -->
        <div class="fixed-col-6">
          <div class="info-card text-end" style="text-align: right;">
            <b><i class="fas fa-receipt me-1"></i> Invoice Details:</b><br>
            <strong>Invoice No:</strong> BRSK{{ $stockInRequest->id }}<br>
            <strong>Date:</strong> {{ $stockInRequest->created_at->timezone(auth()->user()->timezone ?? 'UTC')->format('d M Y, h:i A') }}<br>
            <strong>Receiving Branch:</strong> {{ $stockInRequest->branch->name ?? 'Main' }} Branch<br>
            <strong>Received By:</strong> {{ $stockInRequest->requestedBy->fullname ?? $stockInRequest->requestedBy->name ?? 'N/A' }}<br>
            
          </div>
        </div>
      </div>

      <!-- Items Table -->
      <div class="table-responsive mb-4 mt-3">
        <table class="invoice-table">
          <colgroup>
            @if($hasTreeDeduction)
            <col style="width: 7%;">
            <col style="width: 18%;">
            <col style="width: 31%;">
            <col style="width: 14%;">
            <col style="width: 9%;">
            <col style="width: 9%;">
            <col style="width: 12%;">
            @else
            <col style="width: 8%;">
            <col style="width: 20%;">
            <col style="width: 34%;">
            <col style="width: 16%;">
            <col style="width: 10%;">
            <col style="width: 12%;">
            @endif
          </colgroup>
          <thead>
            <tr>
              <th>S.No</th>
              <th>Category</th>
              <th>Product Description</th>
              <th>Unit Rate</th>
              <th>Qty</th>
              @if($hasTreeDeduction)
              <th>Tree Ded.</th>
              @endif
              <th style="text-align: right;">Total</th>
            </tr>
          </thead>
          <tbody>
            @php $sl = 1; @endphp
            @foreach($items as $item)
            <tr>
              <td style="text-align: center;">{{ $sl++ }}</td>
              <td>{{ $item->product->category->name ?? 'General' }}</td>
              <td><strong>{{ $item->product->name ?? 'N/A' }}</strong></td>
              <td>{{ number_format($item->cost_price, 2) }} ৳</td>
              <td style="text-align: center;">{{ $item->quantity }}</td>
              @if($hasTreeDeduction)
              <td style="text-align: center; color: #0284c7; font-weight: 600;">
                {{ (float)($item->tree_deduction ?? 0) > 0 ? $item->tree_deduction . '%' : '-' }}
              </td>
              @endif
              <td style="text-align: right; font-weight: 600;">{{ number_format($item->total, 2) }} ৳</td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>

      <!-- Summary Section -->
      <div class="fixed-row">
        <!-- Supplier Account Status -->
        <div class="fixed-col-5">
          <div class="summary-card">
            <h6 style="color: #b91c1c; margin-bottom: 8px; font-weight: 700;">
              <i class="fas fa-wallet me-1"></i> Supplier Ledger Status
            </h6>
            <div class="mb-2" style="font-size: 12.5px;">
              Previous Due:
              <strong>{{ number_format($supplierData['previous_due'], 2) }} ৳</strong>
            </div>
            <div class="mb-2" style="font-size: 12.5px;">
              Current Total Due:
              <strong>{{ number_format($supplierData['current_due'], 2) }} ৳</strong>
            </div>
            
          </div>
        </div>

        <!-- Financial Summary -->
        <div class="fixed-col-7">
          <div class="summary-card">
            <div class="summary-table">
              @php
                $grossAmount = $items->sum(fn($i) => (float)($i->cost_price * $i->quantity));
                $deductionSavings = $grossAmount - (float)$stockInRequest->net_total;
              @endphp

              <div class="summary-row">
                <span>Gross Purchase Amount:</span>
                <span>{{ number_format($grossAmount, 2) }} ৳</span>
              </div>

              @if($deductionSavings > 0)
              <div class="summary-row" style="color: #0284c7; font-size: 12.5px;">
                <span>Total Tree Deductions:</span>
                <span>- {{ number_format($deductionSavings, 2) }} ৳</span>
              </div>
              @endif

              <div class="summary-row total-payable">
                <span>Net Purchase Bill:</span>
                <span>{{ number_format($stockInRequest->net_total, 2) }} ৳</span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Signatures Area -->
      <div style="display: flex; justify-content: space-between; margin-top: 45px;">
        <div class="signature-box">Supplier / Carrier Signature</div>
        <div class="signature-box">Store In-Charge / Manager</div>
        <div class="signature-box">Authorized Admin</div>
      </div>

      <!-- Footer Note -->
      <div class="footer-note">
        পণ্য গ্রহণের অফিশিয়াল ক্রয় চালান ও স্টক এন্ট্রি রসিদ </br>
        R Electric Online DMS Platform-এ সংরক্ষিত ও অনুমোদিত স্টক-ইন ভাউচার। </br>
        www.relectricbd.com
      </div>

    </div>
  </div>

  {{-- Action Buttons --}}
  <div class="action-bar d-flex justify-content-center gap-2 no-print mt-4">
    @php
      $backRoute = auth()->user()->role === 'admin' 
        ? route('admin.stock.in.request.show', $stockInRequest->id) 
        : route('manager.stock.in.request.show', $stockInRequest->id);
    @endphp
    <a href="{{ $backRoute }}" class="btn-smart btn-purple me-2">
      <i class="fas fa-arrow-left me-1"></i> Back to Request
    </a>
    <button onclick="downloadPDF()" class="btn-smart btn-green me-2">
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
    if (!wrapper) return;

    const baseWidth = 850;
    const screenWidth = window.innerWidth;

    let scale = screenWidth / baseWidth;
    if (scale > 1) scale = 1; // prevent zoom-in beyond 100%

    wrapper.style.transform = `scale(${scale})`;
    wrapper.style.transformOrigin = "top center";
  }

  window.addEventListener("load", scaleInvoice);
  window.addEventListener("resize", scaleInvoice);

  function printInvoice() {
    window.print();
  }

  function downloadPDF() {
    const element = document.getElementById('printArea');
    const wrapper = document.getElementById("invoice-scale-target");

    // 1. Reset scale temporarily for high quality PDF capture
    const originalTransform = wrapper.style.transform;
    wrapper.style.transform = "none";

    const opt = {
        margin: [2, 1, 2, 3],
        filename: 'Purchase_Invoice_PINV_BRSK{{ $stockInRequest->id }}.pdf',
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

    // 2. Generate and download PDF
    html2pdf().set(opt).from(element).toPdf().get('pdf').then(function (pdf) {
        wrapper.style.transform = originalTransform;
    }).save();
  }
</script>
@endpush
