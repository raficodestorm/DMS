@extends('layouts.adminlayout')

@section('content')
<style>
  .pc-page {
    max-width: 820px;
    margin: 0 auto;
    padding: 8px 0 48px;
  }

  /* ── Header ─────────────────────────────────── */
  .pc-page-header {
    display: flex;
    align-items: center;
    gap: 14px;
    margin-bottom: 24px;
  }

  .pc-page-header .hdr-icon {
    width: 46px; height: 46px;
    border-radius: 13px;
    background: linear-gradient(135deg, #6366f1, #818cf8);
    display: flex; align-items: center; justify-content: center;
    color: #fff; font-size: 19px;
    box-shadow: 0 6px 16px rgba(99,102,241,.28);
    flex-shrink: 0;
  }

  .pc-page-header .hdr-text h2 {
    margin: 0;
    font-size: 1.25rem;
    font-weight: 700;
    color: var(--text-main);
  }

  .pc-page-header .hdr-text p {
    margin: 3px 0 0;
    font-size: 0.78rem;
    color: var(--text-muted);
  }

  /* ── Card sections ───────────────────────────── */
  .pc-card {
    background: var(--section-bg);
    border: 1px solid var(--border-color);
    border-radius: 16px;
    padding: 0;
    margin-bottom: 14px;
    overflow: hidden;
  }

  .pc-card-head {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 13px 20px;
    border-bottom: 1px solid var(--border-color);
    background: var(--primary-soft);
  }

  .pc-card-head i {
    font-size: 13px;
    color: var(--primary);
    width: 18px;
    text-align: center;
  }

  .pc-card-head span {
    font-size: 0.7rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .08em;
    color: var(--text-main);
  }

  .pc-card-body {
    padding: 20px;
  }

  /* ── Grid helpers ────────────────────────────── */
  .g2 { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
  .g3 { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 14px; }

  /* ── Field ───────────────────────────────────── */
  .f-label {
    display: block;
    font-size: 0.75rem;
    font-weight: 600;
    color: var(--text-muted);
    margin-bottom: 6px;
  }

  .f-required { color: #ef4444; margin-left: 2px; }

  .f-ctrl {
    width: 100%;
    padding: 9px 12px;
    border: 1.5px solid var(--border-color);
    border-radius: 9px;
    background: var(--background);
    color: var(--text-main);
    font-size: 0.875rem;
    box-sizing: border-box;
    transition: border-color .18s, box-shadow .18s;
    font-family: inherit;
  }

  .f-ctrl:focus {
    outline: none;
    border-color: var(--primary);
    box-shadow: 0 0 0 3px rgba(99,102,241,.13);
  }

  .f-ctrl::placeholder { color: var(--text-muted); opacity: .7; }
  textarea.f-ctrl { resize: vertical; min-height: 88px; }
  .f-err { color: #ef4444; font-size: .72rem; margin-top: 4px; }

  /* ── Toggle switch (Featured) ────────────────── */
  .toggle-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 9px 14px;
    border: 1.5px solid var(--border-color);
    border-radius: 9px;
    background: var(--background);
    cursor: pointer;
    transition: border-color .18s;
    height: 38px;
    box-sizing: border-box;
  }

  .toggle-row:hover { border-color: var(--primary); }

  .toggle-row .t-label {
    font-size: 0.82rem;
    font-weight: 600;
    color: var(--text-main);
  }

  .toggle-row input[type="checkbox"] { display: none; }

  .t-switch {
    position: relative;
    width: 36px; height: 20px;
    background: var(--border-color);
    border-radius: 20px;
    transition: background .2s;
    flex-shrink: 0;
  }

  .t-switch::after {
    content: '';
    position: absolute;
    top: 3px; left: 3px;
    width: 14px; height: 14px;
    border-radius: 50%;
    background: #fff;
    transition: left .2s;
    box-shadow: 0 1px 3px rgba(0,0,0,.2);
  }

  .toggle-row input:checked ~ .t-switch { background: #6366f1; }
  .toggle-row input:checked ~ .t-switch::after { left: 19px; }

  /* ── Main image upload ───────────────────────── */
  .main-upload-row {
    display: flex;
    align-items: center;
    gap: 14px;
  }

  .main-upload-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 9px 18px;
    border: 1.5px dashed var(--border-color);
    border-radius: 9px;
    background: var(--background);
    color: var(--text-muted);
    font-size: 0.82rem;
    font-weight: 600;
    cursor: pointer;
    transition: border-color .18s, color .18s, background .18s;
    flex-shrink: 0;
    white-space: nowrap;
  }

  .main-upload-btn:hover {
    border-color: #6366f1;
    color: #6366f1;
    background: var(--primary-soft);
  }

  /* Thumbnail box */
  .main-thumb-box {
    position: relative;
    width: 88px;
    height: 88px;
    border-radius: 10px;
    overflow: hidden;
    border: 2px solid #6366f1;
    display: none;
    flex-shrink: 0;
  }

  .main-thumb-box img {
    width: 100%; height: 100%;
    object-fit: cover;
    display: block;
  }

  .main-thumb-box .mtb-actions {
    position: absolute;
    inset: 0;
    background: rgba(0,0,0,.45);
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    opacity: 0;
    transition: opacity .18s;
  }

  .main-thumb-box:hover .mtb-actions { opacity: 1; }

  .mtb-btn {
    width: 28px; height: 28px;
    border-radius: 50%;
    border: none;
    cursor: pointer;
    display: flex; align-items: center; justify-content: center;
    font-size: 11px;
  }

  .mtb-btn-change { background: #6366f1; color: #fff; }
  .mtb-btn-remove { background: #ef4444; color: #fff; }

  /* ── Gallery ─────────────────────────────────── */
  .gallery-add-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 9px 18px;
    border: 1.5px dashed var(--border-color);
    border-radius: 9px;
    background: var(--background);
    color: var(--text-muted);
    font-size: 0.82rem;
    font-weight: 600;
    cursor: pointer;
    transition: border-color .18s, color .18s, background .18s;
  }

  .gallery-add-btn:hover {
    border-color: #6366f1;
    color: #6366f1;
    background: var(--primary-soft);
  }

  .gallery-grid {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    margin-top: 10px;
  }

  .gallery-thumb {
    position: relative;
    width: 72px; height: 72px;
    border-radius: 9px;
    overflow: hidden;
    border: 1.5px solid var(--border-color);
    flex-shrink: 0;
  }

  .gallery-thumb img {
    width: 100%; height: 100%;
    object-fit: cover;
    display: block;
  }

  .gallery-thumb .gt-remove {
    position: absolute;
    top: 3px; right: 3px;
    width: 18px; height: 18px;
    border-radius: 50%;
    background: rgba(220,38,38,.9);
    color: #fff;
    border: none;
    cursor: pointer;
    display: flex; align-items: center; justify-content: center;
    font-size: 9px;
    opacity: 0;
    transition: opacity .15s;
  }

  .gallery-thumb:hover .gt-remove { opacity: 1; }

  /* ── Footer actions ───────────────────────────── */
  .pc-footer {
    display: flex;
    justify-content: flex-end;
    gap: 10px;
    margin-top: 4px;
  }

  @media (max-width: 620px) {
    .g2, .g3 { grid-template-columns: 1fr; }
    .pc-footer { flex-direction: column-reverse; }
  }
</style>

<div class="pc-page">

  {{-- Header --}}
  <div class="pc-page-header">
    <div class="hdr-icon"><i class="fas fa-box-open"></i></div>
    <div class="hdr-text">
      <h2>Add New Product</h2>
      <p>Fill in the details below to create a new product listing</p>
    </div>
  </div>

  @include('components.alert')

  <form method="POST" action="{{ route('admin.products.store') }}" enctype="multipart/form-data" id="productForm">
    @csrf

    {{-- ① Basic Info --}}
    <div class="pc-card">
      <div class="pc-card-head">
        <i class="fas fa-tag"></i>
        <span>Basic Information</span>
      </div>
      <div class="pc-card-body">
        <div class="g2" style="margin-bottom:14px;">
          <div>
            <label class="f-label">Product Name <span class="f-required">*</span></label>
            <input class="f-ctrl" type="text" name="name" value="{{ old('name') }}" placeholder="e.g. TW1G1S" required>
            @error('name')<div class="f-err">{{ $message }}</div>@enderror
          </div>
          <div>
            <label class="f-label">SKU <span class="f-required">*</span></label>
            <input class="f-ctrl" type="text" name="sku" value="{{ old('sku') }}" placeholder="Unique product code" required>
            @error('sku')<div class="f-err">{{ $message }}</div>@enderror
          </div>
        </div>
        <div class="g2">
          <div>
            <label class="f-label">Category</label>
            <select class="f-ctrl" name="category_id">
              <option value="">— Select Category —</option>
              @foreach($categories as $cat)
              <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
              @endforeach
            </select>
          </div>
          <div>
            <label class="f-label">Supplier <span class="f-required">*</span></label>
            <select class="f-ctrl" name="supplier_id" required>
              <option value="">— Select Supplier —</option>
              @foreach($suppliers as $sup)
              <option value="{{ $sup->id }}" {{ old('supplier_id') == $sup->id ? 'selected' : '' }}>{{ $sup->company_name }}</option>
              @endforeach
            </select>
            @error('supplier_id')<div class="f-err">{{ $message }}</div>@enderror
          </div>
        </div>
      </div>
    </div>

    {{-- ② Pricing & Stock --}}
    <div class="pc-card">
      <div class="pc-card-head">
        <i class="fas fa-coins"></i>
        <span>Pricing & Stock</span>
      </div>
      <div class="pc-card-body">
        <div class="g2">
          <div>
            <label class="f-label">Selling Price (৳) <span class="f-required">*</span></label>
            <input class="f-ctrl" type="number" step="0.01" min="0" name="price" value="{{ old('price') }}" placeholder="0.00" required>
            @error('price')<div class="f-err">{{ $message }}</div>@enderror
          </div>
          <div>
            <label class="f-label">Stock Alert Quantity <span class="f-required">*</span></label>
            <input class="f-ctrl" type="number" min="0" name="stock_alert" value="{{ old('stock_alert', 0) }}" required>
            @error('stock_alert')<div class="f-err">{{ $message }}</div>@enderror
          </div>
        </div>

        <div style="margin-top: 14px;">
          <label class="f-label">Featured Product</label>
          <label class="toggle-row" for="is_featured_chk">
            <span class="t-label">Mark this product as featured</span>
            <input type="checkbox" id="is_featured_chk" name="is_featured" value="1" {{ old('is_featured') ? 'checked' : '' }}>
            <div class="t-switch"></div>
          </label>
        </div>
      </div>
    </div>

    {{-- ③ Description --}}
    <div class="pc-card">
      <div class="pc-card-head">
        <i class="fas fa-align-left"></i>
        <span>Description</span>
      </div>
      <div class="pc-card-body" style="display:flex; flex-direction:column; gap:14px;">
        <div>
          <label class="f-label">Short Description</label>
          <input class="f-ctrl" type="text" name="short_description" value="{{ old('short_description') }}" placeholder="Brief one-line summary (max 500 chars)">
          @error('short_description')<div class="f-err">{{ $message }}</div>@enderror
        </div>
        <div>
          <label class="f-label">Long Description</label>
          <textarea class="f-ctrl" name="long_description" placeholder="Detailed product description...">{{ old('long_description') }}</textarea>
          @error('long_description')<div class="f-err">{{ $message }}</div>@enderror
        </div>
      </div>
    </div>

    {{-- ④ Images --}}
    <div class="pc-card">
      <div class="pc-card-head">
        <i class="fas fa-images"></i>
        <span>Product Images</span>
      </div>
      <div class="pc-card-body">
        <div class="g2" style="align-items: start;">

          {{-- Main thumbnail --}}
          <div>
            <label class="f-label">Main Thumbnail</label>
            {{-- Hidden real input --}}
            <input type="file" name="image" id="mainInput" accept="image/*" style="display:none;">
            <div class="main-upload-row" style="flex-wrap:wrap; gap:10px;">
              <button type="button" class="main-upload-btn" id="mainTrigger">
                <i class="fas fa-cloud-upload-alt"></i> Choose Image
              </button>
              <div class="main-thumb-box" id="mainThumbBox">
                <img id="mainImg" src="" alt="">
                <div class="mtb-actions">
                  <button type="button" class="mtb-btn mtb-btn-change" id="mainChange" title="Change">
                    <i class="fas fa-pencil-alt"></i>
                  </button>
                  <button type="button" class="mtb-btn mtb-btn-remove" id="mainRemove" title="Remove">
                    <i class="fas fa-trash"></i>
                  </button>
                </div>
              </div>
            </div>
            <div style="margin-top:6px; font-size:0.72rem; color:var(--text-muted);">PNG, JPG, WEBP — max 2MB</div>
            @error('image')<div class="f-err" style="margin-top:4px;">{{ $message }}</div>@enderror
          </div>

          {{-- Gallery --}}
          <div>
            <label class="f-label">Gallery <small style="font-weight:400; color:var(--text-muted);">(optional, multiple)</small></label>
            {{-- Hidden accumulator input --}}
            <input type="file" name="gallery[]" id="galleryInput" accept="image/*" multiple style="display:none;">
            <button type="button" class="gallery-add-btn" id="galleryTrigger">
              <i class="fas fa-plus"></i> Add Images
            </button>
            
            <div class="gallery-grid" id="galleryPreview"></div>
            @error('gallery.*')<div class="f-err" style="margin-top:4px;">{{ $message }}</div>@enderror
          </div>

        </div>
      </div>
    </div>

    {{-- Footer --}}
    <div class="pc-footer">
      <a href="{{ route('admin.products.index') }}" class="btn-smart btn-gray">
        <i class="fas fa-times"></i> Cancel
      </a>
      <button type="submit" class="btn-smart btn-blue">
        <i class="fas fa-check"></i> Save Product
      </button>
    </div>

  </form>
</div>
@endsection

@push('scripts')
<script>
(function () {
  /* ── Main image ─────────────────────────────── */
  const mainInput    = document.getElementById('mainInput');
  const mainImg      = document.getElementById('mainImg');
  const mainThumbBox = document.getElementById('mainThumbBox');

  document.getElementById('mainTrigger').addEventListener('click', () => mainInput.click());

  mainInput.addEventListener('change', function () {
    const file = this.files[0];
    if (!file) return;
    const reader = new FileReader();
    reader.onload = e => {
      mainImg.src = e.target.result;
      mainThumbBox.style.display = 'block';
    };
    reader.readAsDataURL(file);
  });

  document.getElementById('mainChange').addEventListener('click', function (e) {
    e.stopPropagation();
    mainInput.click();
  });

  document.getElementById('mainRemove').addEventListener('click', function (e) {
    e.stopPropagation();
    mainInput.value = '';
    mainImg.src = '';
    mainThumbBox.style.display = 'none';
  });

  /* ── Gallery — accumulate via plain array ── */
  const galleryInput   = document.getElementById('galleryInput');
  const galleryPreview = document.getElementById('galleryPreview');
  let   fileList       = []; // plain JS array — avoids DataTransfer assignment bugs

  document.getElementById('galleryTrigger').addEventListener('click', () => galleryInput.click());

  galleryInput.addEventListener('change', function () {
    // Merge new picks into fileList (skip duplicates by name+size)
    Array.from(this.files).forEach(file => {
      const dup = fileList.some(f => f.name === file.name && f.size === file.size);
      if (!dup) fileList.push(file);
    });

    // Sync fileList → galleryInput via DataTransfer
    syncInputFiles();

    renderGallery();
  });

  function syncInputFiles() {
    const dt = new DataTransfer();
    fileList.forEach(f => dt.items.add(f));
    galleryInput.files = dt.files;
  }

  function renderGallery() {
    galleryPreview.innerHTML = '';
    fileList.forEach((file, idx) => {
      if (!file.type.startsWith('image/')) return;

      const wrap = document.createElement('div');
      wrap.className = 'gallery-thumb';

      const img = document.createElement('img');
      const reader = new FileReader();
      reader.onload = e => { img.src = e.target.result; };
      reader.readAsDataURL(file);
      wrap.appendChild(img);

      const btn = document.createElement('button');
      btn.type = 'button';
      btn.className = 'gt-remove';
      btn.title = 'Remove';
      btn.innerHTML = '✕';
      btn.addEventListener('click', () => {
        fileList.splice(idx, 1);
        syncInputFiles();
        renderGallery();
      });
      wrap.appendChild(btn);

      galleryPreview.appendChild(wrap);
    });
  }
})();
</script>
@endpush