
<div class="row" style="margin-top: 10px;">
    <div class="{{$viewClass['label']}}"><h4 class="pull-right">{!! $label !!}</h4></div>
    <div class="{{$viewClass['field']}}"></div>
</div>

<hr class="mt-0">

<div class="has-many-{{$columnClass}}">

    <div class="has-many-{{$columnClass}}-forms">

        @foreach($forms as $pk => $form)

            <div class="has-many-{{$columnClass}}-form fields-group">

                {!! $form->render() !!}

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
    

    <template class="{{$columnClass}}-tpl">
        <div class="has-many-{{$columnClass}}-form fields-group">

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
        container = '.has-many-{{ $columnClass }}',
        forms = '.has-many-{{ $columnClass  }}-forms';

    function replaceNestedFormIndex(value) {
        return String(value).replace(/{{ Dcat\Admin\Form\NestedForm::DEFAULT_KEY_NAME }}/g, nestedIndex);
    }

    $(container).on('click', '.add', function () {

        var tpl = $('template.{{ $columnClass }}-tpl');

        nestedIndex++;

        var template = replaceNestedFormIndex(tpl.html());
        $(forms).append(template);
    });

    $(container).on('click', '.remove', function () {
        $(this).closest('.has-many-{{ $columnClass  }}-form').hide();
        $(this).closest('.has-many-{{ $columnClass  }}-form').find('.{{ Dcat\Admin\Form\NestedForm::REMOVE_FLAG_CLASS }}').val(1);
    });
</script>
