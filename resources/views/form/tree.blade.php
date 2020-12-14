<div class="{{$viewClass['form-group']}}">

    <label class="{{$viewClass['label']}} control-label">{!! $label !!}</label>

    <div class="{{$viewClass['field']}} {{ $class }}">

        @include('admin::form.error')

        <div class="input-group" style="width:100%">
            <input {{$disabled}} type="hidden" class="hidden-input" name="{{$name}}" />

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

<script require="@jstree" init="{!! $selector !!}">
    var $tree = $this.find('.jstree-wrapper .da-tree'),
        $input = $this.find('.hidden-input'),
        opts = {!! admin_javascript_json($options) !!},
        parents = {!! json_encode($parents) !!};

    opts.core = opts.core || {};
    opts.core.data = {!! json_encode($nodes) !!};

    $this.find('input[value=1]').on("click", function () {
        $(this).parents('.jstree-wrapper').find('.da-tree').jstree($(this).prop("checked") ? "check_all" : "uncheck_all");
    });
    $this.find('input[value=2]').on("click", function () {
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