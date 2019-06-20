<div class="input-group input-group-sm">
    <span class="input-group-addon"><b>{{$label}}</b></span>
    <select class="form-control {{ $class }}" name="{{$name}}[]" multiple style="width: 100%;">
        @foreach($options as $select => $option)
            <option value="{{$select}}" {{ in_array((string)$select, request($name, []))  ?'selected':'' }}>{{$option}}</option>
        @endforeach
    </select>
</div>