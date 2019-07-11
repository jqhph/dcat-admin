@if(Dcat\Admin\Admin::Menu()->show($item))
    @if(!isset($item['children']))
        @php
            $url = Dcat\Admin\Admin::menu()->getFullUri($item['uri']);
        @endphp

        <li {!! Dcat\Admin\Admin::Menu()->isActive($item) ? 'class="active"' : '' !!}>
                <a href="{{ $url }}">

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
            $active = Dcat\Admin\Admin::Menu()->isActive($item);
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