<div class="input-group input-group-sm">
    <div class="input-group-prepend">
        <span class="input-group-text bg-white text-capitalize"><b>{!! $label !!}</b></span>
    </div>

    <select class="form-control {{ $class }}" name="{{$name}}" data-value="{{ request($name, $value) }}" style="width: 100%;">
        @if($selectAll)
            <option value="">{{trans('admin.all')}}</option>
        @endif
        @foreach($options as $select => $option)
            <option value="{{$select}}" {{ Dcat\Admin\Support\Helper::equal($select, request($name, $value)) ?'selected':'' }}>{{$option}}</option>
        @endforeach
    </select>
</div>

@include('admin::scripts.select')

<script require="@select2">
    var configs = {!! json_encode($configs) !!};

    @yield('admin.select-ajax')

    @if(isset($remote))
    $.ajax({!! $remote['ajaxOptions'] !!}).done(function(data) {
        $("{{ $selector }}").select2($.extend({!! json_encode($configs) !!}, {
            data: data,
        })).val({!! $values !!}).trigger("change");
    });
    @else
    $("{!! $selector !!}").select2(configs);
    @endif
</script>

@yield('admin.select-load')

@yield('admin.select-lang')
