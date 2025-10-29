<nav id="column-left">
    <div id="navigation"><span class="fa-solid fa-bars"></span> {{ __('admin.navigation') }}</div>
    <ul id="menu">
        @foreach($menuItems as $index => $menu)
            <li id="{{ $menu['id'] }}" @class(['active' => $menu['is_active']])>
                @if($menu['children'])
                    <a href="#collapse-{{ $index }}" data-bs-toggle="collapse" @class(['parent', 'collapsed' => !$menu['is_active']])><i class="{{ $menu['icon'] }}"></i> {{ $menu['name'] }}</a>
                    <ul id="collapse-{{ $index }}" @class(['collapse', 'show' => $menu['is_active']])>
                        @foreach($menu['children'] as $childIndex => $children_1)
                            <li @class(['active' => $children_1['is_active']])>
                                @if($children_1['children'])
                                    <a href="#collapse-{{ $index }}-{{ $childIndex }}" data-bs-toggle="collapse" @class(['parent', 'collapsed' => !$children_1['is_active']])>{{ $children_1['name'] }}</a>
                                    <ul id="collapse-{{ $index }}-{{ $childIndex }}" @class(['collapse', 'show' => $children_1['is_active']])>
                                        @foreach($children_1['children'] as $grandChildIndex => $children_2)
                                            <li @class(['active' => $children_2['is_active']])>
                                                @if($children_2['children'])
                                                    <a href="#collapse-{{ $index }}-{{ $childIndex }}-{{ $grandChildIndex }}" data-bs-toggle="collapse" @class(['parent', 'collapsed' => !$children_2['is_active']])>{{ $children_2['name'] }}</a>
                                                    <ul id="collapse-{{ $index }}-{{ $childIndex }}-{{ $grandChildIndex }}" @class(['collapse', 'show' => $children_2['is_active']])>
                                                        @foreach($children_2['children'] as $children_3)
                                                            <li><a href="{{ $children_3['route'] ? route($children_3['route']) : '#' }}">{{ $children_3['name'] }}</a></li>
                                                        @endforeach
                                                    </ul>
                                                @else
                                                    <a href="{{ $children_2['route'] ? route($children_2['route']) : '#' }}">{{ $children_2['name'] }}</a>
                                                @endif
                                            </li>
                                        @endforeach
                                    </ul>
                                @else
                                    <a href="{{ $children_1['route'] ? route($children_1['route']) : '#' }}">{{ $children_1['name'] }}</a>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                @else
                    <a href="{{ $menu['route'] ? route($menu['route']) : '#' }}"><i class="{{ $menu['icon'] }}"></i> {{ $menu['name'] }}</a>
                @endif
            </li>
        @endforeach
    </ul>
</nav>

