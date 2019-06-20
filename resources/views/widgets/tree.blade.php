<div  {!! $attributes !!}><div class="_tree"></div></div>

<script>
LA.ready(function () {
    var opts = {!! json_encode($options) !!}, tree = $('#{{$id}}').find('._tree');

    opts.core.data = {!! json_encode($nodes) !!};

    tree.on("loaded.jstree", function () {
        tree.jstree('open_all');
    }).jstree(opts);
});
</script>