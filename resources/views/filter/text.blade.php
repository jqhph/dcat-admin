<div class="input-group input-group-sm">
    @if($group)
        <div class="input-group-prepend">
            <input type="hidden" name="{{ $id }}_group" class="{{ $group_name }}-operation" value="0"/>
            <a class="filter-group input-group-text bg-white dropdown-toggle" data-toggle="dropdown">
                <span class="{{ $group_name }}-label">{{ $default['label'] }} </span>
                <span class="fa fa-caret-down"></span>
            </a>
            <ul class="dropdown-menu {{ $group_name }}">
                @foreach($group as $index => $item)
                    <li><a  data-index="{{ $index }}"> {{ $item['label'] }} </a></li>
                @endforeach
            </ul>
        </div>
    @endif
    <div class="input-group-prepend">
        <span class="input-group-text bg-white"><b>{!! $label !!}</b></span>
    </div>
    <input type="{{ $type }}" class="form-control {{ $id }}" placeholder="{{$placeholder}}" name="{{$name}}" value="{{ request($name, $value) }}">
</div>