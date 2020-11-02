<div>
    <ul class="nav nav-tabs pl-1" style="margin-top: -1rem">
        @foreach($tabObj->getTabs() as $tab)
            <li class="nav-item">
                <a class="nav-link {{ $tab['active'] ? 'active' : '' }}" href="#{{ $tab['id'] }}" data-toggle="tab">
                    {!! $tab['title'] !!} &nbsp;<i class="feather icon-alert-circle has-tab-error text-danger d-none"></i>
                </a>
            </li>
        @endforeach
    </ul>
    <div class="tab-content fields-group mt-2 pt-1 pb-1">
        @foreach($tabObj->getTabs() as $tab)
            <div class="tab-pane {{ $tab['active'] ? 'active' : '' }}" id="{{ $tab['id'] }}">
                @if($tab['layout']->hasColumns())
                    {!! $tab['layout']->build() !!}
                @else
                    @if($tabObj->hasRows)
                    <div class="ml-2 mb-2" style="margin-top: -1rem">
                        @foreach($tab['fields'] as $field)
                            {!! $field->render() !!}
                        @endforeach
                    </div>
                    @else
                        @foreach($tab['fields'] as $field)
                            {!! $field->render() !!}
                        @endforeach
                    @endif
                @endif
            </div>
        @endforeach

    </div>
</div>