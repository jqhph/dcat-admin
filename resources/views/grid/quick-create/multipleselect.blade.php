<div class="input-group input-group-sm quick-form-field">
    <select class="form-control {{$class}}" style="width: 100%;" name="{{$name}}" {!! $attributes !!} multiple data-placeholder="{{ $label }}">

        <option value=""></option>
        @foreach($options as $select => $option)
            <option value="{{$select}}" {{ $select == old($column, $value) ?'selected':'' }}
            @if(!empty($otherOptions['disableOptions']))
                @if((!empty($otherOptions['disableType']) && in_array($select,$otherOptions['disableOptions'])) || (empty($otherOptions['disableType']) && !in_array($select,$otherOptions['disableOptions'])))
                    disabled
                @endif
            @endif
            >{{$option}}</option>
        @endforeach
    </select>
</div>

