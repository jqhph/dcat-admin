<div {!! $attributes !!} >
    @if($showCloseBtn)
    <button type="button" class="close" data-dismiss="alert">×</button>
    @endif
    @if($title)
    <h4>@if(! empty($icon))<i class="{{ $icon }}"></i>&nbsp;@endif {!! $title !!}</h4>
    @endif
    {!! $content !!}
</div>