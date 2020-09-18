<span style="cursor: pointer" id="button-{{ $id }}">{!! $button !!}</span>

<template id="temp-{$this->id()}">
    <div {!! $attributes !!}>
        <div class="p-2 dialog-body">{!! $table !!}</div>

        @if($footer)
        <div class="dialog-footer layui-layer-btn">{!! $footer !!}</div>
        @endif
    </div>
</template>

<script>
    var id = replaceNestedFormIndex('{{ $id }}'),
        area = screen.width <= 850 ? ['100%', '100%',] : '{{ $width }}',
        offset = screen.width <= 850 ? 0 : '70px',
        _id, _tempId, _btnId, _tb;

    setId(id);

    function hidden(index) {
        {!! $events['hidden'] !!}

        $(_id).trigger('dialog:hidden');
    }

    function open(btn) {
        var index = layer.open({
            type: 1,
            title: '{!! $title !!}',
            area: area,
            offset: offset,
            maxmin: false,
            resize: false,
            content: $(_tempId).html(),
            success: function(layero, index) {
                $(_id).attr('layer', index);

                setDataId($(_id));

                {!! $events['shown'] !!}

                setTimeout(function () {
                    Dcat.grid.AsyncTable({container: _tb});

                    $(_tb).trigger('table:load');
                }, 100);

                $(_id).trigger('dialog:shown');

                $(_id).on('dialog:open', openDialog);
                $(_id).on('dialog:close', closeDialog)
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

        id = val;
        _id = '#'+id;
        _tempId = '#temp-'+id;
        _btnId = '#button-'+id;
        _tb = _id+' .async-table';
    }

    function openDialog () {
        setId($(this).attr('data-id'));
        setDataId($(this));

        if (! $(this).attr('layer')) {
            open($(this));
        }
    }

    function closeDialog() {
        var index = $(this).attr('layer');

        $(_id).removeAttr('layer');
        $(_btnId).removeAttr('layer');

        if (index) {
            layer.close(index);
            hidden(index);
        }
    }

    $(_btnId).on('click', openDialog);
</script>