<form class="form-group {{ $class }}" style="text-align: left" data-key="{{ $key }}">

    @foreach($options as $v => $label)
        @php($checked = \Dcat\Admin\Support\Helper::equal($v, $value) ? 'checked' : '')

        <div class="vs-radio-con">
            <input type="radio" name="grid-radio-{{ $column }}[]" value="{{ $v }}" {{ $checked }} >
            <span class="vs-radio">
              <span class="vs-radio--border"></span>
              <span class="vs-radio--circle"></span>
            </span>
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
        var value = $(this).find('input:radio:checked').val(),
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
                '{{ $column }}': value,
            },
            success: function (d) {
                btn.buttonLoading(false);
                btn.removeAttr('loading');
                if (d.status) {
                    Dcat.success(d.data.message);
                    reload && Dcat.reload();
                } else {
                    Dcat.error(d.data.message);
                }
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
