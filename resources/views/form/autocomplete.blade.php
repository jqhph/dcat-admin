<style>
    .autocomplete-suggestions { -webkit-box-sizing: border-box; -moz-box-sizing: border-box; box-sizing: border-box; border: 1px solid #999; background: #FFF; cursor: default; overflow: auto; -webkit-box-shadow: 1px 4px 3px rgba(50, 50, 50, 0.64); -moz-box-shadow: 1px 4px 3px rgba(50, 50, 50, 0.64); box-shadow: 1px 4px 3px rgba(50, 50, 50, 0.64); }
    .autocomplete-suggestion { padding: 2px 5px; white-space: nowrap; overflow: hidden; }
    .autocomplete-no-suggestion { padding: 2px 5px;}
    .autocomplete-selected { background: #F0F0F0; }
    .autocomplete-suggestions strong { font-weight: bold; color: #000; }
    .autocomplete-group { padding: 2px 5px; font-weight: bold; font-size: 16px; color: #000; display: block; border-bottom: 1px solid #000; }
</style>

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
            dataType: 'json',
            transformResult: function (response, originalQuery) {
                return {
                    suggestions: (function (data, valueField, groupField) {

                        if (valueField) {
                            return $.map(data, function (dat) {
                                return  {value: Dcat.helpers.get(dat,valueField), data: {group: Dcat.helpers.get(dat,groupField)}};
                            });
                        }

                        return data;

                    })(response.data, '{{ $ajax['valueField'] }}', '{{ $ajax['groupField'] }}')
                };
            }
        });
    @else
        configs = $.extend(configs, {
            lookup: {!! $options !!}
        });
    @endif

    $this.autocomplete(configs);
</script>
