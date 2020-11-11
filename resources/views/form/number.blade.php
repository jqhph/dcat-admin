<style>
    .number-group .input-group{flex-wrap: nowrap}
</style>

<div class="{{$viewClass['form-group']}}">

    <div  class="{{$viewClass['label']}} control-label">
        <span>{!! $label !!}</span>
    </div>

    <div class="{{$viewClass['field']}}">

        @include('admin::form.error')

        <div class="input-group number-group">

            @if ($prepend)
                <span class="input-group-prepend"><span class="input-group-text bg-white">{!! $prepend !!}</span></span>
            @endif
            <input {!! $attributes !!} />

            @if ($append)
                <span class="input-group-append">{!! $append !!}</span>
            @endif
        </div>

        @include('admin::form.help-block')

    </div>
</div>

<script require="@number-input" init="{!! $selector !!}">
    $this.bootstrapNumber({!! admin_javascript_json($options) !!});
</script>
