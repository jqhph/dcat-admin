@foreach($options as $k => $label)
    @php
        $id = 'radio'.\Illuminate\Support\Str::random(2).mt_rand(0, 9999);
    @endphp
    <div class="radio radio-{{$style}} {{$inline}}">
        <input {!! in_array($k, $disabled) ? 'disabled' : '' !!} id="{{$id}}" value="{{$k}}" {!! $attributes !!} {!! ($checked == $k && $checked !== null) ? 'checked' : '' !!}>
        <label for="{{$id}}">{!! $label !!}</label>
    </div>
@endforeach