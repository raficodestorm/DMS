@extends('layouts.userlayout')

@section('title', 'About Us — ' . config('app.name'))

@section('content')
<style>
/* ===== ABOUT PAGE — SMART & MINIMAL ===== */
.about-wrap { padding: 40px 0 70px; }

/* Hero */
.ab-hero {
  background: var(--section-bg);
  border: 1px solid var(--border-color);
  border-radius: 20px;
  padding: 48px 40px;
  margin-bottom: 32px;
  position: relative;
  overflow: hidden;
}
.ab-hero::after {
  content: '';
  position: absolute;
  width: 300px; height: 300px;
  background: radial-gradient(circle, rgba(2,2,226,.1), transparent 70%);
  top: -80px; right: -80px;
  border-radius: 50%;
  pointer-events: none;
}
.ab-badge {
  display: inline-flex; align-items: center; gap: 7px;
  background: var(--primary-soft); color: var(--primary);
  padding: 5px 14px; border-radius: 30px;
  font-size: 12px; font-weight: 700;
  text-transform: uppercase; letter-spacing: .6px;
  margin-bottom: 16px;
}
.ab-title {
  font-size: clamp(28px, 4vw, 46px);
  font-weight: 900;
  font-family: "El Messiri", sans-serif;
  color: var(--text-main);
  line-height: 1.15;
  margin-bottom: 14px;
}
.ab-title span {
  background: linear-gradient(135deg, var(--primary), var(--accent));
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
}
.ab-desc { font-size: 15px; color: var(--text-muted); line-height: 1.8; max-width: 680px; }

/* Stats Row */
.ab-stats { display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; margin-top: 28px; }
.ab-stat {
  background: var(--background);
  border: 1px solid var(--border-color);
  border-radius: 14px;
  padding: 20px 16px;
  text-align: center;
  transition: transform .25s, box-shadow .25s;
}
.ab-stat:hover { transform: translateY(-4px); box-shadow: 0 8px 24px rgba(0,0,0,.07); }
.ab-stat-num { font-size: 26px; font-weight: 800; color: var(--primary); line-height: 1; margin-bottom: 4px; }
.ab-stat-lbl { font-size: 12px; color: var(--text-muted); font-weight: 600; }

/* Section Heading */
.ab-section-head { margin-bottom: 24px; }
.ab-section-head h2 { font-size: clamp(20px, 2.5vw, 28px); font-weight: 800; color: var(--text-main); font-family: "El Messiri", sans-serif; margin-bottom: 6px; }
.ab-section-head p { font-size: 14px; color: var(--text-muted); line-height: 1.7; max-width: 580px; }

/* Feature Cards */
.ab-feature {
  background: var(--section-bg);
  border: 1px solid var(--border-color);
  border-radius: 16px;
  padding: 24px;
  height: 100%;
  transition: transform .25s, box-shadow .25s;
  border-top: 3px solid var(--primary);
}
.ab-feature:hover { transform: translateY(-5px); box-shadow: 0 10px 28px rgba(0,0,0,.06); }
.ab-feature-icon {
  width: 46px; height: 46px;
  background: var(--primary-soft);
  color: var(--primary);
  border-radius: 12px;
  display: flex; align-items: center; justify-content: center;
  font-size: 18px;
  margin-bottom: 14px;
  flex-shrink: 0;
}
.ab-feature h5 { font-size: 16px; font-weight: 800; color: var(--text-main); margin-bottom: 8px; }
.ab-feature p { font-size: 13.5px; color: var(--text-muted); line-height: 1.7; margin: 0; }

/* Story / Timeline */
.ab-story {
  background: var(--section-bg);
  border: 1px solid var(--border-color);
  border-radius: 16px;
  padding: 32px;
}
.ab-story p { font-size: 14.5px; color: var(--text-muted); line-height: 1.85; margin: 0; }

.ab-timeline-item {
  padding: 18px 20px;
  border: 1px solid var(--border-color);
  border-left: 3px solid var(--primary);
  border-radius: 10px;
  background: var(--background);
  transition: transform .2s;
}
.ab-timeline-item:hover { transform: translateX(5px); }
.ab-timeline-item .yr { font-size: 11px; font-weight: 800; color: var(--primary); text-transform: uppercase; letter-spacing: 1px; margin-bottom: 4px; }
.ab-timeline-item h6 { font-size: 15px; font-weight: 800; color: var(--text-main); margin-bottom: 4px; }
.ab-timeline-item p { font-size: 13px; color: var(--text-muted); line-height: 1.6; margin: 0; }

/* CTA */
.ab-cta {
  background: linear-gradient(135deg, var(--primary), var(--accent));
  border-radius: 18px;
  padding: 40px 32px;
  text-align: center;
  margin-top: 32px;
  position: relative;
  overflow: hidden;
}
.ab-cta::before {
  content: '';
  position: absolute;
  width: 200px; height: 200px;
  background: rgba(255,255,255,.06);
  border-radius: 50%;
  top: -60px; left: -60px;
}
.ab-cta h3 { font-size: clamp(20px, 3vw, 30px); font-weight: 900; color: #fff; font-family: "El Messiri", sans-serif; margin-bottom: 10px; position: relative; z-index: 1; }
.ab-cta p { font-size: 14px; color: rgba(255,255,255,.85); max-width: 520px; margin: 0 auto 20px; line-height: 1.7; position: relative; z-index: 1; }
.ab-cta a {
  position: relative; z-index: 1;
  display: inline-flex; align-items: center; gap: 8px;
  background: #fff; color: var(--primary);
  font-size: 14px; font-weight: 700;
  padding: 10px 24px; border-radius: 10px;
  text-decoration: none;
  transition: transform .2s, box-shadow .2s;
}
.ab-cta a:hover { transform: translateY(-3px); box-shadow: 0 8px 20px rgba(0,0,0,.15); color: var(--primary); }

/* ===== RESPONSIVE ===== */
@media (max-width: 991px) {
  .about-wrap { padding: 24px 0 48px; }
  .ab-hero { padding: 28px 22px; margin-bottom: 20px; border-radius: 16px; }
  .ab-stats { grid-template-columns: repeat(2, 1fr); gap: 10px; margin-top: 20px; }
  .ab-feature { padding: 18px; display: flex; gap: 14px; align-items: flex-start; border-top: none; border-left: 3px solid var(--primary); }
  .ab-feature-icon { margin-bottom: 0; }
  .ab-feature-body { flex: 1; }
  .ab-story { padding: 22px; }
  .ab-cta { padding: 30px 22px; margin-top: 20px; }
}

@media (max-width: 767px) {
  /* Fix row gap for story+timeline on mobile stacking */
  .ab-story { border-radius: 14px; margin-bottom: 0; }
  .ab-section-head h2 { font-size: 20px; }
  .ab-section-head p { font-size: 13.5px; }
  .ab-title { font-size: clamp(24px, 7vw, 34px); }
  .ab-desc { font-size: 13.5px; }
  .ab-stat-num { font-size: 22px; }
  .ab-stat-lbl { font-size: 11px; }
  .ab-feature h5 { font-size: 15px; }
  .ab-feature p { font-size: 13px; }
  .ab-timeline-item { padding: 14px 16px; }
  .ab-timeline-item h6 { font-size: 14px; }
}

@media (max-width: 575px) {
  .about-wrap { padding: 12px 10px 40px; }
  .ab-hero { padding: 22px 16px; border-radius: 14px; }
  .ab-hero::after { display: none; }
  .ab-badge { font-size: 11px; padding: 4px 11px; }
  .ab-title { font-size: clamp(22px, 8vw, 30px); margin-bottom: 10px; }
  .ab-desc { font-size: 13px; line-height: 1.75; }
  .ab-stats { grid-template-columns: repeat(2, 1fr); gap: 8px; margin-top: 16px; }
  .ab-stat { padding: 14px 10px; border-radius: 10px; }
  .ab-stat-num { font-size: 20px; }
  .ab-section-head { margin-bottom: 16px; padding: 0 4px; }
  .ab-feature { padding: 14px; border-radius: 12px; gap: 12px; }
  .ab-feature-icon { width: 40px; height: 40px; font-size: 16px; margin-bottom: 0; }
  .ab-feature h5 { font-size: 14px; margin-bottom: 6px; }
  .ab-story { padding: 18px; border-radius: 12px; }
  .ab-story p { font-size: 13.5px; }
  .ab-cta { padding: 26px 16px; border-radius: 14px; }
  .ab-cta h3 { font-size: clamp(18px, 6vw, 24px); margin-bottom: 8px; }
  .ab-cta p { font-size: 13px; margin-bottom: 16px; }
  .ab-cta a { font-size: 13px; padding: 9px 20px; }
}
</style>

<div class="container about-wrap">

  {{-- Hero --}}
  <div class="ab-hero">
    <div class="ab-badge">
      <i class="fas fa-boxes-stacked"></i>
      Bangladesh's Trusted Electrical Wholesaler
    </div>
    <h1 class="ab-title">
      Welcome to <span>{{ config('app.name') }}</span>
    </h1>
    <p class="ab-desc">
      {{ config('app.name') }} is a leading wholesale distributor of premium electrical products in Bangladesh. We supply switches, sockets, wires, circuit breakers, LED lighting, and industrial accessories to retailers, contractors, and businesses across the country — with competitive pricing, genuine products, and reliable delivery.
    </p>

    <div class="ab-stats">
      <div class="ab-stat">
        <div class="ab-stat-num">500+</div>
        <div class="ab-stat-lbl">Products in Stock</div>
      </div>
      <div class="ab-stat">
        <div class="ab-stat-num">200+</div>
        <div class="ab-stat-lbl">Retail Partners</div>
      </div>
      <div class="ab-stat">
        <div class="ab-stat-num">10+</div>
        <div class="ab-stat-lbl">Years Experience</div>
      </div>
      <div class="ab-stat">
        <div class="ab-stat-num">24/7</div>
        <div class="ab-stat-lbl">Order Support</div>
      </div>
    </div>
  </div>

  {{-- Why Us --}}
  <div id="services" class="mb-4">
    <div class="ab-section-head">
      <h2>Why Choose {{ config('app.name') }}?</h2>
      <p>We're not just a supplier — we're your wholesale partner. From procurement to delivery, we ensure every step is smooth, transparent, and profitable for your business.</p>
    </div>

    <div class="row g-3">
      <div class="col-lg-4 col-md-6 col-12">
        <div class="ab-feature">
          <div class="ab-feature-icon"><i class="fas fa-boxes-stacked"></i></div>
          <div class="ab-feature-body">
            <h5>Bulk Supply & Distribution</h5>
            <p>We supply electrical products in bulk to retailers and businesses across Bangladesh, ensuring consistent stock availability and competitive wholesale pricing.</p>
          </div>
        </div>
      </div>
      <div class="col-lg-4 col-md-6 col-12">
        <div class="ab-feature">
          <div class="ab-feature-icon"><i class="fas fa-shield-alt"></i></div>
          <div class="ab-feature-body">
            <h5>Genuine, Quality Products</h5>
            <p>Every item we stock is sourced from verified manufacturers and trusted brands, guaranteeing authenticity, safety compliance, and long-term durability.</p>
          </div>
        </div>
      </div>
      <div class="col-lg-4 col-md-6 col-12">
        <div class="ab-feature">
          <div class="ab-feature-icon"><i class="fas fa-truck-fast"></i></div>
          <div class="ab-feature-body">
            <h5>Fast & Reliable Delivery</h5>
            <p>Our logistics network ensures timely delivery to retailers and businesses, reducing downtime and keeping your shelves consistently stocked.</p>
          </div>
        </div>
      </div>
      <div class="col-lg-4 col-md-6 col-12">
        <div class="ab-feature">
          <div class="ab-feature-icon"><i class="fas fa-tags"></i></div>
          <div class="ab-feature-body">
            <h5>Competitive Wholesale Pricing</h5>
            <p>We offer market-leading wholesale rates with flexible payment terms, helping your business maximize profit margins on every order.</p>
          </div>
        </div>
      </div>
      <div class="col-lg-4 col-md-6 col-12">
        <div class="ab-feature">
          <div class="ab-feature-icon"><i class="fas fa-handshake"></i></div>
          <div class="ab-feature-body">
            <h5>Trusted Business Relations</h5>
            <p>With 200+ retail partners across the country, we've built a reputation for reliability, professionalism, and long-term business partnerships.</p>
          </div>
        </div>
      </div>
      <div class="col-lg-4 col-md-6 col-12">
        <div class="ab-feature">
          <div class="ab-feature-icon"><i class="fas fa-headset"></i></div>
          <div class="ab-feature-body">
            <h5>Dedicated Business Support</h5>
            <p>Our experienced team is always available to assist with bulk orders, product sourcing, pricing queries, and after-sale support.</p>
          </div>
        </div>
      </div>
    </div>
  </div>

  {{-- Story + Timeline --}}
  <div id="story" class="row g-3 mb-2">
    <div class="col-lg-6">
      <div class="ab-story h-100">
        <div class="ab-section-head mb-3">
          <h2>Our Story & Vision</h2>
        </div>
        <p>
          {{ config('app.name') }} was founded with a clear mission: to become Bangladesh's most trusted wholesale distributor of electrical products. Starting from a single warehouse, we've grown into a full-scale distribution company with a nationwide retail network.
          <br><br>
          Our vision is to streamline the electrical supply chain in Bangladesh — connecting manufacturers and retailers through a transparent, tech-enabled wholesale platform that ensures quality, speed, and trust at every level.
        </p>
      </div>
    </div>
    <div class="col-lg-6">
      <div class="d-flex flex-column gap-3 h-100">
        <div class="ab-timeline-item">
          <div class="yr">2013 — Founded</div>
          <h6>Company Established</h6>
          <p>Started wholesale distribution of electrical accessories to local retailers in Chattogram with a small but dedicated team.</p>
        </div>
        <div class="ab-timeline-item">
          <div class="yr">2018 — Growth</div>
          <h6>Expanded Nationwide</h6>
          <p>Grew our retail partner network to 100+ businesses across 15+ districts, adding a wider range of brands and product categories.</p>
        </div>
        <div class="ab-timeline-item">
          <div class="yr">2024 — Digital</div>
          <h6>Smart Wholesale Platform</h6>
          <p>Launched our integrated digital order management system — enabling retailers to browse, order, and track wholesale shipments online.</p>
        </div>
      </div>
    </div>
  </div>

  {{-- CTA --}}
  <div class="ab-cta">
    <h3>Ready to Partner with {{ config('app.name') }}?</h3>
    <p>Whether you're a retailer, contractor, or business owner — we have the stock, pricing, and support to help you grow. Let's build a stronger supply chain together.</p>
    <a href="{{ route('contact') }}">
      <i class="fas fa-envelope"></i>
      Get in Touch
    </a>
  </div>

</div>
@endsection