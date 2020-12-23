<div class="{{$viewClass['form-group']}}">

    <label class="{{$viewClass['label']}} control-label">{!! $label !!}</label>

    <div class="{{$viewClass['field']}}">

        @include('admin::form.error')
        <input name="{{$name}}" type="hidden" value="0" />
        <input type="checkbox" name="{{$name}}" class="{{ $class }}" {{ $value == 1 ? 'checked' : '' }} {!! $attributes !!} />

        @include('admin::form.help-block')

    </div>
</div>

<script require="@switchery" init="{!! $selector !!}">
    $this.parent().find('.switchery').remove();

    $this.each(function() {
        new Switchery($(this)[0], $(this).data())
    })
</script>
