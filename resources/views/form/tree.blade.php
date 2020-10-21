<div class="{{$viewClass['form-group']}}">

    <label class="{{$viewClass['label']}} control-label">{!! $label !!}</label>

    <div id="{{ $id }}" class="{{$viewClass['field']}}">

        @include('admin::form.error')

        <div class="input-group" style="width:100%">
            <input {{$disabled}} {!! $attributes !!} class="hidden-input" name="{{$name}}" />

            <div class="jstree-wrapper">
                <div class="d-flex">
                    {!! $checkboxes !!}
                </div>
                <div class="da-tree" style="margin-top:10px"></div>
            </div>
        </div>

        @include('admin::form.help-block')

    </div>
</div>

<script require="@jstree">
    var selector = replaceNestedFormIndex('#{{ $id }}'),
        tree = selector+' .jstree-wrapper .da-tree',
        $tree = $(tree),
        $input = $(selector+' .hidden-input'),
        opts = {!! admin_javascript_json($options) !!},
        parents = {!! json_encode($parents) !!};

    opts.core = opts.core || {};
    opts.core.data = {!! json_encode($nodes) !!};

    $(document).on("click", selector+" input[value=1]", function () {
        $(this).parents('.jstree-wrapper').find('.da-tree').jstree($(this).prop("checked") ? "check_all" : "uncheck_all");
    });
    $(document).on("click", selector+" input[value=2]", function () {
        $(this).parents('.jstree-wrapper').find('.da-tree').jstree($(this).prop("checked") ? "open_all" : "close_all");
    });

    $tree.on("changed.jstree", function (e, data) {
        var i, selected = [];

        $input.val('');

        for (i in data.selected) {
            if (Dcat.helpers.inObject(parents, data.selected[i])) { // 过滤父节点
                continue;
            }
            selected.push(data.selected[i]);
        }

        selected.length && $input.val(selected.join(','));
    }).on("loaded.jstree", function () {
        @if($expand) $(this).jstree('open_all'); @endif
    }).jstree(opts);
</script>