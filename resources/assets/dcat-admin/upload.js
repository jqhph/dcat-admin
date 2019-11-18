(function ($) {
    function Uploader(opts) {
        opts = $.extend({
            wrapper: '.web-uploader', // 图片显示容器选择器
            addFileButton: '.add-file-button', // 继续添加按钮选择器
            isImage: false,
            preview: [], // 数据预览
            deleteUrl: '',
            deleteData: {},
            thumbHeight: 160,
            disabled: false, // 禁止任何上传编辑
            autoUpdateColumn: false,
            disableRemove: false, // 禁止删除图片，允许替换
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
        }, opts);

        var $selector = $(opts.selector),
            updateColumn = opts.upload.formData.upload_column || ('webup' + Math.floor(Math.random()*10000)),
            elementName = opts.elementName;

        if (typeof opts.upload.formData._id == "undefined" || !opts.upload.formData._id) {
            opts.upload.formData._id = updateColumn + Math.floor(Math.random()*10000);
        }

        var $wrap,

            // 展示图片
            showImg = opts.isImage,

            // 图片容器
            $queue,

            // 状态栏，包括进度和控制按钮
            $statusBar,

            // 文件总体选择信息。
            $info,

            // 上传按钮
            $upload,

            // 没选择文件之前的内容。
            $placeHolder,

            $progress,

            // 已上传文件数量
            originalFilesNum = LA.len(opts.preview),

            // 上传表单
            $input = $selector.find('input[name="' + elementName + '"]'),

            // 获取文件视图选择器
            getFileViewSelector = function (fileId) {
                return elementName.replace(/[\[\]]*/g, '_')+'-'+fileId;
            },

            getFileView = function (fileId) {
                return $('#' + getFileViewSelector(fileId));
            },

            // 继续添加按钮选择器
            addFileButtonSelector = opts.addFileButton,

            // 临时存储上传失败的文件，key为file id
            faildFiles = {},

            // 临时存储添加到form表单的文件
            formFiles = {},

            // 添加的文件数量
            fileCount = 0,

            // 添加的文件总大小
            fileSize = 0,

            // 可能有pedding, ready, uploading, confirm, done.
            state = 'pedding',

            // 所有文件的进度信息，key为file id
            percentages = {},
            // 判断浏览器是否支持图片的base64
            isSupportBase64 = (function () {
                var data = new Image();
                var support = true;
                data.onload = data.onerror = function () {
                    if (this.width != 1 || this.height != 1) {
                        support = false;
                    }
                };
                data.src = "data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///ywAAAAAAQABAAACAUwAOw==";
                return support;
            })(),

            // 检测是否已经安装flash，检测flash的版本
            flashVersion = (function () {
                var version;

                try {
                    version = navigator.plugins['Shockwave Flash'];
                    version = version.description;
                } catch (ex) {
                    try {
                        version = new ActiveXObject('ShockwaveFlash.ShockwaveFlash')
                            .GetVariable('$version');
                    } catch (ex2) {
                        version = '0.0';
                    }
                }
                version = version.match(/\d+/g);
                return parseFloat(version[0] + '.' + version[1], 10);
            })(),

            // 判断是否是图片
            isImage = function (file) {
                return file.type.match(/^image/);
            },

            // 翻译
            __ = LA.Translator(opts.lang),

            // WebUploader实例
            uploader;

        // 当有文件添加进来时执行，负责view的创建
        function addFile(file) {
            var size = WebUploader.formatSize(file.size), $li, $btns;

            if (showImg) {
                $li = $('<li id="' + getFileViewSelector(file.id) + '" title="' + file.name + '" style="margin:7px">' +
                    '<p class="file-type">' + (file.ext.toUpperCase() || 'FILE') + '</p>' +
                    '<p class="imgWrap "></p>' +
                    '<p class="title" style="">' + file.name + '</p>' +
                    '<p class="title" style=\'margin-bottom:12px;\'>(<b>' + size + '</b>)</p>' +
                    '</li>');

                $btns = $('<div class="file-panel">' +
                    '<a class=\'btn btn-xs btn-default\' data-file-act="cancel"><i class="fa fa-close red-dark" style=\'font-size:13px\'></i></a>' +
                    '<a class=\'btn btn-xs btn-default\' data-file-act="delete" style="display: none"><i class="ti-trash red-dark" style=\'font-size:13px\'></i></a>' +
                    '<a class=\'btn btn-xs btn-default\' data-file-act="preview" ><i class="glyphicon glyphicon-zoom-in"></i></a>' +
                    '</div>').appendTo($li);
            } else {
                $li = $('<li id="' + getFileViewSelector(file.id) + '" title="' + file.name + '">' +
                    '<p class="title" style="display:block"><i class=\'ti-check green _success\' style=\'font-weight:bold;font-size:17px;display:none\'></i>' +
                    file.name + ' (' + size + ')</p>' +
                    '</li>');

                $btns = $('<span data-file-act="cancel" class="_act" style="font-size:13px"><i class=\'ti-close red-dark\'></i></span>' +
                    '<span data-file-act="delete" class="_act" style="display:none"><i class=\'ti-trash red-dark\'></i></span>'
                ).appendTo($li);
            }

            var $wrap = $li.find('p.imgWrap'),
                $info = $('<p class="error"></p>'),

                showError = function (code, file) {
                    var text = '';
                    switch (code) {
                        case 'exceed_size':
                            text = __('exceed_size');
                            break;

                        case 'interrupt':
                            text = __('interrupt');
                            break;

                        default:
                            text = __('upload_failed');
                            break;
                    }

                    faildFiles[file.id] = file;

                    $info.text(text).appendTo($li);
                };

            $li.appendTo($queue);

            if (file.getStatus() === 'invalid') {
                showError(file.statusText, file);
            } else {
                if (showImg) {
                    var image = uploader.makeThumb(file, function (error, src) {
                        var img;

                        $wrap.empty();
                        if (error) {
                            $li.find('.title').show();
                            $li.find('.file-type').show();
                            return;
                        }

                        if (isSupportBase64) {
                            img = $('<img src="' + src + '">');
                            $wrap.append(img);
                        } else {
                            $li.find('.file-type').show();
                        }
                    });

                    try {
                        image.once('load', function () {
                            file._info = file._info || image.info();
                            file._meta = file._meta || image.meta();
                            var width = file._info.width,
                                height = file._info.height;

                            if (!validateDimensions(file)) {
                                LA.error('The image dimensions is invalid.');
                                uploader.removeFile(file);
                                return false;
                            }

                            image.resize(width, height);
                        });
                    } catch (e) {
                        // 不是图片
                        return setTimeout(function () {
                            uploader.removeFile(file);
                        }, 10);
                    }
                }

                percentages[file.id] = [file.size, 0];
                file.rotation = 0;
            }

            file.on('statuschange', function (cur, prev) {
                if (prev === 'progress') {
                    // $prgress.hide().width(0);
                } else if (prev === 'queued') {
                    $btns.find('[data-file-act="cancel"]').hide();
                    $btns.find('[data-file-act="delete"]').show();
                }

                // 成功
                if (cur === 'error' || cur === 'invalid') {
                    showError(file.statusText, file);
                    percentages[file.id][1] = 1;
                } else if (cur === 'interrupt') {
                    showError('interrupt', file);
                } else if (cur === 'queued') {
                    percentages[file.id][1] = 0;
                } else if (cur === 'progress') {
                    $info.remove();
                    // $prgress.css('display', 'block');
                } else if (cur === 'complete') {
                    if (showImg) {
                        $li.append('<span class="success"><em></em><i class="ti-check"></i></span>');
                    } else {
                        $li.find('._success').show();
                    }
                }

                $li.removeClass('state-' + prev).addClass('state-' + cur);
            });

            var $act = showImg ? $btns.find('a') : $btns;

            $act.on('click', function () {
                var index = $(this).data('file-act');

                switch (index) {
                    case 'cancel':
                        uploader.removeFile(file);
                        return;
                    case 'deleteurl':
                    case 'delete':
                        if (opts.disableRemove) {
                            return uploader.removeFile(file);
                        }

                        var post = opts.deleteData;

                        post.key = file.serverId;
                        if (!post.key) {
                            return uploader.removeFile(file);
                        }
                        post._column = updateColumn;

                        LA.loading();
                        $.post(opts.deleteUrl, post, function (result) {
                            LA.loading(false);
                            if (result.status) {
                                deleteInput(file.serverId);
                                uploader.removeFile(file);
                                return;
                            }

                            LA.error(result.message || 'Remove file failed.');
                        });

                        break;
                    case 'preview':
                        LA.previewImage($wrap.find('img').attr('src'), null, file.name);
                        break;
                }

            });
        }

        // 图片宽高验证
        function validateDimensions(file) {
            // The image dimensions is invalid.
            if (!showImg || !isImage(file) || !LA.len(opts.dimensions)) return true;
            var dimensions = opts.dimensions,
                width = file._info.width,
                height = file._info.height,
                isset = LA.isset;
            if (
                (isset(dimensions, 'width') && dimensions['width'] != width) ||
                (isset(dimensions, 'min_width') && dimensions['min_width'] > width)||
                (isset(dimensions, 'max_width') && dimensions['max_width'] < width) ||
                (isset(dimensions, 'height') && dimensions['height'] != height) ||
                (isset(dimensions, 'min_height') && dimensions['min_height'] > height) ||
                (isset(dimensions, 'max_height') && dimensions['max_height'] < height) ||
                (isset(dimensions, 'ratio') && dimensions['ratio'] != (width / height))
            ) {
                return false;
            }

            return true;
        }

        // 负责view的销毁
        function removeUploadFile(file) {
            var $li = getFileView(file.id);

            delete percentages[file.id];
            updateTotalProgress();
            $li.off().find('.file-panel').off().end().remove();
        }

        function updateTotalProgress() {
            var loaded = 0,
                total = 0,
                $bar = $progress.find('.progress-bar'),
                percent;

            $.each(percentages, function (k, v) {
                total += v[0];
                loaded += v[0] * v[1];
            });

            percent = total ? loaded / total : 0;
            percent = Math.round(percent * 100) + '%';

            $bar.text(percent);
            $bar.css('width', percent);
            updateStatusText();
        }

        function updateStatusText() {
            var text = '', stats;

            if (!uploader) {
                return;
            }

            if (state === 'ready') {
                stats = uploader.getStats();
                if (fileCount) {
                    text = __('selected_files', {num: fileCount, size: WebUploader.formatSize(fileSize)});
                } else {
                    showSuccess();
                }
            } else if (state === 'confirm') {
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
                    text = __('selected_success', {num: fileCount, size: WebUploader.formatSize(fileSize), success: stats.successNum});
                }

                if (stats.uploadFailNum) {
                    text += (text ? __('dot') : '') + __('failed_num', {fail: stats.uploadFailNum});
                }
            }

            $info.html(text);
        }

        // 上传文件后修改字段值
        function updateFileColumn() {
            var values = getInput(),
                num = uploader.getStats().successNum,
                form = $.extend({}, opts.formData);

            if (!num || !values || !opts.autoUpdateColumn) {
                return;
            }

            form[updateColumn] = values.join(',');
            delete form['upload_column'];

            $.post(opts.server, form);
        }

        function setState(val, args) {
            var stats;
            args = args || {};

            if (val === state) {
                return;
            }

            if ($upload) {
                $upload.removeClass('state-' + state);
                $upload.addClass('state-' + val);
            }
            state = val;

            switch (state) {
                case 'pedding':
                    if (opts.disabled) return;
                    $placeHolder.removeClass('element-invisible');
                    $queue.hide();
                    $statusBar.addClass('element-invisible');
                    if (showImg) {
                        $wrap.removeAttr('style');
                        $wrap.find('.queueList').removeAttr('style');
                    }

                    refreshButton();
                    break;

                case 'ready':
                    $placeHolder.addClass('element-invisible');
                    $selector.find(addFileButtonSelector).removeClass('element-invisible');
                    $queue.show();
                    if (!opts.disabled) {
                        $statusBar.removeClass('element-invisible');
                    }
                    refreshButton();
                    if (showImg) {
                        $wrap.find('.queueList').css({'border': '1px solid #d3dde5', 'padding':'5px'});
                        // $wrap.find('.queueList').removeAttr('style');
                    }
                    break;

                case 'uploading':
                    $selector.find(addFileButtonSelector).addClass('element-invisible');
                    $progress.show();
                    $upload.text(__('pause_upload'));
                    break;

                case 'paused':
                    $progress.show();
                    $upload.text(__('go_on_upload'));
                    break;

                case 'confirm':
                    if (uploader) {
                        $progress.hide();
                        $selector.find(addFileButtonSelector).removeClass('element-invisible');
                        $upload.text(__('start_upload'));

                        stats = uploader.getStats();
                        if (stats.successNum && !stats.uploadFailNum) {
                            setState('finish');
                            return;
                        }
                    }
                    break;
                case 'finish':
                    if (uploader) {
                        stats = uploader.getStats();
                        if (stats.successNum) {
                            LA.success(__('upload_success_message', {success: stats.successNum}));
                        } else {
                            // 没有成功的图片，重设
                            state = 'done';
                            location.reload();
                        }
                    }
                    break;
                case 'decrOriginalFileNum':
                    if (originalFilesNum > 0) originalFilesNum --;
                    break;

                case 'incrOriginalFileNum':
                    originalFilesNum ++;
                    break;

                case 'decrFileNumLimit': // 减少上传文件数量限制
                    if (!uploader) {
                        return;
                    }
                    var ofl = uploader.option('fileNumLimit'),
                        num = args.num || 1;

                    if (ofl == '-1') ofl = 0;

                    num = ofl >= num ? ofl - num : 0;

                    if (num == 0) num = '-1';

                    uploader.option('fileNumLimit', num);

                    break;
                case 'incrFileNumLimit': // 增加上传文件数量限制
                    if (!uploader) {
                        return;
                    }
                    var ofl = uploader.option('fileNumLimit'),
                        num = args.num || 1;

                    if (ofl == '-1') ofl = 0;

                    num = ofl + num;

                    uploader.option('fileNumLimit', num);
                    break;
                case 'init': // 初始化
                    $upload.addClass('state-' + state);
                    updateTotalProgress();

                    if (originalFilesNum || opts.disabled) {
                        $placeHolder.addClass('element-invisible');
                        if (!opts.disabled) {
                            $statusBar.show();
                        } else {
                            $wrap.addClass('disabled');
                        }
                        setState('ready');
                    } else if (showImg) {
                        $wrap.removeAttr('style');
                        $wrap.find('.queueList').css('margin', '0');
                    }
                    refreshButton();
                    break;

            }

            updateStatusText();
        }

        // 移除form表单的文件
        function removeFormFile(fileId) {
            if (!fileId) return;

            var file = formFiles[fileId];

            deleteInput(fileId);
            delete formFiles[fileId];
            if (uploader && !file.fake) {
                uploader.removeFile(file);
            }

            setState('decrOriginalFileNum');
            setState('incrFileNumLimit');

            if (!LA.len(formFiles) && !LA.len(percentages)) {
                setState('pedding');
            }
        }

        // 获取表单值
        function getInput() {
            var val = $input.val();

            return val ? val.split(',') : [];
        }

        // 新增表单值
        function addInput(id) {
            var val = getInput();
            val.push(id);
            setInput(val);
        }

        // 设置表单值
        function setInput(arr) {
            arr = arr.filter(function(v, k, self) {
                return self.indexOf(v) === k;
            }).filter(function (v) {
                return v ? true : false;
            });

            $input.val(arr.join(','));
        }

        // 删除表单值
        function deleteInput(id) {
            if (!id) {
                return $input.val('');
            }
            setInput(getInput().filter(function (v) {
                return v != id;
            }));
        }

        // 重新计算按钮定位
        function refreshButton() {
            uploader.refresh();
        }

        // 添加上传成功文件到表单区域
        function appendUploadedFileForm(file) {
            var html = "";
            html += "<li title='" + file.serverPath + "'>";

            if (showImg) {
                html += "<p class='imgWrap'>";
                html += "	<img src='" + file.serverUrl + "'>";
                html += "</p>";
            } else if (!opts.disabled) {
                html += '<p class="_act" data-file-act=\'delete\' data-id="' + file.serverId + '"><i class=\'ti-trash red-dark\'></i></p>';
            }

            html += "<p class='title' style=''><i class='ti-check green _success' style='font-weight:bold;font-size:17px;display:none'></i>";
            html += file.serverPath;
            html += "</p>";

            if (showImg) {
                html += "<p class='title' style='margin-bottom:12px;'>&nbsp;</p>";
                html += "<div class='file-panel' >";

                if (!opts.disabled) {
                    html += "<a class='btn btn-xs btn-default' data-file-act='deleteurl' data-id='" + file.serverId + "'><i class='ti-trash red-dark' style='font-size:13px'></i></a>";
                }
                html += "<a class='btn btn-xs btn-default' data-file-act='preview' data-url='" + file.serverUrl + "' ><i class='glyphicon glyphicon-zoom-in'></i></a>";

                html += "</div>";
            }

            html += "</li>";
            html = $(html);

            if (!showImg) {
                html.find('.file-type').show();
                html.find('.title').show();
                $wrap.css('background', 'transparent');
            }

            var deleteFile = function () {
                var fileId = $(this).data('id'), post = opts.deleteData;

                if (opts.disableRemove) {
                    html.remove();

                    return removeFormFile(fileId);
                }

                post.key = fileId;
                post._column = updateColumn;

                LA.loading();
                $.post(opts.deleteUrl, post, function (result) {
                    LA.loading(false);
                    if (result.status) {
                        // 移除
                        html.remove();

                        removeFormFile(fileId);
                        return;
                    }

                    LA.error(result.message || 'Remove file failed.')
                });
            };

            // 删除按钮点击事件
            html.find('[data-file-act="deleteurl"]').click(deleteFile);
            html.find('[data-file-act="delete"]').click(deleteFile);


            // 放大图片
            html.find('[data-file-act="preview"]').click(function () {
                var url = $(this).data('url');

                LA.previewImage(url);
            });

            setState('incrOriginalFileNum');
            setState('decrFileNumLimit');
            formFiles[file.serverId] = file;

            addInput(file.serverId);

            $queue.append(html);

            if (showImg) {
                setTimeout(function () { html.css('margin', '7px');}, 80);
            }
        }

        // 初始化web-uploader
        function build() {
            $wrap = $selector.find(opts.wrapper);

            // 图片容器
            $queue = $('<ul class="filelist"></ul>').appendTo($wrap.find('.queueList'));

            // 状态栏，包括进度和控制按钮
            $statusBar = $wrap.find('.statusBar');

            // 文件总体选择信息。
            $info = $statusBar.find('.info');

            // 上传按钮
            $upload = $wrap.find('.uploadBtn');

            // 没选择文件之前的内容。
            $placeHolder = $wrap.find('.placeholder');

            $progress = $statusBar.find('.upload-progress').hide();

            // IE;
            supportIe();

            // 实例化
            uploader = WebUploader.create(opts.upload);

            // 拖拽时不接受 js, txt 文件。
            uploader.on('dndAccept', function (items) {
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

            if (opts.upload.fileNumLimit > 1 && !opts.disabled) {
                // 添加“添加文件”的按钮，
                uploader.addButton({
                    id: addFileButtonSelector,
                    label: '<i class="glyphicon glyphicon-folder-open"></i> &nbsp;' + __('go_on_add')
                });
            }

            uploader.onUploadProgress = function (file, percentage) {
                percentages[file.id][1] = percentage;
                updateTotalProgress();
            };

            uploader.onBeforeFileQueued = function (file) {

            };

            uploader.onFileQueued = function (file) {
                fileCount++;
                fileSize += file.size;

                if (fileCount === 1) {
                    $placeHolder.addClass('element-invisible');
                    $statusBar.show();
                }

                addFile(file);
                setState('ready');
                updateTotalProgress();
            };

            // 删除文件事件监听
            uploader.onFileDequeued = function (file) {
                fileCount--;
                fileSize -= file.size;

                if (!fileCount && !LA.len(formFiles)) {
                    setState('pedding');
                }

                removeUploadFile(file);
            };

            uploader.on('all', function (type, obj, reason) {
                switch (type) {
                    case 'uploadFinished':
                        setState('confirm');
                        updateFileColumn();
                        break;

                    case 'startUpload':
                        setState('uploading');
                        break;

                    case 'stopUpload':
                        setState('paused');
                        break;
                    case  'uploadAccept':
                        // 上传失败，返回false
                        if (reason && reason.error) {
                            LA.error(reason.error.message);

                            faildFiles[obj.file.id] = obj.file;

                            return false;
                        }

                        if (reason.merge) {
                            // 分片上传
                            return;
                        }

                        // 上传成功，保存新文件名和路径到file对象
                        obj.file.serverId   = reason.id;
                        obj.file.serverName = reason.name;
                        obj.file.serverPath = reason.path;
                        obj.file.serverUrl  = reason.url || null;

                        addInput(reason.id);

                        if (!showImg) {
                            var $li = getFileView(obj.file.id);
                            $li.find('._act').hide();
                            $li.find('[data-file-act="delete"]').show();
                        }

                        break;
                }

            });

            uploader.onError = function (code) {
                switch (code) {
                    case 'Q_TYPE_DENIED':
                        LA.error(__('Q_TYPE_DENIED'));
                        break;
                    case 'Q_EXCEED_NUM_LIMIT':
                        LA.error(__('Q_EXCEED_NUM_LIMIT', {num: opts.upload.fileNumLimit}));
                        break;
                    case 'F_EXCEED_SIZE':
                        LA.error(__('F_EXCEED_SIZE'));
                        break;
                    case 'Q_EXCEED_SIZE_LIMIT':
                        LA.error(__('Q_EXCEED_SIZE_LIMIT'));
                        break;
                    case 'F_DUPLICATE':
                        LA.warning(__('F_DUPLICATE'));
                        break;
                    default:
                        LA.error('Error: ' + code);
                }

            };

            $upload.on('click', function () {
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

            $info.on('click', '.retry', function () {
                uploader.retry();
            });

            $info.on('click', '.ignore', function () {
                for (var i in faildFiles) {
                    uploader.removeFile(i, true);
                    delete faildFiles[i];
                }

            });

            setState('init');
        }

        // 预览
        function preview() {
            for (var i in opts.preview) {
                var path = opts.preview[i].path, ext;

                if (path.indexOf('.')) {
                    ext = path.split('.').pop();
                }

                appendUploadedFileForm({
                    serverId: opts.preview[i].id,
                    serverUrl: opts.preview[i].url,
                    serverPath: path,
                    ext: ext,
                    fake: 1,
                })
            }
        }

        this.uploader = uploader;
        this.options = opts;
        this.build = build;
        this.preview = preview;
        this.setState = setState;
        this.refreshButton = refreshButton;
        this.getFileView = getFileView;
        this.getFileViewSelector = getFileViewSelector;
        this.addFileView = addFile;
        this.removeUploadFileView = removeUploadFile;
        this.isImage = isImage;
        this.getColumn = function () {
            return updateColumn;
        };

        function supportIe() {
            if (!WebUploader.Uploader.support('flash') && WebUploader.browser.ie) {

                // flash 安装了但是版本过低。
                if (flashVersion) {
                    (function (container) {
                        window['expressinstallcallback'] = function (state) {
                            switch (state) {
                                case 'Download.Cancelled':
                                    break;

                                case 'Download.Failed':
                                    LA.error('Install failed!');
                                    break;

                                default:
                                    LA.success('Install Success！');
                                    break;
                            }
                            delete window['expressinstallcallback'];
                        };

                        var swf = './expressInstall.swf';
                        // insert flash object
                        var html = '<object type="application/' +
                            'x-shockwave-flash" data="' + swf + '" ';

                        if (WebUploader.browser.ie) {
                            html += 'classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" ';
                        }

                        html += 'width="100%" height="100%" style="outline:0">' +
                            '<param name="movie" value="' + swf + '" />' +
                            '<param name="wmode" value="transparent" />' +
                            '<param name="allowscriptaccess" value="always" />' +
                            '</object>';

                        container.html(html);

                    })($wrap);

                    // 压根就没有安转。
                } else {
                    $wrap.html('<a href="http://www.adobe.com/go/getflashplayer" target="_blank" border="0"><img alt="get flash player" src="http://www.adobe.com/macromedia/style_guide/images/160x41_Get_Flash_Player.jpg" /></a>');
                }

                return;
            } else if (!WebUploader.Uploader.support()) {
                LA.error('Web Uploader 不支持您的浏览器！');
                return;
            }
        }

        return this;
    }

    LA.Uploader = Uploader;

})(jQuery);