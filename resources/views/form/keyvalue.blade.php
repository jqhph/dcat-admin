<style>
    td .form-group {margin-bottom: 0 !important;}
</style>

<div class="{{$viewClass['form-group']}} {{ $class }}">

    <label class="{{$viewClass['label']}} control-label">{{$label}}</label>

    <div class="{{$viewClass['field']}}">
        <span name="{{$name}}"></span>
        <input name="{{ $name }}[{{ \Dcat\Admin\Form\Field\KeyValue::DEFAULT_FLAG_NAME }}]" type="hidden" />

        <div class="help-block with-errors"></div>

        <table class="table table-hover">
            <thead>
            <tr>
                <th>{!! $keyLabel !!}</th>
                <th>{!! $valueLabel !!}</th>
                <th style="width: 85px;"></th>
            </tr>
            </thead>
            <tbody class="kv-table">

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
                            <div class="kv-remove btn btn-white btn-sm pull-right">
                                <i class="feather icon-trash">&nbsp;</i>
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
                    <div class="kv-add btn btn-primary btn-outline btn-sm pull-right">
                        <i class="feather icon-save"></i>&nbsp;{{ __('admin.new') }}
                    </div>
                </td>
            </tr>
            </tfoot>
        </table>
    </div>

    <template>
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
                    <div class="kv-remove btn btn-white btn-sm pull-right">
                        <i class="feather icon-trash">&nbsp;</i>
                    </div>
                </div>
            </td>
        </tr>
    </template>
</div>

<script init="{!! $selector !!}">
    var index = {{ $count }};
    $this.find('.kv-add').on('click', function () {
        var tpl = $this.find('template').html().replace('{key}', index).replace('{key}', index);
        $this.find('tbody.kv-table').append(tpl);

        index++;
    });

    $this.find('tbody.kv-table').on('click', '.kv-remove', function () {
        $(this).closest('tr').remove();
    });
</script>