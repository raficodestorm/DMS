@php
    $footerCategories = \App\Models\Category::where('is_featured', true)
        ->orderBy('name', 'asc')
        ->take(4)
        ->get();

    if ($footerCategories->isEmpty()) {
        $footerCategories = \App\Models\Category::orderBy('name', 'asc')->take(4)->get();
    }
@endphp

<footer class="mt-5">
    <div class="container">
        <div class="row g-4 g-lg-5">
            <!-- Brand & Info -->
            <div class="col-lg-4 col-md-6">
                <a class="brand d-inline-block" href="{{ route('home-page') }}">
                    <img src="{{ asset('image/relectric-logo.png') }}" class="footer-brand-logo img-fluid" alt="{{ config('app.name', 'R Electric') }}">
                </a>
                <p class="mt-3"
                    style="font-size: 13.5px; color: var(--text-muted); line-height: 1.8;">
                    <strong style="color: var(--primary);">"Touch and Shock"</strong><br>
                    Delivering electrical excellence nationwide. We combine nationwide
                    accessibility with a commitment to service that powers your happiness.
                </p>
                <div class="mt-4 footer-social-wrap">
                    <a href="#" class="social-circle" title="Facebook"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" class="social-circle" title="Instagram"><i class="fab fa-instagram"></i></a>
                    <a href="#" class="social-circle" title="YouTube"><i class="fab fa-youtube"></i></a>
                </div>
            </div>

            <!-- Navigation Links -->
            <div class="col-lg-2 col-md-6 col-6">
                <h5 class="footer-section-title">Navigation</h5>
                <ul class="footer-links">
                    <li><a href="{{ route('home-page') }}">Home</a></li>
                    <li><a href="{{ route('home-page') }}#products">All Products</a></li>
                    <li><a href="{{ route('about') }}">About Us</a></li>
                    <li><a href="{{ route('contact') }}">Contact Us</a></li>
                </ul>
            </div>

            <!-- Dynamic Featured Categories -->
            <div class="col-lg-3 col-md-6 col-6">
                <h5 class="footer-section-title">Categories</h5>
                <ul class="footer-links">
                    @forelse($footerCategories as $fCat)
                        <li><a href="{{ route('home-page') }}?category={{ $fCat->id }}">{{ $fCat->name }}</a></li>
                    @empty
                        <li><a href="{{ route('home-page') }}">Cables & Wires</a></li>
                        <li><a href="{{ route('home-page') }}">Switches & Sockets</a></li>
                        <li><a href="{{ route('home-page') }}">Lighting Solutions</a></li>
                        <li><a href="{{ route('home-page') }}">Professional Tools</a></li>
                    @endforelse
                </ul>
            </div>

            <!-- Ask a Question Glass Card -->
            <div class="col-lg-3 col-md-6">
                <div class="footer-ask-card">
                    <h5 class="footer-section-title" style="margin-bottom: 8px;">Ask a Question</h5>
                    <p style="font-size: 12.5px; color: var(--text-muted); margin-bottom: 14px; line-height: 1.5;">
                        Have a thought or a curious question? Send it to us!
                    </p>

                    <div id="success-message" style="display: none;" class="mb-3">
                        <div class="d-flex align-items-center gap-2"
                            style="background: var(--primary-soft); border: 1px solid var(--accent); padding: 10px; border-radius: 10px;">
                            <i class="fas fa-check-circle" style="color: var(--accent);"></i>
                            <small style="color: var(--text-main); font-weight: 500;">Thanks! We'll reply soon.</small>
                        </div>
                    </div>

                    <form action="" method="POST">
                        <textarea name="user_question" class="form-control question-input mb-3" rows="2"
                            placeholder="What's on your mind?" required></textarea>
                        <button type="submit" name="submit_question" class="btn btn-ask w-100">
                            <i class="fas fa-paper-plane me-1"></i> Submit
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Footer Bottom Bar -->
        <div class="footer-bottom d-md-flex justify-content-between align-items-center">
            <p class="mb-0">&copy;
                <span id="current-year">{{ date('Y') }}</span> <span style="color: var(--primary); font-weight: 700;">{{ config('app.name') }}</span>.
                All rights reserved.
            </p>
            <p class="mb-0">Built with ❤️ by <a class="rafi-link" href="https://safiulrafi.top" target="_blank" rel="noopener">S A Rafi</a></p>
        </div>
    </div>
</footer>