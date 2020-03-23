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

            {!! Dcat\Admin\Support\Helper::render($options['contents']) !!}
        </div>

        @if(! empty($options['ranges']))
        <div class="dropdown chart-dropdown">
            <button class="btn btn-sm shadow-0 dropdown-toggle p-0 waves-effect" type="button" id="dropdownItem5" data-toggle="dropdown">
                {{ current($options['ranges']) }}
            </button>
            <div class="dropdown-menu dropdown-menu-right">
                @foreach($options['ranges'] as $key => $range)
                <li class="dropdown-item"><a href="#" data-key="{{ $key }}">{{ $range }}</a></li>
                @endforeach
            </div>
        </div>
        @endif
    </div>
    <div class="card-content" style="position: relative;width: 100%">
        {!! $chart !!}
    </div>
</div>