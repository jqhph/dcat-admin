<thead>
<tr class="{{ $elementClass }} quick-create">
    <td colspan="{{ $columnCount }}" style="height: 43px;padding-left: 57px;background-color: #f9f9f9; vertical-align: middle;">

        <span class="create" style="color: #bdbdbd;cursor: pointer;display: block;">
             <i class="ti-plus"></i>&nbsp;{{ __('admin.quick_create') }}
        </span>

        <form class="form-inline create-form" style="display: none;" method="post">
            @foreach($fields as $field)
                &nbsp;{!! $field->render() !!}
            @endforeach
                &nbsp;
            <button type="submit" class="btn btn-primary btn-sm">{{ __('admin.submit') }}</button>&nbsp;
            <a href="javascript:void(0);" class="cancel">{{ __('admin.cancel') }}</a>
            {{ csrf_field() }}
        </form>
    </td>
</tr>
</thead>