<div class="input-group input-group-sm">
    <div class="input-group-prepend">
        <span class="input-group-text bg-white text-capitalize"><b>{!! $label !!}</b></span>
    </div>

    <select class="form-control {{ $class }}" name="{{$name}}[]" multiple style="width: 100%;" data-value="{{implode(',',(array) $value)}}">
        @foreach($options as $select => $option)
            <option value="{{$select}}" {{ in_array((string)$select, (array) $value)  ?'selected':'' }}>{{$option}}</option>
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

@yield('admin.select-load')
