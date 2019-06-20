<div id="dcat-admin-terminal"></div>
<script>
    LA.ready(function () {
        var _terminal = $('#dcat-admin-terminal').lxhTerminal({!! json_encode($options) !!});

    });
</script>