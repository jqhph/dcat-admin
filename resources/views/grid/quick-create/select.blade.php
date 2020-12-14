<div class="input-group input-group-sm quick-form-field">
    <select class="form-control {{$class}}" style="width: 100%;" name="{{$name}}" {!! $attributes !!} >

        <option value=""></option>
        @foreach($options as $select => $option)
            <option value="{{$select}}" {{ Dcat\Admin\Support\Helper::equal($select, old($column, $value)) ?'selected':'' }}>{{$option}}</option>
        @endforeach
    </select>
</div>

@include('admin::form.select-script')

