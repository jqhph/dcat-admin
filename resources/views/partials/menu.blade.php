@php
    $depth = $item['depth'] ?? 0;

    $horizontal = config('admin.layout.horizontal_menu');
@endphp

@if($builder->visible($item))
    @if(empty($item['children']))
        <li class="nav-item">
            <a @if(mb_strpos($item['uri'], '://') !== false) target="_blank" @endif
               href="{{ $builder->getUrl($item['uri']) }}"
               class="nav-link {!! $builder->isActive($item) ? 'active' : '' !!}">
                {!! str_repeat('&nbsp;', $depth) !!}<i class="fa fa-fw {{ $item['icon'] ?: 'feather icon-circle' }}"></i>
                <p>
                    {{ $builder->translate($item['title']) }}
                </p>
            </a>
        </li>
    @else

        <li class="{{ $horizontal ? 'dropdown menu-open' : '' }} nav-item has-treeview {{ $builder->isActive($item) ? 'menu-open' : '' }}">
            <a href="#" class="nav-link {{ $horizontal ? 'dropdown-toggle' : '' }}" {{ $horizontal ? 'data-toggle="dropdown"' : '' }}>
                {!! str_repeat('&nbsp;', $depth) !!}<i class="fa fa-fw {{ $item['icon'] ?: 'feather icon-circle' }}"></i>
                <p>
                    {{ $builder->translate($item['title']) }}

                    @if(! $horizontal)
                        <i class="right fa fa-angle-left"></i>
                    @endif
                </p>
            </a>
            <ul class="nav nav-treeview {{ $horizontal ? 'dropdown-menu' : '' }}">
                @foreach($item['children'] as $item)
                    @php
                        $item['depth'] = $depth + 1;
                    @endphp

                    @include('admin::partials.menu', $item)
                @endforeach
            </ul>
        </li>
    @endif
@endif
