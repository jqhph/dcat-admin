<span class="ie-wrap">
    <a
        href="javascript:void(0);"
        class="{{ $class }}"
        data-editinline="popover"
        data-temp="grid-editinline-{{ $type }}-{{ $name }}"
        data-value="{{ $value }}"
        data-original="{{ $value }}"
        data-key="{{ $key }}"
        data-name="{{ $name }}"
        data-url="{!! $url !!}"
        data-refresh="{{ $refresh }}"
    >
        <span class="ie-display">
            {{ $display }}
            @if(! $display)
                <i class="feather icon-edit-2"></i>
            @endif
        </span>
    </a>
</span>

<template>
    <template id="grid-editinline-{{ $type }}-{{ $name }}">
        <div class="ie-content ie-content-{{ $name }}" data-type="{{ $type }}">
            <div class="ie-container">
                @yield('field')
                <div class="error"></div>
            </div>
            <div class="ie-action">
                <button class="btn btn-primary btn-sm ie-submit">{{ __('admin.submit') }}</button>
                <button class="btn btn-white btn-sm ie-cancel">{{ __('admin.cancel') }}</button>
            </div>
        </div>
    </template>
</template>

<style>
    .ie-action button {
        margin: 10px 0 10px 10px;
        float: right;
    }
    [data-editinline="popover"] {
        border-bottom:dashed 1px @primary;
        color: @primary;
        display: inline-block;
    }
    body.dark-mode [data-editinline="popover"] {
        color: @primary;
        border-color: @primary;
    }
</style>

<script>
    function hide() {
        $('[data-editinline="popover"]').popover('hide');
    }

    $('.{{ $class }}').popover({
        html: true,
        container: 'body',
        trigger: 'manual',
        sanitize: false,
        placement: function (context, source) {
            var position = $(source).position();
            if (position.left < 100) return "right";
            if (position.top < 110) return "bottom";
            if ($(window).height() - $(source).offset().top < 370) {
                return 'top';
            }
            return "bottom";
        },
        content: function () {
            var $trigger = $(this);
            var $template = $($('template#'+$(this).data('temp')).html());

            @yield('popover-content')

            return $template.prop("outerHTML");
        }
    }).on('shown.bs.popover', function (e) {

        var $popover = $($(this).data('bs.popover').tip).find('.ie-content');
        var $display = $(this).parents('.ie-wrap').find('.ie-display');
        var $trigger = $(this);

        $popover.data('display', $display);
        $popover.data('trigger', $trigger);

        @yield('popover-shown')

    }).click(function () {
        hide();
        $(this).popover('toggle');
    });
</script>

<script>
    function hide() {
        $('[data-editinline="popover"]').popover('hide');
    }

    $(document).off('click', '.ie-content .ie-cancel').on('click', '.ie-content .ie-cancel', hide)

    $(document).off('click', '.ie-content .ie-submit').on('click', '.ie-content .ie-submit', function () {
        var $popover = $(this).closest('.ie-content'),
            $trigger = $popover.data('trigger'),
            name = $trigger.data('name'),
            original = $trigger.data('original'),
            refresh = $trigger.data('refresh'),
            val,
            label;

        switch($popover.data('type')) {
            case 'input':
            case 'textarea':
                val = $popover.find('.ie-input').val();
                label = val;
                break;
            case 'checkbox':
                val = [];
                label = [];
                $popover.find('.ie-input:checked').each(function(){
                    val.push($(this).val());
                    label.push($(this).parent().text());
                });
                label = label.join(';');
                break;
            case 'radio':
                val = $popover.find('.ie-input:checked').val();
                label = $popover.find('.ie-input:checked').parent().text();
                break;
        }

        if (val == original) {
            hide();
            return;
        }

        Dcat.NP.start();

        var data = {};

        if (name.indexOf('.') === -1) {
            data[name] = val;
        } else {
            name = name.split('.');

            data[name[0]] = {};
            data[name[0]][name[1]] = val;
        }
        data['_inline_edit_'] = 1;

        $.put({
            url: $trigger.data('url'),
            data: data,
            error:function(a,b,c) {
                Dcat.handleAjaxError(a, b, c);
            },
        }).done(function (res) {
            Dcat.NP.done();
            var data = res.data;
            if (res.status === true) {
                Dcat.success(data.message);
                var $display = $popover.data('display');
                $display.text(label);
                if (! label) {
                    $display.html('<i class="feather icon-edit-2"></i>');
                }
                $trigger.data('value', val).data('original', val);
                hide();
                refresh && Dcat.reload();
            } else {
                Dcat.error(data.message);
            }
        });
    });
</script>
