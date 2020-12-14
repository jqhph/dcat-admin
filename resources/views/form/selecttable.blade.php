<div class="{{$viewClass['form-group']}}">
    <label class="{{$viewClass['label']}} control-label">{!! $label !!}</label>
    <div class="{{$viewClass['field']}} select-resource">
        @include('admin::form.error')

        <div class="input-group">
            <div {!! $attributes !!}>
                <span class="default-text" style="opacity:0.75">{{ $placeholder }}</span>
                <span class="option d-none"></span>

                @if(! $disabled)
                    <input name="{{ $name }}" type="hidden" value="{{ implode(',', Dcat\Admin\Support\Helper::array($value)) }}" />
                @endif
            </div>

            <div class="input-group-append">
                {!! $dialog !!}
            </div>
        </div>

        @include('admin::form.help-block')

    </div>
</div>

<script require="@select-table" init="{!! $selector !!}">
    var dialogId = $this.parent().find('{!! $dialogSelector !!}').attr('id');

    Dcat.grid.SelectTable({
        dialog: '[data-id="' + dialogId + '"]',
        container: $this,
        input: $(this).find('input'),
        @if(isset($max))
        multiple: true,
        max: {{ $max }},
        @endif
        values: {!! json_encode($options) !!},
    });
</script>