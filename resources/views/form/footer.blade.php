<div class="box-footer">

    {{ csrf_field() }}

    <div class="col-md-{{$width['label']}}">
    </div>

    <div class="col-md-{{$width['field']}}">

        @if(! empty($buttons['submit']))
        <div class="btn-group pull-right">
            <button class="btn btn-primary submit">{{ trans('admin.submit') }}</button>
        </div>

        @if($checkboxes)
            <div class="d-flex pull-right" style="margin:10px 15px 0 0;">{!! $checkboxes !!}</div>
        @endif

        @endif

        @if(! empty($buttons['reset']))
        <div class="btn-group pull-left">
            <button type="reset" class="btn btn-warning">{{ trans('admin.reset') }}</button>
        </div>
        @endif
    </div>
</div>