<div class="{{$viewClass['form-group']}}">

    <label class="{{$viewClass['label']}} control-label">{!! $label !!}</label>

    <div class="{{$viewClass['field']}}">

        @include('admin::form.error')

        <select class="form-control {{$class}}" style="width: 100%;" name="{{$name}}[]" multiple="multiple" data-placeholder="{{ $placeholder }}" {!! $attributes !!} >
            @foreach($options as $key => $option)
                <option value="{{ $keyAsValue ? $key : $option}}" {{ in_array($option, $value) ? 'selected' : '' }}>{{$option}}</option>
            @endforeach
        </select>
        <input type="hidden" name="{{$name}}[]" />

        @include('admin::form.help-block')

    </div>
</div>

<script init="{!! $selector !!}" require="@select2?lang={{ config('app.locale') === 'en' ? '' : str_replace('_', '-', config('app.locale')) }}">
    var options = {
        tags: true,
        createTag: function(params) {
            if (/[,;，； ]/.test(params.term)) {
                var str = params.term.trim().replace(/[,;，；]*$/, '');
                return { id: str, text: str }
            } else {
                return null;
            }
        }
    };

    @if(isset($ajax))
    options = $.extend(options, {
        ajax: {
            url: "{!! $ajax['url'] !!}",
            dataType: 'json',
            delay: 250,
            cache: true,
            data: function (params) {
                return {
                    q: params.term,
                    page: params.page
                };
            },
            processResults: function (data, params) {
                params.page = params.page || 1;

                return {
                    results: $.map(data.data, function (d) {
                        d.id = d.{{ $ajax['idField'] }};
                        d.text = d.{{ $ajax['textField'] }};
                        return d;
                    }),
                    pagination: {
                        more: data.next_page_url
                    }
                };
            },
        },
        escapeMarkup: function (markup) {
            return markup;
        },
    });
    @endif

    $this.select2(options);
</script>

<script once>
    var selector = '.select2-selection--multiple .select2-search__field';
    $(document).off('keyup', selector).on('keyup', selector, function (event) {
        try {
            if (event.keyCode == 13) {
                var $this = $(this), optionText = $this.val();
                if (optionText != "" && $this.find("option[value='" + optionText + "']").length === 0) {
                    var $select = $this.parents('.select2-container').prev("select");
                    var newOption = new Option(optionText, optionText, true, true);
                    $select.append(newOption).trigger('change');
                    $this.val('');
                    $select.select2('close');
                }
            }
        } catch (e) {
            console.error(e);
        }
    });
</script>

