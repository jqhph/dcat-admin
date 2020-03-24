<div {!! $attributes !!}>
    <div class="card-header d-flex justify-content-between align-items-start pb-0">
        <div>
            @if($options['icon'])
            <div class="avatar bg-rgba-{{ $options['style'] }} p-50 m-0">
                <div class="avatar-content">
                    <i class="{{ $options['icon'] }} text-{{ $options['style'] }} font-medium-5"></i>
                </div>
            </div>
            @endif

            <div class="metric-content">{!! Dcat\Admin\Support\Helper::render($options['content']) !!}</div>
        </div>

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
    <div class="card-content" style="position: relative;width: 100%">
        {!! ! empty($chart) ? $chart : '' !!}
    </div>
</div>