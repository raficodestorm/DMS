@extends('layouts.userlayout')

@section('title','Contact Us')

@section('content')

<style>
  .contact-page {
    background: linear-gradient(180deg, #f8fafc 0%, #ffffff 100%);
    min-height: 100vh;
    overflow: hidden;
  }

  .contact-hero {
    padding: 90px 0 70px;
    position: relative;
  }

  .contact-glow {
    position: absolute;
    width: 420px;
    height: 420px;
    border-radius: 50%;
    background: radial-gradient(circle, rgba(49, 49, 255, 0.16) 0%, rgba(174, 4, 241, 0.08) 45%, transparent 75%);
    top: -120px;
    left: -120px;
    pointer-events: none;
  }

  .contact-card {
    background: rgba(255, 255, 255, 0.92);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.5);
    border-radius: 34px;
    padding: 55px;
    box-shadow: 0 20px 60px rgba(15, 23, 42, 0.08);
    position: relative;
    z-index: 2;
  }

  .section-badge {
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

  .contact-title {
    font-size: 60px;
    font-weight: 800;
    line-height: 1.1;
    color: var(--text-main);
    margin-bottom: 24px;
  }

  .gradient-text {
    background: linear-gradient(135deg, var(--primary), var(--accent));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
  }

  .contact-description {
    font-size: 18px;
    line-height: 1.9;
    color: var(--text-muted);
    max-width: 760px;
    margin-bottom: 45px;
  }

  .info-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 24px;
  }

  .info-card {
    background: var(--white);
    border-radius: 24px;
    padding: 30px;
    border: 1px solid var(--border-color);
    transition: 0.35s ease;
    height: 100%;
  }

  .info-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 24px 50px rgba(15, 23, 42, 0.08);
  }

  .info-icon {
    width: 70px;
    height: 70px;
    border-radius: 22px;
    background: linear-gradient(135deg, var(--primary), var(--accent));
    color: var(--white);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 28px;
    margin-bottom: 22px;
  }

  .info-title {
    font-size: 24px;
    font-weight: 800;
    color: var(--text-main);
    margin-bottom: 12px;
  }

  .info-text {
    color: var(--text-muted);
    line-height: 1.9;
    font-size: 15px;
  }

  .main-section {
    padding-bottom: 100px;
  }

  .contact-form-wrapper {
    background: var(--white);
    border-radius: 34px;
    padding: 50px;
    border: 1px solid var(--border-color);
    box-shadow: 0 20px 50px rgba(15, 23, 42, 0.05);
    height: 100%;
  }

  .form-title {
    font-size: 40px;
    font-weight: 800;
    color: var(--text-main);
    margin-bottom: 16px;
  }

  .form-description {
    color: var(--text-muted);
    line-height: 1.8;
    margin-bottom: 35px;
  }

  .input-group-modern {
    margin-bottom: 24px;
  }

  .input-label {
    display: block;
    margin-bottom: 10px;
    color: var(--text-main);
    font-weight: 700;
    font-size: 15px;
  }

  .form-control-modern {
    width: 100%;
    border: 1.5px solid var(--border-color);
    border-radius: 16px;
    padding: 16px 18px;
    font-size: 15px;
    color: var(--text-main);
    background: var(--white);
    transition: 0.3s ease;
  }

  .form-control-modern:focus {
    outline: none;
    border-color: var(--primary);
    box-shadow: 0 0 0 4px rgba(49, 49, 255, 0.10);
  }

  textarea.form-control-modern {
    min-height: 160px;
    resize: none;
  }

  .btn-contact {
    border: none;
    background: linear-gradient(135deg, var(--primary), var(--accent));
    color: var(--white);
    padding: 16px 32px;
    border-radius: 16px;
    font-weight: 800;
    font-size: 15px;
    display: inline-flex;
    align-items: center;
    gap: 12px;
    transition: 0.35s ease;
    box-shadow: 0 12px 30px rgba(49, 49, 255, 0.25);
  }

  .btn-contact:hover {
    transform: translateY(-5px);
  }

  .map-card {
    background: linear-gradient(135deg, #ffffff 0%, #f6f8ff 100%);
    border-radius: 34px;
    padding: 50px;
    border: 1px solid var(--border-color);
    height: 100%;
    position: relative;
    overflow: hidden;
  }

  .map-card::after {
    content: '';
    position: absolute;
    width: 300px;
    height: 300px;
    border-radius: 50%;
    background: radial-gradient(circle, rgba(174, 4, 241, 0.08), transparent 70%);
    bottom: -100px;
    right: -100px;
  }

  .map-title {
    font-size: 40px;
    font-weight: 800;
    color: var(--text-main);
    margin-bottom: 18px;
    position: relative;
    z-index: 2;
  }

  .map-description {
    color: var(--text-muted);
    line-height: 1.9;
    margin-bottom: 35px;
    position: relative;
    z-index: 2;
  }

  .contact-list {
    position: relative;
    z-index: 2;
  }

  .contact-item {
    display: flex;
    align-items: flex-start;
    gap: 18px;
    margin-bottom: 28px;
  }

  .contact-item:last-child {
    margin-bottom: 0;
  }

  .contact-item-icon {
    width: 58px;
    height: 58px;
    border-radius: 18px;
    background: var(--primary-soft);
    color: var(--primary);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 22px;
    flex-shrink: 0;
  }

  .contact-item-title {
    font-size: 18px;
    font-weight: 800;
    color: var(--text-main);
    margin-bottom: 6px;
  }

  .contact-item-text {
    color: var(--text-muted);
    line-height: 1.8;
    font-size: 15px;
  }

  .bottom-cta {
    margin-top: 70px;
    background: linear-gradient(135deg, var(--primary), var(--accent));
    border-radius: 38px;
    padding: 70px 50px;
    text-align: center;
    position: relative;
    overflow: hidden;
  }

  .bottom-cta::before {
    content: '';
    position: absolute;
    width: 250px;
    height: 250px;
    background: rgba(255, 255, 255, 0.08);
    border-radius: 50%;
    top: -80px;
    left: -70px;
  }

  .bottom-cta::after {
    content: '';
    position: absolute;
    width: 320px;
    height: 320px;
    background: rgba(255, 255, 255, 0.05);
    border-radius: 50%;
    right: -120px;
    bottom: -120px;
  }

  .cta-title {
    position: relative;
    z-index: 2;
    color: var(--white);
    font-size: 50px;
    font-weight: 800;
    margin-bottom: 18px;
  }

  .cta-text {
    position: relative;
    z-index: 2;
    color: rgba(255, 255, 255, 0.88);
    max-width: 760px;
    margin: 0 auto 35px;
    line-height: 1.9;
    font-size: 18px;
  }

  .btn-light-modern {
    position: relative;
    z-index: 2;
    background: var(--white);
    color: var(--primary);
    padding: 16px 34px;
    border-radius: 16px;
    text-decoration: none;
    font-weight: 800;
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

    .contact-card,
    .contact-form-wrapper,
    .map-card,
    .bottom-cta {
      padding: 35px;
    }

    .contact-title,
    .form-title,
    .map-title,
    .cta-title {
      font-size: 38px;
    }

    .info-grid {
      grid-template-columns: repeat(2, 1fr);
    }
  }

  @media(max-width: 576px) {

    .contact-title,
    .form-title,
    .map-title,
    .cta-title {
      font-size: 30px;
    }

    .contact-description,
    .map-description,
    .cta-text {
      font-size: 15px;
    }

    .contact-card,
    .contact-form-wrapper,
    .map-card,
    .bottom-cta,
    .info-card {
      padding: 24px;
      border-radius: 24px;
    }

    .info-grid {
      grid-template-columns: 1fr;
    }

    .btn-contact,
    .btn-light-modern {
      width: 100%;
      justify-content: center;
    }
  }
</style>

<div class="contact-page">

  <!-- Hero -->
  <section class="contact-hero">

    <div class="contact-glow"></div>

    <div class="container">

      <div class="contact-card">

        <span class="section-badge">
          <i class="fas fa-headset"></i>
          We’re Always Ready To Help
        </span>

        <h1 class="contact-title">
          Contact
          <span class="gradient-text">R Electric</span>
        </h1>

        <p class="contact-description">
          Have questions, business inquiries, or partnership ideas? Our team is here to assist you with fast
          communication, reliable support, and professional service.
        </p>

        <div class="info-grid">

          <div class="info-card">
            <div class="info-icon">
              <i class="fas fa-phone-alt"></i>
            </div>

            <h4 class="info-title">Call Us</h4>

            <p class="info-text">
              +880 1871-923000
              <br>
              +880 1871-923000
            </p>
          </div>

          <div class="info-card">
            <div class="info-icon">
              <i class="fas fa-envelope"></i>
            </div>

            <h4 class="info-title">Email Address</h4>

            <p class="info-text">
              relectricbdofficial@gmail.com
              <br>
              relectricbdofficial@gmail.com
            </p>
          </div>

          <div class="info-card">
            <div class="info-icon">
              <i class="fas fa-map-marker-alt"></i>
            </div>

            <h4 class="info-title">Office Location</h4>

            <p class="info-text">
              Mistiripara, Chattogram, Bangladesh
              <br>
              Corporate Business Area
            </p>
          </div>

        </div>

      </div>

    </div>

  </section>


  <!-- Main Contact -->
  <section class="main-section">

    <div class="container">

      <div class="row g-4 align-items-stretch">

        <!-- Form -->
        <div class="col-lg-7">

          <div class="contact-form-wrapper">

            <h2 class="form-title">
              Send Us A Message
            </h2>

            <p class="form-description">
              Fill out the form below and our team will contact you as soon as possible.
            </p>

            <form>

              <div class="row">

                <div class="col-md-6">
                  <div class="input-group-modern">
                    <label class="input-label">Full Name</label>
                    <input type="text" class="form-control-modern" placeholder="Enter your full name">
                  </div>
                </div>

                <div class="col-md-6">
                  <div class="input-group-modern">
                    <label class="input-label">Phone Number</label>
                    <input type="text" class="form-control-modern" placeholder="Enter phone number">
                  </div>
                </div>

              </div>

              <div class="input-group-modern">
                <label class="input-label">Email Address</label>
                <input type="email" class="form-control-modern" placeholder="Enter your email">
              </div>

              <div class="input-group-modern">
                <label class="input-label">Subject</label>
                <input type="text" class="form-control-modern" placeholder="Write message subject">
              </div>

              <div class="input-group-modern">
                <label class="input-label">Your Message</label>
                <textarea class="form-control-modern" placeholder="Write your message here..."></textarea>
              </div>

              <button type="submit" class="btn-contact">
                <i class="fas fa-paper-plane"></i>
                Send Message
              </button>

            </form>

          </div>

        </div>


        <!-- Contact Info -->
        <div class="col-lg-5">

          <div class="map-card">

            <h2 class="map-title">
              Contact Information
            </h2>

            <p class="map-description">
              Reach out to us anytime for support, service inquiries, or business discussions.
            </p>

            <div class="contact-list">

              <div class="contact-item">
                <div class="contact-item-icon">
                  <i class="fas fa-map-marked-alt"></i>
                </div>

                <div>
                  <h5 class="contact-item-title">Office Address</h5>
                  <p class="contact-item-text">
                    R Electric Corporate Office,
                    <br>
                    Chattogram, Bangladesh
                  </p>
                </div>
              </div>

              <div class="contact-item">
                <div class="contact-item-icon">
                  <i class="fas fa-phone-volume"></i>
                </div>

                <div>
                  <h5 class="contact-item-title">Phone Support</h5>
                  <p class="contact-item-text">
                    +880 1871-923000
                    <br>
                    Available 24/7
                  </p>
                </div>
              </div>

              <div class="contact-item">
                <div class="contact-item-icon">
                  <i class="fas fa-envelope-open-text"></i>
                </div>

                <div>
                  <h5 class="contact-item-title">Email Support</h5>
                  <p class="contact-item-text">
                    support@relectric.com
                    <br>
                    relectricbdofficial@gmail.com
                  </p>
                </div>
              </div>

              <div class="contact-item">
                <div class="contact-item-icon">
                  <i class="fas fa-clock"></i>
                </div>

                <div>
                  <h5 class="contact-item-title">Working Hours</h5>
                  <p class="contact-item-text">
                    Saturday - Thursday
                    <br>
                    9:00 AM - 9:00 PM
                  </p>
                </div>
              </div>

            </div>

          </div>

        </div>

      </div>


      <!-- CTA -->
      <div class="bottom-cta">

        <h2 class="cta-title">
          Ready To Work With R Electric?
        </h2>

        <p class="cta-text">
          We are committed to providing modern electrical solutions, trusted services, and long-term business
          relationships.
        </p>

        <a href="#" class="btn-light-modern">
          <i class="fas fa-arrow-right"></i>
          Start Conversation
        </a>

      </div>

    </div>

  </section>

</div>

@endsection