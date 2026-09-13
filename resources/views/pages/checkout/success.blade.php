@extends('layouts.userlayout')

@section('content')
<div class="order-success-wrapper">
  <div class="order-success-container">

    <!-- Printable Area for PDF Download -->
    <div id="orderPrintableArea" class="order-printable-area">
      <!-- Success Header Card -->
      <div class="success-card">
        <div class="success-icon-wrap">
          <i class="fas fa-check"></i>
        </div>
        <h1 class="success-title">Alhamdulillah</h1>
        <h2 class="success-title-2">Your Order Placed Successfully!</h2>
        <p class="success-subtitle">
           Thank you for your order. We have received your request and will confirm your delivery shortly.
        </p>

        <div class="order-badge-row">
          <span class="order-id-badge">Order ID: <strong>{{ $order->order_id ?? ('BRS' . $order->id) }}</strong></span>
        </div>
        <p class="order-track-hint">
          <i class="fas fa-info-circle"></i> অর্ডার ট্র্যাক করার জন্য Order ID সংরক্ষণ করুন।
        </p>
      </div>

      <!-- Details Grid -->
      <div class="order-details-grid">
        
        <!-- Delivery Details Card -->
        <div class="details-card">
          <div class="details-card-header">
            <i class="fas fa-map-marker-alt"></i>
            <h3>Delivery Information</h3>
          </div>
          <div class="details-body">
            <div class="detail-row">
              <span class="detail-label">Recipient:</span>
              <span class="detail-value">{{ $order->customer_name }}</span>
            </div>
            <div class="detail-row">
              <span class="detail-label">Phone / WhatsApp:</span>
              <span class="detail-value">{{ $order->customer_phone }}</span>
            </div>
            <div class="detail-row">
              <span class="detail-label">Location:</span>
              <span class="detail-value">{{ $order->city }}, {{ $order->country }}</span>
            </div>
            <div class="detail-row">
              <span class="detail-label">Delivery Address:</span>
              <span class="detail-value">{{ $order->address }}</span>
            </div>
            @if(!empty($order->note))
            <div class="detail-row">
              <span class="detail-label">Order Note:</span>
              <span class="detail-value">{{ $order->note }}</span>
            </div>
            @endif
            <div class="detail-row">
              <span class="detail-label">Payment Method:</span>
              <span class="detail-value badge-cod">{{ $order->payment_method ?? 'Cash on Delivery (COD)' }}</span>
            </div>
          </div>
        </div>

        <!-- Order Items Summary Card -->
        <div class="details-card">
          <div class="details-card-header">
            <i class="fas fa-receipt"></i>
            <h3>Order Summary</h3>
          </div>
          <div class="details-body">
            
            <div class="success-items-list">
              @foreach($order->items as $item)
              <div class="success-item-row">
                <div class="success-item-info">
                  <span class="success-item-name">{{ $item->product->name ?? 'Product' }}</span>
                  <span class="success-item-qty">{{ $item->quantity }} × ৳{{ number_format(round($item->selling_rate - $item->discount_amount), 0) }}</span>
                </div>
                <span class="success-item-price">৳{{ number_format(round($item->net_total), 0) }}</span>
              </div>
              @endforeach
            </div>

            <div class="summary-line-divider"></div>

            <div class="summary-cost-breakdown">
              @php
                $itemsSubtotal = $order->items->sum('net_total');
                $shippingCharge = (float) ($order->shipping_charge ?? 0);
              @endphp
              @if($order->discount_amount > 0)
              <div class="cost-row text-success">
                <span>Offer Savings</span>
                <span>-৳{{ number_format(round($order->discount_amount), 0) }}</span>
              </div>
              @endif
              <div class="cost-row">
                <span>Subtotal</span>
                <span>৳{{ number_format(round($itemsSubtotal), 0) }}</span>
              </div>
              
              <div class="cost-row">
                <span>Shipping Fee</span>
                <span>{{ $shippingCharge > 0 ? '৳' . number_format(round($shippingCharge), 0) : 'FREE 🎉' }}</span>
              </div>
              <div class="summary-line-divider"></div>
              <div class="cost-row total-cost-row">
                <span>Total Payable</span>
                <span>৳{{ number_format(round($order->net_total), 0) }}</span>
              </div>
            </div>

          </div>
        </div>

      </div>
    </div>

    <!-- Actions -->
    <div class="success-actions-row">
      <button type="button" class="btn-download-action" id="btnDownloadPDF" onclick="downloadInvoicePDF()">
        <i class="fas fa-file-pdf"></i>
        <span>Download PDF</span>
      </button>
      <a href="{{ route('home-page') }}" class="btn-primary-action">
        <i class="fas fa-shopping-bag"></i>
        <span>Continue Shopping</span>
      </a>
    </div>

  </div>
</div>

<style>
  .order-success-wrapper {
    background-color: var(--background);
    min-height: 80vh;
    padding: 40px 16px 80px 16px;
    font-family: 'Inter', 'Roboto', sans-serif;
    color: var(--text-main);
  }

  .order-success-container {
    max-width: 920px;
    margin: 0 auto;
  }

  .order-printable-area {
    width: 100%;
  }

  .success-card {
    background: var(--section-bg);
    border: 1px solid var(--border-color);
    border-radius: 20px;
    padding: 20px 24px;
    text-align: center;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
    margin-bottom: 28px;
  }

  .success-icon-wrap {
    width: 72px;
    height: 72px;
    border-radius: 50%;
    background: linear-gradient(135deg, rgba(22, 163, 74, 0.15) 0%, rgba(34, 197, 94, 0.07) 100%);
    color: var(--green, #16a34a);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 30px;
    margin: 4px auto 18px auto;
    border: 2px solid rgba(22, 163, 74, 0.25);
  }

  .success-icon-wrap i {
    animation: checkPop 1.8s ease-in-out infinite;
  }

  @keyframes checkPop {
    0%, 100% { transform: scale(1); }
    45%       { transform: scale(1.22); }
    60%       { transform: scale(0.92); }
    75%       { transform: scale(1.08); }
  }

  .success-title {
    font-size: 30px;
    font-weight: 800;
    color: var(--green);
    margin: 0 0 3px 0;
  }

  .success-title-2 {
    font-size: 20px;
    font-weight: 700;
    color: var(--text-main);
    margin: 0 0 8px 0;
  }

  .success-subtitle {
    font-size: 15px;
    color: var(--text-muted);
    max-width: 580px;
    margin: 0 auto 24px auto;
    line-height: 1.5;
  }

  .order-badge-row {
    display: inline-flex;
    flex-wrap: wrap;
    align-items: center;
    justify-content: center;
    gap: 12px;
  }

  .order-id-badge {
    background: var(--primary-soft);
    color: var(--primary);
    padding: 8px 16px;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 600;
  }

  .order-track-hint {
    font-size: 13px;
    color: var(--text-muted);
    margin: 12px auto 0 auto;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    font-weight: 500;
  }

  .order-track-hint i {
    color: var(--accent);
    font-size: 14px;
  }

  /* Details Grid */
  .order-details-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 24px;
    margin-bottom: 32px;
  }

  .details-card {
    background: var(--section-bg);
    border: 1px solid var(--border-color);
    border-radius: 18px;
    padding: 24px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.02);
  }

  .details-card-header {
    display: flex;
    align-items: center;
    gap: 10px;
    padding-bottom: 14px;
    border-bottom: 1px solid var(--border-color);
    margin-bottom: 18px;
    color: var(--primary);
  }

  .details-card-header h3 {
    font-size: 17px;
    font-weight: 700;
    color: var(--text-main);
    margin: 0;
  }

  .detail-row {
    display: flex;
    flex-direction: column;
    margin-bottom: 12px;
  }

  .detail-label {
    font-size: 12px;
    color: var(--text-muted);
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.3px;
    margin-bottom: 3px;
  }

  .detail-value {
    font-size: 14px;
    color: var(--text-main);
    font-weight: 600;
  }

  .badge-cod {
    display: inline-block;
    background: rgba(22, 163, 74, 0.1);
    color: #16a34a;
    padding: 4px 10px;
    border-radius: 6px;
    font-size: 12.5px;
    width: fit-content;
  }

  /* Items List */
  .success-items-list {
    display: flex;
    flex-direction: column;
    gap: 12px;
    margin-bottom: 16px;
  }

  .success-item-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 13.5px;
  }

  .success-item-info {
    display: flex;
    flex-direction: column;
  }

  .success-item-name {
    font-weight: 600;
    color: var(--text-main);
  }

  .success-item-qty {
    font-size: 12px;
    color: var(--text-muted);
  }

  .success-item-price {
    font-weight: 700;
    color: var(--text-main);
  }

  .summary-line-divider {
    height: 1px;
    background: var(--border-color);
    margin: 14px 0;
  }

  .summary-cost-breakdown {
    display: flex;
    flex-direction: column;
    gap: 10px;
  }

  .cost-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 13.5px;
    color: var(--text-muted);
    font-weight: 500;
  }

  .cost-row.text-success {
    color: #16a34a;
    font-weight: 600;
  }

  .total-cost-row {
    font-size: 17px;
    font-weight: 800;
    color: var(--text-main);
  }

  .success-actions-row {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 16px;
    flex-wrap: wrap;
  }

  .btn-download-action {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 14px 28px;
    background: var(--section-bg);
    color: var(--text-main);
    border: 1.5px solid var(--border-color);
    border-radius: 30px;
    font-size: 15px;
    font-weight: 600;
    cursor: pointer;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.03);
    transition: all 0.2s ease;
  }

  .btn-download-action:hover {
    border-color: var(--primary);
    color: var(--primary);
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.06);
  }

  .btn-download-action:disabled {
    opacity: 0.6;
    cursor: not-allowed;
    transform: none;
  }

  .btn-primary-action {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    padding: 14px 32px;
    background: var(--primary);
    color: #fff;
    border-radius: 30px;
    font-size: 15px;
    font-weight: 700;
    text-decoration: none;
    box-shadow: 0 4px 15px rgba(2, 2, 226, 0.2);
    transition: all 0.2s ease;
  }

  .btn-primary-action:hover {
    filter: brightness(1.1);
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(2, 2, 226, 0.3);
  }

  @media (max-width: 768px) {
    .order-details-grid {
      grid-template-columns: 1fr;
    }
  }
</style>

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
<script>
  // Ensure cart is cleared upon reaching success page
  document.addEventListener('DOMContentLoaded', () => {
    document.cookie = 'dms_cart=; path=/; max-age=0';
    document.cookie = 'dms_applied_coupons=; path=/; max-age=0';
    localStorage.removeItem('dms_cart');
    localStorage.removeItem('dms_applied_coupons');
  });

  function downloadInvoicePDF() {
    const element = document.getElementById('orderPrintableArea');
    const btn = document.getElementById('btnDownloadPDF');
    if (!element) return;

    const originalBtnHtml = btn ? btn.innerHTML : '';
    if (btn) {
      btn.disabled = true;
      btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> <span>Generating PDF...</span>';
    }

    const orderId = "{{ $order->order_id ?? ('BRS' . $order->id) }}";

    const opt = {
      margin: [10, 8, 10, 8],
      filename: `Order_Invoice_${orderId}.pdf`,
      image: { type: 'jpeg', quality: 0.98 },
      html2canvas: {
        scale: 2,
        useCORS: true,
        logging: false,
        letterRendering: true,
        scrollY: 0,
        scrollX: 0
      },
      jsPDF: {
        unit: 'mm',
        format: 'a4',
        orientation: 'portrait'
      }
    };

    html2pdf().set(opt).from(element).save().then(() => {
      if (btn) {
        btn.disabled = false;
        btn.innerHTML = originalBtnHtml;
      }
    }).catch(err => {
      console.error('PDF error:', err);
      if (btn) {
        btn.disabled = false;
        btn.innerHTML = originalBtnHtml;
      }
    });
  }
</script>
@endpush
@endsection
