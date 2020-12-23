<div>
    <span class="grid-expand" data-url="{{ $url }}" data-inserted="0" data-id="{{ $key }}" data-key="{{ $dataKey }}" data-toggle="collapse" data-target="#grid-collapse-{{ $dataKey }}">
       <a href="javascript:void(0)"><i class="feather icon-chevrons-right"></i>  {!! $button !!}</a>
    </span>
    <template class="grid-expand-{{ $dataKey }}">
        <div id="grid-collapse-{{ $dataKey }}">{!! $html !!}</div>
    </template>
</div>

<script once>
    $('.grid-expand').off('click').on('click', function () {
        var _th = $(this), url = _th.data('url');

        if ($(this).data('inserted') == '0') {

            var key = _th.data('key');
            var row = _th.closest('tr');
            var html = $('template.grid-expand-'+key).html();
            var id = 'expand-'+key+Dcat.helpers.random(10);
            var rowKey = _th.data('id');

            $(this).attr('data-expand', '#'+id);
            row.after("<tr id="+id+"><td colspan='"+(row.find('td').length)+"' style='padding:0 !important; border:0;height:0;'>"+html+"</td></tr>");

            if (url) {
                var collapse = $('#grid-collapse-'+key);
                collapse.find('div').loading();
                $('.dcat-loading').css({position: 'inherit', 'padding-top': '70px'});

                Dcat.helpers.asyncRender(url+'&key='+rowKey, function (html) {
                    collapse.html(html);
                })
            }
            $(this).data('inserted', 1);
        } else {
            if ($("i", this).hasClass('icon-chevrons-right')) {
                $(_th.data('expand')).show();
            } else {
                setTimeout(function() {
                    $(_th.data('expand')).hide();
                }, 250);
            }
        }

        $("i", this).toggleClass("icon-chevrons-right icon-chevrons-down");
    });
</script>