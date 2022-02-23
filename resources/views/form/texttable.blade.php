{{-- licjie --}}
<div class="{{$viewClass['form-group']}}">
    <label class="{{$viewClass['label']}} control-label">{!! $label !!}</label>
    <div class="{{$viewClass['field']}} select-resource">
        @include('admin::form.error')

        <div class="input-group">
            <input {!! $attributes !!} @if($disabled) disabled @endif value="{{ implode(',', Dcat\Admin\Support\Helper::array($value)) }}"/>

                <div class="input-group-append">
                    {!! $dialog !!}
                </div>
            </div>
        @include('admin::form.help-block')

        </div>



    </div>
</div>

<script require="@select-table" init="{!! $selector !!}">
    var dialogId = $this.parent().find('{!! $dialogSelector !!}').attr('id');
    var $input = $(this).parents('.input-group').find('input');

    Dcat.grid.SelectTable({
        dialog: '[data-id="' + dialogId + '"]',
        container: $this,
        input: $input,
        @if(isset($max))
        multiple: true,
        max: {{ $max }},
        @endif
        values: {!! json_encode($options) !!},
    });

    @if(! empty($loads))
    var fields = '{!! $loads['fields'] !!}'.split('^');
    var urls = '{!! $loads['urls'] !!}'.split('^');

    $input.on('change', function () {
        var values = this.value;

        Dcat.helpers.loadFields(this, {
            group: '.fields-group',
            urls: urls,
            fields: fields,
            textField: "{{ $loads['textField'] }}",
            idField: "{{ $loads['idField'] }}",
            values: values,
        });
    }).trigger('change');
    @endif
</script>