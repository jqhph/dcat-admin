
import Helper from './Upload/Helper'
import Request from './Upload/Request'
import Input from './Upload/Input'
import Status from './Upload/Status'
import AddFile from './Upload/AddFile'
import AddUploadedFile from './Upload/AddUploadedFile'

/**
 * WebUploader 上传组件
 *
 * @see http://fex.baidu.com/webuploader/
 */
(function (w, $) {
    let Dcat = w.Dcat;

    class Uploader {
        constructor(options) {
            this.options = options = $.extend({
                wrapper: '.web-uploader', // 图片显示容器选择器
                addFileButton: '.add-file-button', // 继续添加按钮选择器
                inputSelector: '',
                isImage: false,
                preview: [], // 数据预览
                server: '',
                updateServer: '',
                autoUpload: false,
                sortable: false,
                deleteUrl: '',
                deleteData: {},
                thumbHeight: 160,
                elementName: '',
                disabled: false, // 禁止任何上传编辑
                autoUpdateColumn: false,
                removable: false, // 是否允许直接删除服务器图片
                downloadable: false, // 是否允许下载文件
                dimensions: {
                    // width: 100, // 图片宽限制
                    // height: 100, // 图片高限制
                    // min_width: 100, //
                    // min_height: 100,
                    // max_width: 100,
                    // max_height: 100,
                    // ratio: 3/2, // 宽高比
                },
                lang: {
                    exceed_size: '文件大小超出',
                    interrupt: '上传暂停',
                    upload_failed: '上传失败，请重试',
                    selected_files: '选中:num个文件，共:size。',
                    selected_has_failed: '已成功上传:success个文件，:fail个文件上传失败，<a class="retry"  href="javascript:"";">重新上传</a>失败文件或<a class="ignore" href="javascript:"";">忽略</a>',
                    selected_success: '共:num个(:size)，已上传:success个。',
                    dot: '，',
                    failed_num: '失败:fail个。',
                    pause_upload: '暂停上传',
                    go_on_upload: '继续上传',
                    start_upload: '开始上传',
                    upload_success_message: '已成功上传:success个文件',
                    go_on_add: '继续添加',
                    Q_TYPE_DENIED: '对不起，不允许上传此类型文件',
                    Q_EXCEED_NUM_LIMIT: '对不起，已超出文件上传数量限制，最多只能上传:num个文件',
                    F_EXCEED_SIZE: '对不起，当前选择的文件过大',
                    Q_EXCEED_SIZE_LIMIT: '对不起，已超出文件大小限制',
                    F_DUPLICATE: '文件重复',
                    confirm_delete_file: '您确定要删除这个文件吗？',
                },
                upload: { // web-uploader配置
                    formData: {
                        _id: null, // 唯一id
                    },
                    thumb: {
                        width: 160,
                        height: 160,
                        quality: 70,
                        allowMagnify: true,
                        crop: true,
                        preserveHeaders: false,
                        // 为空的话则保留原有图片格式。
                        // 否则强制转换成指定的类型。
                        // IE 8下面 base64 大小不能超过 32K 否则预览失败，而非 jpeg 编码的图片很可
                        // 能会超过 32k, 所以这里设置成预览的时候都是 image/jpeg
                        type: 'image/jpeg'
                    },
                }
            }, options);

            let _this = this;

            // WebUploader
            // @see http://fex.baidu.com/webuploader/
            _this.uploader = WebUploader.create(options.upload);

            _this.$selector = $(options.selector);
            _this.updateColumn = options.upload.formData.upload_column || ('webup' + Dcat.helpers.random());
            _this.relation = options.upload.formData._relation; // 一对多关联关系名称

            // 帮助函数
            let helper = new Helper(this),
                // 请求处理
                request = new Request(this),
                // 状态管理
                status = new Status(this),
                // 添加文件
                addFile = new AddFile(this),
                // 添加已上传文件
                addUploadedFile = new AddUploadedFile(this),
                // 表单
                input = new Input(this);

            _this.helper = helper;
            _this.request = request;
            _this.status = status;
            _this.addFile = addFile;
            _this.addUploadedFile = addUploadedFile;
            _this.input = input;

            // 翻译
            _this.lang = Dcat.Translator(options.lang);

            // 所有文件的进度信息，key为file id
            _this.percentages = {};
            // 临时存储上传失败的文件，key为file id
            _this.faildFiles = {};
            // 临时存储添加到form表单的文件
            _this.formFiles = {};
            // 添加的文件数量
            _this.fileCount = 0;
            // 添加的文件总大小
            _this.fileSize = 0;

            if (typeof options.upload.formData._id === "undefined" || ! options.upload.formData._id) {
                options.upload.formData._id = _this.updateColumn + Dcat.helpers.random();
            }
        }

        // 初始化
        build() {
            let _this = this,
                uploader = _this.uploader,
                options = _this.options,
                $wrap = _this.$selector.find(options.wrapper),
                // 图片容器
                $queue = $('<ul class="filelist"></ul>').appendTo($wrap.find('.queueList')),
                // 状态栏，包括进度和控制按钮
                $statusBar = $wrap.find('.statusBar'),
                // 文件总体选择信息。
                $info = $statusBar.find('.info'),
                // 上传按钮
                $upload = $wrap.find('.upload-btn'),
                // 没选择文件之前的内容。
                $placeholder = $wrap.find('.placeholder'),
                $progress = $statusBar.find('.upload-progress').hide();

            // jq选择器
            _this.$wrapper = $wrap;
            _this.$files = $queue;
            _this.$statusBar = $statusBar;
            _this.$uploadButton = $upload;
            _this.$placeholder = $placeholder;
            _this.$progress = $progress;
            _this.$infoBox = $info;

            if (options.upload.fileNumLimit > 1 && ! options.disabled) {
                // 添加“添加文件”的按钮，
                uploader.addButton({
                    id: options.addFileButton,
                    label: '<i class="feather icon-folder"></i> &nbsp;' + _this.lang.trans('go_on_add')
                });
            }

            // 拖拽时不接受 js, txt 文件。
            _this.uploader.on('dndAccept', function (items) {
                var denied = false,
                    len = items.length,
                    i = 0,
                    // 修改js类型
                    unAllowed = 'text/plain;application/javascript ';

                for (; i < len; i++) {
                    // 如果在列表里面
                    if (~unAllowed.indexOf(items[i].type)) {
                        denied = true;
                        break;
                    }
                }

                return !denied;
            });

            // 进度条更新
            uploader.onUploadProgress = function (file, percentage) {
                _this.percentages[file.id][1] = percentage;
                _this.status.updateProgress();
            };

            // uploader.onBeforeFileQueued = function (file) {};

            // 添加文件
            uploader.onFileQueued = function (file) {
                _this.fileCount++;
                _this.fileSize += file.size;

                if (_this.fileCount === 1) {
                    // 隐藏 placeholder
                    $placeholder.addClass('element-invisible');
                    $statusBar.show();
                }

                // 添加文件
                _this.addFile.render(file);
                _this.status.switch('ready');

                // 更新进度条
                _this.status.updateProgress();

                if (!options.disabled && options.autoUpload) {
                    // 自动上传
                    uploader.upload()
                }
            };

            // 删除文件事件监听
            uploader.onFileDequeued = function (file) {
                _this.fileCount--;
                _this.fileSize -= file.size;

                if (! _this.fileCount && !Dcat.helpers.len(_this.formFiles)) {
                    _this.status.switch('pending');
                }

                _this.removeUploadFile(file);
            };

            uploader.on('all', function (type, obj, reason) {
                switch (type) {
                    case 'uploadFinished':
                        _this.status.switch('confirm');
                        // 保存已上传的文件名到服务器
                        _this.request.update();
                        break;

                    case 'startUpload':
                        _this.status.switch('uploading');
                        break;

                    case 'stopUpload':
                        _this.status.switch('paused');
                        break;
                    case  'uploadAccept':
                        if (_this._uploadAccept(obj, reason) === false) {
                            return false;
                        }

                        break;
                }
            });

            uploader.onError = function (code) {
                switch (code) {
                    case 'Q_TYPE_DENIED':
                        Dcat.error(_this.lang.trans('Q_TYPE_DENIED'));
                        break;
                    case 'Q_EXCEED_NUM_LIMIT':
                        Dcat.error(_this.lang.trans('Q_EXCEED_NUM_LIMIT', {num: options.upload.fileNumLimit}));
                        break;
                    case 'F_EXCEED_SIZE':
                        Dcat.error(_this.lang.trans('F_EXCEED_SIZE'));
                        break;
                    case 'Q_EXCEED_SIZE_LIMIT':
                        Dcat.error(_this.lang.trans('Q_EXCEED_SIZE_LIMIT'));
                        break;
                    case 'F_DUPLICATE':
                        Dcat.warning(_this.lang.trans('F_DUPLICATE'));
                        break;
                    default:
                        Dcat.error('Error: ' + code);
                }

            };

            // 上传按钮点击
            $upload.on('click', function () {
                let state = _this.status.state;

                if ($(this).hasClass('disabled')) {
                    return false;
                }

                if (state === 'ready') {
                    uploader.upload();
                } else if (state === 'paused') {
                    uploader.upload();
                } else if (state === 'uploading') {
                    uploader.stop();
                }
            });

            // 重试按钮
            $info.on('click', '.retry', function () {
                uploader.retry();
            });

            // 忽略按钮
            $info.on('click', '.ignore', function () {
                for (let i in _this.faildFiles) {
                    uploader.removeFile(i, true);

                    delete _this.faildFiles[i];
                }

            });

            // 初始化
            _this.status.switch('init');
        }

        _uploadAccept(obj, reason) {
            let _this = this,
                options = _this.options;

            // 上传失败，返回false
            if (! reason || ! reason.status) {
                _this.helper.showError(reason);

                _this.faildFiles[obj.file.id] = obj.file;

                return false;
            }

            if (reason.data && reason.data.merge) {
                // 分片上传
                return;
            }

            // 上传成功，保存新文件名和路径到file对象
            obj.file.serverId = reason.data.id;
            obj.file.serverName = reason.data.name;
            obj.file.serverPath = reason.data.path;
            obj.file.serverUrl = reason.data.url || null;

            _this.addUploadedFile.add(obj.file);

            _this.input.add(reason.data.id);

            let $li = _this.getFileView(obj.file.id);

            if (! _this.isImage()) {
                $li.find('.file-action').hide();
                $li.find('[data-file-act="delete"]').show();
            }

            if (options.sortable) {
                $li.find('[data-file-act="order"]').removeClass('d-none').show();
            }
            if (options.downloadable) {
                let $download = $li.find('[data-file-act="download"]');
                $download.removeClass('d-none').show();
                $download.attr('data-id', obj.file.serverUrl);
            }
        }

        // 预览
        preview() {
            let _this = this,
                options = _this.options,
                i;

            for (i in options.preview) {
                let path = options.preview[i].path, ext;

                if (path.indexOf('.')) {
                    ext = path.split('.').pop();
                }

                let file = {
                    serverId: options.preview[i].id,
                    serverUrl: options.preview[i].url,
                    serverPath: path,
                    ext: ext,
                    fake: 1,
                };

                _this.status.switch('incrOriginalFileNum');
                _this.status.switch('decrFileNumLimit');

                // 添加文件到预览区域
                _this.addUploadedFile.render(file);
                _this.addUploadedFile.add(file);
            }
        }

        // 重新渲染已上传文件
        reRenderUploadedFiles() {
            let _this = this;

            _this.$files.html('');

            _this.addUploadedFile.reRender();
        }

        // 重置按钮位置
        refreshButton() {
            this.uploader.refresh();
        }

        // 获取文件视图选择器
        getFileViewSelector(fileId) {
            return this.options.elementName.replace(/[\[\]]*/g, '_') + '-' + fileId;
        }

        getFileView(fileId) {
            return $('#' + this.getFileViewSelector(fileId));
        }

        // 负责view的销毁
        removeUploadFile(file) {
            let _this = this,
                $li = _this.getFileView(file.id);

            delete _this.percentages[file.id];
            _this.status.updateProgress();

            $li.off().find('.file-panel').off().end().remove();
        }

        // 上传字段名称
        getColumn() {
            return this.updateColumn
        }

        // 判断是否是图片上传
        isImage() {
            return this.options.isImage
        }
    }

    Dcat.Uploader = function (options) {
        return new Uploader(options)
    };

})(window, jQuery);
