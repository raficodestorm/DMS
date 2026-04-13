@extends('layouts.blank')

@section('content')
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<style>
  /* 🔥 Animated Gradient Background */
  .spark-page-wrapper {
    min-height: 100vh;
    width: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 20px;
    position: relative;
    overflow: hidden;

    background: linear-gradient(-45deg, #000000, #410146, #000000);

  }

  /* Bubble container */
  .bubbles {
    position: absolute;
    inset: 0;
    z-index: 0;
    overflow: hidden;
  }

  /* Bubble style */
  .bubble {
    position: absolute;
    bottom: -150px;
    background: rgba(255, 255, 255, 0.274);
    border: 0.5px solid rgba(228, 4, 236, 0.523);
    border-radius: 50%;
    opacity: 0;
    animation: moveBubbles linear infinite;
    box-shadow: 0 0 25px rgba(255, 255, 255, 0.25);
    backdrop-filter: blur(3px);
  }

  /* Bubble animation */
  @keyframes moveBubbles {
    0% {
      transform: translateY(0) scale(0.5);
      opacity: 0;
    }

    20% {
      opacity: 0.6;
    }

    80% {
      opacity: 0.4;
    }

    100% {
      transform: translateY(-170vh) scale(1.2);
      opacity: 0;
    }
  }

  /* 🔥 MORE BUBBLES (12 total) */
  .bubble:nth-child(1) {
    left: 5%;
    width: 60px;
    height: 60px;
    animation-duration: 8s;
  }

  .bubble:nth-child(2) {
    left: 15%;
    width: 30px;
    height: 30px;
    animation-duration: 10s;
    animation-delay: 2s;
  }

  .bubble:nth-child(3) {
    left: 25%;
    width: 90px;
    height: 90px;
    animation-duration: 12s;
    animation-delay: 4s;
  }

  .bubble:nth-child(4) {
    left: 35%;
    width: 50px;
    height: 50px;
    animation-duration: 9s;
  }

  .bubble:nth-child(5) {
    left: 45%;
    width: 120px;
    height: 120px;
    animation-duration: 14s;
    animation-delay: 3s;
  }

  .bubble:nth-child(6) {
    left: 55%;
    width: 40px;
    height: 40px;
    animation-duration: 11s;
    animation-delay: 5s;
  }

  .bubble:nth-child(7) {
    left: 65%;
    width: 80px;
    height: 80px;
    animation-duration: 13s;
  }

  .bubble:nth-child(8) {
    left: 75%;
    width: 35px;
    height: 35px;
    animation-duration: 9s;
    animation-delay: 6s;
  }

  .bubble:nth-child(9) {
    left: 85%;
    width: 100px;
    height: 100px;
    animation-duration: 15s;
    animation-delay: 2s;
  }

  .bubble:nth-child(10) {
    left: 95%;
    width: 50px;
    height: 50px;
    animation-duration: 10s;
  }

  .bubble:nth-child(11) {
    left: 70%;
    width: 70px;
    height: 70px;
    animation-duration: 12s;
    animation-delay: 7s;
  }

  .bubble:nth-child(12) {
    left: 30%;
    width: 45px;
    height: 45px;
    animation-duration: 8s;
    animation-delay: 3s;
  }

  /* ==============================
     CARD (UNCHANGED)
  ============================== */
  .premium-id-card {
    width: 100%;
    max-width: 400px;
    height: auto;
    background: var(--section-bg);
    border-radius: 30px;
    position: relative;
    z-index: 10;
    overflow: hidden;
    border: 1px solid var(--glass);
    box-shadow: 3px 20px 60px 0px rgba(255, 255, 255, 0.69);
    display: flex;
    flex-direction: column;
  }

  .header-accent {
    height: 100px;
    background: linear-gradient(135deg, var(--primary) 0%, var(--accent) 100%);
    position: relative;
    display: flex;
    justify-content: center;
  }

  .photo-container {
    position: absolute;
    bottom: -60px;
    width: 130px;
    height: 130px;
    overflow: hidden;
    border-radius: 50%;
    background: var(--section-bg);
    box-shadow: 0 15px 30px rgba(0, 0, 0, 0.2);
  }

  .photo-container img {
    width: 100%;
    height: 100%;
    object-fit: cover;
  }

  .content-area {
    flex: 1;
    padding: 75px 30px 20px;
    text-align: center;
  }

  .employee-name {
    font-size: 2rem;
    font-weight: 800;
    color: var(--text-main);
    margin: 0;
  }

  .rank-pill {
    display: inline-block;
    margin-top: 8px;
    padding: 4px 16px;
    background: var(--primary-soft);
    color: var(--primary);
    border-radius: 50px;
    font-size: 0.75rem;
    font-weight: 700;
  }

  .info-list {
    margin-top: 30px;
    text-align: left;
    background: var(--background);
    border-radius: 24px;
    padding: 15px;
    border: 1px solid var(--border-color);
  }

  .info-group {
    display: flex;
    justify-content: space-between;
    padding: 9px 0;
    border-bottom: 1px solid var(--border-color);
  }

  .info-group:last-child {
    border: 0;
  }

  .i-label {
    color: var(--primary);
    font-size: 0.8rem;
  }

  .i-value {
    color: var(--text-main);
    font-size: 0.8rem;
  }

  .employee-statement {
    margin-top: 25px;
    padding: 15px;
    background: var(--glass);
    border-radius: 18px;
    border-left: 4px solid var(--accent);
  }

  .statement-text {
    font-size: 0.8rem;
    color: var(--text-muted);
    font-style: italic;
  }

  .card-footer-branding {
    padding: 20px;
    text-align: center;
    background: var(--secondary);
  }

  .brand-text {
    color: #fff;
    font-weight: 900;
    font-size: 0.9rem;
    letter-spacing: 2px;
  }

  @media (max-width: 480px) {
    .premium-id-card {
      max-width: 100%;
      border-radius: 25px;
    }

    .content-area {
      padding: 70px 15px 15px;
    }

    .employee-name {
      font-size: 1.5rem;
    }

    .info-list {
      padding: 15px;
    }

    .photo-container {
      width: 110px;
      height: 110px;
      bottom: -50px;
    }

    .header-accent {
      height: 90px;
    }
  }
</style>

<div class="spark-page-wrapper">

  <!-- Bubbles -->
  <div class="bubbles">
    <div class="bubble"></div>
    <div class="bubble"></div>
    <div class="bubble"></div>
    <div class="bubble"></div>
    <div class="bubble"></div>
    <div class="bubble"></div>
    <div class="bubble"></div>
    <div class="bubble"></div>
    <div class="bubble"></div>
    <div class="bubble"></div>
    <div class="bubble"></div>
    <div class="bubble"></div>
  </div>

  <!-- Card -->
  <div class="premium-id-card">
    <div class="header-accent">
      <div class="photo-container">
        <img class="img-fluid"
          src="{{ $employee->photo ? asset('storage/' . $employee->photo) : 'https://ui-avatars.com/api/?name='.urlencode($employee->name).'&background=3131ff&color=fff' }}">
      </div>
    </div>

    <div class="content-area">
      <h1 class="employee-name">{{ $employee->name }}</h1>
      <div class="rank-pill">{{ $employee->rank }}</div>

      <div class="info-list">
        <div class="info-group">
          <span class="i-label">Employee ID</span>
          <span class="i-value">BRE100{{ $employee->id }}</span>
        </div>
        <div class="info-group">
          <span class="i-label">Branch</span>
          <span class="i-value">{{ $employee->branch->name ?? 'Head Office' }}</span>
        </div>
        <div class="info-group">
          <span class="i-label">Contact</span>
          <span class="i-value">{{ $employee->phone }}</span>
        </div>

      </div>
      <div class="employee-statement">
        <p class="statement-text">
          "This pass certifies that <strong>{{ $employee->name }}</strong> is a verified professional of
          <strong>R.Electric</strong>.
          Dedicated to high-performance engineering and operational excellence,
          this team member plays a vital role in our mission to lead the industry."
        </p>
      </div>
    </div>

    <div class="card-footer-branding">
      <a href="https://relectric.com" class="brand-text">R.ELECTRIC BANGLADESH<i
          class="fa-solid fa-arrow-right"></i></a>
    </div>
  </div>

</div>
@endsection