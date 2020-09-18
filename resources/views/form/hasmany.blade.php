
<div class="row">
    <div class="{{$viewClass['label']}}"><h4 class="pull-right">{!! $label !!}</h4></div>
    <div class="{{$viewClass['field']}}"></div>
</div>

<hr style="margin-top: 0px;">

<div class="has-many-{{$column}}">

    <div class="has-many-{{$column}}-forms">

        @foreach($forms as $pk => $form)

            <div class="has-many-{{$column}}-form fields-group">

                @foreach($form->fields() as $field)
                    {!! $field->render() !!}
                @endforeach

                @if($options['allowDelete'])
                <div class="form-group row">
                    <label class="{{$viewClass['label']}} control-label"></label>
                    <div class="{{$viewClass['field']}}">
                        <div class="remove btn btn-white btn-sm pull-right"><i class="feather icon-trash">&nbsp;</i>{{ trans('admin.remove') }}</div>
                    </div>
                </div>
                @endif
                <hr>
            </div>

        @endforeach
    </div>
    

    <template class="{{$column}}-tpl">
        <div class="has-many-{{$column}}-form fields-group">

            {!! $template !!}

            <div class="form-group row">
                <label class="{{$viewClass['label']}} control-label"></label>
                <div class="{{$viewClass['field']}}">
                    <div class="remove btn btn-white btn-sm pull-right"><i class="feather icon-trash"></i>&nbsp;{{ trans('admin.remove') }}</div>
                </div>
            </div>
            <hr>
        </div>
    </template>

    @if($options['allowCreate'])
    <div class="form-group row">
        <label class="{{$viewClass['label']}} control-label"></label>
        <div class="{{$viewClass['field']}}">
            <div class="add btn btn-primary btn-outline btn-sm"><i class="feather icon-plus"></i>&nbsp;{{ trans('admin.new') }}</div>
        </div>
    </div>
    @endif

</div>

<script>
    var nestedIndex = {!! $count !!},
        container = '.has-many-{{ $column }}',
        forms = '.has-many-{{ $column  }}-forms';

    function replaceNestedFormIndex(value) {
        return String(value).replace(/{{ Dcat\Admin\Form\NestedForm::DEFAULT_KEY_NAME }}/g, nestedIndex);
    }

    $(container).on('click', '.add', function () {

        var tpl = $('template.{{ $column }}-tpl');

        nestedIndex++;

        var template = replaceNestedFormIndex(tpl.html());
        $(forms).append(template);
        {!! $templateScript !!}
    });

    $(container).on('click', '.remove', function () {
        $(this).closest('.has-many-{{ $column  }}-form').hide();
        $(this).closest('.has-many-{{ $column  }}-form').find('.{{ Dcat\Admin\Form\NestedForm::REMOVE_FLAG_CLASS }}').val(1);
    });
</script>
