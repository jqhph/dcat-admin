@php
   $random = \Illuminate\Support\Str::random(8);
@endphp
<div class="{{$viewClass['form-group']}} {!! !$errors->has($errorKey) ? '' : 'has-error' !!}">

    <label for="{{$column}}" class="{{$viewClass['label']}} control-label">{!! $label !!}</label>

    <div class="{{$viewClass['field']}}">

        @include('admin::form.error')

        <input name="{{$name}}" id="{{$id}}" type="hidden" />

        <div class="web-uploader {{$_files}}" id="web-uploader-{{$random}}" style="">
            <div class="queueList">
                <div id="dnd-area-{{$random}}" class="placeholder">
                    <div id="file-picker-{{$random}}"></div>
                    <p>{{trans('admin.uploader.drag_file')}}</p>
                </div>
            </div>
            <div class="statusBar" style="display:none;">
                <div class="upload-progress progress pull-left">
                    <div class="progress-bar progress-bar-primary progress-bar-striped active" style="line-height:18px">0%</div>
                </div>
                <div class="info"></div>
                <div class="btns">
                    <div id="add-file-{{$random}}" class="add-file-button"></div> &nbsp;
                    <div class="uploadBtn btn btn-primary"><i class="fa fa-upload"></i> &nbsp;{{trans('admin.upload')}}</div>
                </div>
            </div>
        </div>

        @include('admin::form.help-block')
    </div>
</div>

<script data-exec-on-popstate>
LA.ready(function () {
    var upload, options = {!! $options !!};

    init();

    function init() {
        var opts = $.extend({
            wrapper: '#web-uploader-{{$random}}',
            addFileButton: '#add-file-{{$random}}',
        }, options);

        opts.upload = $.extend({
            pick: {
                id: '#file-picker-{{$random}}',
                label: '<i class="glyphicon glyphicon-folder-open"></i>&nbsp; {{trans('admin.uploader.add_new_media')}}'
            },
            dnd: '#dnd-area-{{$random}}',
            paste: '#web-uploader-{{$random}}'
        }, opts);

        upload = LA.Uploader(opts);

        upload.build();

        upload.preview();
    }


});
</script>
