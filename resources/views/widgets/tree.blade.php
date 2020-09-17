<div  {!! $attributes !!}><div class="da-tree"></div></div>

<script require="@jstree">
    var opts = {!! json_encode($options) !!}, tree = $('#{{$id}}').find('.da-tree');

    opts.core.data = {!! json_encode($nodes) !!};

    tree.on("loaded.jstree", function () {
        tree.jstree('open_all');
    }).jstree(opts);
</script>