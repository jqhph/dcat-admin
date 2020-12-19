(function (w, $) {
    function Uploader(opts) {
        opts = $.extend({
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
        }, opts);

        var $selector = $(opts.selector),
            updateColumn = opts.upload.formData.upload_column || ('webup' + Math.floor(Math.random() * 10000)),
            relation = opts.upload.formData._relation, // 一对多关联关系名称
            elementName = opts.elementName;

        if (typeof opts.upload.formData._id == "undefined" || !opts.upload.formData._id) {
            opts.upload.formData._id = updateColumn + Math.floor(Math.random() * 10000);
        }

        let Dcat = w.Dcat,

            $wrap,

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
            originalFilesNum = Dcat.helpers.len(opts.preview),

            // 上传表单
            $input = $selector.find(opts.inputSelector),

            // 获取文件视图选择器
            getFileViewSelector = function (fileId) {
                return elementName.replace(/[\[\]]*/g, '_') + '-' + fileId;
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
            lang = Dcat.Translator(opts.lang),
            __ = lang.trans.bind(lang),

            // WebUploader实例
            uploader,

            // 已上传的文件
            uploadedFiles = [],

            initImg;

        // 当有文件添加进来时执行，负责view的创建
        function addFile(file) {
            var size = WebUploader.formatSize(file.size), $li, $btns, fileName = file.name || null;

            if (showImg) {
                $li = $(`<li id="${getFileViewSelector(file.id)}" title="${fileName}" >
                    <p class="file-type">${(file.ext.toUpperCase() || 'FILE')}</p>
                    <p class="imgWrap "></p>
                    <p class="title" style="">${file.name}</p>
                    <p class="title" style="margin-bottom:20px;">(<b>${size}</b>)</p>
                    </li>`);

                $btns = $(`<div class="file-panel">
                    <a class="btn btn-sm btn-white" data-file-act="cancel"><i class="feather icon-x red-dark" style="font-size:13px"></i></a>
                    <a class="btn btn-sm btn-white" data-file-act="delete" style="display: none">
                    <i class="feather icon-trash red-dark" style="font-size:13px"></i></a>
                    <a class="btn btn-sm btn-white" data-file-act="preview" ><i class="feather icon-zoom-in"></i></a>
                    <a class='btn btn-sm btn-white' data-file-act='order' data-order="1" style="display: none"><i class='feather icon-arrow-up'></i></a>
                    <a class='btn btn-sm btn-white' data-file-act='order' data-order="0" style="display: none"><i class='feather icon-arrow-down'></i></a>

                    </div>`).appendTo($li);
            } else {
                $li = $(`
                    <li id="${getFileViewSelector(file.id)}" title="${file.nam}">
                    <p class="title" style="display:block">
                        <i class="feather icon-check green _success icon-success"></i>
                        ${file.name} (${size})
                    </p>
                    </li>
                `);

                $btns = $(`
<span style="right: 45px;" class="file-action d-none" data-file-act='order' data-order="1"><i class='feather icon-arrow-up'></i></span>
<span style="right: 25px;" class="file-action d-none" data-file-act='order' data-order="0"><i class='feather icon-arrow-down'></i></span>
<span data-file-act="cancel" class="file-action" style="font-size:13px">
    <i class="feather icon-x red-dark"></i>
</span>
<span data-file-act="delete" class="file-action" style="display:none">
    <i class="feather icon-trash red-dark"></i>
</span>
`).appendTo($li);
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

            setTimeout(function () {
                $li.css({margin: '5px'});
            }, 50);

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
                                Dcat.error('The image dimensions is invalid.');
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
                        $li.append('<span class="success"><em></em><i class="feather icon-check"></i></span>');
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
                            deleteInput(file.serverId);
                            return uploader.removeFile(file);
                        }

                        Dcat.confirm(__('confirm_delete_file'), file.serverId, function () {
                            var post = opts.deleteData;

                            post.key = file.serverId;
                            if (!post.key) {
                                deleteInput(file.serverId);
                                return uploader.removeFile(file);
                            }
                            post._column = updateColumn;
                            post._relation = relation;

                            Dcat.loading();
                            $.post({
                                url: opts.deleteUrl,
                                data: post,
                                success: function (result) {
                                    Dcat.loading(false);
                                    if (result.status) {
                                        deleteInput(file.serverId);
                                        uploader.removeFile(file);
                                        return;
                                    }

                                    showErrorResponse(result)
                                }
                            });

                        });

                        break;
                    case 'preview':
                        Dcat.helpers.previewImage($wrap.find('img').attr('src'), null, file.name);
                        break;
                    case 'order':
                        $(this).attr('data-id', file.serverId);

                        orderFiles.apply(this);
                        break;
                }

            });
        }

        // 图片宽高验证
        function validateDimensions(file) {
            // The image dimensions is invalid.
            if (!showImg || !isImage(file) || !Dcat.helpers.len(opts.dimensions)) return true;
            var dimensions = opts.dimensions,
                width = file._info.width,
                height = file._info.height,
                isset = Dcat.helpers.isset;
            if (
                (isset(dimensions, 'width') && dimensions['width'] != width) ||
                (isset(dimensions, 'min_width') && dimensions['min_width'] > width) ||
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

            if (relation) {
                if (!relation[1]) {
                    // 新增子表记录，则不调用update接口
                    return;
                }

                form[relation[0]] = {};

                form[relation[0]][relation[1]] = {};
                form[relation[0]][relation[1]][updateColumn] = values.join(',');
            } else {
                form[updateColumn] = values.join(',');
            }

            delete form['_relation'];
            delete form['upload_column'];

            $.post({
                url: opts.updateServer,
                data: form,
            });
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
                        $wrap.find('.queueList').css({'border': '1px solid #d3dde5', 'padding': '5px'});
                        // $wrap.find('.queueList').removeAttr('style');
                    }

                    setTimeout(removeValidatorErrors, 1);
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
                            Dcat.success(__('upload_success_message', {success: stats.successNum}));

                            setTimeout(function () {
                                if (opts.upload.fileNumLimit == 1) {
                                    // 单文件上传，需要重置文件上传个数
                                    uploader.request('get-stats').numOfSuccess = 0;
                                }
                            }, 10);

                        } else {
                            // 没有成功的图片，重设
                            state = 'done';
                            location.reload();
                        }
                    }
                    break;
                case 'decrOriginalFileNum':
                    if (originalFilesNum > 0) originalFilesNum--;
                    break;

                case 'incrOriginalFileNum':
                    originalFilesNum++;
                    break;

                case 'decrFileNumLimit': // 减少上传文件数量限制
                    if (!uploader) {
                        return;
                    }
                    var fileLimit = uploader.option('fileNumLimit'),
                        num = args.num || 1;

                    if (fileLimit == '-1') fileLimit = 0;

                    num = fileLimit >= num ? fileLimit - num : 0;

                    if (num == 0) num = '-1';

                    uploader.option('fileNumLimit', num);

                    break;
                case 'incrFileNumLimit': // 增加上传文件数量限制
                    if (!uploader) {
                        return;
                    }
                    var fileLimit = uploader.option('fileNumLimit'),
                        num = args.num || 1;

                    if (fileLimit == '-1') fileLimit = 0;

                    num = fileLimit + num;

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

        // 显示api响应的错误信息
        function showErrorResponse(response) {
            var message = 'Unknown error!';
            if (response && response.data) {
                message = response.data.message || message;
            }

            Dcat.error(message)
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

            if (!Dcat.helpers.len(formFiles) && !Dcat.helpers.len(percentages)) {
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
            arr = arr.filter(function (v, k, self) {
                return self.indexOf(v) === k;
            }).filter(function (v) {
                return v ? true : false;
            });

            // 手动触发change事件，方便监听文件变化
            $input.val(arr.join(',')).trigger('change');
        }

        // 删除表单值
        function deleteInput(id) {
            deleteUploadedFile(id);

            if (!id) {
                return $input.val('');
            }
            setInput(getInput().filter(function (v) {
                return v != id;
            }));
        }

        // 添加已上传文件
        function appendUploadedFile(file) {
            if (!file.serverId || searchUploadedFile(file.serverId) !== -1) {
                return;
            }

            uploadedFiles.push(file)
        }

        function syncUploadedFiles() {
            var files = [];
            for (var i in uploadedFiles) {
                if (uploadedFiles[i]) {
                    files.push(uploadedFiles[i].serverId);
                }
            }

            setInput(files);
        }

        function deleteUploadedFile(fileId) {
            uploadedFiles = uploadedFiles.filter(function (v) {
                return v.serverId != fileId;
            });
        }

        // 查找文件位置
        function searchUploadedFile(fileId) {
            for (var i in uploadedFiles) {
                if (uploadedFiles[i].serverId === fileId) {
                    return i;
                }
            }

            return -1;
        }

        // 交换文件排序
        function swrapUploadedFile(fileId, order) {
            var index = parseInt(searchUploadedFile(fileId)),
                currentFile = uploadedFiles[index],
                prevFile = uploadedFiles[index - 1],
                nextFile = uploadedFiles[index + 1];

            if (order) {
                if (index === 0) {
                    return;
                }

                uploadedFiles[index - 1] = currentFile;
                uploadedFiles[index] = prevFile;
            } else {
                if (!nextFile) {
                    return;
                }

                uploadedFiles[index + 1] = currentFile;
                uploadedFiles[index] = nextFile;
            }

            syncUploadedFiles();
        }

        // 重新渲染已上传文件
        function rerenderUploadedFiles() {
            $queue.html('');

            for (var i in uploadedFiles) {
                if (uploadedFiles[i]) {
                    appendUploadedFileForm(uploadedFiles[i])
                }
            }
        }

        // 重新计算按钮定位
        function refreshButton() {
            uploader.refresh();
        }

        function removeValidatorErrors() {
            $input.parents('.form-group,.form-label-group,.form-field').find('.with-errors').html('')
        }

        // 文件排序
        function orderFiles() {
            var $this = $(this),
                $li = $this.parents('li').first(),
                fileId = $this.data('id'),
                order = $this.data('order'),
                $prev = $li.prev(),
                $next = $li.next();

            if (order) {
                // 升序
                if (!$prev.length) {
                    return;
                }
                swrapUploadedFile(fileId, order);
                rerenderUploadedFiles();

                return;
            }

            if (!$next.length) {
                return;
            }

            swrapUploadedFile(fileId, order);
            rerenderUploadedFiles();
        }

        // 添加上传成功文件到表单区域
        function appendUploadedFileForm(file) {
            var html = "";
            html += "<li title='" + file.serverPath + "'>";

            if (!showImg && opts.sortable) {
                // 文件排序
                html += `
<p style="right: 45px" class="file-action" data-file-act='order' data-order="1" data-id='${file.serverId}'><i class='feather icon-arrow-up'></i></p>
<p style="right: 25px" class="file-action" data-file-act='order' data-order="0" data-id='${file.serverId}'><i class='feather icon-arrow-down'></i></p>
`;
            }

            if (showImg) {
                html += `<p class='imgWrap'><img src='${file.serverUrl}'></p>`
            } else if (!opts.disabled) {
                html += `<p class="file-action" data-file-act="delete" data-id="${file.serverId}"><i class="feather icon-trash red-dark"></i></p>`;
            }

            html += "<p class='title' style=''><i class='feather icon-check text-white icon-success text-white'></i>";
            html += file.serverPath;
            html += "</p>";

            if (showImg) {
                html += "<p class='title' style='margin-bottom:20px;'>&nbsp;</p>";
                html += "<div class='file-panel' >";

                if (!opts.disabled) {
                    html += `<a class='btn btn-sm btn-white' data-file-act='deleteurl' data-id='${file.serverId}'><i class='feather icon-trash red-dark' style='font-size:13px'></i></a>`;
                }
                html += `<a class='btn btn-sm btn-white' data-file-act='preview' data-url='${file.serverUrl}' ><i class='feather icon-zoom-in'></i></a>`;

                if (opts.sortable) {
                    // 文件排序
                    html += `
<a class='btn btn-sm btn-white' data-file-act='order' data-order="1" data-id='${file.serverId}'><i class='feather icon-arrow-up'></i></a>
<a class='btn btn-sm btn-white' data-file-act='order' data-order="0" data-id='${file.serverId}'><i class='feather icon-arrow-down'></i></a>
`;
                }

                html += "</div>";
            } else {

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

                Dcat.confirm(__('confirm_delete_file'), file.serverId, function () {
                    post.key = fileId;
                    post._column = updateColumn;
                    post._relation = relation;

                    Dcat.loading();
                    $.post({
                        url: opts.deleteUrl,
                        data: post,
                        success: function (result) {
                            Dcat.loading(false);
                            if (result.status) {
                                // 移除
                                html.remove();

                                removeFormFile(fileId);
                                return;
                            }

                            showErrorResponse(result)
                        }
                    });
                });
            };

            // 删除按钮点击事件
            html.find('[data-file-act="deleteurl"]').click(deleteFile);
            html.find('[data-file-act="delete"]').click(deleteFile);

            // 文件排序
            if (opts.sortable) {
                html.find('[data-file-act="order"').click(orderFiles);
            }

            // 放大图片
            html.find('[data-file-act="preview"]').click(function () {
                var url = $(this).data('url');

                Dcat.helpers.previewImage(url);
            });

            formFiles[file.serverId] = file;

            addInput(file.serverId);

            $queue.append(html);

            if (showImg) {
                setTimeout(function () {
                    html.css('margin', '5px');
                }, initImg ? 0 : 400);

                initImg = 1;
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
            $upload = $wrap.find('.upload-btn');

            // 没选择文件之前的内容。
            $placeHolder = $wrap.find('.placeholder');

            $progress = $statusBar.find('.upload-progress').hide();

            // 实例化
            this.uploader = uploader = WebUploader.create(opts.upload);

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
                    label: '<i class="feather icon-folder"></i> &nbsp;' + __('go_on_add')
                });
            }

            uploader.onUploadProgress = function (file, percentage) {
                percentages[file.id][1] = percentage;
                updateTotalProgress();
            };

            uploader.onBeforeFileQueued = function (file) {

            };

            // 添加文件
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

                if (!opts.disabled && opts.autoUpload) {
                    // 自动上传
                    uploader.upload()
                }
            };

            // 删除文件事件监听
            uploader.onFileDequeued = function (file) {
                fileCount--;
                fileSize -= file.size;

                if (!fileCount && !Dcat.helpers.len(formFiles)) {
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
                        if (! reason || ! reason.status) {
                            showErrorResponse(reason);

                            faildFiles[obj.file.id] = obj.file;

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

                        appendUploadedFile(obj.file);

                        addInput(reason.data.id);

                        var $li = getFileView(obj.file.id);

                        if (!showImg) {
                            $li.find('.file-action').hide();
                            $li.find('[data-file-act="delete"]').show();
                        }

                        if (opts.sortable) {
                            $li.find('[data-file-act="order"]').removeClass('d-none').show();
                        }

                        break;
                }

            });

            uploader.onError = function (code) {
                switch (code) {
                    case 'Q_TYPE_DENIED':
                        Dcat.error(__('Q_TYPE_DENIED'));
                        break;
                    case 'Q_EXCEED_NUM_LIMIT':
                        Dcat.error(__('Q_EXCEED_NUM_LIMIT', {num: opts.upload.fileNumLimit}));
                        break;
                    case 'F_EXCEED_SIZE':
                        Dcat.error(__('F_EXCEED_SIZE'));
                        break;
                    case 'Q_EXCEED_SIZE_LIMIT':
                        Dcat.error(__('Q_EXCEED_SIZE_LIMIT'));
                        break;
                    case 'F_DUPLICATE':
                        Dcat.warning(__('F_DUPLICATE'));
                        break;
                    default:
                        Dcat.error('Error: ' + code);
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

                var file = {
                    serverId: opts.preview[i].id,
                    serverUrl: opts.preview[i].url,
                    serverPath: path,
                    ext: ext,
                    fake: 1,
                };

                setState('incrOriginalFileNum');
                setState('decrFileNumLimit');

                appendUploadedFileForm(file);
                appendUploadedFile(file);
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

        return this;
    }

    Dcat.Uploader = function (options) {
        return new Uploader(options)
    };

})(window, jQuery);
