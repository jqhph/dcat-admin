<div class="input-group input-group-sm select-resource">
    <div class="input-group-prepend">
        <span class="input-group-text bg-white"><b>{!! $label !!}</b></span>
    </div>

    <div item="{{ $maxItem }}" class="{{ $id }} {!! $containerClass !!}" placeholder="{{$placeholder}}" name="{{$name}}">
        @if($maxItem > 2 || ! $maxItem)
            <span class="selection">
                <span class="select2-selection select2-selection--multiple" role="combobox" >
                    <ul class="select2-selection__rendered"></ul>
                </span>
            </span>
        @endif
    </div>
    <input name="{{$name}}" type="hidden" />
    <div class="input-group-append">
        <div class="btn btn-{{$btnStyle}} btn-sm " id="{{$name}}-filter-select-source">
            &nbsp;<i class="feather icon-arrow-up"></i>&nbsp;
        </div>
    </div>
</div>

<script data-exec-on-popstate>
Dcat.ready(function () {
    Dcat.ResourceSelector({
        title: '{!! ucfirst(trans('admin.choose')) !!} {!! $label !!}',
        selector: '#{{$name}}-filter-select-source',
        column: "{!! $name !!}",
        source: '{!! $source !!}',
        maxItem: {!! (int)$maxItem !!},
        area: {!! $area !!},
        items: {!! $value !!},
        placeholder: '{{$placeholder ?: $label}}',
        showCloseButton: false,
        @if($maxItem > 2 || !$maxItem) displayerContainer: $('div[name="{!! $name !!}"] .select2-selection'), @endif
    });
});
</script>