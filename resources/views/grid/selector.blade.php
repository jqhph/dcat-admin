<style>
    .grid-selector {
        margin: -10px;
    }
    .grid-selector .wrap {
        position: relative;
        line-height: 40px;
        border-bottom: 1px dashed #eee;
        padding: 0 30px;
        font-size: 13px;
        overflow:auto;
    }
    .grid-selector .wrap:last-child {
        border-bottom: none;
    }
    .grid-selector .select-label {
        float: left;
        width: 100px;
        padding-left: 10px;
        color: #888;
    }
    .grid-selector .select-options {
        margin-left: 100px;
    }
    .grid-selector ul {
        height: 25px;
        list-style: none;
    }
    .grid-selector ul > li {
        margin-right: 30px;
        float: left;
    }
    .grid-selector ul > li a {
        color: #666;
        text-decoration: none;
    }
    .grid-selector .select-options a.active {
        color: {{ \Dcat\Admin\Widgets\Color::primaryDark() }};
        font-weight: bold;
    }
    .grid-selector li .add {
        visibility: hidden;
    }
    .grid-selector li:hover .add {
        visibility: visible;
    }
    .grid-selector ul .clear {
        visibility: hidden;
    }
    .grid-selector ul:hover .clear {
        color: {{ \Dcat\Admin\Widgets\Color::dangerDark() }};
        visibility: visible;
    }
</style>

<div class="grid-selector">
    @foreach($self->all() as $column => $selector)
        <div class="wrap">
            <div class="select-label">{{ $selector['label'] }}</div>
            <div class="select-options">
                <ul>
                    @foreach($selector['options'] as $value => $option)
                        @php
                            $active = in_array($value, \Illuminate\Support\Arr::get($selected, $column, []));
                        @endphp
                        <li>
                            <a href="{{ $self->url($column, $value, true) }}"
                               class="{{$active ? 'active' : ''}}">{{ $option }}</a>
                            @if(!$active && $selector['type'] == 'many')
                                &nbsp;
                                <a href="{{ $self->url($column, $value) }}" class="add"><i class="fa fa-plus-square-o"></i></a>
                            @else
                                <a style="visibility: hidden;"><i class="fa fa-plus-square-o"></i></a>
                            @endif
                        </li>
                    @endforeach
                    <li>
                        <a href="{{ $self->url($column) }}" class="clear"><i class="fa fa-trash"></i></a>
                    </li>
                </ul>
            </div>
        </div>
    @endforeach
</div>
