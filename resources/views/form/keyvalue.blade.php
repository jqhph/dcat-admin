<div class="{{$viewClass['form-group']}}">

    <label class="{{$viewClass['label']}} control-label">{{$label}}</label>

    <div class="{{$viewClass['field']}}">
        <span name="{{$name}}"></span>
        <input name="{{ $name }}[{{ \Dcat\Admin\Form\Field\KeyValue::DEFAULT_FLAG_NAME }}]" type="hidden" />

        <div class="help-block with-errors"></div>

        <table class="table table-hover">
            <thead>
            <tr>
                <th>{{ __('Key') }}</th>
                <th>{{ __('Value') }}</th>
                <th style="width: 85px;"></th>
            </tr>
            </thead>
            <tbody class="kv-{{ $class }}-table">

            @foreach(($value ?: []) as $k => $v)
                <tr>
                    <td>
                        <div class="form-group">
                            <div class="col-sm-12">
                                <div class="help-block with-errors"></div>

                                <input name="{{ $name }}[keys][{{ $loop->index }}]" value="{{ $k }}" class="form-control" required/>

                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="form-group">
                            <div class="col-sm-12">
                                <div class="help-block with-errors"></div>
                                <input name="{{ $name }}[values][{{ $loop->index }}]" value="{{ $v }}" class="form-control" />
                            </div>
                        </div>
                    </td>

                    <td class="form-group">
                        <div>
                            <div class="{{ $class }}-remove btn btn-white btn-sm pull-right">
                                <i class="feather icon-trash">&nbsp;</i>{{ __('admin.remove') }}
                            </div>
                        </div>
                    </td>
                </tr>
            @endforeach
            </tbody>
            <tfoot>
            <tr>
                <td></td>
                <td></td>
                <td>
                    <div class="{{ $class }}-add btn btn-primary btn-outline btn-sm pull-right">
                        <i class="feather icon-save"></i>&nbsp;{{ __('admin.new') }}
                    </div>
                </td>
            </tr>
            </tfoot>
        </table>
    </div>
</div>

<template class="{{$class}}-tpl">
    <tr>
        <td>
            <div class="form-group  ">
                <div class="col-sm-12">
                    <div class="help-block with-errors"></div>
                    <input name="{{ $name }}[keys][{key}]" class="form-control" required/>
                </div>
            </div>
        </td>
        <td>
            <div class="form-group  ">
                <div class="col-sm-12">
                    <div class="help-block with-errors"></div>
                    <input name="{{ $name }}[values][{key}]" class="form-control" />
                </div>
            </div>
        </td>

        <td class="form-group">
            <div>
                <div class="{{ $class }}-remove btn btn-white btn-sm pull-right">
                    <i class="feather icon-trash">&nbsp;</i>{{ __('admin.remove') }}
                </div>
            </div>
        </td>
    </tr>
</template>