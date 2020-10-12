<thead>
<tr class="{{ $elementClass }} quick-create" style="cursor: pointer">
    <td colspan="{{ $columnCount }}" style="background: {{ Dcat\Admin\Admin::color()->darken('#ededed', 1) }}">
        <span class="create cursor-pointer" style="display: block;">
             <i class="feather icon-plus"></i>&nbsp;{{ __('admin.quick_create') }}
        </span>

        <form class="form-inline create-form" style="display: none;" method="post">
            @foreach($fields as $field)
                &nbsp;{!! $field->render() !!}
            @endforeach
                &nbsp;
            &nbsp;
            <button type="submit" class="btn btn-primary btn-sm">{{ __('admin.submit') }}</button>&nbsp;
            &nbsp;
            <a href="javascript:void(0);" class="cancel">{{ __('admin.cancel') }}</a>
        </form>
    </td>
</tr>
</thead>

<script>
    var ctr = $('.{!! $elementClass !!}'),
        btn = $('.quick-create-button-{!! $uniqueName !!}');

    btn.on('click', function () {
        ctr.toggle().click();
    });

    ctr.on('click', function () {
        ctr.find('.create-form').show();
        ctr.find('.create').hide();
    });

    ctr.find('.cancel').on('click', function () {
        if (btn.length) {
            ctr.hide();
            return;
        }

        ctr.find('.create-form').hide();
        ctr.find('.create').show();
        return false;
    });

    ctr.find('.create-form').submit(function (e) {
        e.preventDefault();

        if (ctr.attr('submitting')) {
            return;
        }

        var btn = $(this).find(':submit').buttonLoading();

        ctr.attr('submitting', 1);

        $.ajax({
            url: '{!! $url !!}',
            type: '{!! $method !!}',
            data: $(this).serialize(),
            success: function(data) {
                ctr.attr('submitting', '');
                btn.buttonLoading(false);

                Dcat.handleJsonResponse(data);
            },
            error:function(xhq){
                btn.buttonLoading(false);
                ctr.attr('submitting', '');
                var json = xhq.responseJSON;
                if (typeof json === 'object') {
                    if (json.message) {
                        Dcat.error(json.message);
                    } else if (json.errors) {
                        var i, errors = [];
                        for (i in json.errors) {
                            errors.push(json.errors[i].join("<br>"));
                        }

                        Dcat.error(errors.join("<br>"));
                    }
                }
            }
        });

        return false;
    });
</script>