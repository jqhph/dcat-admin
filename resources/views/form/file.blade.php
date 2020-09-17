<style>
    @php($primary = admin_color()->primary())

    .webuploader-pick {
        background-color: {!! $primary !!}
    }

    .web-uploader .placeholder .webuploader-pick {
        background: {!! admin_color()->lighten('primary', 12) !!};
    }

    .web-uploader .placeholder .flashTip a {
        color: {!! admin_color()->lighten('primary', 12) !!};
    }

    .web-uploader .statusBar .upload-progress span.percentage,
    .web-uploader .filelist li p.upload-progress span {
        background: {!! admin_color()->lighten('primary', 10) !!};
    }
</style>

<div id="{{ $id }}-container" class="{{$viewClass['form-group']}}">

    <label for="{{$column}}" class="{{$viewClass['label']}} control-label">{!! $label !!}</label>

    <div class="{{$viewClass['field']}}">

        @include('admin::form.error')

        <input name="{{ $name }}" id="{{ $id }}" type="hidden" />

        <div class="web-uploader {{ $fileType }}">
            <div class="queueList">
                <div class="placeholder dnd-area">
                    <div class="file-picker"></div>
                    <p>{{trans('admin.uploader.drag_file')}}</p>
                </div>
            </div>
            <div class="statusBar" style="display:none;">
                <div class="upload-progress progress progress-bar-primary pull-left">
                    <div class="progress-bar progress-bar-striped active" style="line-height:18px">0%</div>
                </div>
                <div class="info"></div>
                <div class="btns">
                    <div class="add-file-button"></div>
                    @if($showUploadBtn)
                    &nbsp;
                    <div class="upload-btn btn btn-primary"><i class="feather icon-upload"></i> &nbsp;{{trans('admin.upload')}}</div>
                    @endif
                </div>
            </div>
        </div>

        @include('admin::form.help-block')
    </div>
</div>

<script require="@webuploader">
    var uploader,
        newPage,
        cID = replaceNestedFormIndex('#{{ $id }}-container'),
        ID = replaceNestedFormIndex('#{{ $id }}'),
        options = {!! $options !!};

    init();

    function init() {
        var opts = $.extend({
            selector: cID,
            addFileButton: cID+' .add-file-button',
            inputSelector: ID,
        }, options);

        opts.upload = $.extend({
            pick: {
                id: cID+' .file-picker',
                name: '_file_',
                label: '<i class="feather icon-folder"></i>&nbsp; {!! trans('admin.uploader.add_new_media') !!}'
            },
            dnd: cID+' .dnd-area',
            paste: cID+' .web-uploader'
        }, opts);

        uploader = Dcat.Uploader(opts);
        uploader.build();
        uploader.preview();

        function resize() {
            setTimeout(function () {
                if (! uploader) return;

                uploader.refreshButton();
                resize();

                if (! newPage) {
                    newPage = 1;
                    $(document).one('pjax:complete', function () {
                        uploader = null;
                    });
                }
            }, 250);
        }
        resize();
    }
</script>
