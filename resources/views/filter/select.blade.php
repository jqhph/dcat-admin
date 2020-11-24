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