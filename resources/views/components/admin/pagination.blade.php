@props(['paginator'])

@if($paginator->hasPages())
    <div class="pagination-wrapper">
        <div class="pagination-links">
            {{ $paginator->onEachSide(2)->links() }}
        </div>
        <div class="pagination-info">
            <p class="text-muted mb-0">
                {{ __('pagination.showing', [
                    'first' => $paginator->firstItem(),
                    'last' => $paginator->lastItem(),
                    'total' => $paginator->total(),
                    'pages' => $paginator->lastPage(),
                ]) }}
            </p>
        </div>
    </div>
@endif
