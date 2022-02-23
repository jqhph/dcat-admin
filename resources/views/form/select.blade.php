<div class="{{$viewClass['form-group']}}">

    <div  class="{{ $viewClass['label'] }} control-label">
        <span>{!! $label !!}</span>
    </div>

    <div class="{{$viewClass['field']}}">

        @include('admin::form.error')

        <input type="hidden" name="{{$name}}"/>

        <select class="form-control {{$class}}" style="width: 100%;" name="{{$name}}" {!! $attributes !!} >
            <option value=""></option>
            @if($groups)
                @foreach($groups as $group)
                    <optgroup label="{{ $group['label'] }}">
                        @foreach($group['options'] as $select => $option)
                            {{-- licjie --}}
                            <option value="{{$select}}" {{ $select == $value ?'selected':'' }}
                            @if(!empty($otherOptions['disableOptions']))
                                @if((!empty($otherOptions['disableType']) && in_array($select,$otherOptions['disableOptions'])) || (empty($otherOptions['disableType']) && !in_array($select,$otherOptions['disableOptions'])))
                                    disabled
                                @endif
                            @endif
                            >{{$option}}</option>
                        @endforeach
                    </optgroup>
                @endforeach
             @else
                @foreach($options as $select => $option)
                    {{-- licjie --}}
                    <option value="{{$select}}" {{ Dcat\Admin\Support\Helper::equal($select, $value) ?'selected':'' }}
                    @if(!empty($otherOptions['disableOptions']))
                        @if((!empty($otherOptions['disableType']) && in_array($select,$otherOptions['disableOptions'])) || (empty($otherOptions['disableType']) && !in_array($select,$otherOptions['disableOptions'])))
                            disabled
                        @endif
                    @endif
                    >{{$option}}</option>
                @endforeach
            @endif
        </select>

        @include('admin::form.help-block')

    </div>
</div>

@include('admin::form.select-script')
