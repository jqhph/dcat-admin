<div class="input-group input-group-sm quick-form-field">
    <select class="form-control {{$class}}" style="width: 100%;" name="{{$name}}[]" multiple="multiple" data-placeholder="{{ $label }}" {!! $attributes !!} >
        @foreach($options as $key => $option)
            <option value="{{ $keyAsValue ? $key : $option}}" {{ in_array($option, $value) ? 'selected' : '' }}>{{$option}}</option>
        @endforeach
    </select>
    <input type="hidden" name="{{$name}}[]" />
</div>