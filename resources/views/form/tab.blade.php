<div class="nav-tabs-default">
    <ul class="nav nav-tabs">

        @foreach($tabObj->getTabs() as $tab)
            <li {{ $tab['active'] ? 'class=active' : '' }}>
                <a href="#tab-{{ $tab['id'] }}" data-toggle="tab">
                    {!! $tab['title'] !!} <i class="fa fa-exclamation-circle text-red hide"></i>
                </a>
            </li>
        @endforeach

    </ul>
    <div class="tab-content fields-group" style="padding:18px 0">

        @foreach($tabObj->getTabs() as $tab)
            <div class="tab-pane {{ $tab['active'] ? 'active' : '' }}" id="tab-{{ $tab['id'] }}">
                @foreach($tab['fields'] as $field)
                    {!! $field->render() !!}
                @endforeach
            </div>
        @endforeach

    </div>
</div>