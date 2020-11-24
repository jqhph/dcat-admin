<thead>
<tr class="{{ $elementClass }} quick-create" style="cursor: pointer">
    <td colspan="{{ $columnCount }}" style="background: {{ Admin::color()->darken('#ededed', 1) }}">
        <span class="create cursor-pointer" style="display: block;">
             <i class="feather icon-plus"></i>&nbsp;{{ __('admin.quick_create') }}
        </span>

        <form class="form-inline create-form" style="display: none;" method="post">
            @foreach($fields as $field)
                &nbsp;{!! $field->render() !!}
            @endforeach
                &nbsp;
            &nbsp;
            <button type="submit" class="btn btn-primary btn-sm">{{ __('admin.submit') }}</button>&nbsp;
            &nbsp;
            <a href="javascript:void(0);" class="cancel">{{ __('admin.cancel') }}</a>
            {{ csrf_field() }}
        </form>
    </td>
</tr>
</thead>