<div class="input-group input-group-sm select-resource">
    <span class="input-group-addon"><b>{!! $label !!}</b></span>
    <div class="{{ $id }} {!! $containerClass !!}" placeholder="{{$placeholder}}" name="{{$name}}" style="width:100%;">
        @if($maxItem > 2 || !$maxItem)
            <span class="selection">
                <span class="select2-selection select2-selection--multiple" role="combobox" >
                    <ul class="select2-selection__rendered"></ul>
                </span>
            </span>
        @endif
    </div>
    <input name="{{$name}}" type="hidden" />
    <div class="input-group-btn">
        <div class="btn btn-{{$btnStyle}} btn-sm " id="{{$name}}-filter-select-source">
            &nbsp;<i class="fa fa-long-arrow-up"></i>&nbsp;
        </div>
    </div>
</div>

<script data-exec-on-popstate>
LA.ready(function () {
    LA.ResourceSelector({
        title: '{!! ucfirst(trans('admin.choose')) !!} {!! $label !!}',
        selector: '#{{$name}}-filter-select-source',
        column: "{!! $name !!}",
        source: '{!! $source !!}',
        maxItem: {!! (int)$maxItem !!},
        area: {!! $area !!},
        items: {!! $value !!},
        placeholder: '{{$placeholder ?: $label}}',
        showCloseButton: false,
        closeButtonText: '{!! ucfirst(trans('admin.close')) !!}',
        exceedMaxItemTip: '{{trans('admin.selected_must_less_then', ['num' => $maxItem])}}',
        @if($maxItem > 2 || !$maxItem) $displayerContainer: $('div[name="{!! $name !!}"] .select2-selection'), @endif
    });
});
</script>