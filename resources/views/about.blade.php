
@extends('layouts.userlayout')

@section('title','About Us')

@section('content')

<style>
  .about-page {
    background: linear-gradient(180deg, #f8fafc 0%, #ffffff 100%);
    min-height: 100vh;
    overflow: hidden;
  }

  .hero-section {
    position: relative;
    padding: 90px 0 70px;
  }

  .hero-glow {
    position: absolute;
    width: 420px;
    height: 420px;
    background: radial-gradient(circle, rgba(49,49,255,0.18) 0%, rgba(174,4,241,0.08) 45%, transparent 75%);
    top: -120px;
    right: -120px;
    border-radius: 50%;
    pointer-events: none;
  }

  .hero-card {
    background: rgba(255,255,255,0.88);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255,255,255,0.5);
    border-radius: 32px;
    padding: 55px;
    box-shadow: 0 20px 60px rgba(15,23,42,0.08);
    position: relative;
    z-index: 2;
  }

  .hero-badge {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    background: var(--primary-soft);
    color: var(--primary);
    padding: 10px 18px;
    border-radius: 50px;
    font-size: 14px;
    font-weight: 700;
    margin-bottom: 22px;
  }

  .hero-title {
    font-size: 62px;
    line-height: 1.1;
    font-weight: 800;
    color: var(--text-main);
    margin-bottom: 24px;
  }

  .gradient-text {
    background: linear-gradient(135deg, var(--primary), var(--accent));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
  }

  .hero-description {
    font-size: 18px;
    line-height: 1.9;
    color: var(--text-muted);
    max-width: 760px;
    margin-bottom: 35px;
  }

  .hero-actions {
    display: flex;
    flex-wrap: wrap;
    gap: 16px;
  }

  .btn-modern {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    padding: 14px 28px;
    border-radius: 14px;
    font-weight: 700;
    text-decoration: none;
    transition: 0.35s ease;
    border: none;
  }

  .btn-primary-modern {
    background: linear-gradient(135deg, var(--primary), var(--accent));
    color: var(--white);
    box-shadow: 0 10px 30px rgba(49,49,255,0.28);
  }

  .btn-primary-modern:hover {
    transform: translateY(-4px);
    color: var(--white);
  }

  .btn-outline-modern {
    border: 1.5px solid var(--border-color);
    color: var(--text-main);
    background: var(--white);
  }

  .btn-outline-modern:hover {
    background: var(--primary-soft);
    border-color: var(--primary-soft);
    color: var(--primary);
    transform: translateY(-4px);
  }

  .stats-grid {
    margin-top: 50px;
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 22px;
  }

  .stat-card {
    background: var(--white);
    border-radius: 22px;
    padding: 28px;
    border: 1px solid var(--border-color);
    transition: 0.35s ease;
  }

  .stat-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 20px 40px rgba(15,23,42,0.08);
  }

  .stat-icon {
    width: 58px;
    height: 58px;
    border-radius: 18px;
    background: linear-gradient(135deg, var(--primary), var(--accent));
    color: var(--white);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 22px;
    margin-bottom: 18px;
  }

  .stat-number {
    font-size: 34px;
    font-weight: 800;
    color: var(--text-main);
    margin-bottom: 6px;
  }

  .stat-label {
    color: var(--text-muted);
    font-size: 15px;
  }

  .section {
    padding: 90px 0;
  }

  .section-title {
    font-size: 46px;
    font-weight: 800;
    color: var(--text-main);
    margin-bottom: 18px;
  }

  .section-description {
    max-width: 720px;
    margin: 0 auto 55px;
    color: var(--text-muted);
    line-height: 1.9;
    font-size: 17px;
  }

  .feature-card {
    background: var(--white);
    border-radius: 28px;
    padding: 35px;
    height: 100%;
    border: 1px solid var(--border-color);
    transition: 0.35s ease;
    position: relative;
    overflow: hidden;
  }

  .feature-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 4px;
    background: linear-gradient(135deg, var(--primary), var(--accent));
  }

  .feature-card:hover {
    transform: translateY(-10px);
    box-shadow: 0 24px 60px rgba(15,23,42,0.08);
  }

  .feature-icon {
    width: 72px;
    height: 72px;
    border-radius: 22px;
    background: var(--primary-soft);
    color: var(--primary);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 28px;
    margin-bottom: 24px;
  }

  .feature-title {
    font-size: 24px;
    font-weight: 800;
    color: var(--text-main);
    margin-bottom: 15px;
  }

  .feature-text {
    color: var(--text-muted);
    line-height: 1.9;
    font-size: 15px;
  }

  .story-wrapper {
    background: linear-gradient(135deg, #ffffff 0%, #f6f8ff 100%);
    border-radius: 36px;
    padding: 60px;
    border: 1px solid var(--border-color);
    position: relative;
    overflow: hidden;
  }

  .story-wrapper::after {
    content: '';
    position: absolute;
    width: 320px;
    height: 320px;
    background: radial-gradient(circle, rgba(174,4,241,0.08), transparent 70%);
    right: -100px;
    bottom: -100px;
    border-radius: 50%;
  }

  .story-text {
    font-size: 17px;
    line-height: 2;
    color: var(--text-muted);
  }

  .timeline-card {
    background: var(--white);
    border-radius: 24px;
    padding: 30px;
    border: 1px solid var(--border-color);
    margin-bottom: 24px;
    transition: 0.35s ease;
  }

  .timeline-card:hover {
    transform: translateX(10px);
    box-shadow: 0 18px 40px rgba(15,23,42,0.07);
  }

  .timeline-year {
    color: var(--primary);
    font-weight: 800;
    font-size: 15px;
    margin-bottom: 10px;
  }

  .timeline-title {
    font-size: 22px;
    font-weight: 800;
    color: var(--text-main);
    margin-bottom: 10px;
  }

  .timeline-text {
    color: var(--text-muted);
    line-height: 1.8;
  }

  .cta-section {
    padding-bottom: 90px;
  }

  .cta-card {
    background: linear-gradient(135deg, var(--primary), var(--accent));
    border-radius: 38px;
    padding: 70px 50px;
    text-align: center;
    position: relative;
    overflow: hidden;
  }

  .cta-card::before {
    content: '';
    position: absolute;
    width: 240px;
    height: 240px;
    border-radius: 50%;
    background: rgba(255,255,255,0.08);
    top: -80px;
    left: -60px;
  }

  .cta-card::after {
    content: '';
    position: absolute;
    width: 320px;
    height: 320px;
    border-radius: 50%;
    background: rgba(255,255,255,0.05);
    bottom: -120px;
    right: -80px;
  }

  .cta-title {
    position: relative;
    z-index: 2;
    color: var(--white);
    font-size: 52px;
    font-weight: 800;
    margin-bottom: 20px;
  }

  .cta-text {
    position: relative;
    z-index: 2;
    color: rgba(255,255,255,0.88);
    font-size: 18px;
    line-height: 1.9;
    max-width: 760px;
    margin: 0 auto 35px;
  }

  .btn-light-modern {
    position: relative;
    z-index: 2;
    background: var(--white);
    color: var(--primary);
    font-weight: 800;
    padding: 16px 34px;
    border-radius: 16px;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 12px;
    transition: 0.35s ease;
  }

  .btn-light-modern:hover {
    transform: translateY(-5px);
    color: var(--primary);
  }

  @media(max-width: 991px) {

    .hero-section {
      padding: 70px 0 50px;
    }

    .hero-card,
    .story-wrapper,
    .cta-card {
      padding: 35px;
    }

    .hero-title,
    .cta-title,
    .section-title {
      font-size: 38px;
    }

    .stats-grid {
      grid-template-columns: repeat(2, 1fr);
    }
  }

  @media(max-width: 576px) {

    .hero-title,
    .cta-title,
    .section-title {
      font-size: 30px;
    }

    .hero-description,
    .section-description,
    .story-text,
    .cta-text {
      font-size: 15px;
    }

    .hero-card,
    .feature-card,
    .story-wrapper,
    .cta-card,
    .timeline-card {
      padding: 25px;
      border-radius: 24px;
    }

    .stats-grid {
      grid-template-columns: 1fr;
    }

    .hero-actions {
      flex-direction: column;
    }

    .btn-modern,
    .btn-light-modern {
      justify-content: center;
    }
  }
</style>

<div class="about-page">

  <!-- Hero Section -->
  <section class="hero-section">
    <div class="hero-glow"></div>

    <div class="container">
      <div class="hero-card">

        <span class="hero-badge">
          <i class="fas fa-bolt"></i>
          Powering Innovation Since Day One
        </span>

        <h1 class="hero-title">
          Welcome to
          <span class="gradient-text">R Electric</span>
        </h1>

        <p class="hero-description">
          R Electric is a modern electrical and technology-driven company committed to delivering premium-quality products, trusted services, and innovative business solutions. We combine reliability, smart engineering, and customer satisfaction to create a powerful experience for every client.
        </p>

        <div class="hero-actions">
          <a href="#services" class="btn-modern btn-primary-modern">
            <i class="fas fa-arrow-right"></i>
            Explore Services
          </a>

          <a href="#story" class="btn-modern btn-outline-modern">
            <i class="fas fa-play-circle"></i>
            Our Story
          </a>
        </div>

        <div class="stats-grid">

          <div class="stat-card">
            <div class="stat-icon">
              <i class="fas fa-users"></i>
            </div>
            <div class="stat-number">10K+</div>
            <div class="stat-label">Happy Customers</div>
          </div>

          <div class="stat-card">
            <div class="stat-icon">
              <i class="fas fa-building"></i>
            </div>
            <div class="stat-number">120+</div>
            <div class="stat-label">Business Partners</div>
          </div>

          <div class="stat-card">
            <div class="stat-icon">
              <i class="fas fa-award"></i>
            </div>
            <div class="stat-number">15+</div>
            <div class="stat-label">Years Experience</div>
          </div>

          <div class="stat-card">
            <div class="stat-icon">
              <i class="fas fa-bolt"></i>
            </div>
            <div class="stat-number">24/7</div>
            <div class="stat-label">Support Service</div>
          </div>

        </div>

      </div>
    </div>
  </section>


  <!-- Services -->
  <section class="section" id="services">
    <div class="container">

      <div class="text-center mb-5">
        <h2 class="section-title">What Makes Us Different</h2>
        <p class="section-description">
          We are not just another electrical company. We focus on smart technology, customer trust, elegant service delivery, and long-term reliability.
        </p>
      </div>

      <div class="row g-4">

        <div class="col-lg-4 col-md-6">
          <div class="feature-card">
            <div class="feature-icon">
              <i class="fas fa-microchip"></i>
            </div>

            <h4 class="feature-title">Smart Technology</h4>

            <p class="feature-text">
              We use modern systems, automation, and advanced solutions to provide reliable and efficient electrical services for businesses and customers.
            </p>
          </div>
        </div>

        <div class="col-lg-4 col-md-6">
          <div class="feature-card">
            <div class="feature-icon">
              <i class="fas fa-shield-alt"></i>
            </div>

            <h4 class="feature-title">Trusted Quality</h4>

            <p class="feature-text">
              Every product and service is carefully managed to maintain high quality standards, safety, and long-term durability.
            </p>
          </div>
        </div>

        <div class="col-lg-4 col-md-6">
          <div class="feature-card">
            <div class="feature-icon">
              <i class="fas fa-headset"></i>
            </div>

            <h4 class="feature-title">Customer Support</h4>

            <p class="feature-text">
              Our dedicated support team ensures fast communication, problem solving, and complete customer satisfaction at every stage.
            </p>
          </div>
        </div>

      </div>
    </div>
  </section>


  <!-- Story -->
  <section class="section pt-0" id="story">
    <div class="container">

      <div class="story-wrapper">

        <div class="row align-items-center g-5">

          <div class="col-lg-6">
            <h2 class="section-title mb-4">
              Our Journey & Vision
            </h2>

            <p class="story-text">
              R Electric started with a vision to transform the electrical business industry through innovation, trust, and professionalism. Over the years, we have built strong relationships with clients, suppliers, and partners while continuously improving our services and technology.
              <br><br>
              Today, we are proud to deliver modern electrical solutions with a strong commitment to excellence, integrity, and customer success.
            </p>
          </div>

          <div class="col-lg-6">

            <div class="timeline-card">
              <div class="timeline-year">2010</div>
              <h5 class="timeline-title">Company Founded</h5>
              <p class="timeline-text">
                Started with a mission to provide reliable electrical products and premium customer service.
              </p>
            </div>

            <div class="timeline-card">
              <div class="timeline-year">2018</div>
              <h5 class="timeline-title">Business Expansion</h5>
              <p class="timeline-text">
                Expanded operations with modern inventory systems and large-scale distribution management.
              </p>
            </div>

            <div class="timeline-card mb-0">
              <div class="timeline-year">2026</div>
              <h5 class="timeline-title">Digital Innovation</h5>
              <p class="timeline-text">
                Integrating smart software solutions and advanced technology to build the future of R Electric.
              </p>
            </div>

          </div>

        </div>

      </div>

    </div>
  </section>


  <!-- CTA -->
  <section class="cta-section">
    <div class="container">

      <div class="cta-card">

        <h2 class="cta-title">
          Let’s Build The Future Together
        </h2>

        <p class="cta-text">
          Whether you are looking for reliable electrical products, smart business solutions, or long-term partnership opportunities — R Electric is ready to power your success.
        </p>

        <a href="{{ route('contact') }}" class="btn-light-modern">
          <i class="fas fa-envelope"></i>
          Contact Us Today
        </a>

      </div>

    </div>
  </section>

</div>

@endsection