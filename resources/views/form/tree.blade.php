<div class="{{$viewClass['form-group']}} {!! !$errors->has($errorKey) ? '' : 'has-error' !!}">

    <label for="{{$id}}" class="{{$viewClass['label']}} control-label">{!! $label !!}</label>

    <div class="{{$viewClass['field']}}">

        @include('admin::form.error')

        <div class="input-group" style="width:100%">
            <input {{$disabled}} {!! $attributes !!} name="{{$name}}" />

            <div class="jstree-wrapper {{$class}}-tree-wrapper">
                {!! $checkboxes !!}
                <div class="_tree" style="margin-top:10px"></div>
            </div>
        </div>

        @include('admin::form.help-block')

    </div>
</div>

@php
    $formId = $formId ? '#'.$formId : '';
@endphp
<script data-exec-on-popstate>
LA.ready(function () {
    var $tree = $('{!!$formId !!} .{{$class}}-tree-wrapper').find('._tree'),
        opts = {!! $options !!},
        $input = $('{!!$formId !!} input[name="{{$name}}"]'),
        parents = {!! $parents !!};

    opts.core = opts.core || {};
    opts.core.data = {!! $nodes !!};

    $(document).on("click", "{!!$formId !!} .{{$class}}-tree-wrapper input[value=1]", function () {
        $tree.jstree($(this).prop("checked") ? "check_all" : "uncheck_all");
    });
    $(document).on("click", "{!!$formId !!} .{{$class}}-tree-wrapper input[value=2]", function () {
        $tree.jstree($(this).prop("checked") ? "open_all" : "close_all");
    });

    $tree.on("changed.jstree", function (e, data) {
        $input.val('');

        var i, selected = [];
        for (i in data.selected) {
            if (LA.arr.in(parents, data.selected[i])) { // 过滤父节点
                continue;
            }
            selected.push(data.selected[i]);
        }

        selected.length && $input.val(selected.join(','));
    }).on("loaded.jstree", function () {
        @if($expand) $tree.jstree('open_all'); @endif
    }).jstree(opts);

});
</script>