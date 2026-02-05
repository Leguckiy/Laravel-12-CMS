<footer id="footer" class="footer mt-auto">
    <div class="footer-columns bg-dark text-light py-4">
        <div class="container">
            <div class="row g-4">
                @foreach ($frontFooterColumns as $column)
                    <div class="col-12 col-md-4">
                        <h6 class="footer-column-title text-uppercase fw-bold mb-3 d-none d-md-block">{{ $column['title'] }}</h6>
                        <button class="footer-column-toggle btn btn-link p-0 text-start d-md-none w-100 text-uppercase fw-bold text-light text-decoration-none d-flex justify-content-between align-items-center" type="button" data-bs-toggle="collapse" data-bs-target="#footer-collapse-{{ $column['id'] }}" aria-expanded="false" aria-controls="footer-collapse-{{ $column['id'] }}">
                            <span>{{ $column['title'] }}</span>
                            <i class="fa-solid fa-chevron-down footer-column-chevron"></i>
                        </button>
                        <div class="collapse d-md-block" id="footer-collapse-{{ $column['id'] }}">
                            <ul class="footer-links list-unstyled mb-0 mt-2 mt-md-0">
                                @foreach ($column['links'] as $link)
                                    <li><a href="{{ $link['url'] }}" class="text-light text-decoration-none">{{ $link['label'] }}</a></li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    <div class="footer-bottom bg-dark border-top border-secondary py-3">
        <div class="container">
            <p class="mb-0 text-secondary small">&copy; {{ date('Y') }} {{ config('app.name') }}. {{ __('front/general.all_rights_reserved') }}</p>
        </div>
    </div>
</footer>
