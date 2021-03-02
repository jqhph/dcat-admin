<div class="input-group input-group-sm">
    <div class="input-group-prepend">
        <span class="input-group-text bg-white text-capitalize"><b>{!! $label !!}</b></span>
    </div>

    <select class="form-control {{ $class }}" name="{{$name}}" data-value="{{ $value }}" style="width: 100%;">
        <option value=""></option>
        @foreach($options as $select => $option)
            <option value="{{$select}}" {{ Dcat\Admin\Support\Helper::equal($select, $value) ?'selected':'' }}>{{$option}}</option>
        @endforeach
    </select>
</div>

@include('admin::scripts.select')

<script require="@select2?lang={{ config('app.locale') === 'en' ? '' : str_replace('_', '-', config('app.locale')) }}">
    var configs = {!! admin_javascript_json($configs) !!};

    @yield('admin.select-ajax')

    @if(isset($remote))
    $.ajax({!! admin_javascript_json($remote['ajaxOptions']) !!}).done(function(data) {
        $("{{ $selector }}").select2($.extend({!! admin_javascript_json($configs) !!}, {
            data: data,
        })).val({!! json_encode($remote['values']) !!}).trigger("change");
    });
    @else
    $("{!! $selector !!}").select2(configs);
    @endif
</script>
@if(isset($loads))
{{--loadsÁª¶¯--}}
<script once>
    var selector = '{!! $selector !!}';

    var fields = '{!! $loads['fields'] !!}'.split('^');
    var urls = '{!! $loads['urls'] !!}'.split('^');

    var refreshOptions = function(url, target) {
        $.ajax(url).then(function(data) {
            target.find("option").remove();
            $(target).select2({
                data: $.map(data, function (d) {
                    d.id = d.{{ $loads['idField'] }};
                    d.text = d.{{ $loads['textField'] }};
                    return d;
                })
            }).val(String(target.data('value')).split(',')).trigger('change');
        });
    };

    $(document).off('change', selector);
    $(document).on('change', selector, function () {
        var _this = this;
        var promises = [];

        fields.forEach(function(field, index){
            var target = $(_this).closest('.filter-box').find('.' + fields[index]);

            if (_this.value !== '0' && ! _this.value) {
                return;
            }
            promises.push(refreshOptions(urls[index] + (urls[index].match(/\?/)?'&':'?') + "q="+ _this.value, target));
        });

        $.when(promises).then(function() {});
    });
    $(selector).trigger('change');
</script>
@endif
@yield('admin.select-load')
