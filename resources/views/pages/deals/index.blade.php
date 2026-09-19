@extends('layouts.userlayout')

@section('title', "Today's Exclusive Deals — R Electric")

@section('content')
<style>
/* ===================== COMPACT & SMART TODAY'S DEALS ===================== */
.deals-page {
  padding: 20px 15px 60px;
  position: relative;
}
#sparkleCanvas {
  position: absolute;
  top: 0; left: 0; width: 100%; height: 100%;
  pointer-events: none; z-index: 0;
}
.deals-hero {
  position: relative;
  z-index: 1;
  background: radial-gradient(circle at 50% 0%, rgba(245, 158, 11, 0.16) 0%, transparent 70%), var(--section-bg);
  border: 1.5px solid rgba(245, 158, 11, 0.3);
  border-radius: 20px;
  padding: 30px 20px;
  text-align: center;
  margin-bottom: 30px;
  box-shadow: 0 10px 30px -10px rgba(245, 158, 11, 0.15);
}
.deals-badge {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  background: linear-gradient(135deg, #ef4444, #f59e0b);
  color: #fff;
  font-size: 11px;
  font-weight: 800;
  padding: 4px 14px;
  border-radius: 20px;
  letter-spacing: 1px;
  text-transform: uppercase;
  animation: popIn 0.6s cubic-bezier(0.34, 1.56, 0.64, 1);
}
.deals-title {
  font-size: clamp(24px, 4.5vw, 42px);
  font-weight: 900;
  font-family: "El Messiri", sans-serif;
  background: linear-gradient(135deg, #ff2a5f, #f59e0b, #ffd700);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  margin: 10px 0 6px;
  opacity: 0;
  transform: scale(0.85);
  animation: sparkleReveal 0.9s cubic-bezier(0.16, 1, 0.3, 1) 0.1s forwards;
}
.deals-timer {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  background: rgba(0, 0, 0, 0.04);
  border: 1px solid var(--border-color);
  padding: 6px 16px;
  border-radius: 30px;
  font-size: 12.5px;
  font-weight: 700;
  color: var(--text-muted);
}
[data-theme="dark"] .deals-timer { background: rgba(255, 255, 255, 0.05); }
.timer-val {
  font-family: monospace;
  font-size: 15px;
  color: #ef4444;
  font-weight: 800;
}

/* Staggered Cards Reveal after text sparkle */
.deal-col {
  position: relative;
  opacity: 0;
  transform: translateY(26px) scale(0.97);
  animation: cardEntrance 0.7s cubic-bezier(0.16, 1, 0.3, 1) forwards;
}
.deal-col:nth-child(1) { animation-delay: 0.40s; }
.deal-col:nth-child(2) { animation-delay: 0.55s; }
.deal-col:nth-child(3) { animation-delay: 0.70s; }
.deal-col:nth-child(4) { animation-delay: 0.85s; }

.deal-rank-ribbon {
  position: absolute;
  top: -8px; left: 14px;
  z-index: 10;
  background: linear-gradient(135deg, #ef4444, #f59e0b);
  color: #fff;
  font-size: 10px;
  font-weight: 800;
  padding: 2px 9px;
  border-radius: 6px;
  box-shadow: 0 4px 10px rgba(239, 68, 68, 0.35);
}

@keyframes popIn {
  from { transform: scale(0.6); opacity: 0; }
  to { transform: scale(1); opacity: 1; }
}
@keyframes sparkleReveal {
  0% { transform: scale(0.8); opacity: 0; filter: brightness(2.2) drop-shadow(0 0 25px #f59e0b); }
  60% { transform: scale(1.03); opacity: 1; filter: brightness(1.4) drop-shadow(0 0 18px #ef4444); }
  100% { transform: scale(1); opacity: 1; filter: drop-shadow(0 0 8px rgba(245, 158, 11, 0.3)); }
}
@keyframes cardEntrance {
  to { opacity: 1; transform: translateY(0) scale(1); }
}
</style>

<div class="deals-page container">
  {{-- Sparkling Particles Background --}}
  <canvas id="sparkleCanvas"></canvas>

  {{-- Hero Section --}}
  <div class="deals-hero">
    <div class="mb-2">
      <span class="deals-badge">
        <i class="fas fa-fire text-warning"></i> 24-Hour Flash Deals <i class="fas fa-bolt text-warning"></i>
      </span>
    </div>

    <h1 class="deals-title">
      TODAY'S SPECIAL DEALS
    </h1>

    <p class="text-muted small mb-3 mx-auto" style="max-width: 520px;">
      Exclusive daily picks with limited-time deals! 4 in-stock products renewed every 24 hours at midnight.
    </p>

    {{-- Countdown Timer --}}
    <div class="deals-timer">
      <i class="fas fa-stopwatch text-danger"></i>
      <span>Deals Reset In:</span>
      <span class="timer-val" id="dealClock">--:--:--</span>
    </div>
  </div>

  {{-- 4 Daily Deal Products Grid --}}
  <div style="position: relative; z-index: 1;">
    @if($dealProducts->count() > 0)
      <div class="row g-3 g-md-4">
        @foreach($dealProducts as $index => $product)
          <div class="col-xl-3 col-lg-3 col-md-6 col-6 d-flex deal-col">
            <div class="deal-rank-ribbon">
              <i class="fas fa-bolt"></i> Deal #{{ $index + 1 }}
            </div>
            @include('components.product-card', ['product' => $product])
          </div>
        @endforeach
      </div>
    @else
      <div class="text-center py-5">
        <i class="fas fa-box-open fa-3x text-muted opacity-50 mb-3"></i>
        <h4>No deals currently active.</h4>
        <a href="{{ route('shop') }}" class="btn btn-primary rounded-pill mt-2">Explore All Products</a>
      </div>
    @endif
  </div>

  {{-- Bottom Shop Redirect --}}
  <div class="text-center mt-5 position-relative" style="z-index: 1;">
    <a href="{{ route('shop') }}" class="btn btn-outline-primary rounded-pill px-4 py-2 font-weight-bold">
      <i class="fas fa-th-large me-2"></i> Browse All Products in Shop
    </a>
  </div>
</div>

<script>
/* Midnight Countdown */
(function() {
  const target = {{ $midnightTimestamp }} * 1000;
  const clock = document.getElementById('dealClock');
  function update() {
    const diff = Math.max(0, target - Date.now());
    const h = String(Math.floor(diff / 3600000)).padStart(2, '0');
    const m = String(Math.floor((diff % 3600000) / 60000)).padStart(2, '0');
    const s = String(Math.floor((diff % 60000) / 1000)).padStart(2, '0');
    if (clock) clock.textContent = `${h}h : ${m}m : ${s}s`;
    if (diff <= 0) setTimeout(() => window.location.reload(), 1500);
  }
  update();
  setInterval(update, 1000);
})();

/* Sparkle Canvas Animation */
(function() {
  const canvas = document.getElementById('sparkleCanvas');
  if (!canvas) return;
  const ctx = canvas.getContext('2d');
  let particles = [];
  let w, h;
  function resize() {
    w = canvas.width = canvas.parentElement.offsetWidth;
    h = canvas.height = canvas.parentElement.offsetHeight;
  }
  window.addEventListener('resize', resize);
  resize();

  class Sparkle {
    constructor() { this.reset(); }
    reset() {
      this.x = Math.random() * w;
      this.y = Math.random() * Math.min(h, 450);
      this.size = Math.random() * 2.2 + 0.8;
      this.speedX = (Math.random() - 0.5) * 0.6;
      this.speedY = -Math.random() * 0.7 - 0.2;
      this.alpha = Math.random() * 0.8 + 0.2;
      this.fade = Math.random() * 0.015 + 0.005;
      this.color = Math.random() > 0.5 ? '245, 158, 11' : (Math.random() > 0.5 ? '239, 68, 68' : '255, 215, 0');
    }
    update() {
      this.x += this.speedX;
      this.y += this.speedY;
      this.alpha -= this.fade;
      if (this.alpha <= 0 || this.y < 0) this.reset();
    }
    draw() {
      ctx.save();
      ctx.beginPath();
      ctx.arc(this.x, this.y, this.size, 0, Math.PI * 2);
      ctx.fillStyle = `rgba(${this.color}, ${this.alpha})`;
      ctx.shadowBlur = 6;
      ctx.shadowColor = `rgba(${this.color}, 0.8)`;
      ctx.fill();
      ctx.restore();
    }
  }

  for (let i = 0; i < (w < 768 ? 20 : 40); i++) particles.push(new Sparkle());
  function loop() {
    ctx.clearRect(0, 0, w, h);
    particles.forEach(p => { p.update(); p.draw(); });
    requestAnimationFrame(loop);
  }
  loop();
})();
</script>
@endsection
