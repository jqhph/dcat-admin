<div {!! $attributes !!}>
    <div class="card-header d-flex justify-content-between align-items-start pb-0">
        <div>
            @if($options['icon'])
            <div class="avatar bg-rgba-{{ $style }} p-50 m-0">
                <div class="avatar-content">
                    <i class="{{ $options['icon'] }} text-{{ $style }} font-medium-5"></i>
                </div>
            </div>
            @endif

            @if($options['title'])
                <h4 class="card-title mb-1">{!! $options['title'] !!}</h4>
            @endif

            <div class="metric-header">{!! $header !!}</div>
        </div>

        @if (! empty($options['subTitle']))
            <span class="btn btn-sm bg-light shadow-0 p-0">
                {{ $options['subTitle'] }}
            </span>
        @endif

        @if(! empty($options['dropdown']))
        <div class="dropdown chart-dropdown">
            <button class="btn btn-sm btn-light shadow-0 dropdown-toggle p-0 waves-effect" data-toggle="dropdown">
                {{ current($options['dropdown']) }}
            </button>
            <div class="dropdown-menu dropdown-menu-right">
                @foreach($options['dropdown'] as $key => $value)
                <li class="dropdown-item"><a href="javascript:void(0)" class="select-option" data-option="{{ $key }}">{{ $value }}</a></li>
                @endforeach
            </div>
        </div>
        @endif
    </div>

    <div class="metric-content">{!! $content !!}</div>
</div>