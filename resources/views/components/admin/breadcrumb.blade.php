@props(['items' => []])

@if(!empty($items))
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            @foreach($items as $index => $item)
                <li class="breadcrumb-item">
                    @if(!empty($item['route']) && Route::has($item['route']))
                        <a href="{{ route($item['route']) }}">{{ $item['title'] }}</a>
                    @else
                        <span>{{ $item['title'] }}</span>
                    @endif
                </li>
            @endforeach
        </ol>
    </nav>
@endif


