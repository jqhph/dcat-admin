<div class="box-footer">

    {{ csrf_field() }}

    <div class="col-md-{{$width['label']}}">
    </div>

    <div class="col-md-{{$width['field']}}">

        @if(! empty($buttons['submit']))
        <div class="btn-group pull-right">
            <button class="btn btn-primary submit"><i class="feather icon-save"></i> {{ trans('admin.submit') }}</button>
        </div>

        @if($checkboxes)
            <div class="d-flex pull-right" style="margin:10px 15px 0 0;">{!! $checkboxes !!}</div>
        @endif

        @endif

        @if(! empty($buttons['reset']))
        <div class="btn-group pull-left">
            <button type="reset" class="btn btn-warning"><i class="feather icon-rotate-ccw"></i> {{ trans('admin.reset') }}</button>
        </div>
        @endif
    </div>
</div>