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
        color: #999;
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
        color: var(--primary-dark);
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
        color: var(--danger);
        visibility: visible;
    }
</style>

<div class="grid-selector">
    @foreach($values as $column => $value)
        <div class="wrap">
            <div class="select-label">{{ $value['label'] }}</div>
            <div class="select-options">
                <ul>
                    @foreach($value['options'] as $value => $option)
                        @php
                            $active = in_array($value, \Illuminate\Support\Arr::get($selected, $column, []));
                        @endphp
                        <li>
                            <a href="{{ $selector->url($column, $value, true) }}"
                               class="{{$active ? 'active' : ''}}">{{ $option }}</a>
                            @if(!$active && $value['type'] == 'many')
                                &nbsp;
                                <a href="{{ $selector->url($column, $value) }}" class="add"><i class="fa fa-plus-square-o"></i></a>
                            @else
                                <a style="visibility: hidden;"><i class="fa fa-plus-square-o"></i></a>
                            @endif
                        </li>
                    @endforeach
                    <li>
                        <a href="{{ $selector->url($column) }}" class="clear"><i class="fa fa-trash"></i></a>
                    </li>
                </ul>
            </div>
        </div>
    @endforeach
</div>
