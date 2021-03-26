<span class="dropdown column-selector" >
    <button class="btn btn-primary btn-outline dropdown-toggle" data-toggle="dropdown">
        <i class="fa fa-table"></i>
        <span class="caret"></span>
    </button>
    <ul class="dropdown-menu" role="menu" style="min-width: 155px">
        <li class="dropdown-item">
            <ul class="selectors">
                {!! $selectAll !!}
            </ul>
        </li>
        <li class="dropdown-divider"></li>
        <li class="dropdown-item">
            <ul class="selectors">
                {!! $checkbox !!}
            </ul>
        </li>
    </ul>
</span>

<script once>
    $('.column-selector input[name="_all_"]').on('change', function () {
        $(this).parents('.column-selector').find('.column-select-item').prop('checked', this.checked).change()
    });

    var submit = Dcat.helpers.debounce(function ($this) {
        var defaults = {!! json_encode($defaults) !!};
        var selected = [];
        var $parent = $this.parents('.column-selector');
        var column = '{{ $columnName }}'

        $parent.find('.column-select-item:checked').each(function () {
            selected.push($(this).val());
        });

        if (selected.length == 0) {
            return;
        }

        var url = new URL(location);

        if (selected.sort().toString() == defaults.sort().toString()) {
            url.searchParams.set(column, '');
        } else {
            url.searchParams.set(column, selected.join());
        }

        Dcat.reload(url.toString());
    }, 200);

    $('.column-selector .column-select-item').on('change', function () {
        submit($(this));
    });
</script>
