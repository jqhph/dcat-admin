@php
    $timestamps = new \Dcat\Admin\Widgets\Checkbox('timestamps');
    $timestamps->inline();
    $timestamps->options([1 => 'Created_at & Updated_at'])->checked(1);

    $soft = new \Dcat\Admin\Widgets\Checkbox('soft_deletes');
    $soft->inline();
    $soft->options([1 => ucfirst(trans('admin.scaffold.soft_delete'))]);

    $actionCreators = new \Dcat\Admin\Widgets\Checkbox('create[]');
    $actionCreators->inline();
    $actionCreators->options([
        'migration' => ucfirst(trans('admin.scaffold.create_migration')),
        'model' => ucfirst(trans('admin.scaffold.create_model')),
        'repository' => ucfirst(trans('admin.scaffold.create_repository')),
        'controller' => ucfirst(trans('admin.scaffold.create_controller')),
        'migrate' => ucfirst(trans('admin.scaffold.run_migrate')),
        'lang' => ucfirst(trans('admin.scaffold.create_lang')),
    ])->checkedAll(['migrate', 'migration']);
@endphp
<style>
    /*.table>thead>tr>th {*/
    /*    font-size:12px;*/
    /*    text-transform:uppercase;*/
    /*}*/
    .select2-container .select2-selection--single {
        height: 34px !important;
    }
    #inputTableName {
        width:300px;
        border-right:0;
    }
    #inputModelName {
        width:600px;
    }
    #inputControllerName {
        width:600px;
    }
    .choose-exist-table {
        min-width:300px
    }
</style>
<div class="card">
    <div style="height:10px"></div>
    <!-- /.box-header -->
    <div class="card-body" style="padding:18px 0 0">

        <form method="post" action="{{$action}}" id="scaffold" pjax-container>

            <div class="form-horizontal">

                <div class="form-group">

                    <label for="inputTableName" class="col-sm-1 control-label">{{ucfirst(trans('admin.scaffold.table'))}}</label>

                    <div class="col-sm-11 ">
                        <div class="input-group">
                            <input type="text" name="table_name" class="form-control" id="inputTableName" placeholder="{{ucfirst(trans('admin.scaffold.table'))}}" value="{{ old('table_name') }}">
                            <div class=" pull-left" style="">
                                <select class="choose-exist-table"  name="exist-table">
                                    <option value="0" selected>{{trans('admin.scaffold.choose')}}</option>
                                    @foreach($tables as $db => $tb)
                                        <optgroup label="{!! $db !!}">
                                            @foreach($tb as $v)
                                                <option value="{{$db}}|{{$v}}">{{$v}}</option>
                                            @endforeach
                                        </optgroup>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <span class="help-block hide" id="table-name-help" style="margin-left:100px">
                        <i class="fa fa-info"></i>&nbsp; Table name can't be empty!
                    </span>

                </div>
                <div class="form-group">
                    <label for="inputModelName" class="col-sm-1 control-label">{{ucfirst(trans('admin.scaffold.model'))}}</label>

                    <div class="col-sm-4">
                        <input type="text" name="model_name" class="form-control" id="inputModelName" placeholder="{{ucfirst(trans('admin.scaffold.model'))}}" value="{{ old('model_name', "App\\Models\\") }}">
                    </div>
                </div>

                <div class="form-group">
                    <label for="inputControllerName" class="col-sm-1 control-label">{{ucfirst(trans('admin.scaffold.controller'))}}</label>

                    <div class="col-sm-4">
                        <input type="text" name="controller_name" class="form-control" id="inputControllerName" placeholder="{{ucfirst(trans('admin.scaffold.controller'))}}" value="{{ old('controller_name', "App\\Admin\\Controllers\\") }}">
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-sm-offset-1 col-sm-11">
                        {!! $actionCreators->render(); !!}
                    </div>
                </div>

            </div>

            {{--            <hr />--}}
            <table class="table table-hover responsive table-header-gray " id="table-fields" style="margin-top:25px;">
                <thead>
                <tr>
                    <th style="width: 200px">{{trans('admin.scaffold.field_name')}}</th>
                    <th>{{trans('admin.scaffold.translation')}}</th>
                    <th>{{trans('admin.scaffold.type')}}</th>
                    <th>{{trans('admin.scaffold.nullable')}}</th>
                    <th>{{trans('admin.scaffold.key')}}</th>
                    <th>{{trans('admin.scaffold.default')}}</th>
                    <th>{{trans('admin.scaffold.comment')}}</th>
                    <th>{{trans('admin.action')}}</th>
                </tr>
                </thead>
                <tbody>
                @if(old('fields'))
                    @foreach(old('fields') as $index => $field)
                        <tr>
                            <td>
                                <input type="text" name="fields[{{$index}}][name]" class="form-control" placeholder="{{trans('admin.scaffold.field')}}" value="{{$field['name']}}" />
                            </td>
                            <td>
                                <input type="text" name="fields[{{$index}}][translation]" class="form-control" placeholder="{{trans('admin.scaffold.translation')}}" value="{{$field['translation']}}" />
                            </td>
                            <td>
                                <select style="width: 200px" name="fields[{{$index}}][type]">
                                    @foreach($dbTypes as $type)
                                        <option value="{{ $type }}" {{$field['type'] == $type ? 'selected' : '' }}>{{$type}}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td>
                                <div class="checkbox checkbox-primary checkbox-inline checkbox-circle">
                                    <input name="fields[{{$index}}][nullable]" type="checkbox" {{ \Illuminate\Support\Arr::get($field, 'nullable') == 'on' ? 'checked': '' }}>
                                    <label></label>
                                </div>
                            </td>
                            <td>
                                <select style="width: 150px" name="fields[{{$index}}][key]">
                                    {{--<option value="primary">Primary</option>--}}
                                    <option value="" {{$field['key'] == '' ? 'selected' : '' }}>NULL</option>
                                    <option value="unique" {{$field['key'] == 'unique' ? 'selected' : '' }}>Unique</option>
                                    <option value="index" {{$field['key'] == 'index' ? 'selected' : '' }}>Index</option>
                                </select>
                            </td>
                            <td><input type="text" class="form-control" placeholder="{{trans('admin.scaffold.default')}}" name="fields[{{$index}}][default]" value="{{$field['default']}}"/></td>
                            <td><input type="text" class="form-control" placeholder="{{trans('admin.scaffold.comment')}}" name="fields[{{$index}}][comment]" value="{{$field['comment']}}" /></td>
                            <td><a class="btn btn-sm btn-danger table-field-remove"><i class="ti-trash"></i></a></td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td>
                            <input type="text" name="fields[0][name]" class="form-control" placeholder="{{trans('admin.scaffold.field')}}" />
                        </td>
                        <td>
                            <input type="text" name="fields[0][translation]" class="form-control" placeholder="{{trans('admin.scaffold.translation')}}" />
                        </td>
                        <td>
                            <select style="width: 200px" name="fields[0][type]">
                                @foreach($dbTypes as $type)
                                    <option value="{{ $type }}">{{$type}}</option>
                                @endforeach
                            </select>
                        </td>
                        <td>
                            <div class="checkbox checkbox-primary checkbox-inline checkbox-circle">
                                <input name="fields[0][nullable]" type="checkbox"  />
                                <label></label>
                            </div>
                        <td>
                            <select style="width: 150px" name="fields[0][key]">
                                {{--<option value="primary">Primary</option>--}}
                                <option value="" selected>NULL</option>
                                <option value="unique">Unique</option>
                                <option value="index">Index</option>
                            </select>
                        </td>
                        <td><input type="text" class="form-control" placeholder="{{trans('admin.scaffold.default')}}" name="fields[0][default]"></td>
                        <td><input type="text" class="form-control" placeholder="{{trans('admin.scaffold.comment')}}" name="fields[0][comment]"></td>
                        <td><a class="btn btn-sm btn-danger table-field-remove"><i class="ti-trash"></i></a></td>
                    </tr>
                @endif
                </tbody>
            </table>

            <hr style="margin-top: 0;"/>

            <div class='form-inline margin' style="width: 100%">


                <div class='form-group'>
                    <button type="button" class="btn btn-sm btn-success" id="add-table-field"><i class="ti-plus"></i>&nbsp;&nbsp;{{ucfirst(trans('admin.scaffold.add_field'))}}</button>
                </div>

                <div class='form-group pull-right' style="margin-right: 20px; margin-top: 5px;">
                    {!! $timestamps->render() !!}
                    {!! $soft->render() !!}
                </div>

                <div class="form-group pull-right" style="margin-right: 20px;">
                    <label for="inputPrimaryKey">{{ucfirst(trans('admin.scaffold.pk'))}}</label>
                    <input type="text" name="primary_key" class="form-control" id="inputPrimaryKey" placeholder="{{ucfirst(trans('admin.scaffold.pk'))}}" value="id" style="width: 100px;">
                </div>

            </div>

            <!-- /.box-body -->
            <div class="box-footer">
                <button type="submit" class="btn btn-primary pull-right">{{ucfirst(trans('admin.submit'))}}</button>
            </div>

        {{ csrf_field() }}

        <!-- /.box-footer -->
        </form>

    </div>

</div>

<template id="table-field-tpl">
    <tr>
        <td>
            <input type="text" value="{name}" name="fields[__index__][name]" class="form-control" placeholder="{{trans('admin.scaffold.field')}}" />
        </td>
        <td>
            <input type="text" value="{translation}" name="fields[__index__][translation]" class="form-control" placeholder="{{trans('admin.scaffold.translation')}}" />
        </td>
        <td>
            <select style="width: 200px" name="fields[__index__][type]">
                @foreach($dbTypes as $type)
                    <option value="{{ $type }}">{{$type}}</option>
                @endforeach
            </select>
        </td>
        <td>
            <div class="checkbox checkbox-primary checkbox-inline checkbox-circle">
                <input {nullable} name="fields[__index__][nullable]" type="checkbox"  />
                <label></label>
            </div>
        <td>
            <select style="width: 150px" name="fields[__index__][key]">
                <option value="" selected>NULL</option>
                <option value="unique">Unique</option>
                <option value="index">Index</option>
            </select>
        </td>
        <td><input value="{default}" type="text" class="form-control" placeholder="{{trans('admin.scaffold.default')}}" name="fields[__index__][default]"></td>
        <td><input value="{comment}" type="text" class="form-control" placeholder="{{trans('admin.scaffold.comment')}}" name="fields[__index__][comment]"></td>
        <td><a class="btn btn-sm btn-danger table-field-remove"><i class="ti-trash"></i></a></td>
    </tr>
</template>

<script>
    LA.ready(function () {
        var typing = 0,
            $model = $('#inputModelName'),
            $controller = $('#inputControllerName'),
            $table = $('#inputTableName'),
            $fieldsBody = $('#table-fields tbody'),
            tpl = $('#table-field-tpl').html(),
            modelNamespace = 'App\\Models\\',
            controllerNamespace = 'App\\Admin\\Controllers\\',
            dataTypeMap = {!! json_encode($dataTypeMap) !!};

        $('select').select2();

        $('#add-table-field').click(function (event) {
            add_field();
        });

        $('#table-fields').on('click', '.table-field-remove', function(event) {
            $(event.target).closest('tr').remove();
        });

        $('#scaffold').on('submit', function (event) {

            //event.preventDefault();

            if ($table.val() == '') {
                $table.closest('.form-group').addClass('has-error');
                $('#table-name-help').removeClass('hide');

                return false;
            }

            return true;
        });

        $('.choose-exist-table').on('change', function () {
            var val = $(this).val(), tb, db;
            if (val == '0') {
                $table.val('');
                get_tr().remove();
                return;
            }
            val = val.split('|');
            db = val[0];
            tb = val[1];

            LA.loading();
            $table.val(tb);

            write_controller(tb);
            write_model(tb);

            $.post('{{admin_url('helpers/scaffold/table')}}', {db: db, tb: tb, _token: LA.token}, function (res) {
                LA.loading(false);

                if (!res.list) return;
                var i, list = res.list, $id = $('#inputPrimaryKey'), updated, created, soft;

                get_tr().remove();
                for (i in list) {
                    if (list[i].id) {
                        $id.val(i);
                        continue;
                    }
                    if (i == 'updated_at') {
                        updated = list[i];
                        continue;
                    }
                    if (i == 'created_at') {
                        created = list[i];
                        continue;
                    }
                    if (i == 'deleted_at') {
                        soft = list[i];
                        continue;
                    }

                    var c = LA.str.replace(list[i].comment, '"', '');
                    add_field({
                        name: i,
                        lang: c,
                        type: list[i].type,
                        default: LA.str.replace(list[i].default, '"', ''),
                        comment: c,
                        nullable: list[i].nullable != 'NO',
                    });
                }

                add_timestamps(updated, created);
                add_softdelete(soft);
            });

        });


        $table.on('keyup', function (e) {
            var $this = $(this);
            $this.val($this.val());

            if (typing == 1) {
                return;
            }
            typing = 1;

            setTimeout(function () {
                typing = 0;

                write_controller($this.val());
                write_model($this.val());
            }, 100);

        });

        function add_timestamps(updated, created) {
            if (updated && created) {
                return;
            }
            $('[name="timestamps"]').prop('checked', false);

            var c;
            if (updated) {
                c = LA.str.replace(updated.comment, '"', '');
                add_field({
                    name: 'updated_at',
                    lang: c,
                    type: updated.type,
                    default: LA.str.replace(updated.default, '"', ''),
                    comment: c,
                    nullable: updated.nullable != 'NO',
                });
            }
            if (created) {
                c = LA.str.replace(created.comment, '"', '');
                add_field({
                    name: 'created_at',
                    lang: c,
                    type: created.type,
                    default: LA.str.replace(created.default, '"', ''),
                    comment: c,
                    nullable: created.nullable != 'NO',
                });
            }
        }

        function add_softdelete(soft) {
            if (soft) {
                $('[name="soft_deletes"]').prop('checked', true);
            }
        }

        function add_field(val) {
            val = val || {};

            var idx = get_tr().length,
                $field = $(
                    tpl
                        .replace(/__index__/g, idx)
                        .replace(/{name}/g, val.name || '')
                        .replace(/{translation}/g, val.lang || '')
                        .replace(/{default}/g, val.default || '')
                        .replace(/{comment}/g, val.comment || '')
                        .replace(/{nullable}/g, val.nullable ? 'checked' : '')
                ),
                i;

            $fieldsBody.append($field);
            $('select').select2();

            // 选中字段类型
            for (i in dataTypeMap) {
                if (val.type == i) {
                    $field.find('[name="fields['+ idx +'][type]"]')
                        .val(dataTypeMap[i])
                        .trigger("change");
                }
            }

        }

        function write_controller(val) {
            val = ucfirst(to_hump(to_line(val)));
            $controller.val(val ? (controllerNamespace + val + 'Controller') : controllerNamespace);
        }
        function write_model(val) {
            $model.val(modelNamespace + ucfirst(ucfirst(to_hump(to_line(val)))));
        }

        function get_tr() {
            return $('#table-fields tbody tr');
        }

        // 下划线转换驼峰
        function to_hump(name) {
            return name.replace(/\_(\w)/g, function (all, letter) {
                return letter.toUpperCase();
            });
        }

        // 驼峰转换下划线
        function to_line(name) {
            return name.replace(/([A-Z])/g,"_$1").toLowerCase();
        }

        function ucfirst(str) {
            var reg = /\b(\w)|\s(\w)/g;
            return str.replace(reg,function(m){
                return m.toUpperCase()
            });
        }


    });

</script>