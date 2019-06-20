@foreach($options as $k => $label)
    @php
        $id = 'ckb'.\Illuminate\Support\Str::random(2).mt_rand(0, 9999);
    @endphp
    <div class="checkbox checkbox-{{$style}} {{$inline}} {{$circle}}">
        <input {!! in_array($k, $disabled) ? 'disabled' : '' !!} id="{{$id}}" value="{{$k}}" {!! $attributes !!} {!! (in_array($k, $checked)) ? 'checked' : '' !!}>
        <label for="{{$id}}">{!! $label !!}</label>
    </div>
@endforeach