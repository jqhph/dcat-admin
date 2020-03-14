@if($builder->isVisible($item))
    @if(isset($item['is_header']))
        <li class="navigation-header">
            <span>{{ $item['title'] }}</span>
        </li>
    @elseif(! isset($item['children']))
        <li class="nav-item {!! $builder->isActive($item) ? 'active' : '' !!}">
            <a href="{{ $builder->getUrl($item['uri']) }}">

                <i class="{{$item['icon']}}"></i>
                @if (Lang::has($titleTranslation = 'admin.menu_titles.' . trim(str_replace(' ', '_', strtolower($item['title'])))))
                    <span class="menu-title">{{ __($titleTranslation) }}</span>
                @else
                    <span class="menu-title">{{ $item['title'] }}</span>
                @endif
            </a>
        </li>
    @else
        <li class="nav-item has-sub">
            <a href="#">
                <i class="fa {{ $item['icon'] }}"></i>
                @if (Lang::has($titleTranslation = 'admin.menu_titles.' . trim(str_replace(' ', '_', strtolower($item['title'])))))
                    <span class="menu-title">{{ __($titleTranslation) }}</span>
                @else
                    <span class="menu-title">{{ $item['title'] }}</span>
                @endif
            </a>
            <ul class="menu-content">
                @foreach($item['children'] as $item)
                    @include('admin::partials.menu', $item)
                @endforeach
            </ul>
        </li>
    @endif
@endif