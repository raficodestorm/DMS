<footer class="mt-5">
    <div class="container">
        <div class="row g-5">
            <div class="col-lg-4 col-md-6">
                <a class="brand" href="{{ route('home-page') }}">
      <img src="{{ asset('image/relectric-logo.png') }}" class="img-logo img-fluid" id="img-logo" alt="relectric" >
    </a>
                <p class="mt-3"
                    style="font-size: clamp(0.8rem, 2vw, 0.9rem); color: var(--text-muted); line-height: 1.8;">
                    "Touch and Shock" <br>
                    Delivering electrical excellence nationwide. We combine nationwide
                    accessibility with a
                    commitment to service that powers your happiness
                </p>
                <div class="mt-4">
                    <a href="#" class="social-circle"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" class="social-circle"><i class="fab fa-instagram"></i></a>
                    <a href="#" class="social-circle"><i class="fab fa-youtube"></i></a>
                </div>
            </div>

            <div class="col-lg-2 col-md-6">
                <h5 class="footer-section-title">Navigation</h5>
                <ul class="footer-links">
                    <li><a href="#">Home</a></li>
                    <li><a href="#">All Products</a></li>
                    <li><a href="#">About Author</a></li>
                    <li><a href="#">Contact</a></li>
                </ul>
            </div>

            <div class="col-lg-3 col-md-6">
                <h5 class="footer-section-title">Categories</h5>
                <ul class="footer-links">

                    <li><a href="">Cable</a></li>
                    <li><a href="">Socket</a></li>
                    <li><a href="">Light</a></li>
                    <li><a href="">All Electronics</a></li>

                </ul>
            </div>

            <div class="col-lg-3 col-md-6">
                <h5 class="footer-section-title">Ask a Question</h5>
                <p
                    style="font-size: clamp(0.8rem, 2vw, 0.9rem); color: var(--text-muted); margin-bottom: clamp(0.8rem, 3vw, 1.2rem);">
                    Have a thought or a curious question? Send it my way!
                </p>

                <div id="success-message" style="display: none;" class="mb-3">
                    <div class="d-flex align-items-center gap-2"
                        style="background: rgba(197, 160, 89, 0.1); border: 1px solid var(--accent); padding: 12px; border-radius: 12px;">
                        <i class="fas fa-check-circle" style="color: var(--accent-light);"></i>
                        <small style="color: var(--text-main); font-weight: 500;">Thanks! I'll read your question
                            soon.</small>
                    </div>
                </div>

                <form action="" method="POST">
                    <textarea name="user_question" class="form-control question-input mb-2" rows="2"
                        placeholder="What's on your mind?" required></textarea>
                    <button type="submit" name="submit_question" class="btn btn-ask w-100">
                        Submit
                    </button>
                </form>
            </div>
        </div>

        <div class="footer-bottom d-md-flex justify-content-between align-items-center">
            <p class="mb-0">&copy;
                <?= date('Y') ?> <span style="color: var(--accent); font-weight: 600;">{{ config('app.name') }}</span>.
                All rights
                reserved.
            </p>
            <p class="mb-0">Built with ❤️ by <a class="rafi-link" href="https://safiulrafi.top"> S A Rafi</a></p>
        </div>
    </div>
</footer>

<script>
    // set current year (keeps HTML static but copyright current)
    (function() {
        const el = document.getElementById('current-year');
        if (el) el.textContent = new Date().getFullYear();
    })();
</script>