<div class="input-group input-group-sm quick-form-field">
    <app></app>
    <div class="input-group-sm">
        @if(!$disabled)
            <input name="{{$name}}" type="hidden" />
        @endif
        <div {!! $attributes !!}>
        </div>
        <div class="input-group-btn">
            <div class="btn btn-sm btn-{{$style}} " id="{{ $btnId }}">
                &nbsp;<i class="fa fa-long-arrow-up"></i>&nbsp;
            </div>
        </div>
    </div>
</div>