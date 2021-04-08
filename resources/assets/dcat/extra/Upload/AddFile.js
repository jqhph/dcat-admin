
export default class AddFile {
    constructor(Uploder) {
        this.uploader = Uploder;
    }

    // 渲染新文件
    render(file) {
        let _this = this,
            parent = _this.uploader,
            showImg = parent.isImage(),
            size = WebUploader.formatSize(file.size),
            $li,
            $btns,
            fileName = file.name || null;

        if (showImg) {
            $li = $(`<li id="${parent.getFileViewSelector(file.id)}" title="${fileName}" >
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
                    <li id="${parent.getFileViewSelector(file.id)}" title="${file.nam}">
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

        $li.appendTo(parent.$files);

        setTimeout(function () {
            $li.css({margin: '5px'});
        }, 50);

        if (file.getStatus() === 'invalid') {
            _this.showError($li, file.statusText, file);
        } else {
            if (showImg) {
                // 显示图片
                _this.showImage($li, file)
            }

            parent.percentages[file.id] = [file.size, 0];
            file.rotation = 0;
        }

        file.on('statuschange', _this.resolveStatusChangeCallback($li, $btns, file));

        let $act = showImg ? $btns.find('a') : $btns;

        $act.on('click', _this.resolveActionsCallback(file));
    }

    // 显示错误信息
    showError ($li, code, file) {
        let _this = this,
            lang = _this.uploader.lang,
            text = '',
            $info = $('<p class="error"></p>');

        switch (code) {
            case 'exceed_size':
                text = lang.trans('exceed_size');
                break;

            case 'interrupt':
                text = lang.trans('interrupt');
                break;

            default:
                text = lang.trans('upload_failed');
                break;
        }

        _this.uploader.faildFiles[file.id] = file;

        $info.text(text).appendTo($li);
    }

    // 显示图片
    showImage($li, file) {
        let _this = this,
            uploader = _this.uploader.uploader,
            $wrap = $li.find('p.imgWrap');

        var image = uploader.makeThumb(file, function (error, src) {
            var img;

            $wrap.empty();
            if (error) {
                $li.find('.title').show();
                $li.find('.file-type').show();
                return;
            }

            if (_this.uploader.helper.isSupportBase64) {
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

                // 验证图片宽高
                if (! _this.validateDimensions(file)) {
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

    // 状态变化回调
    resolveStatusChangeCallback($li, $btns, file) {
        let _this = this,
            parent = _this.uploader;

        return function (cur, prev, a) {
            console.log(123, cur, prev, file);

            if (prev === 'progress') {
                // $prgress.hide().width(0);
            } else if (prev === 'queued') {
                $btns.find('[data-file-act="cancel"]').hide();
                $btns.find('[data-file-act="delete"]').show();
            }

            // 成功
            if (cur === 'error' || cur === 'invalid') {
                _this.showError($li, file.statusText, file);
                parent.percentages[file.id][1] = 1;

            } else if (cur === 'interrupt') {
                _this.showError($li, 'interrupt', file);

            } else if (cur === 'queued') {
                parent.percentages[file.id][1] = 0;

            } else if (cur === 'progress') {
                // 移除错误信息
                _this.removeError($li);
                // $prgress.css('display', 'block');

            } else if (cur === 'complete') {
                if (_this.uploader.isImage()) {
                    $li.append('<span class="success"><em></em><i class="feather icon-check"></i></span>');
                } else {
                    $li.find('._success').show();
                }
            }

            $li.removeClass('state-' + prev).addClass('state-' + cur);
        };
    }

    // 操作按钮回调
    resolveActionsCallback(file) {
        let _this = this,
            parent = _this.uploader,
            uploader = parent.uploader,
            helper = parent.helper;

        return function () {
            var index = $(this).data('file-act');

            switch (index) {
                case 'cancel':
                    uploader.removeFile(file);
                    return;
                case 'deleteurl':
                case 'delete':
                    // 本地删除
                    if (parent.options.removable) {
                        parent.input.delete(file.serverId);

                        return uploader.removeFile(file);
                    }

                    // 删除请求
                    parent.request.delete(file, function () {
                        // 删除成功回调
                        parent.input.delete(file.serverId);

                        uploader.removeFile(file);
                    });

                    break;
                case 'preview':
                    Dcat.helpers.previewImage(parent.$wrapper.find('img').attr('src'), null, file.name);

                    break;
                case 'order':
                    $(this).attr('data-id', file.serverId);

                    helper.orderFiles($(this));

                    break;
            }

        };
    }

    // 移除错误信息
    removeError($li) {
        $li.find('.error').remove()
    }

    // 图片宽高验证
    validateDimensions(file) {
        let _this = this,
            parent = _this.uploader,
            options = parent.options,
            dimensions = options.dimensions,
            width = file._info.width,
            height = file._info.height,
            isset = Dcat.helpers.isset;

        // The image dimensions is invalid.
        if (! parent.isImage() || ! _this.isImage(file) || ! Dcat.helpers.len(options.dimensions)) {
            return true;
        }

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

    // 判断是否是图片
    isImage (file) {
        return file.type.match(/^image/);
    }
}
