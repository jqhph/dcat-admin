<div {!! $attributes !!}>
    @foreach($items as $k => $item)
        <div class="panel {{$panelStyle}} no-shadow">
            <div class="panel-heading " >
                <h4 class="panel-title">
                    <a data-parent="#{!! $id !!}" data-toggle="collapse" href="#{!! $item['id'] !!}" class=" {!! $item['expand'] ? '' : 'collapsed' !!}">
                        {!! $item['title'] !!}
                    </a>
                </h4>
            </div>
            <div id="{{$item['id']}}" class="panel-collapse collapse {!! $item['expand'] ? 'in' : '' !!}"   >
                <div class="panel-body">
                    {!! $item['content'] !!}
                </div>
            </div>
        </div>
    @endforeach
</div>
