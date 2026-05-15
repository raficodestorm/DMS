@extends('layouts.userlayout')

@section('title','Home')

@section('content')


<style>
    
  /* ======================
GLOBAL
====================== */

  .section {
    padding: 40px 0;
  }

  .section-title {
    font-size: 28px;
    font-weight: 700;
    color: var(--text-main);
    margin-bottom: 45px;
    text-align: center;
  }


  /* SLIDER HEIGHT */
  .main-slider {
    margin-top: 85px;
  }

  .hero-slider {
    height: 250px;
  }

  @media(max-width:768px) {

    .hero-slider {
      height: 180px;
    }

    .main-slider {
      margin-top: 55px;
    }

  }


  /* SLIDE DESIGN */

  .hero-slide {

    height: 100%;
    border-radius: 16px;
    background: var(--section-bg);
    border: 1px solid var(--border-color);
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 35px;
    overflow: hidden;
    position: relative;
  }

  /* decorative background */

  .hero-slide::before {

    content: "";
    position: absolute;
    width: 260px;
    height: 260px;
    background: var(--primary-soft);
    border-radius: 50%;
    top: -120px;
    right: -100px;
    filter: blur(50px);

  }

  /* TEXT */

  .hero-text h2 {

    font-weight: 800;
    color: var(--primary);
    margin-bottom: 6px;

  }

  .hero-text p {

    color: var(--text-muted);
    font-size: 14px;

  }

  .hero-btn {

    background: linear-gradient(90deg, var(--primary), var(--accent));
    color: white;
    padding: 8px 20px;
    border-radius: 6px;
    text-decoration: none;
    display: inline-block;
    margin-top: 8px;

  }

  /* IMAGE */

  .hero-img img {

    height: 180px;
    transition: 0.7s ease;

  }

  /* ACTIVE ANIMATION */

  .swiper-slide-active .hero-img img {

    transform: scale(1.15) rotate(6deg);

  }

  /* MOBILE FIX */

  @media(max-width:768px) {

    .hero-slide {
      text-align: center;
      padding: 20px;
    }

    .hero-img img {

      height: 60px;
      margin-top: 8px;

    }

    .hero-text h2 {

      font-size: 15px;

    }

    .hero-text p {

      font-size: 9px;

    }

    .hero-btn {
      padding: 5px 10px;
      border-radius: 5px;
      font-size: small;
    }

  }


  /* ======================
CATEGORY
====================== */

  .category-card {
    background: var(--section-bg);
    border-radius: 14px;
    border: 1px solid var(--border-color);
    padding: 30px;
    text-align: center;
    transition: .3s;
    cursor: pointer;
  }

  .category-card:hover {
    transform: translateY(-10px);
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.05);
  }

  .category-icon {
    width: 60px;
    height: 60px;
    background: var(--primary-soft);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: auto;
    margin-bottom: 12px;
    font-size: 24px;
    color: var(--primary);
  }

  .category-card h6 {
    margin: 0;
    font-weight: 600;
    color: var(--text-main);
  }


  .about-section {
    padding: 70px 0;
    background: var(--background);
  }

  .about-card {
    background: var(--section-bg);
    border: 1px solid var(--border-color);
    border-radius: 16px;
    padding: 40px;
    height: 100%;
    transition: .3s;
  }

  .about-card:hover {
    transform: translateY(-6px);
    box-shadow: 0 15px 40px rgba(0, 0, 0, 0.05);
  }

  .about-title {
    font-weight: 700;
    color: var(--text-main);
    margin-bottom: 15px;
  }

  .about-text {
    color: var(--text-muted);
    line-height: 1.7;
  }

  .about-info {
    margin-top: 20px;
  }

  .about-info div {
    margin-bottom: 10px;
    color: var(--text-main);
    font-weight: 500;
  }

  .about-icon {
    color: var(--primary);
    margin-right: 8px;
  }

  .about-image {
    display: flex;
    align-items: center;
    justify-content: center;
    height: 80%;
  }

  .about-image img {
    max-width: 60%;
    border-radius: 14px;
  }

  @media(max-width:768px) {

    .about-card {
      padding: 25px;
    }

    .about-image img {
      max-width: 50%;
    }

  }

  /* ======================
TRUST
====================== */

  .trust-card {
    background: var(--section-bg);
    border-radius: 14px;
    border: 1px solid var(--border-color);
    padding: 30px;
    text-align: center;
    transition: .3s;
  }

  .trust-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 15px 35px rgba(0, 0, 0, 0.05);
  }

  .trust-icon {
    font-size: 38px;
    color: var(--primary);
    margin-bottom: 10px;
  }

  .trust-card h5 {
    color: var(--text-main);
  }

  .trust-card p {
    color: var(--text-muted);
    font-size: 14px;
  }
</style>


<!-- HERO SLIDER -->

<section class="container main-slider">

  <div class="swiper hero-slider">

    <div class="swiper-wrapper">


      <!-- SLIDE 1 -->

      <div class="swiper-slide">

        <div class="hero-slide">

          <div class="hero-text">

            <h2>Premium Electrical Products</h2>
            <p>Smart accessories for modern homes</p>

            <a href="#" class="hero-btn">
              Explore
            </a>

          </div>

          <div class="hero-img">

            <img src="{{ asset('image/electronics.png') }}">

          </div>

        </div>

      </div>


      <!-- SLIDE 2 -->

      <div class="swiper-slide">

        <div class="hero-slide">

          <div class="hero-text">

            <h2>Smart Lighting Solution</h2>
            <p>Energy efficient lighting system</p>

            <a href="#" class="hero-btn">
              Explore
            </a>

          </div>

          <div class="hero-img">

            <img src="{{ asset('image/light.webp') }}">

          </div>

        </div>

      </div>


      <!-- SLIDE 3 -->

      <div class="swiper-slide">

        <div class="hero-slide">

          <div class="hero-text">

            <h2>Professional Electrical Tools</h2>
            <p>Trusted tools for electricians</p>

            <a href="#" class="hero-btn">
              Explore
            </a>

          </div>

          <div class="hero-img">

            <img src="{{ asset('image/tools.webp') }}">

          </div>

        </div>

      </div>


    </div>

  </div>

</section>


<!-- CATEGORY -->

<section class="section">

  <div class="container">

    <h3 class="section-title">Shop By Category</h3>

    <div class="row g-4">

      <div class="col-lg-3 col-6">
        <div class="category-card">
          <div class="category-icon"><i class="fas fa-lightbulb"></i></div>
          <h6>Lighting</h6>
        </div>
      </div>

      <div class="col-lg-3 col-6">
        <div class="category-card">
          <div class="category-icon"><i class="fas fa-plug"></i></div>
          <h6>Switch & Plug</h6>
        </div>
      </div>

      <div class="col-lg-3 col-6">
        <div class="category-card">
          <div class="category-icon"><i class="fas fa-bolt"></i></div>
          <h6>Wiring</h6>
        </div>
      </div>

      <div class="col-lg-3 col-6">
        <div class="category-card">
          <div class="category-icon"><i class="fas fa-tools"></i></div>
          <h6>Tools</h6>
        </div>
      </div>

    </div>

  </div>

</section>


<section class="about-section">

  <div class="container">

    <div class="row align-items-center g-4">

      <!-- TEXT -->

      <div class="col-lg-6">

        <div class="about-card">

          <h3 class="about-title">About {{ config('app.name') }}</h3>

          <p class="about-text">
            {{ config('app.name') }} is a trusted electrical product supplier providing both
            <strong>wholesale and retail sales</strong>. We supply high-quality electrical
            items including lighting, switches, wiring accessories, and professional
            tools for homes, shops, and industrial use.
          </p>

          <p class="about-text">
            Our goal is to deliver reliable products at the best price while ensuring
            excellent customer service. With years of experience in the electrical
            market, {{ config('app.name') }} has built strong trust among electricians, contractors,
            and homeowners.
          </p>

          <div class="about-info">

            <div>
              <i class="fas fa-map-marker-alt about-icon"></i>
              Address: Chattogram, Bangladesh
            </div>

            <div>
              <i class="fas fa-phone about-icon"></i>
              Phone: +880 1828333233
            </div>

            <div>
              <i class="fas fa-envelope about-icon"></i>
              Email: relectric@gmail.com
            </div>
            <div>
              <i class="fas fa-user about-icon"></i>
              Owner: MD Sarwar Hossain
            </div>

          </div>

        </div>

      </div>


      <!-- IMAGE -->

      <div class="col-lg-6">

        <div class="about-image">

          <img src="{{ asset('image/electronics.png') }}">

        </div>

      </div>

    </div>

  </div>

</section>


<!-- TRUST -->

<section class="section">

  <div class="container">

    <div class="row g-4">

      <div class="col-lg-3 col-6">
        <div class="trust-card">
          <div class="trust-icon"><i class="fas fa-shipping-fast"></i></div>
          <h5>Fast Delivery</h5>
          <p>Across Bangladesh.</p>
        </div>
      </div>

      <div class="col-lg-3 col-6">
        <div class="trust-card">
          <div class="trust-icon"><i class="fas fa-shield-alt"></i></div>
          <h5>Secure Payment</h5>
          <p>Safe payment system.</p>
        </div>
      </div>

      <div class="col-lg-3 col-6">
        <div class="trust-card">
          <div class="trust-icon"><i class="fas fa-star"></i></div>
          <h5>Top Quality</h5>
          <p>Best electrical brands.</p>
        </div>
      </div>

      <div class="col-lg-3 col-6">
        <div class="trust-card">
          <div class="trust-icon"><i class="fas fa-headset"></i></div>
          <h5>24/7 Support</h5>
          <p>Always ready to help.</p>
        </div>
      </div>

    </div>

  </div>

</section>


<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
<script src="https://unpkg.com/aos@2.3.4/dist/aos.js"></script>

<script>
  new Swiper(".hero-slider",{

loop:true,
speed:1200,

autoplay:{
delay:3500
},

effect:"slide",

spaceBetween:20,

pagination:{
el:".swiper-pagination",
clickable:true
}

});
</script>

@endsection