@if(! empty($button['text']) || $click)
    <span class="drop{{ $direction }}" style="display:inline-block">
        <a id="{{ $buttonId }}" class="dropdown-toggle {{ $button['class'] }}" style="{{ $button['style'] }}" data-toggle="dropdown" href="javascript:void(0)">
            <stub>{!! $button['text'] !!}</stub>
            <span class="caret"></span>
        </a>
        <ul class="dropdown-menu">{!! $options !!}</ul>
    </span>
@else
    <ul class="dropdown-menu">{!! $options !!}</ul>
@endif

@if($click)
    <script>
        var $btn = $('#{{ $buttonId }}'),
            $a = $btn.parent().find('ul li a'),
            text = String($btn.text());

        $a.on('click', function () {
            $btn.find('stub').html($(this).html() + ' &nbsp;');
        });

        if (text.replace(/(^\s*)|(\s*$)/g,"")) {
            $btn.find('stub').html(text + ' &nbsp;');
        } else {
            (!$a.length) || $btn.find('stub').html($($a[0]).html() + ' &nbsp;');
        }
    </script>
@endif
