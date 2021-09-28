<div class="{{$viewClass['form-group']}}">

    <div class="{{$viewClass['label']}} control-label">
        <span>{!! $label !!}</span>
    </div>

    <div class="{{$viewClass['field']}}">

        @include('admin::form.error')

        <div class="input-group">

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
<script init="{!! $selector !!}" require="@autocomplete">

    var configs = {!! $configs !!};

    @if(isset($ajax))

        configs = $.extend(configs, {

            serviceUrl: '{{ $ajax['url'] }}',
            transformResult: function (response, originalQuery) {
                return {
                    suggestions: (function (response, valueField, groupField) {

                        if (valueField) {
                            return $.map(response, function (res) {
                                return groupField
                                    ? {value: res[valueField], data: {group: res[groupField]}}
                                    : {value: res[valueField], data: {}}
                            });
                        }

                        return response;

                    })(response, '{{ $ajax['valueField'] }}', '{{ $ajax['groupField'] }}')
                };
            }
        });
    @else
        configs = $.extend(configs, {
            lookup: {{ $options }}
        });
    @endif

    $this.autocomplete(configs);
</script>
