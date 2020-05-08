@if ($breadcrumb)
    <div class="breadcrumb-wrapper col-12">
    <ol class="breadcrumb float-right text-capitalize">
        <li class="breadcrumb-item"><a href="{{ admin_url('/') }}"><i class="fa fa-dashboard"></i> {{admin_trans('admin.home')}}</a></li>
        @foreach($breadcrumb as $item)
            @if($loop->last)
                <li class="active breadcrumb-item">
                    @if (\Illuminate\Support\Arr::has($item, 'icon'))
                        <i class="fa {{ $item['icon'] }}"></i>
                    @endif
                    {{ $item['text'] }}
                </li>
            @else
                <li class="breadcrumb-item">
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
    </div>
@elseif(config('admin.enable_default_breadcrumb'))
    <div class="breadcrumb-wrapper col-12">
    <ol class="breadcrumb float-right text-capitalize">
        <li class="breadcrumb-item"><a href="{{ admin_url('/') }}"><i class="fa fa-dashboard"></i> {{admin_trans('admin.home')}}</a></li>
        @for($i = 2; $i <= ($len = count(Request::segments())); $i++)
            <li class="breadcrumb-item">
                @if($i == $len) <a href=""> @endif
                {{admin_trans_label(Request::segment($i))}}
                @if($i == $len) </a> @endif
            </li>
        @endfor
    </ol>
    </div>
@endif

<div class="clearfix"></div>
