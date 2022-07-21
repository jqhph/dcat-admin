
<style>
    .has-many-{{$columnClass}}-forms .has-many-{{$columnClass}}-form:last-child .{{$columnClass}}-down {
        display: none;
    }

    .has-many-{{$columnClass}}-forms .has-many-{{$columnClass}}-form:first-child .{{$columnClass}}-up {
        display: none;
    }

</style>

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

                @if($options['allowDelete'] || $options['allowMove'])
                    <div class="form-group row">
                        <label class="{{$viewClass['label']}} control-label"></label>
                        <div class="{{$viewClass['field']}}">
                            @if($options['allowMove'])
                                <div class="{{$columnClass}}-up btn btn-success btn-sm mr-1"><i class="fa fa-arrow-up">&nbsp;</i>{{ trans('admin.move_up') }}</div>
                                <div class="{{$columnClass}}-down btn btn-info btn-sm"><i class="fa fa-arrow-down">&nbsp;</i>{{ trans('admin.move_down') }}</div>
                            @endif
                            @if($options['allowDelete'])
                                <div class="{{$columnClass}}-remove btn btn-white btn-sm pull-right"><i class="feather icon-trash">&nbsp;</i>{{ trans('admin.remove') }}</div>
                            @endif
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

            @if($options['allowDelete'] || $options['allowMove'])
                <div class="form-group row">
                    <label class="{{$viewClass['label']}} control-label"></label>
                    <div class="{{$viewClass['field']}}">
                        @if($options['allowMove'])
                            <div class="{{$columnClass}}-up btn btn-success btn-sm mr-1"><i class="fa fa-arrow-up"></i>&nbsp;{{ trans('admin.move_up') }}</div>
                            <div class="{{$columnClass}}-down btn btn-info btn-sm"><i class="fa fa-arrow-down"></i>&nbsp;{{ trans('admin.move_down') }}</div>
                        @endif
                        @if($options['allowDelete'])
                            <div class="{{$columnClass}}-remove btn btn-white btn-sm pull-right"><i class="feather icon-trash"></i>&nbsp;{{ trans('admin.remove') }}</div>
                        @endif
                    </div>
                </div>
            @endif
            <hr>
        </div>
    </template>

    @if($options['allowCreate'])
        <div class="form-group row">
            <label class="{{$viewClass['label']}} control-label"></label>
            <div class="{{$viewClass['field']}}">
                <div class="{{$columnClass}}-add btn btn-primary btn-outline btn-sm"><i class="feather icon-plus"></i>&nbsp;{{ trans('admin.new') }}</div>
            </div>
        </div>
    @endif

</div>

<script>
    var nestedIndex = {!! $count !!},
        container = '.has-many-{{ $columnClass }}',
        forms = '.has-many-{{ $columnClass  }}-forms';

    function replaceNestedFormIndex(value) {
        return String(value)
            .replace(/{{ Dcat\Admin\Form\NestedForm::DEFAULT_KEY_NAME }}/g, nestedIndex)
            .replace(/{{ Dcat\Admin\Form\NestedForm::DEFAULT_PARENT_KEY_NAME }}/g, nestedIndex);
    }

    $(container).on('click', '.{{$columnClass}}-add', function () {
        var tpl = $('template.{{ $columnClass }}-tpl');

        nestedIndex++;

        $(forms).append(replaceNestedFormIndex(tpl.html()));
    });

    $(container).on('click', '.{{$columnClass}}-remove', function () {
        var $form = $(this).closest('.has-many-{{ $columnClass }}-form');

        $form.hide();
        $form.find('.{{ Dcat\Admin\Form\NestedForm::REMOVE_FLAG_CLASS }}').val(1);
        $form.find('[required]').prop('required', false);
    });

    // move up
    $(container).on('click', '.{{$columnClass}}-up', function () {
        var $form = $(this).closest('.has-many-{{ $columnClass }}-form');
        if ($form.prev().length === 0) {
            return;
        }

        exchange($form.prev(), $form);
    });

    // move down
    $(container).on('click', '.{{$columnClass}}-down', function () {
        var $form = $(this).closest('.has-many-{{ $columnClass }}-form');
        if ($form.next().length === 0) {
            return;
        }

        exchange($form, $form.next());
    });

    var exchange = function (a, b) {
        var n = a.next(), p = b.prev();
        b.insertBefore(p);
        a.insertAfter(n);
    };
</script>
