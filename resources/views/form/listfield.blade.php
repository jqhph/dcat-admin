<style>
    td .form-group {margin-bottom: 0 !important;}
</style>

<div class="{{$viewClass['form-group']}} {{$class}}">

    <label class="{{$viewClass['label']}} control-label">{{$label}}</label>

    <div class="{{$viewClass['field']}}">

        <div class="help-block with-errors"></div>

        <span name="{{$name}}"></span>
        <input name="{{ $name }}[values][{{ Dcat\Admin\Form\Field\ListField::DEFAULT_FLAG_NAME }}]" type="hidden" />

        <table class="table table-hover">

            <tbody class="list-table">

            @foreach(($value ?: []) as $k => $v)
                <tr>
                    <td>
                        <div class="form-group">
                            <div class="col-sm-12">
                                <input name="{{ $name }}[values][{{ (int) $k }}]" value="{{ $v }}" class="form-control" />
                                <div class="help-block with-errors"></div>
                            </div>
                        </div>
                    </td>

                    <td style="width: 85px;">
                        <div class="{{$class}}-remove list-remove btn btn-white btn-sm pull-right">
                            <i class="feather icon-trash">&nbsp;</i>
                        </div>
                    </td>
                </tr>
            @endforeach
            </tbody>
            <tfoot>
            <tr>
                <td colspan="2">
                    <div class="list-add btn btn-primary btn-outline btn-sm pull-left">
                        <i class="feather icon-save"></i>&nbsp;{{ __('admin.new') }}
                    </div>
                    <div class="text-center">
                        @include('admin::form.help-block')
                    </div>
                </td>
            </tr>
            </tfoot>
        </table>
    </div>

    <template>
        <tr>
            <td>
                <div class="form-group">
                    <div class="col-sm-12">
                        <input name="{{ $name }}[values][{key}]" class="form-control" />
                        <div class="help-block with-errors"></div>
                    </div>
                </div>
            </td>

            <td style="width: 85px;">
                <div class="list-remove btn btn-white btn-sm pull-right">
                    <i class="feather icon-trash">&nbsp;</i>
                </div>
            </td>
        </tr>
    </template>
</div>

<script init="{!! $selector !!}">
    var index = {{ $count }};
    $this.find('.list-add').on('click', function () {
        var tpl = $this.find('template').html().replace('{key}', index);
        $this.find('tbody.list-table').append(tpl);

        index++;
    });
    $this.find('tbody.list-table').on('click', '.list-remove', function () {
        $(this).closest('tr').remove();
    });
</script>
