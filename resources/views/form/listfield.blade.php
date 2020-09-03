<div class="{{$viewClass['form-group']}}">

    <label class="{{$viewClass['label']}} control-label">{{$label}}</label>

    <div class="{{$viewClass['field']}}">

        <div class="help-block with-errors"></div>

        <span name="{{$name}}"></span>
        <input name="{{ $name }}[values][{{ \Dcat\Admin\Form\Field\ListField::DEFAULT_FLAG_NAME }}]" type="hidden" />

        <table class="table table-hover">

            <tbody class="list-{{$columnClass}}-table">

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
                        <div class="{{$columnClass}}-remove btn btn-white btn-sm pull-right">
                            <i class="feather icon-trash">&nbsp;</i>{{ __('admin.remove') }}
                        </div>
                    </td>
                </tr>
            @endforeach
            </tbody>
            <tfoot>
            <tr>
                <td></td>
                <td>
                    <div class="{{ $columnClass }}-add btn btn-primary btn-outline btn-sm pull-right">
                        <i class="feather icon-save"></i>&nbsp;{{ __('admin.new') }}
                    </div>
                </td>
            </tr>
            </tfoot>
        </table>
    </div>
</div>

<template class="{{$columnClass}}-tpl">
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
            <div class="{{$columnClass}}-remove btn btn-white btn-sm pull-right">
                <i class="feather icon-trash">&nbsp;</i>{{ __('admin.remove') }}
            </div>
        </td>
    </tr>
</template>