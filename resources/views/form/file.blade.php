<div id="{{ $containerId }}" class="{{$viewClass['form-group']}}">

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