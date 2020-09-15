<div class="{{$viewClass['form-group']}}">

    <label for="{{$id}}" class="{{$viewClass['label']}} control-label">{!! $label !!}</label>

    <div class="{{$viewClass['field']}}">

        @include('admin::form.error')
        <input name="{{$name}}" type="hidden" value="0" />
        <input type="checkbox" name="{{$name}}" class="{{$class}} la_checkbox" {{ $value == 1 ? 'checked' : '' }} {!! $attributes !!} />

        @include('admin::form.help-block')

    </div>
</div>

<script require="@switchery">
    var $input = $('[data-plugin="{{ $formId }}switchery"]');

    $input.parent().find('.switchery').remove();

    $input.each(function() {
        new Switchery($(this)[0], $(this).data())
    })
</script>
