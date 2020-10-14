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
    <div class="tab-content fields-group mt-2" style="padding:18px 0">
        @foreach($tabObj->getTabs() as $tab)
            <div class="tab-pane {{ $tab['active'] ? 'active' : '' }}" id="{{ $tab['id'] }}">
                @if($form->hasRows())
                    <div class="ml-2 mb-2">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        @foreach($form->rows() as $row)
                            {!! $row->render() !!}
                        @endforeach
                    </div>
                @elseif($form->layout()->hasColumns())
                    {!! $form->layout()->build() !!}
                @else
                    @foreach($form->fields() as $field)
                        {!! $field->render() !!}
                    @endforeach
                @endif
            </div>
        @endforeach

    </div>
</div>