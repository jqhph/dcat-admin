<div class="{{$viewClass['form-group']}}" >

    <label class="{{$viewClass['label']}} control-label">{!! $label !!}</label>

    <div class="{{$viewClass['field']}}">

        @include('admin::form.error')

        <input type="hidden" name="{{$name}}">

        {!! $radio !!}

        @include('admin::form.help-block')

    </div>
</div>

@if(! empty($load))
<script once>
    var selector = '{!! $selector !!}';

    $(document).off('change', selector);
    $(document).on('change', selector, function () {
        var values = this.value;

        Dcat.helpers.loadField(this, {
            group: '{{ $load['group'] ?? '.fields-group' }}',
            class: '.{{ $load['class'] }}',
            url: "{!! $load['url'].(strpos($load['url'],'?')?'&':'?') !!}q=",
            textField: "{{ $load['textField'] }}",
            idField: "{{ $load['idField'] }}",
            values: values,
        });
    });
    $(selector+':checked').trigger('change')
</script>
@endif

@if(! empty($loads))
<script once>
    var selector = '{!! $selector !!}',
        fields = '{!! $loads['fields'] !!}'.split('^'),
        urls = '{!! $loads['urls'] !!}'.split('^');

    $(document).off('change', selector);
    $(document).on('change', selector, function () {
        var values = this.value;

        Dcat.helpers.loadFields(this, {
            group: '.fields-group',
            urls: urls,
            fields: fields,
            textField: "{{ $loads['textField'] }}",
            idField: "{{ $loads['idField'] }}",
            values: values,
        });
    });
    $(selector+':checked').trigger('change')
</script>
@endif