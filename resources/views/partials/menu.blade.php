@php
    $depth = $item['depth'] ?? 0;

    $horizontal = config('admin.layout.horizontal_menu');

    $defaultIcon = config('admin.menu.default_icon', 'feather icon-circle');
@endphp

@if($builder->visible($item))
    @if(empty($item['children']))
        <li class="nav-item">
            <a @if(mb_strpos($item['uri'], '://') !== false) target="_blank" @endif
               href="{{ $builder->getUrl($item['uri']) }}"
               class="nav-link {!! $builder->isActive($item) ? 'active' : '' !!}">
                {!! str_repeat('&nbsp;', $depth) !!}<i class="fa fa-fw {{ $item['icon'] ?: $defaultIcon }}"></i>
                <p>
                    {{ $builder->translate($item['title']) }}
                </p>
            </a>
        </li>
    @else

        <li class="{{ $horizontal ? 'dropdown' : 'has-treeview' }} {{ $depth > 0 ? 'dropdown-submenu' : '' }} nav-item {{ $builder->isActive($item) ? 'menu-open' : '' }}">
            <a href="#"
               class="nav-link {{ $builder->isActive($item) ? ($horizontal ? 'active' : '') : '' }}
                    {{ $horizontal ? 'dropdown-toggle' : '' }}">
                {!! str_repeat('&nbsp;', $depth) !!}<i class="fa fa-fw {{ $item['icon'] ?: $defaultIcon }}"></i>
                <p>
                    {{ $builder->translate($item['title']) }}

                    @if(! $horizontal)
                        <i class="right fa fa-angle-left"></i>
                    @endif
                </p>
            </a>
            <ul class="nav {{ $horizontal ? 'dropdown-menu' : 'nav-treeview' }}">
                @foreach($item['children'] as $item)
                    @php
                        $item['depth'] = $depth + 1;
                    @endphp

                    @include('admin::partials.menu', ['item' => $item])
                @endforeach
            </ul>
        </li>
    @endif
@endif
