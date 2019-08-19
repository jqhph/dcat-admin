@if ($breadcrumb)
    <ol class="breadcrumb" style="margin-right:30px;">
        <li><a href="{{ admin_url('/') }}"><i class="fa fa-dashboard"></i> {{ucfirst(admin_trans('admin.home'))}}</a></li>
        @foreach($breadcrumb as $item)
            @if($loop->last)
                <li class="active">
                    @if (\Illuminate\Support\Arr::has($item, 'icon'))
                        <i class="fa {{ $item['icon'] }}"></i>
                    @endif
                    {{ $item['text'] }}
                </li>
            @else
                <li>
                    <a href="{{ admin_url(\Illuminate\Support\Arr::get($item, 'url')) }}">
                        @if (\Illuminate\Support\Arr::has($item, 'icon'))
                            <i class="fa {{ $item['icon'] }}"></i>
                        @endif
                        {{ $item['text'] }}
                    </a>
                </li>
            @endif
        @endforeach
    </ol>
@elseif(config('admin.enable_default_breadcrumb'))
    <ol class="breadcrumb" style="margin-right:30px;">
        <li><a href="{{ admin_url('/') }}"><i class="fa fa-dashboard"></i> {{ucfirst(admin_trans('admin.home'))}}</a></li>
        @for($i = 2; $i <= ($len = count(Request::segments())); $i++)
            <li>
                @if($i == $len) <a href=""> @endif
                {{ucfirst(admin_trans_label(Request::segment($i)))}}
                @if($i == $len) </a> @endif
            </li>
        @endfor
    </ol>
@endif