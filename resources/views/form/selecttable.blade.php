<div class="{{$viewClass['form-group']}} {!! !$errors->has($column) ?: 'has-error' !!}">
    <label class="{{$viewClass['label']}} control-label">{!! $label !!}</label>
    <div class="{{$viewClass['field']}} select-resource">
        @include('admin::form.error')

        <div class="input-group">
            <div {!! $attributes !!}>
                <span class="default-text" style="opacity:0.75">{{ $placeholder }}</span>
                <span class="option d-none"></span>
            </div>

            @if(! $disabled)
                <input name="{{ $name }}" type="hidden" id="hidden-{{ $id }}" value="{{ implode(',', \Dcat\Admin\Support\Helper::array($value)) }}" />
            @endif
            <div class="input-group-append">
                <div class="btn btn-{{ $style }} " data-toggle="modal" data-target="#{{ $id }}">
                    &nbsp;<i class="feather icon-arrow-up"></i>&nbsp;
                </div>
            </div>
        </div>

        {!! $modal !!}

        @include('admin::form.help-block')

    </div>
</div>