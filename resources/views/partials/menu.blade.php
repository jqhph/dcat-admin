@php
    $active = $builder->isActive($item);

    $depth = $item['depth'] ?? 0;
@endphp

@if($builder->visible($item))
    @if(empty($item['children']))
        <li class="nav-item">
            <a @if(mb_strpos($item['uri'], '://') !== false) target="_blank" @endif href="{{ $builder->getUrl($item['uri']) }}" class="nav-link {!! $builder->isActive($item) ? 'active' : '' !!}">
                {!! str_repeat('&nbsp;', $depth) !!}<i class="fa {{ $item['icon'] ?: 'feather icon-circle' }}"></i>
                <p>
                    {{ $builder->translate($item['title']) }}
                </p>
            </a>
        </li>
    @else
        @php
            $active = $builder->isActive($item);
        @endphp

        <li class="nav-item has-treeview {{ $active ? 'menu-open' : '' }}">
            <a href="#" class="nav-link">
                {!! str_repeat('&nbsp;', $depth) !!}<i class="fa {{ $item['icon'] ?: 'feather icon-circle' }}"></i>
                <p>
                    {{ $builder->translate($item['title']) }}
                    <i class="right fa fa-angle-left"></i>
                </p>
            </a>
            <ul class="nav nav-treeview">
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
