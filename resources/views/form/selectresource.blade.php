<div class="{{$viewClass['form-group']}} {!! !$errors->has($column) ?: 'has-error' !!}">
    <label for="{{$id}}" class="{{$viewClass['label']}} control-label">{!! $label !!}</label>
    <div class="{{$viewClass['field']}} select-resource">
        @include('admin::form.error')

        <app></app>

        <div class="input-group">
            @if(!$disabled)
                <input name="{{$name}}" type="hidden" />
            @endif
            <div {!! $attributes !!}>
            </div>
            <div class="input-group-btn">
                <div class="btn btn-{{$style}} " id="{{ $btnId }}">
                    &nbsp;<i class="fa fa-long-arrow-up"></i>&nbsp;
                </div>
            </div>
        </div>

        @include('admin::form.help-block')

    </div>
</div>