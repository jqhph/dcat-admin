<div class="{{$viewClass['form-group']}}">

    <div  class="{{$viewClass['label']}} control-label">
        <span>{!! $label !!}</span>
    </div>

    <div class="{{$viewClass['field']}}">

        @include('admin::form.error')

        <select class="form-control {{$class}}" style="width: 100%!important;" name="{{$name}}[]" multiple="multiple" data-placeholder="{{ $placeholder }}" {!! $attributes !!} >
            @foreach($options as $select => $option)
                {{-- licjie --}}
                <option value="{{ $select }}" {{  in_array($select, (array) $value) ?'selected':'' }}
                @if(!empty($otherOptions['disableOptions']))
                    @if((!empty($otherOptions['disableType']) && in_array($select,$otherOptions['disableOptions'])) || (empty($otherOptions['disableType']) && !in_array($select,$otherOptions['disableOptions'])))
                        disabled
                    @endif
                @endif
                >{{$option}}</option>
            @endforeach
        </select>
        <input type="hidden" name="{{$name}}[]" />

        @include('admin::form.help-block')

    </div>
</div>

@include('admin::form.select-script')
