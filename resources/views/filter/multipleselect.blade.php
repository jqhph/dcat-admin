<div class="input-group input-group-sm">
    <div class="input-group-prepend">
        <span class="input-group-text bg-white text-capitalize"><b>{!! $label !!}</b></span>
    </div>

    <select class="form-control {{ $class }}" name="{{ $name }}[]" multiple style="width: 100%;">
        @foreach($options as $select => $option)
            <option value="{{$select}}" {{ in_array((string)$select, (array) $value)  ?'selected':'' }}>{{$option}}</option>
        @endforeach
    </select>
</div>