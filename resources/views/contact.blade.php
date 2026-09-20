@extends('layouts.userlayout')

@section('title', 'Contact Us — ' . config('app.name'))

@section('content')
<style>
/* ===== CONTACT PAGE — SMART & MINIMAL ===== */
.contact-wrap { padding: 40px 0 70px; }

/* Hero */
.ct-hero {
  background: var(--section-bg);
  border: 1px solid var(--border-color);
  border-radius: 20px;
  padding: 44px 36px;
  margin-bottom: 28px;
  position: relative;
  overflow: hidden;
}
.ct-hero::after {
  content: '';
  position: absolute;
  width: 280px; height: 280px;
  background: radial-gradient(circle, rgba(2,2,226,.1), transparent 70%);
  top: -80px; right: -80px;
  border-radius: 50%;
  pointer-events: none;
}
.ct-badge {
  display: inline-flex; align-items: center; gap: 7px;
  background: var(--primary-soft); color: var(--primary);
  padding: 5px 14px; border-radius: 30px;
  font-size: 12px; font-weight: 700;
  text-transform: uppercase; letter-spacing: .6px;
  margin-bottom: 14px;
}
.ct-title {
  font-size: clamp(26px, 4vw, 42px);
  font-weight: 900;
  font-family: "El Messiri", sans-serif;
  color: var(--text-main);
  line-height: 1.18;
  margin-bottom: 12px;
}
.ct-title span {
  background: linear-gradient(135deg, var(--primary), var(--accent));
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
}
.ct-desc { font-size: 14.5px; color: var(--text-muted); line-height: 1.8; max-width: 660px; margin: 0; }

/* Quick Action Cards Grid */
.ct-quick-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 16px;
  margin-bottom: 28px;
}
.ct-quick-card {
  background: var(--section-bg);
  border: 1px solid var(--border-color);
  border-radius: 16px;
  padding: 22px 20px;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
  transition: transform .25s, box-shadow .25s, border-color .25s;
  text-decoration: none;
  color: inherit;
  position: relative;
  overflow: hidden;
}
.ct-quick-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 10px 24px rgba(0,0,0,.06);
  border-color: var(--primary);
  color: inherit;
}
.ct-icon-box {
  width: 46px; height: 46px;
  border-radius: 12px;
  display: flex; align-items: center; justify-content: center;
  font-size: 20px;
  margin-bottom: 14px;
  background: var(--primary-soft);
  color: var(--primary);
  transition: transform .25s;
}
.ct-quick-card:hover .ct-icon-box { transform: scale(1.08); }
.ct-icon-box.wa-icon { background: rgba(37, 211, 102, 0.14); color: #25d366; }
.ct-icon-box.mail-icon { background: rgba(234, 67, 53, 0.12); color: #ea4335; }
.ct-icon-box.map-icon { background: rgba(66, 133, 244, 0.14); color: #4285f4; }

.ct-quick-label { font-size: 11.5px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: .5px; margin-bottom: 4px; }
.ct-quick-val { font-size: 15px; font-weight: 800; color: var(--text-main); margin-bottom: 4px; word-break: break-word; }
.ct-quick-sub { font-size: 12.5px; color: var(--text-muted); margin-bottom: 14px; line-height: 1.4; }
.ct-action-btn {
  display: inline-flex; align-items: center; gap: 6px;
  font-size: 12.5px; font-weight: 700; color: var(--primary);
  margin-top: auto;
}
.ct-quick-card:hover .ct-action-btn { text-decoration: underline; }

/* Section Headings */
.ct-section-head { margin-bottom: 20px; }
.ct-section-head h2 { font-size: clamp(20px, 2.5vw, 26px); font-weight: 800; color: var(--text-main); font-family: "El Messiri", sans-serif; margin-bottom: 6px; }
.ct-section-head p { font-size: 13.5px; color: var(--text-muted); line-height: 1.6; max-width: 600px; margin: 0; }

/* Department Info Cards */
.ct-dept-card {
  background: var(--section-bg);
  border: 1px solid var(--border-color);
  border-radius: 16px;
  padding: 24px;
  height: 100%;
  transition: transform .25s, box-shadow .25s;
  border-top: 3px solid var(--primary);
}
.ct-dept-card:hover { transform: translateY(-4px); box-shadow: 0 10px 24px rgba(0,0,0,.06); }
.ct-dept-header { display: flex; align-items: center; gap: 12px; margin-bottom: 14px; }
.ct-dept-icon {
  width: 42px; height: 42px;
  background: var(--primary-soft);
  color: var(--primary);
  border-radius: 10px;
  display: flex; align-items: center; justify-content: center;
  font-size: 17px;
  flex-shrink: 0;
}
.ct-dept-title { font-size: 16px; font-weight: 800; color: var(--text-main); margin: 0; }
.ct-dept-desc { font-size: 13px; color: var(--text-muted); line-height: 1.6; margin-bottom: 16px; }

.ct-item-list { list-style: none; padding: 0; margin: 0; display: flex; flex-direction: column; gap: 10px; }
.ct-item-list li {
  display: flex; align-items: flex-start; gap: 10px;
  font-size: 13px; color: var(--text-main);
  background: var(--background);
  padding: 10px 12px; border-radius: 10px;
  border: 1px solid var(--border-color);
}
.ct-item-list li i { color: var(--primary); font-size: 13px; margin-top: 3px; flex-shrink: 0; }
.ct-item-list li strong { color: var(--text-main); font-weight: 700; }
.ct-item-list li a { color: var(--primary); text-decoration: none; font-weight: 600; word-break: break-all; }
.ct-item-list li a:hover { text-decoration: underline; }

/* Map & Location Block */
.ct-map-card {
  background: var(--section-bg);
  border: 1px solid var(--border-color);
  border-radius: 18px;
  padding: 24px;
  margin-top: 28px;
  overflow: hidden;
}
.ct-map-iframe {
  width: 100%;
  height: 320px;
  border: 0;
  border-radius: 12px;
  display: block;
}

/* Bottom Wholesale CTA */
.ct-cta {
  background: linear-gradient(135deg, var(--primary), var(--accent));
  border-radius: 18px;
  padding: 36px 28px;
  text-align: center;
  margin-top: 28px;
  position: relative;
  overflow: hidden;
}
.ct-cta::before {
  content: '';
  position: absolute;
  width: 200px; height: 200px;
  background: rgba(255,255,255,.07);
  border-radius: 50%;
  top: -60px; left: -60px;
}
.ct-cta h3 { font-size: clamp(20px, 3vw, 28px); font-weight: 900; color: #fff; font-family: "El Messiri", sans-serif; margin-bottom: 8px; position: relative; z-index: 1; }
.ct-cta p { font-size: 14px; color: rgba(255,255,255,.88); max-width: 540px; margin: 0 auto 20px; line-height: 1.65; position: relative; z-index: 1; }
.ct-cta-actions {
  display: flex; justify-content: center; gap: 12px; flex-wrap: wrap;
  position: relative; z-index: 1;
}
.ct-btn-light {
  display: inline-flex; align-items: center; gap: 8px;
  background: #fff; color: var(--primary);
  font-size: 13.5px; font-weight: 700;
  padding: 10px 22px; border-radius: 10px;
  text-decoration: none;
  transition: transform .2s, box-shadow .2s;
}
.ct-btn-light:hover { transform: translateY(-2px); box-shadow: 0 6px 18px rgba(0,0,0,.15); color: var(--primary); }
.ct-btn-outline {
  display: inline-flex; align-items: center; gap: 8px;
  background: rgba(255,255,255,.12); color: #fff;
  border: 1px solid rgba(255,255,255,.35);
  font-size: 13.5px; font-weight: 700;
  padding: 10px 22px; border-radius: 10px;
  text-decoration: none;
  backdrop-filter: blur(4px);
  transition: transform .2s, background .2s;
}
.ct-btn-outline:hover { transform: translateY(-2px); background: rgba(255,255,255,.2); color: #fff; }

/* ===== RESPONSIVE ===== */
@media (max-width: 991px) {
  .contact-wrap { padding: 24px 0 48px; }
  .ct-hero { padding: 28px 20px; margin-bottom: 20px; border-radius: 16px; }
  .ct-quick-grid { grid-template-columns: repeat(2, 1fr); gap: 12px; margin-bottom: 20px; }
  .ct-map-card { padding: 18px; margin-top: 20px; }
  .ct-map-iframe { height: 260px; }
  .ct-cta { padding: 28px 18px; margin-top: 20px; }
}

@media (max-width: 575px) {
  .contact-wrap { padding: 12px 10px 40px; }
  .ct-hero { padding: 20px 15px; border-radius: 14px; }
  .ct-hero::after { display: none; }
  .ct-badge { font-size: 11px; padding: 4px 10px; }
  .ct-title { font-size: clamp(21px, 7.5vw, 28px); margin-bottom: 8px; }
  .ct-desc { font-size: 13px; line-height: 1.7; }
  .ct-quick-grid { grid-template-columns: repeat(2, 1fr); gap: 10px; margin-bottom: 18px; }
  .ct-quick-card { padding: 16px 14px; border-radius: 12px; }
  .ct-icon-box { width: 38px; height: 38px; font-size: 17px; margin-bottom: 10px; }
  .ct-quick-label { font-size: 10px; font-weight: 600; color: var(--text-muted); text-transform: uppercase; letter-spacing: .5px; margin-bottom: 4px; }
.ct-quick-val { font-size: 13px; font-weight: 700; color: var(--text-main); margin-bottom: 4px; word-break: break-word; }
.ct-quick-sub { font-size: 10px; color: var(--text-muted); margin-bottom: 14px; line-height: 1.4; }
  .ct-section-head { margin-bottom: 14px; padding: 0 2px; }
  .ct-section-head h2 { font-size: 19px; }
  .ct-section-head p { font-size: 12.5px; }
  .ct-dept-card { padding: 16px 14px; border-radius: 12px; }
  .ct-dept-header { margin-bottom: 10px; gap: 10px; }
  .ct-dept-icon { width: 36px; height: 36px; font-size: 15px; }
  .ct-dept-title { font-size: 15px; }
  .ct-dept-desc { font-size: 12.5px; margin-bottom: 12px; }
  .ct-item-list li { padding: 8px 10px; font-size: 12.5px; }
  .ct-map-card { padding: 14px; border-radius: 14px; }
  .ct-map-iframe { height: 220px; }
  .ct-cta { padding: 24px 14px; border-radius: 14px; }
  .ct-cta h3 { font-size: clamp(18px, 6vw, 22px); margin-bottom: 6px; }
  .ct-cta p { font-size: 12.5px; margin-bottom: 14px; }
  .ct-btn-light, .ct-btn-outline { width: 100%; justify-content: center; font-size: 13px; padding: 9px 16px; }
}
</style>

<div class="container contact-wrap">

  {{-- Hero --}}
  <div class="ct-hero">
    <div class="ct-badge">
      <i class="fas fa-headset"></i>
      Direct Wholesale & Support Channels
    </div>
    <h1 class="ct-title">
      Contact <span>{{ config('app.name') }}</span>
    </h1>
    <p class="ct-desc">
      Have a wholesale inquiry, bulk order requirement, or need product specifications? Connect with our dedicated sales and support team directly via phone, WhatsApp, email, or visit our central hub.
    </p>
  </div>

  {{-- Quick Action Hub --}}
  <div class="ct-quick-grid">
    
    {{-- Call --}}
    <a href="tel:+8801871923000" class="ct-quick-card">
      <div>
        <div class="ct-icon-box">
          <i class="fas fa-phone-alt"></i>
        </div>
        <div class="ct-quick-label">Direct Hotline</div>
        <div class="ct-quick-val">+880 1871-923000</div>
        <div class="ct-quick-sub">Sat – Thu: 9:00 AM – 9:00 PM</div>
      </div>
      <div class="ct-action-btn">
        <i class="fas fa-phone"></i> Call Now <i class="fas fa-arrow-right ms-1"></i>
      </div>
    </a>

    {{-- WhatsApp --}}
    <a href="https://wa.me/8801871923000" target="_blank" rel="noopener" class="ct-quick-card">
      <div>
        <div class="ct-icon-box wa-icon">
          <i class="fab fa-whatsapp"></i>
        </div>
        <div class="ct-quick-label">WhatsApp Chat</div>
        <div class="ct-quick-val">+880 1871-923000</div>
        <div class="ct-quick-sub">Instant quotes & product catalog</div>
      </div>
      <div class="ct-action-btn" style="color: #25d366;">
        <i class="fab fa-whatsapp"></i> Message Us <i class="fas fa-arrow-right ms-1"></i>
      </div>
    </a>

    {{-- Email --}}
    <a href="mailto:relectricbdofficial@gmail.com" class="ct-quick-card">
      <div>
        <div class="ct-icon-box mail-icon">
          <i class="fas fa-envelope"></i>
        </div>
        <div class="ct-quick-label">Official Email</div>
        <div class="ct-quick-val">relectricbdofficial@gmail.com</div>
        <div class="ct-quick-sub">Official quotations & business RFQs</div>
      </div>
      <div class="ct-action-btn" style="color: #ea4335;">
        <i class="fas fa-envelope"></i> Send Email <i class="fas fa-arrow-right ms-1"></i>
      </div>
    </a>

    {{-- Central Hub --}}
    <a href="#location-map" class="ct-quick-card">
      <div>
        <div class="ct-icon-box map-icon">
          <i class="fas fa-map-marker-alt"></i>
        </div>
        <div class="ct-quick-label">Central Hub</div>
        <div class="ct-quick-val">Chattogram, Bangladesh</div>
        <div class="ct-quick-sub">Mistiripara, Corporate Distribution</div>
      </div>
      <div class="ct-action-btn" style="color: #4285f4;">
        <i class="fas fa-location-arrow"></i> View Location <i class="fas fa-arrow-down ms-1"></i>
      </div>
    </a>

  </div>

  {{-- Contact Information & Channels --}}
  <div class="mb-4">
    <div class="ct-section-head">
      <h2>Departmental Information & Support</h2>
      <p>Select the specific channel best suited for your requirements to get the fastest assistance.</p>
    </div>

    <div class="row g-3">
      
      {{-- Card 1: Wholesale & Bulk Sales --}}
      <div class="col-lg-4 col-md-6 col-12">
        <div class="ct-dept-card">
          <div class="ct-dept-header">
            <div class="ct-dept-icon">
              <i class="fas fa-boxes-stacked"></i>
            </div>
            <h5 class="ct-dept-title">Wholesale & Bulk Orders</h5>
          </div>
          <p class="ct-dept-desc">Dedicated assistance for dealers, retailers, contractors, and project procurement.</p>
          <ul class="ct-item-list">
            <li>
              <i class="fas fa-phone-volume"></i>
              <div>
                <strong>Sales Hotline:</strong><br>
                <a href="tel:+8801871923000">+880 1871-923000</a>
              </div>
            </li>
            <li>
              <i class="fab fa-whatsapp"></i>
              <div>
                <strong>WhatsApp Orders:</strong><br>
                <a href="https://wa.me/8801871923000" target="_blank" rel="noopener">+880 1871-923000</a>
              </div>
            </li>
            <li>
              <i class="fas fa-file-invoice-dollar"></i>
              <div>
                <strong>Quotation Request:</strong><br>
                <span>Instant bulk price sheets on WhatsApp</span>
              </div>
            </li>
          </ul>
        </div>
      </div>

      {{-- Card 2: Customer Care & Warranty --}}
      <div class="col-lg-4 col-md-6 col-12">
        <div class="ct-dept-card" style="border-top-color: var(--accent);">
          <div class="ct-dept-header">
            <div class="ct-dept-icon" style="background: rgba(174, 4, 241, 0.12); color: var(--accent);">
              <i class="fas fa-shield-halved"></i>
            </div>
            <h5 class="ct-dept-title">Customer Care & Support</h5>
          </div>
          <p class="ct-dept-desc">After-sales support, warranty verification, dispatch tracking, and product queries.</p>
          <ul class="ct-item-list">
            <li>
              <i class="fas fa-envelope-open-text" style="color: var(--accent);"></i>
              <div>
                <strong>Support Email:</strong><br>
                <a href="mailto:relectricbdofficial@gmail.com" style="color: var(--accent);">relectricbdofficial@gmail.com</a>
              </div>
            </li>
            <li>
              <i class="fas fa-truck-fast" style="color: var(--accent);"></i>
              <div>
                <strong>Dispatch Inquiries:</strong><br>
                <span>Same-day dispatch updates available</span>
              </div>
            </li>
            <li>
              <i class="fas fa-award" style="color: var(--accent);"></i>
              <div>
                <strong>Authenticity:</strong><br>
                <span>100% Genuine, tested electrical items</span>
              </div>
            </li>
          </ul>
        </div>
      </div>

      {{-- Card 3: Business Hours & Warehouse --}}
      <div class="col-lg-4 col-md-12 col-12">
        <div class="ct-dept-card" style="border-top-color: #10b981;">
          <div class="ct-dept-header">
            <div class="ct-dept-icon" style="background: rgba(16, 185, 129, 0.12); color: #10b981;">
              <i class="fas fa-clock"></i>
            </div>
            <h5 class="ct-dept-title">Working Hours & Hub</h5>
          </div>
          <p class="ct-dept-desc">Warehouse dispatch timings and customer service availability schedule.</p>
          <ul class="ct-item-list">
            <li>
              <i class="fas fa-calendar-check" style="color: #10b981;"></i>
              <div>
                <strong>Saturday – Thursday:</strong><br>
                <span>9:00 AM – 9:00 PM (Full Operations)</span>
              </div>
            </li>
            <li>
              <i class="fas fa-calendar-day" style="color: #10b981;"></i>
              <div>
                <strong>Friday:</strong><br>
                <span>3:00 PM – 8:00 PM (Emergency WhatsApp)</span>
              </div>
            </li>
            <li>
              <i class="fas fa-warehouse" style="color: #10b981;"></i>
              <div>
                <strong>Warehouse Location:</strong><br>
                <span>Mistiripara, Chattogram, Bangladesh</span>
              </div>
            </li>
          </ul>
        </div>
      </div>

    </div>
  </div>

  {{-- Location Map Section --}}
  <div id="location-map" class="ct-map-card">
    <div class="ct-section-head d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-2 mb-3">
      <div>
        <h2>Our Central Hub Location</h2>
        <p>Visit our distribution center or direct your transport for bulk product pickup.</p>
      </div>
      <a href="https://maps.google.com/?q=Mistiripara+Chattogram+Bangladesh" target="_blank" rel="noopener" class="ct-btn-light" style="background: var(--primary-soft); color: var(--primary); border: 1px solid var(--border-color); flex-shrink: 0;">
        <i class="fas fa-directions"></i> Get Directions
      </a>
    </div>

    <iframe 
      class="ct-map-iframe"
      src="https://maps.google.com/maps?q=Mistiripara,%20Chattogram,%20Bangladesh&t=&z=14&ie=UTF8&iwloc=&output=embed"
      loading="lazy"
      referrerpolicy="no-referrer-when-downgrade"
      title="{{ config('app.name') }} Location Map">
    </iframe>
  </div>

  {{-- Wholesale CTA --}}
  <div class="ct-cta">
    <h3>Need Fast Bulk Quotations or Special Pricing?</h3>
    <p>
      Contact our wholesale team today for exclusive dealer discounts, customized packaging, and immediate transport arrangements across Bangladesh.
    </p>
    <div class="ct-cta-actions">
      <a href="https://wa.me/8801871923000" target="_blank" rel="noopener" class="ct-btn-light">
        <i class="fab fa-whatsapp text-success fs-5"></i> Chat on WhatsApp
      </a>
      <a href="tel:+8801871923000" class="ct-btn-outline">
        <i class="fas fa-phone-alt"></i> Call +880 1871-923000
      </a>
      <a href="{{ route('home-page') }}#products" class="ct-btn-outline">
        <i class="fas fa-boxes-stacked"></i> Explore Catalog
      </a>
    </div>
  </div>

</div>
@endsection