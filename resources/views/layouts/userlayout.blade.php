<!DOCTYPE html>
<html lang="en">

<head>
  <script>
    if (localStorage.getItem('theme') === 'dark') {
      document.documentElement.setAttribute('data-theme', 'dark');
    }
  </script>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>{{ config('app.name') }}</title>
  <meta name="title" content="R Electric | Electrical Solutions">
  <meta name="description"
    content="R Electric is a trusted electrical and engineering solutions company providing professional services, quality products, and reliable technical support in Bangladesh.">
  <meta name="keywords"
    content="R Electric, Electrical Company Bangladesh, Engineering Solutions, Electrical Services, Electrical Products, Industrial Electrical Solutions, R Electric Bangladesh">
  <meta name="author" content="R Electric">

  <meta property="og:type" content="website">
  <meta property="og:url" content="https://relectricbd.com/">
  <meta property="og:title" content="R Electric | Electrical Solutions">
  <meta property="og:description"
    content="Professional electrical solutions, engineering services, and quality products for modern businesses and industries.">
  <meta property="og:image" content="https://relectricbd.com/image/relectric-r-logo.webp">

  <meta property="og:site_name" content="R Electric">

  <link rel="canonical" href="https://relectricbd.com/">

  <link rel="icon" type="image/png" sizes="35x35" href="{{ asset('image/relectric-r-logo.webp') }}">

  <link rel="stylesheet" href="{{ asset('css/color-root.css') }}">
  {{-- swiper js --}}
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css">
  <link rel="stylesheet" href="https://unpkg.com/aos@2.3.4/dist/aos.css">
  <!-- Vite CSS + JS -->
  @vite(['resources/css/app.css','resources/js/app.js'])

  <link rel="stylesheet" href="{{ asset('./css/user/userstyle.css') }}">
  <link rel="stylesheet" href="{{ asset('./css/user/product_card.css') }}">

  @if(request()->is('/') || request()->routeIs('home-page'))
  <style>
    #preloader {
      position: fixed;
      inset: 0;
      display: flex;
      justify-content: center;
      align-items: center;
      flex-direction: column;
      background: #f9fcfeff;
      z-index: 99999;
      transition: opacity 0.6s ease, visibility 0.6s ease;
    }

    .loader-logo {
      width: 240px;
      margin-bottom: 30px;
      filter: drop-shadow(0 0 15px rgba(99, 102, 241, 0.3));
      animation: logoPulse 2s ease-in-out infinite;
    }

    .dots-container {
      display: flex;
      gap: 8px;
    }

    .dot {
      width: 10px;
      height: 10px;
      border-radius: 50%;
      background: var(--primary);
      animation: dotJump 1.4s infinite ease-in-out both;
    }

    .dot:nth-child(2) {
      background: var(--third);
      animation-delay: 0.2s;
    }

    .dot:nth-child(3) {
      animation-delay: 0.4s;
    }

    @keyframes logoPulse {
      0%, 100% {
        transform: scale(1);
        opacity: 0.8;
      }
      50% {
        transform: scale(1.1);
        opacity: 1;
      }
    }

    @keyframes dotJump {
      0%, 80%, 100% {
        transform: scale(0);
        opacity: 0.3;
      }
      40% {
        transform: scale(1);
        opacity: 1;
      }
    }

    body.loaded #preloader {
      opacity: 0;
      visibility: hidden;
      pointer-events: none;
    }
  </style>
  @endif
</head>

<body>
  @if(request()->is('/') || request()->routeIs('home-page'))
  <div id="preloader">
    <img src="{{ asset('image/relectric-logo.png') }}" class="loader-logo" alt="R ELECTRIC">
    <div class="dots-container">
      <div class="dot"></div>
      <div class="dot"></div>
      <div class="dot"></div>
    </div>
  </div>
  @endif

  @include('components.navbar')
  @include('components.login-modal')
  @include('components.cart-modal')
  @include('components.wishlist-modal')

  <div>
    @yield('content')
  </div>
  @include('components.footer')
  @stack('scripts')
  <script src="{{ asset('./js/user.js') }}"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous">

  </script>
  <!-- jQuery -->
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

  @if(request()->is('/') || request()->routeIs('home-page'))
  <script>
    window.addEventListener("load", () => {
      setTimeout(() => {
        document.body.classList.add("loaded");
      }, 2000);
    });
  </script>
  @endif

</body>

</html>