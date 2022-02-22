<style>
    .table-has-many .input-group{flex-wrap: nowrap!important}
</style>

<div class="row form-group">
    <div class="{{$viewClass['label']}} "><label class="control-label pull-right">{!! $label !!}</label></div>
    <div class="{{$viewClass['field']}}">
        @include('admin::form.error')

        <span name="{{$column}}"></span> {{-- 用于显示错误信息 --}}

        <div class="has-many-table-{{$columnClass}}" >
            <table class="table table-has-many has-many-table-{{$columnClass}}">
                <thead>
                <tr>
                    @foreach($headers as $header)
                        <th>{{ $header }}</th>
                    @endforeach

                    <th class="hidden"></th>

                    @if($options['allowDelete'])
                        <th></th>
                    @endif
                </tr>
                </thead>
                <tbody class="has-many-table-{{$columnClass}}-forms">
                @foreach($forms as $pk => $form)
                    <tr class="has-many-table-{{$columnClass}}-form fields-group">

                        <?php $hidden = ''; ?>

                        @foreach($form->fields() as $field)

                            @if (is_a($field, Dcat\Admin\Form\Field\Hidden::class))
                                <?php $hidden .= $field->render(); ?>
                                @continue
                            @endif

                            <td>{!! $field->setLabelClass(['hidden'])->width(12, 0)->render() !!}</td>
                        @endforeach

                        <td class="hidden">{!! $hidden !!}</td>

                        @if($options['allowDelete'])
                            <td class="form-group">
                                <div>
                                    <div class="remove btn btn-white btn-sm pull-right"><i class="feather icon-trash"></i></div>
                                </div>
                            </td>
                        @endif
                    </tr>
                @endforeach
                </tbody>
            </table>

            <template class="{{$columnClass}}-tpl">
                <tr class="has-many-table-{{$columnClass}}-form fields-group">

                    {!! $template !!}

                    <td class="form-group">
                        <div>
                            <div class="remove btn btn-white btn-sm pull-right"><i class="feather icon-trash"></i></div>
                        </div>
                    </td>
                </tr>
            </template>

            @if($options['allowCreate'])
                <div class="form-group row m-t-10">
                    <div class="{{$viewClass['field']}}" style="margin-top: 8px">
                        <div class="add btn btn-primary btn-outline btn-sm"><i class="feather icon-plus"></i>&nbsp;{{ trans('admin.new') }}</div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

{{--<hr style="margin-top: 0px;">--}}

<script>
(function () {
    var nestedIndex = {!! $count !!},
        container = '.has-many-table-{{ $columnClass }}';

    function replaceNestedFormIndex(value) {
        return String(value).replace(/{{ $parentKey ?: Dcat\Admin\Form\NestedForm::DEFAULT_KEY_NAME }}/g, nestedIndex);
    }

    $(document).off('click', container+' .add').on('click', container+' .add', function (e) {
        var $con = $(this).closest(container);
        var tpl = $con.find('template.{{ $columnClass }}-tpl');

        nestedIndex++;

        $con.find('.has-many-table-{{ $columnClass }}-forms').append(replaceNestedFormIndex(tpl.html()));

        e.preventDefault();
        return false
    });

    $(document).off('click', container+' .remove').on('click', container+' .remove', function () {
        var $form = $(this).closest('.has-many-table-{{ $columnClass }}-form');

        $form.hide();
        $form.find('[required]').prop('required', false);
        $form.find('.{{ Dcat\Admin\Form\NestedForm::REMOVE_FLAG_CLASS }}').val(1);
    });
})();
</script>
