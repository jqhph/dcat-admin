<div class="{{$viewClass['form-group']}} {!! !$errors->has($column) ?: 'has-error' !!}">
    <label class="{{$viewClass['label']}} control-label">{!! $label !!}</label>
    <div class="{{$viewClass['field']}} select-resource">
        @include('admin::form.error')

        <div class="input-group">
            <div {!! $attributes !!}>
                @if($maxItem > 2 || ! $maxItem)
                    <span class="select2-selection select2-selection--multiple" role="combobox" >
                        <ul class="select2-selection__rendered"></ul>
                    </span>
                @endif
            </div>

            @if(! $disabled)
                <input name="{{$name}}" type="hidden" />
            @endif
            <div class="input-group-append">
                <div class="btn btn-{{$style}} " id="{{ $btnId }}">
                    &nbsp;<i class="feather icon-arrow-up"></i>&nbsp;
                </div>
            </div>
        </div>

        @include('admin::form.help-block')

    </div>
</div>