<div {!! $attributes !!}>
    <div class="card-header d-flex justify-content-between pb-0">
        <h4 class="card-title mb-1">{!! $options['title'] !!}</h4>

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
    <div class="card-content">
        <div class="card-body pt-0">
            <div class="row">
                <div class="metric-content col-sm-2 col-12 d-flex flex-column flex-wrap text-center">
                    {!! \Dcat\Admin\Support\Helper::render($options['content']) !!}
                </div>
                <div class="col-sm-10 col-12 d-flex justify-content-center">
                    {!! ! empty($chart) ? $chart : '' !!}
                </div>
            </div>
            <div class="chart-info metric-footer d-flex justify-content-between">
                {!! \Dcat\Admin\Support\Helper::render($options['footer']) !!}
            </div>
        </div>
    </div>
</div>