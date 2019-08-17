@php($listErrorKey = "$column.values")

<div class="{{$viewClass['form-group']}} {{ $errors->has($listErrorKey) ? 'has-error' : '' }}">

    <label class="{{$viewClass['label']}} control-label">{{$label}}</label>

    <div class="{{$viewClass['field']}}">

        @if($errors->has($listErrorKey))
            @foreach($errors->get($listErrorKey) as $message)
                <label class="control-label" for="inputError"><i class="fa fa-times-circle-o"></i> {{$message}}</label><br/>
            @endforeach
        @endif
        <error></error>

        <span name="{{$name}}"></span>
        <input name="{{ $name }}[values][{{ \Dcat\Admin\Form\Field\ListField::DEFAULT_FLAG_NAME }}]" type="hidden" />

        <table class="table table-hover">

            <tbody class="list-{{$column}}-table">

            @foreach(old("{$column}.values", ($value ?: [])) as $k => $v)

                @php($itemErrorKey = "{$column}.values.{$loop->index}")

                <tr>
                    <td>
                        <div class="form-group {{ $errors->has($itemErrorKey) ? 'has-error' : '' }}">
                            <div class="col-sm-12">
                                <input name="{{ $name }}[values][{{ (int) $k }}]" value="{{ old("{$column}.values.{$k}", $v) }}" class="form-control" />
                                <error></error>
                                @if($errors->has($itemErrorKey))
                                    @foreach($errors->get($itemErrorKey) as $message)
                                        <label class="control-label" for="inputError"><i class="fa fa-times-circle-o"></i> {{$message}}</label><br/>
                                    @endforeach
                                @endif
                            </div>
                        </div>
                    </td>

                    <td style="width: 75px;">
                        <div class="{{$column}}-remove btn btn-warning btn-sm pull-right">
                            <i class="fa fa-trash">&nbsp;</i>{{ __('admin.remove') }}
                        </div>
                    </td>
                </tr>
            @endforeach
            </tbody>
            <tfoot>
            <tr>
                <td></td>
                <td>
                    <div class="{{ $column }}-add btn btn-success btn-sm pull-right">
                        <i class="fa fa-save"></i>&nbsp;{{ __('admin.new') }}
                    </div>
                </td>
            </tr>
            </tfoot>
        </table>
    </div>
</div>

<template class="{{$column}}-tpl">
    <tr>
        <td>
            <div class="form-group">
                <div class="col-sm-12">
                    <input name="{{ $name }}[values][{key}]" class="form-control" />
                    <error></error>
                </div>
            </div>
        </td>

        <td style="width: 75px;">
            <div class="{{$column}}-remove btn btn-warning btn-sm pull-right">
                <i class="fa fa-trash">&nbsp;</i>{{ __('admin.remove') }}
            </div>
        </td>
    </tr>
</template>