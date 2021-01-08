<div class="@if(isset($options['label'])) form-group @endif row form-field">
    @if(isset($options['width']['label']))
    <div class="col-md-{{ $options['width']['label'] }} text-capitalize control-label">
        <span>{{ $options['label'] }}</span>
    </div>
    @endif
    <div class="@if(isset($options['width']['field'])) col-md-{{ $options['width']['field'] }} @endif row">
        @foreach($fields as $field)
        <div class="col-md-{{ $field['width'] }}">
            {!! $field['element']->render() !!}
        </div>
        @endforeach
    </div>
</div>
