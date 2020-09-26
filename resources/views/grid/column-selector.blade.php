<div class="dropdown pull-right column-selector" style="margin-right: 10px">
    <button type="button" class="btn btn-sm btn-instagram dropdown-toggle" data-toggle="dropdown">
        <i class="fa fa-table"></i>
        &nbsp;
        <span class="caret"></span>
    </button>
    <ul class="dropdown-menu" role="menu" style="padding: 10px;height: auto;max-height: 500px;overflow-x: hidden;">
        <li>
            <ul style='padding: 0;'>
                {!! $checkbox !!}
            </ul>
        </li>
        <li class="divider"></li>
        <li class="text-right">
            <button class="btn btn-sm btn-default column-select-all">{!! trans('admin.all') !!}</button>&nbsp;&nbsp;
            <button class="btn btn-sm btn-primary column-select-submit">{!! trans('admin.submit') !!}</button>
        </li>
    </ul>
</div>

<script>
    $('.column-select-submit').on('click', function () {

        var defaults = {!! json_encode($defaults) !!};
        var selected = [];

        $('.column-select-item:checked').each(function () {
            selected.push($(this).val());
        });

        if (selected.length == 0) {
            return;
        }

        var url = new URL(location);

        if (selected.sort().toString() == defaults.sort().toString()) {
            url.searchParams.delete('_columns_');
        } else {
            url.searchParams.set('_columns_', selected.join());
        }

        $.pjax({container:'#pjax-container', url: url.toString()});
    });

    $('.column-select-all').on('click', function () {
        $('.column-select-item').iCheck('check');
        return false;
    });

    $('.column-select-item').iCheck({
        checkboxClass:'icheckbox_minimal-blue'
    });
</script>
