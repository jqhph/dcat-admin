<span class="{{ $class }}">
    <span style="cursor: pointer" class="switch-dialog">{!! $button !!}</span>

    <template class="content">
        <div {!! $attributes !!}>
            <div class="p-2 dialog-body">{!! $table !!}</div>

            @if($footer)
                <div class="dialog-footer layui-layer-btn">{!! $footer !!}</div>
            @endif
        </div>
    </template>

    {{-- 标题 --}}
    <template class="title">{!! $title !!}</template>

    {{-- 事件监听 --}}
    <template class="event">
        {!! $events['shown'] !!}

        @if(!empty($events['load']))
            $t.on('table:loaded', function (e) { {!! $events['load'] !!} });
        @endif
    </template>
</span>

<script init=".{{ $class }}">
    var area = screen.width <= 850 ? ['100%', '100%',] : '{{ $width }}',
        offset = screen.width <= 850 ? 0 : '70px',
        _tb = '.async-table',
        _container = '.dialog-table',
        _id,
        _temp,
        _title,
        _event,
        _btnId;

    setId(id);

    function hidden(index) {
        {!! $events['hidden'] !!}

        getLayer(index).find(_container).trigger('dialog:hidden');
    }

    function open(btn) {
        var index = layer.open({
            type: 1,
            title: $(_title).html(),
            area: area,
            offset: offset,
            maxmin: {{ $maxmin }},
            resize: {{ $resize }},
            content: $(_temp).html(),
            success: function(layero, index) {
                var $c = getLayer(index).find(_container),
                    $t = getLayer(index).find(_tb);

                $c.attr('layer', index);

                setDataId($c);
                setMaxHeight(index);

                eval($(_event).html());

                setTimeout(function () {
                    Dcat.grid.AsyncTable({container: $t});

                    $t.trigger('table:load');
                }, 100);

                $c.trigger('dialog:shown');

                $c.on('dialog:open', openDialog);
                $c.on('dialog:close', closeDialog)
            },
            cancel: function (index) {
                btn && btn.removeAttr('layer');

                hidden(index)
            }
        });

        btn && btn.attr('layer', index);
    }

    function setDataId(obj) {
        if (! obj.attr('data-id')) {
            obj.attr('data-id', id);
        }
    }

    function setId(val) {
        if (! val) return;

        _id = '#' + val;
        _temp = _id + ' .content';
        _title = _id + ' .title';
        _event = _id + ' .event';
        _btnId = _id + ' .switch-dialog';
    }

    function openDialog () {
        setId($(this).attr('data-id'));
        setDataId($(this));

        if (! $(this).attr('layer')) {
            open($(this));
        }
    }

    function getLayer(index) {
        return $('#layui-layer'+index)
    }

    function closeDialog() {
        var index = $(this).attr('layer');

        getLayer(index).find(_container).removeAttr('layer');
        $(_btnId).removeAttr('layer');

        if (index) {
            layer.close(index);
            hidden(index);
        }
    }

    function setMaxHeight(index) {
        var maxHeight = ($(window).height() - 220);
        if (maxHeight < 250) {
            maxHeight = maxHeight + 120;
        }

        getLayer(index).find('.layui-layer-content').css({'max-height': maxHeight});
    }

    $(_btnId).on('click', openDialog);
</script>
