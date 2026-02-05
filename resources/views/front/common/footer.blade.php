<footer id="footer" class="footer mt-auto">
    <div class="footer-columns bg-dark text-light py-4">
        <div class="container">
            <div class="row g-4">
                <div class="col-12 col-md-4">
                    <h6 class="footer-column-title text-uppercase fw-bold mb-3 d-none d-md-block">Information</h6>
                    <button class="footer-column-toggle btn btn-link p-0 text-start d-md-none w-100 text-uppercase fw-bold text-light text-decoration-none d-flex justify-content-between align-items-center" type="button" data-bs-toggle="collapse" data-bs-target="#footer-collapse-information" aria-expanded="false" aria-controls="footer-collapse-information">
                        <span>Information</span>
                        <i class="fa-solid fa-chevron-down footer-column-chevron"></i>
                    </button>
                    <div class="collapse d-md-block" id="footer-collapse-information">
                        <ul class="footer-links list-unstyled mb-0 mt-2 mt-md-0">
                            <li><a href="#">Terms &amp; Condition</a></li>
                            <li><a href="#">Delivery Information</a></li>
                            <li><a href="#">About Us</a></li>
                            <li><a href="#">Privacy Policy</a></li>
                        </ul>
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <h6 class="footer-column-title text-uppercase fw-bold mb-3 d-none d-md-block">Customer Service</h6>
                    <button class="footer-column-toggle btn btn-link p-0 text-start d-md-none w-100 text-uppercase fw-bold text-light text-decoration-none d-flex justify-content-between align-items-center" type="button" data-bs-toggle="collapse" data-bs-target="#footer-collapse-customer" aria-expanded="false" aria-controls="footer-collapse-customer">
                        <span>Customer Service</span>
                        <i class="fa-solid fa-chevron-down footer-column-chevron"></i>
                    </button>
                    <div class="collapse d-md-block" id="footer-collapse-customer">
                        <ul class="footer-links list-unstyled mb-0 mt-2 mt-md-0">
                            <li><a href="#">Contact Us</a></li>
                        </ul>
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <h6 class="footer-column-title text-uppercase fw-bold mb-3 d-none d-md-block">My Account</h6>
                    <button class="footer-column-toggle btn btn-link p-0 text-start d-md-none w-100 text-uppercase fw-bold text-light text-decoration-none d-flex justify-content-between align-items-center" type="button" data-bs-toggle="collapse" data-bs-target="#footer-collapse-account" aria-expanded="false" aria-controls="footer-collapse-account">
                        <span>My Account</span>
                        <i class="fa-solid fa-chevron-down footer-column-chevron"></i>
                    </button>
                    <div class="collapse d-md-block" id="footer-collapse-account">
                        <ul class="footer-links list-unstyled mb-0 mt-2 mt-md-0">
                            <li><a href="#">My Account</a></li>
                            <li><a href="#">Order History</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="footer-bottom bg-dark border-top border-secondary py-3">
        <div class="container">
            <p class="mb-0 text-secondary small">&copy; {{ date('Y') }} {{ config('app.name') }}. {{ __('front/general.all_rights_reserved') }}</p>
        </div>
    </div>
</footer>
