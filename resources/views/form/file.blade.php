<div id="{{$_id}}" class="{{$viewClass['form-group']}} {!! !$errors->has($errorKey) ? '' : 'has-error' !!}">

    <label for="{{$column}}" class="{{$viewClass['label']}} control-label">{!! $label !!}</label>

    <div class="{{$viewClass['field']}}">

        @include('admin::form.error')

        <input name="{{$name}}" id="{{$id}}" type="hidden" />

        <div class="web-uploader {{$_files}}"  style="">
            <div class="queueList">
                <div class="placeholder dnd-area">
                    <div class="file-picker"></div>
                    <p>{{trans('admin.uploader.drag_file')}}</p>
                </div>
            </div>
            <div class="statusBar" style="display:none;">
                <div class="upload-progress progress pull-left">
                    <div class="progress-bar progress-bar-primary progress-bar-striped active" style="line-height:18px">0%</div>
                </div>
                <div class="info"></div>
                <div class="btns">
                    <div class="add-file-button"></div> &nbsp;
                    <div class="uploadBtn btn btn-primary"><i class="fa fa-upload"></i> &nbsp;{{trans('admin.upload')}}</div>
                </div>
            </div>
        </div>

        @include('admin::form.help-block')
    </div>
</div>

<script data-exec-on-popstate>
LA.ready(function () {
    var upload, options = {!! $options !!}, listenComplete;

    init();

    function init() {
        var opts = $.extend({
            selector: '#{{$_id}}',
        }, options);

        opts.upload = $.extend({
            pick: {
                id: '#{{$_id}} .file-picker',
                label: '<i class="glyphicon glyphicon-folder-open"></i>&nbsp; {{trans('admin.uploader.add_new_media')}}'
            },
            dnd: '#{{$_id}} .dnd-area',
            paste: '#{{$_id}} .web-uploader'
        }, opts);

        upload = LA.Uploader(opts);
        upload.build();
        upload.preview();

        function resize() {
            setTimeout(function () {
                if (! upload) return;

                upload.refreshButton();
                resize();

                if (! listenComplete) {
                    listenComplete = 1;
                    $(document).one('pjax:complete', function () {
                        upload = null;
                    });
                }
            }, 250);
        }
        resize();


    }
});
</script>
