<div id="dcat-admin-terminal"></div>
<script>
    Dcat.ready(function () {
        var _terminal = $('#dcat-admin-terminal').lxhTerminal({!! json_encode($options) !!});

    });
</script>