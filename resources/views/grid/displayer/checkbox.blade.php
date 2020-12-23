<form class="form-group {{ $class }}" style="text-align:left;" data-key="{{ $key }}">
    @foreach($options as $v => $label)
        @php($checked = \Dcat\Admin\Support\Helper::inArray($v, $value) ? 'checked' : '')

        <div class="vs-checkbox-con vs-checkbox-primary" style="margin-bottom: 4px">
            <input type="checkbox" name="grid-checkbox-{{ $column }}[]" value="{{ $v }}" {{ $checked }}>
            <span class="vs-checkbox vs-checkbox-sm"><span class="vs-checkbox--check"><i class="vs-icon feather icon-check"></i></span></span>
            <span class="">{{ $label }}</span>
        </div>
    @endforeach

    <button type="submit" class="btn btn-primary btn-sm pull-left">
        <i class="feather icon-save"></i>&nbsp;{{ trans('admin.save') }}
    </button>
    <button type="reset" class="btn btn-white btn-sm pull-left" style="margin-left:5px;">
        <i class="feather icon-trash"></i>&nbsp;{{ trans('admin.reset') }}
    </button>
</form>

<script>
    $(document).off('submit', 'form.{{ $class }}').on('submit', 'form.{{ $class }}', function () {
        var values = $(this).find('input:checkbox:checked').map(function (_, el) {
                return $(el).val();
            }).get(),
            btn = $(this).find('[type="submit"]'),
            reload = '{{ $refresh }}';

        if (btn.attr('loading')) {
            return;
        }
        btn.attr('loading', 1);
        btn.buttonLoading();

        $.put({
            url: "{{ $resource }}/" + $(this).data('key'),
            data: {
                '{{ $column }}': values,
            },
            success: function (d) {
                btn.buttonLoading(false);
                btn.removeAttr('loading');
                Dcat.success(d.data.message || d.message);
                reload && Dcat.reload();
            },
            error: function (a, b, c) {
                btn.buttonLoading(false);
                btn.removeAttr('loading');
                Dcat.handleAjaxError(a, b, c);
            },
        });

        return false;
    });
</script>