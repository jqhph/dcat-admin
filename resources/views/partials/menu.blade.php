@if($builder->isVisible($item))
    @if(! isset($item['children']))
        <li {!! $builder->isActive($item) ? 'class="active"' : '' !!}>
                <a href="{{ $builder->getUrl($item['uri']) }}">

                <i class="fa {{$item['icon']}}"></i>
                @if (Lang::has($titleTranslation = 'admin.menu_titles.' . trim(str_replace(' ', '_', strtolower($item['title'])))))
                    <span>{{ __($titleTranslation) }}</span>
                @else
                    <span>{{ $item['title'] }}</span>
                @endif
            </a>
        </li>
    @else
        @php
            $active = $builder->isActive($item);
        @endphp
        <li class="treeview {!! $active ? 'active' : '' !!}">
            <a href="#">
                <i class="fa {{ $item['icon'] }}"></i>
                @if (Lang::has($titleTranslation = 'admin.menu_titles.' . trim(str_replace(' ', '_', strtolower($item['title'])))))
                    <span>{{ __($titleTranslation) }}</span>
                @else
                    <span>{{ $item['title'] }}</span>
                @endif
                <i class="fa fa-angle-left pull-right"></i>
            </a>
            <ul class="treeview-menu {!! $active ? 'menu-open' : '' !!}">
                @foreach($item['children'] as $item)
                    @include('admin::partials.menu', $item)
                @endforeach
            </ul>
        </li>
    @endif
@endif