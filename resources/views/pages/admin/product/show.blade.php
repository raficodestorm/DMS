@extends('layouts.adminlayout')

@section('content')
<style>
  /* ── Page wrapper ── */
  .ps-page {
    max-width: 900px;
    margin: 0 auto;
    padding: 10px 0 40px;
  }

  /* ── Action bar (Back / Edit / Delete) ── */
  .ps-actions {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 16px;
  }

  /* ── Card shell ── */
  .ps-card {
    background: var(--section-bg);
    border: 1px solid var(--border-color);
    border-radius: 16px;
    overflow: hidden;
  }

  /* ── Branded header ── */
  .ps-header {
    background: var(--secondary);
    display: flex;
    align-items: center;
    gap: 14px;
    padding: 14px 24px;
  }

  .ps-header img {
    height: 40px;
    width: auto;
    object-fit: contain;

  }

  .ps-header-divider {
    width: 1px;
    height: 32px;
    background: rgba(255,255,255,0.25);
  }

  .ps-header-title {
    font-size: 0.85rem;
    font-weight: 700;
    color: rgba(255,255,255,0.9);
    letter-spacing: 0.08em;
    text-transform: uppercase;
  }

  /* ── Two-column body ── */
  .ps-body {
    display: grid;
    grid-template-columns: 300px 1fr;
    gap: 0;
  }

  /* ── Left column ── */
  .ps-left {
    border-right: 1px solid var(--border-color);
    padding: 20px;
    display: flex;
    flex-direction: column;
    gap: 12px;
  }

  .ps-main-img {
    width: 100%;
    aspect-ratio: 1 / 1;
    border-radius: 10px;
    border: 1px solid var(--border-color);
    background: var(--background);
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
  }

  .ps-main-img img {
    width: 100%;
    height: 100%;
    object-fit: contain;
  }

  .ps-thumbs {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
  }

  .ps-thumb {
    width: 54px;
    height: 54px;
    border-radius: 7px;
    border: 1.5px solid var(--border-color);
    object-fit: cover;
    cursor: pointer;
    background: var(--background);
    transition: border-color .15s;
  }

  .ps-thumb:hover,
  .ps-thumb.active {
    border-color: var(--primary);
    box-shadow: 0 0 0 2px var(--primary-soft);
  }

  /* ── Barcode box ── */
  .ps-barcode-box {
    background: var(--background);
    border: 1.5px solid var(--border-color);
    border-radius: 10px;
    padding: 10px 12px;
  }

  .ps-barcode-head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    font-size: 0.68rem;
    font-weight: 700;
    color: var(--text-muted);
    text-transform: uppercase;
    letter-spacing: .05em;
    margin-bottom: 8px;
  }

  .ps-barcode-svg {
    background: #ffffff;
    border-radius: 6px;
    padding: 6px 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    min-height: 60px;
    border: 1px solid var(--border-color);
  }

  .ps-barcode-svg svg {
    max-width: 100%;
    height: 52px;
    display: block;
  }

  .btn-copy-bc {
    background: transparent;
    border: 1px solid var(--border-color);
    border-radius: 5px;
    color: var(--text-muted);
    cursor: pointer;
    padding: 2px 7px;
    font-size: 0.68rem;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 3px;
    transition: all .15s;
  }

  .btn-copy-bc:hover {
    color: var(--primary);
    border-color: var(--primary);
    background: var(--primary-soft);
  }

  /* ── Right column ── */
  .ps-right {
    padding: 20px 22px;
    display: flex;
    flex-direction: column;
    gap: 14px;
  }

  /* Badge */
  .ps-badge {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    background: #c9a22720;
    color: #b8860b;
    border: 1px solid #c9a22740;
    border-radius: 5px;
    font-size: 0.65rem;
    font-weight: 700;
    letter-spacing: .07em;
    text-transform: uppercase;
    padding: 3px 9px;
    width: fit-content;
  }

  /* Product name */
  .ps-name {
    margin: 0;
    font-size: 1.5rem;
    font-weight: 800;
    color: var(--text-main);
    line-height: 1.2;
  }

  /* Short desc / category tags */
  .ps-tags {
    font-size: 0.78rem;
    color: var(--text-muted);
  }

  .ps-tag-sep {
    color: var(--border-color);
  }

  /* Price + status banner */
  .ps-price-row {
    background: var(--primary-soft);
    border: 1px solid var(--border-color);
    border-radius: 10px;
    padding: 12px 16px;
    display: flex;
    justify-content: space-between;
    align-items: center;
  }

  .ps-price-label {
    font-size: 0.65rem;
    font-weight: 700;
    color: var(--text-muted);
    text-transform: uppercase;
    letter-spacing: .06em;
    display: block;
    margin-bottom: 2px;
  }

  .ps-price-val {
    font-size: 1.55rem;
    font-weight: 800;
    color: var(--text-main);
  }

  .ps-status-pill {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    font-size: 0.78rem;
    font-weight: 700;
    padding: 5px 12px;
    border-radius: 20px;
  }

  .ps-status-pill.active {
    background: #dcfce7;
    color: #16a34a;
  }

  .ps-status-pill.inactive {
    background: #fee2e2;
    color: #dc2626;
  }

  .ps-status-dot {
    width: 7px;
    height: 7px;
    border-radius: 50%;
    background: currentColor;
  }

  /* 2-col specs grid */
  .ps-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 8px;
  }

  .ps-item {
    background: var(--background);
    border: 1px solid var(--border-color);
    border-radius: 8px;
    padding: 8px 11px;
  }

  .ps-item-icon {
    font-size: 0.8rem;
    color: var(--primary);
    margin-bottom: 4px;
  }

  .ps-item label {
    display: block;
    font-size: 0.63rem;
    font-weight: 700;
    color: var(--text-muted);
    text-transform: uppercase;
    letter-spacing: .05em;
    margin-bottom: 2px;
  }

  .ps-item strong {
    font-size: 0.85rem;
    color: var(--text-main);
    font-weight: 600;
    word-break: break-word;
  }

  /* ── Section Group (header bound to content) ── */
  .ps-section-group {
    margin: 0 24px 12px;
    border: 1px solid var(--border-color);
    border-radius: 10px;
    overflow: hidden;
  }

  .ps-section-header {
    background: var(--secondary);
    padding: 7px 14px;
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 0.7rem;
    font-weight: 700;
    color: rgba(255,255,255,0.9);
    text-transform: uppercase;
    letter-spacing: .07em;
  }

  .ps-section-header i {
    color: #c9a227;
  }

  .ps-section-body {
    padding: 10px 12px;
    background: var(--section-bg);
  }

  /* ── Dimensions ── */
  .ps-dim-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 8px;
  }

  .ps-dim-item {
    background: var(--background);
    border: 1px solid var(--border-color);
    border-radius: 7px;
    padding: 7px 8px 8px;
    text-align: center;
  }

  .ps-dim-icon {
    font-size: 0.95rem;
    color: var(--primary);
    margin-bottom: 3px;
  }

  .ps-dim-item label {
    display: block;
    font-size: 0.58rem;
    font-weight: 700;
    color: var(--text-muted);
    text-transform: uppercase;
    letter-spacing: .05em;
    margin-bottom: 2px;
    cursor: default;
  }

  .ps-dim-item strong {
    font-size: 0.82rem;
    font-weight: 700;
    color: var(--text-main);
  }

  /* ── Long Description ── */
  .ps-desc-body {
    background: var(--background);
    border: 1px solid var(--border-color);
    border-radius: 8px;
    padding: 10px 14px;
    font-size: 0.82rem;
    color: var(--text-main);
    line-height: 1.55;
    white-space: pre-line;
    word-break: break-word;
  }

  /* ── Branded footer ── */
  .ps-footer {
    background: var(--secondary);
    padding: 8px 24px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 12px;
  }

  .ps-footer-items {
    display: flex;
    align-items: center;
    gap: 28px;
    flex-wrap: wrap;
  }

  .ps-footer-item {
    display: flex;
    align-items: center;
    gap: 8px;
  }

  .ps-footer-item-icon {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    border: 1.5px solid rgba(255,255,255,0.25);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.82rem;
    color: rgba(255,255,255,0.85);
  }

  .ps-footer-item-text h5 {
    margin: 0;
    font-size: 0.72rem;
    font-weight: 700;
    color: rgba(255,255,255,0.9);
  }

  .ps-footer-item-text p {
    margin: 0;
    font-size: 0.62rem;
    color: rgba(255,255,255,0.5);
  }

  .ps-footer-logo img {
    height: 38px;
    width: auto;
    object-fit: contain;
    opacity: 0.85;
  }
  .ps-footer-logo {
    display: flex;
    justify-content: right;
    flex-direction: column;
    align-items: center;
  }

  /* ── Responsive ── */
  @media (max-width: 720px) {
    .ps-body { grid-template-columns: 1fr; }
    .ps-left { border-right: none; border-bottom: 1px solid var(--border-color); }
    .ps-dim-grid { grid-template-columns: repeat(2, 1fr); }
  }

  /* ── Print: Fit 1 Single A4 Page ── */
  @page {
    size: A4 portrait;
    margin: 6mm;
  }
  @media print {
    *, *::before, *::after {
      -webkit-print-color-adjust: exact !important;
      print-color-adjust: exact !important;
      color-adjust: exact !important;
    }
    html, body {
      margin: 0 !important;
      padding: 0 !important;
      background: #ffffff !important;
      width: 100% !important;
      height: 100% !important;
    }
    body * {
      visibility: hidden;
    }
    .ps-page, .ps-page * {
      visibility: visible;
    }
    .ps-page {
      position: absolute !important;
      top: 0 !important;
      left: 0 !important;
      width: 100% !important;
      max-width: 100% !important;
      margin: 0 !important;
      padding: 0 !important;
    }
    .ps-actions, nav, .navbar, header, footer, .sidebar, .alert, .btn-copy-bc, .ps-thumbs {
      display: none !important;
    }
    .ps-card {
      position: static !important;
      width: 100% !important;
      max-width: 100% !important;
      margin: 0 !important;
      border: 1px solid var(--border-color) !important;
      border-radius: 12px !important;
      overflow: hidden !important;
      box-shadow: none !important;
      page-break-inside: avoid !important;
      break-inside: avoid !important;
    }
    /* Compact sizing in print so all content fits onto 1 page */
    .ps-header { padding: 8px 18px !important; }
    .ps-header img { height: 30px !important; }
    .ps-left { padding: 10px 14px !important; gap: 6px !important; }
    .ps-main-img { max-height: 190px !important; }
    .ps-right { padding: 10px 14px !important; gap: 7px !important; }
    .ps-name { font-size: 1.2rem !important; }
    .ps-price-row { padding: 6px 12px !important; }
    .ps-price-val { font-size: 1.25rem !important; }
    .ps-grid { gap: 5px !important; }
    .ps-item { padding: 4px 7px !important; }
    .ps-item-icon { font-size: 0.72rem !important; margin-bottom: 2px !important; }
    .ps-item strong { font-size: 0.78rem !important; }
    .ps-section-header { padding: 4px 10px !important; font-size: 0.64rem !important; }
    .ps-dim-icon { font-size: 0.8rem !important; margin-bottom: 2px !important; }
    .ps-dim-item strong { font-size: 0.75rem !important; }
    .ps-desc-body { padding: 6px 10px !important; font-size: 0.72rem !important; line-height: 1.35 !important; }
    .ps-footer { padding: 6px 16px !important; }
    .ps-footer-items { gap: 14px !important; }
    .ps-footer-item-icon { width: 26px !important; height: 26px !important; font-size: 0.7rem !important; }
    .ps-footer-item-text h5 { font-size: 0.64rem !important; }
    .ps-footer-item-text p { font-size: 0.54rem !important; }
    .ps-footer-logo img { height: 28px !important; }
  }
</style>

<div class="ps-page">

  {{-- Action bar --}}
  <div class="ps-actions">
    <a href="{{ route('admin.products.index') }}" class="btn-smart btn-gray">
      <i class="fas fa-arrow-left"></i> Back
    </a>
    <a href="{{ route('admin.products.edit', $product) }}" class="btn-smart btn-blue">
      <i class="fas fa-edit"></i> Edit
    </a>
    <form action="{{ route('admin.products.destroy', $product) }}" method="POST" class="d-inline"
          onsubmit="return confirm('Are you sure you want to delete this product?')">
      @csrf
      @method('DELETE')
      <button type="submit" class="btn-smart btn-red">
        <i class="fas fa-trash"></i> Delete
      </button>
    </form>
    <button id="btnDownloadPdf" onclick="downloadPdf()" class="btn-smart btn-green">
      <i class="fas fa-file-pdf"></i> Download PDF
    </button>
    <button onclick="window.print()" class="btn-smart btn-purple" >
      <i class="fas fa-print"></i> Print
    </button>
  </div>

  @include('components.alert')

  <div class="ps-card">

    {{-- Branded Header --}}
    <div class="ps-header">
      <img src="{{ asset('image/relectric-logo.png') }}" alt="Logo">
      <div class="ps-header-divider"></div>
      <span class="ps-header-title">Product Information</span>
    </div>

    {{-- Two-column Body --}}
    <div class="ps-body">

      {{-- Left: Image Gallery + Barcode --}}
      <div class="ps-left">
        @php
          $mainImg = $product->image
            ? (str_starts_with($product->image, 'uploads/') ? asset($product->image) : asset('uploads/' . $product->image))
            : 'https://ui-avatars.com/api/?name='.urlencode($product->name).'&background=3131ff&color=fff&size=300';
        @endphp

        <div class="ps-main-img">
          <img id="psMainImg" src="{{ $mainImg }}" alt="{{ $product->name }}">
        </div>

        @if($product->image || $product->images->count() > 0)
        <div class="ps-thumbs">
          @if($product->image)
            <img src="{{ $mainImg }}" class="ps-thumb active" onclick="switchImg(this.src, this)" alt="Main">
          @endif
          @foreach($product->images as $img)
            @php $tUrl = str_starts_with($img->image, 'uploads/') ? asset($img->image) : asset('uploads/' . $img->image); @endphp
            <img src="{{ $tUrl }}" class="ps-thumb" onclick="switchImg(this.src, this)" alt="Gallery">
          @endforeach
        </div>
        @endif

        {{-- Barcode --}}
        @if($product->barcode)
        @php
          $barcodeSvg = app(\App\Services\BarcodeService::class)->generateBarcodeSvg($product->barcode, 46, true);
        @endphp
        <div class="ps-barcode-box">
          <div class="ps-barcode-head">
            <span><i class="fas fa-barcode"></i> Product Barcode</span>
            <button type="button" class="btn-copy-bc" onclick="copyBarcode('{{ $product->barcode }}', this)">
              <i class="fas fa-copy"></i> Copy
            </button>
          </div>
          <div class="ps-barcode-svg">{!! $barcodeSvg !!}</div>
        </div>
        @endif
      </div>

      {{-- Right: Info --}}
      <div class="ps-right">

        <span class="ps-badge"><i class="fas fa-crown"></i> Premium Series</span>

        <h2 class="ps-name">{{ $product->name }}</h2>

        <div class="ps-tags">
          @if($product->short_description)
            <span>{{ $product->short_description }}</span>
          @endif
          
        </div>

        {{-- Price & Status --}}
        <div class="ps-price-row">
          <div>
            <span class="ps-price-label">Price</span>
            <div class="ps-price-val">৳ {{ number_format($product->price, 2) }}</div>
          </div>
          <div>
            @if($product->status == 1)
              <span class="ps-status-pill active"><span class="ps-status-dot"></span> Active</span>
            @else
              <span class="ps-status-pill inactive"><span class="ps-status-dot"></span> Inactive</span>
            @endif
          </div>
        </div>

        {{-- Specs Grid --}}
        <div class="ps-grid">
          <div class="ps-item">
            <div class="ps-item-icon"><i class="fas fa-tag"></i>   Category</div>
            
            <strong>{{ $product->category->name ?? '-' }}</strong>
          </div>
          <div class="ps-item">
            <div class="ps-item-icon"><i class="fas fa-cog"></i>   SKU</div>
            
            <strong>{{ $product->sku ?? '-' }}</strong>
          </div>
          <div class="ps-item">
            <div class="ps-item-icon"><i class="fas fa-barcode"></i>   Barcode</div>
            
            <strong style="font-family:monospace;">{{ $product->barcode ?? '-' }}</strong>
          </div>
          <div class="ps-item">
            <div class="ps-item-icon"><i class="fas fa-building"></i>   Brand</div>
            
            <strong>{{ $product->supplier->company_name ?? '-' }}</strong>
          </div>
          <div class="ps-item">
            <div class="ps-item-icon"><i class="fas fa-bell"></i>   Stock Alert</div>
            
            <strong>{{ $product->stock_alert }} units</strong>
          </div>
          <div class="ps-item">
            <div class="ps-item-icon"><i class="fas fa-star"></i>   Featured</div>
            
            <strong>{{ $product->is_featured ? 'Yes' : 'No' }}</strong>
          </div>
        </div>

      </div>{{-- /ps-right --}}
    </div>{{-- /ps-body --}}

    {{-- Product Dimensions (Full-Width) --}}
    <div class="ps-section-group">
      <div class="ps-section-header">
        <i class="fas fa-cube"></i> Product Dimensions
      </div>
      <div class="ps-section-body">
        <div class="ps-dim-grid">
          <div class="ps-dim-item">
            <div class="ps-dim-icon"><i class="fas fa-weight-hanging"></i></div>
            <label>Weight</label>
            <strong>{{ $product->weight ? $product->weight.' kg' : '—' }}</strong>
          </div>
          <div class="ps-dim-item">
            <div class="ps-dim-icon"><i class="fas fa-arrows-alt-h"></i></div>
            <label>Length</label>
            <strong>{{ $product->length ? $product->length.' cm' : '—' }}</strong>
          </div>
          <div class="ps-dim-item">
            <div class="ps-dim-icon"><i class="fas fa-expand-alt"></i></div>
            <label>Width</label>
            <strong>{{ $product->width ? $product->width.' cm' : '—' }}</strong>
          </div>
          <div class="ps-dim-item">
            <div class="ps-dim-icon"><i class="fas fa-arrows-alt-v"></i></div>
            <label>Height</label>
            <strong>{{ $product->height ? $product->height.' cm' : '—' }}</strong>
          </div>
        </div>
      </div>
    </div>

    

    {{-- Long Description --}}
    @if($product->long_description)
    <div class="ps-section-group">
      <div class="ps-section-header">
        <i class="fas fa-file-alt"></i> Long Description
      </div>
      <div class="ps-section-body">
        <div class="ps-desc-body">{!! nl2br(e($product->long_description)) !!}</div>
      </div>
    </div>
    @endif

    {{-- Branded Footer (Static) --}}
    <div class="ps-footer">
      <div class="ps-footer-items">
        <div class="ps-footer-item">
          <div class="ps-footer-item-icon"><i class="fas fa-check-circle"></i></div>
          <div class="ps-footer-item-text">
            <h5>Original Product</h5>
            <p>100% Authentic</p>
          </div>
        </div>
        <div class="ps-footer-item">
          <div class="ps-footer-item-icon"><i class="fas fa-headset"></i></div>
          <div class="ps-footer-item-text">
            <h5>Trusted Support</h5>
            <p>Always With You</p>
          </div>
        </div>
        <div class="ps-footer-item">
          <div class="ps-footer-item-icon"><i class="fas fa-medal"></i></div>
          <div class="ps-footer-item-text">
            <h5>Quality Guaranteed</h5>
            <p>Built to Last</p>
          </div>
        </div>
      </div>
      <div class="ps-footer-logo">
        <img src="{{ asset('image/relectric-logo.png') }}" alt="Logo">
        <small style="color: var(--text-muted); font-size: 12px; margin-top: -5px;">www.relectricbd.com</small>
      </div>
    </div>

  </div>{{-- /ps-card --}}
</div>{{-- /ps-page --}}

<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script>
  function switchImg(src, el) {
    document.getElementById('psMainImg').src = src;
    document.querySelectorAll('.ps-thumb').forEach(t => t.classList.remove('active'));
    el.classList.add('active');
  }

  function copyBarcode(text, btn) {
    if (navigator.clipboard) {
      navigator.clipboard.writeText(text).then(() => {
        const orig = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-check" style="color:#16a34a;"></i> Copied!';
        setTimeout(() => { btn.innerHTML = orig; }, 1800);
      });
    }
  }

  async function downloadPdf() {
    const btn = document.getElementById('btnDownloadPdf');
    const orig = btn.innerHTML;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Generating...';
    btn.disabled = true;

    try {
      const card = document.querySelector('.ps-card');

      // High resolution capture
      const canvas = await html2canvas(card, {
        scale: 2,
        useCORS: true,
        allowTaint: true,
        backgroundColor: '#ffffff',
        logging: false,
      });

      const imgData = canvas.toDataURL('image/png', 1.0);

      const { jsPDF } = window.jspdf;
      // Single A4 portrait PDF (210 x 297 mm)
      const pdf = new jsPDF({
        orientation: 'portrait',
        unit: 'mm',
        format: 'a4',
        compress: true
      });

      const pdfW = 210;
      const pdfH = 297;
      const marginX = 8; // 8mm side margin
      const marginY = 8; // 8mm top & bottom margin

      const usableW = pdfW - (marginX * 2);
      const usableH = pdfH - (marginY * 2);

      // Fit entire card onto 1 single A4 page inside the margins
      const imgRatio = canvas.width / canvas.height;
      const pageRatio = usableW / usableH;

      let renderW, renderH, posX, posY;

      if (imgRatio < pageRatio) {
        // Taller than usable area ratio -> fit to usable height
        renderH = usableH;
        renderW = usableH * imgRatio;
        posX = marginX + (usableW - renderW) / 2;
        posY = marginY;
      } else {
        // Wider than usable area ratio -> fit to usable width
        renderW = usableW;
        renderH = usableW / imgRatio;
        posX = marginX;
        posY = marginY + (usableH - renderH) / 2;
      }

      pdf.addImage(imgData, 'PNG', posX, posY, renderW, renderH, undefined, 'FAST');

      const name = @json(Str::slug($product->name ?: 'product'));
      pdf.save(name + '-details.pdf');

    } catch (e) {
      console.error('PDF Generation Error:', e);
      alert('PDF generation failed. Please try again.');
    }

    btn.innerHTML = orig;
    btn.disabled = false;
  }
</script>
@endsection