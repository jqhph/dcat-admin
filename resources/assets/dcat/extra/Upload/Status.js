
export default class Status {
    constructor(Uploder) {
        this.uploader = Uploder;

        // 可能有pending, ready, uploading, confirm, done.
        this.state = 'pending';

        // 已上传文件数量
        this.originalFilesNum = Dcat.helpers.len(Uploder.options.preview);
    }

    switch(val, args) {
        let _this = this,
            parent = _this.uploader;

        args = args || {};

        if (val === _this.state) {
            return;
        }

        // 上传按钮状态
        if (parent.$uploadButton) {
            parent.$uploadButton.removeClass('state-' + _this.state);
            parent.$uploadButton.addClass('state-' + val);
        }

        _this.state = val;

        switch (_this.state) {
            case 'pending':
                _this.pending();

                break;

            case 'ready':
                _this.ready();

                break;

            case 'uploading':
                _this.uploading();

                break;

            case 'paused':
                _this.paused();

                break;

            case 'confirm':
                _this.confirm();

                break;
            case 'finish':
                _this.finish();

                break;
            case 'decrOriginalFileNum':
                _this.decrOriginalFileNum();

                break;

            case 'incrOriginalFileNum':
                _this.incrOriginalFileNum();

                break;

            case 'decrFileNumLimit': // 减少上传文件数量限制
                _this.decrFileNumLimit(args.num);

                break;
            case 'incrFileNumLimit': // 增加上传文件数量限制
                _this.incrFileNumLimit(args.num || 1);

                break;
            case 'init': // 初始化
                _this.init();

                break;
        }

        // 更新状态显示
        _this.updateStatusText();
    }

    incrOriginalFileNum() {
        this.originalFilesNum++;
    }

    decrOriginalFileNum() {
        if (this.originalFilesNum > 0) {
            this.originalFilesNum--;
        }
    }

    confirm() {
        let _this = this,
            parent = _this.uploader,
            uploader = parent.uploader,
            stats;

        if (uploader) {
            parent.$progress.hide();
            parent.$selector.find(parent.options.addFileButton).removeClass('element-invisible');
            parent.$uploadButton.text(parent.lang.trans('start_upload'));

            stats = uploader.getStats();

            if (stats.successNum && !stats.uploadFailNum) {
                _this.switch('finish');
            }
        }
    }

    paused() {
        let _this = this,
            parent = _this.uploader;

        parent.$progress.show();
        parent.$uploadButton.text(parent.lang.trans('go_on_upload'));
    }

    uploading() {
        let _this = this,
            parent = _this.uploader;

        parent.$selector.find(parent.options.addFileButton).addClass('element-invisible');
        parent.$progress.show();
        parent.$uploadButton.text(parent.lang.trans('pause_upload'));
    }

    pending() {
        let _this = this,
            parent = _this.uploader,
            options = parent.options;

        if (options.disabled) {
            return;
        }
        parent.$placeholder.removeClass('element-invisible');
        parent.$files.hide();
        parent.$statusBar.addClass('element-invisible');

        if (parent.isImage()) {
            parent.$wrapper.removeAttr('style');
            parent.$wrapper.find('.queueList').removeAttr('style');
        }

        parent.uploader.refresh();
    }

    // 减少上传文件数量限制
    decrFileNumLimit(num) {
        let _this = this,
            parent = _this.uploader,
            uploader = parent.uploader,
            fileLimit;

        if (!uploader) {
            return;
        }
        fileLimit = uploader.option('fileNumLimit');
        num = num || 1;

        if (fileLimit == '-1') {
            fileLimit = 0;
        }

        num = fileLimit >= num ? fileLimit - num : 0;

        if (num == 0) {
            num = '-1';
        }

        uploader.option('fileNumLimit', num);
    }

    // 增加上传文件数量限制
    incrFileNumLimit(num) {
        let _this = this,
            parent = _this.uploader,
            uploader = parent.uploader,
            fileLimit;

        if (!uploader) {
            return;
        }
        fileLimit = uploader.option('fileNumLimit');
        num = num || 1;

        if (fileLimit == '-1') {
            fileLimit = 0;
        }

        num = fileLimit + num;

        uploader.option('fileNumLimit', num);
    }

    ready() {
        let _this = this,
            parent = _this.uploader,
            options = parent.options;

        parent.$placeholder.addClass('element-invisible');
        parent.$selector.find(parent.options.addFileButton).removeClass('element-invisible');
        parent.$files.show();
        if (!options.disabled) {
            parent.$statusBar.removeClass('element-invisible');
        }

        parent.uploader.refresh();

        if (parent.isImage()) {
            parent.$wrapper.find('.queueList').css({'border': '1px solid #d3dde5', 'padding': '5px'});
            // $wrap.find('.queueList').removeAttr('style');
        }

        // 移除字段验证错误信息
        setTimeout(function () {
            parent.input.removeValidatorErrors();
        }, 10);
    }

    finish() {
        let _this = this,
            parent = _this.uploader,
            options = parent.options,
            uploader = parent.uploader,
            stats;

        if (uploader) {
            stats = uploader.getStats();
            if (stats.successNum) {
                Dcat.success(parent.lang.trans('upload_success_message', {success: stats.successNum}));

                setTimeout(function () {
                    if (options.upload.fileNumLimit == 1) {
                        // 单文件上传，需要重置文件上传个数
                        uploader.request('get-stats').numOfSuccess = 0;
                    }
                }, 10);

            } else {
                // 没有成功的图片，重设
                _this.state = 'done';

                Dcat.reload();
            }
        }
    }

    // 初始化
    init() {
        let _this = this,
            parent = _this.uploader,
            options = parent.options;

        parent.$uploadButton.addClass('state-' + _this.state);
        _this.updateProgress();

        if (_this.originalFilesNum || options.disabled) {
            parent.$placeholder.addClass('element-invisible');
            if (!options.disabled) {
                parent.$statusBar.show();
            } else {
                parent.$wrapper.addClass('disabled');
            }
            _this.switch('ready');
        } else if (parent.isImage()) {
            parent.$wrapper.removeAttr('style');
            parent.$wrapper.find('.queueList').css('margin', '0');
        }

        parent.uploader.refresh();
    }

    // 状态文本
    updateStatusText() {
        let _this = this,
            parent = _this.uploader,
            uploader = parent.uploader,
            __ = parent.lang.trans.bind(parent.lang),
            text = '',
            stats;

        if (!uploader) {
            return;
        }

        if (_this.state === 'ready') {
            stats = uploader.getStats();
            if (parent.fileCount) {
                text = __('selected_files', {num: parent.fileCount, size: WebUploader.formatSize(parent.fileSize)});
            } else {
                showSuccess();
            }
        } else if (_this.state === 'confirm') {
            stats = uploader.getStats();
            if (stats.uploadFailNum) {
                text = __('selected_has_failed', {success: stats.successNum, fail: stats.uploadFailNum});
            }
        } else {
            showSuccess();
        }

        function showSuccess() {
            stats = uploader.getStats();
            if (stats.successNum) {
                text = __('selected_success', {num: parent.fileCount, size: WebUploader.formatSize(parent.fileSize), success: stats.successNum});
            }

            if (stats.uploadFailNum) {
                text += (text ? __('dot') : '') + __('failed_num', {fail: stats.uploadFailNum});
            }
        }

        parent.$infoBox.html(text);
    }

    // 进度条更新
    updateProgress() {
        let _this = this,
            parent = _this.uploader,
            loaded = 0,
            total = 0,
            $bar = parent.$progress.find('.progress-bar'),
            percent;

        $.each(parent.percentages, function (k, v) {
            total += v[0];
            loaded += v[0] * v[1];
        });

        percent = total ? loaded / total : 0;
        percent = Math.round(percent * 100) + '%';

        $bar.text(percent);
        $bar.css('width', percent);

        _this.updateStatusText();
    }
}