<div {!! $attributes !!} >
    @if($showCloseBtn)
    <button type="button" class="close" data-dismiss="alert">×</button>
    @endif
    @if($title)
    <h4>@if($icon)<i class="{{ $icon }}"></i>@endif {!! $title !!}</h4>
    @endif
    {!! $content !!}
</div>