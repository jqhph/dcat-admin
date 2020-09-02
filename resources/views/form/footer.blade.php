<div class="box-footer">

    <div class="col-md-{{$width['label']}} d-md-block" style="display: none"></div>

    <div class="col-md-{{$width['field']}}">

        @if(! empty($buttons['submit']))
            <div class="btn-group pull-right">
                <button class="btn btn-primary submit"><i class="feather icon-save"></i> {{ trans('admin.submit') }}</button>
            </div>

            @if($checkboxes)
                <div class="pull-right d-md-flex" style="margin:10px 15px 0 0;display: none">{!! $checkboxes !!}</div>
            @endif

        @endif

        @if(! empty($buttons['reset']))
        <div class="btn-group pull-left">
            <button type="reset" class="btn btn-white"><i class="feather icon-rotate-ccw"></i> {{ trans('admin.reset') }}</button>
        </div>
        @endif
    </div>
</div>