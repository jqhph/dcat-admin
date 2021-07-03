
export default class AddUploadedFile {
    constructor(Uploder) {
        this.uploader = Uploder;

        // 已上传的文件
        this.uploadedFiles = [];

        this.init = false;
    }

    // 渲染已上传文件
    render(file) {
        let _this = this,
            parent =  _this.uploader,
            options = parent.options,
            showImg = parent.isImage(),
            html = "";

        html += "<li title='" + file.serverPath + "'>";

        if (! showImg && options.sortable) {
            // 文件排序
            html += `
<p style="right: 65px" class="file-action" data-file-act='order' data-order="1" data-id='${file.serverId}'><i class='feather icon-arrow-up'></i></p>
<p style="right: 45px" class="file-action" data-file-act='order' data-order="0" data-id='${file.serverId}'><i class='feather icon-arrow-down'></i></p>
`;
        }

        // 下载
        if (! showImg && options.downloadable) {
            html += `
<p style="right: 25px" class="file-action" data-file-act='download' data-id='${file.serverUrl}'><i class='feather icon-download-cloud'></i></p>
`;
        }

        if (showImg) {
            html += `<p class='imgWrap'><img src='${file.serverUrl}'></p>`
        } else if (!options.disabled) {
            html += `<p class="file-action" data-file-act="delete" data-id="${file.serverId}"><i class="feather icon-trash red-dark"></i></p>`;
        }

        html += "<p class='title' style=''><i class='feather icon-check text-white icon-success text-white'></i>";
        html += file.serverPath;
        html += "</p>";

        if (showImg) {
            html += "<p class='title' style='margin-bottom:20px;'>&nbsp;</p>";
            html += "<div class='file-panel' >";

            if (!options.disabled) {
                html += `<a class='btn btn-sm btn-white' data-file-act='deleteurl' data-id='${file.serverId}'><i class='feather icon-trash red-dark' style='font-size:13px'></i></a>`;
            }
            html += `<a class='btn btn-sm btn-white' data-file-act='preview' data-url='${file.serverUrl}' ><i class='feather icon-zoom-in'></i></a>`;

            if (options.sortable) {
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
            parent.$wrapper.css('background', 'transparent');
        }

        // 删除操作
        let deleteFile = function () {
            var fileId = $(this).data('id');

            // 本地删除
            if (options.removable) {
                html.remove();

                return _this.removeFormFile(fileId);
            }

            // 发起删除请求
            parent.request.delete({serverId: fileId}, function () {
                // 移除
                html.remove();

                _this.removeFormFile(fileId);
            });
        };

        // 删除按钮点击事件
        html.find('[data-file-act="deleteurl"]').click(deleteFile);
        html.find('[data-file-act="delete"]').click(deleteFile);

        // 文件排序
        if (options.sortable) {
            html.find('[data-file-act="order"]').click(function () {
                parent.helper.orderFiles($(this));
            });
        }

        if (options.downloadable) {
            html.find('[data-file-act="download"]').click(function () {
                window.open($(this).attr('data-id'));
            });
        }

        // 图片预览
        html.find('[data-file-act="preview"]').click(function () {
            var url = $(this).data('url');

            Dcat.helpers.previewImage(url);
        });

        parent.formFiles[file.serverId] = file;

        parent.input.add(file.serverId);

        parent.$files.append(html);

        if (showImg) {
            setTimeout(function () {
                html.css('margin', '5px');
            }, _this.init ? 0 : 400);

            _this.init = 1;
        }
    }

    // 重新渲染已上传的文件
    reRender() {
        for (let i in this.uploadedFiles) {
            if (this.uploadedFiles[i]) {
                this.render(this.uploadedFiles[i])
            }
        }
    }

    // 移除已上传文件
    removeFormFile(fileId) {
        if (!fileId) {
            return;
        }

        let _this = this,
            parent = _this.uploader,
            uploader = _this.uploader,
            file = parent.formFiles[fileId];

        parent.input.delete(fileId);

        delete parent.formFiles[fileId];

        if (uploader && !file.fake) {
            uploader.removeFile(file);
        }

        parent.status.switch('decrOriginalFileNum');
        parent.status.switch('incrFileNumLimit');

        if (! Dcat.helpers.len(parent.formFiles) && ! Dcat.helpers.len(parent.percentages)) {
            parent.status.switch('pending');
        }
    }

    add(file) {
        if (!file.serverId || this.uploader.helper.searchUploadedFile(file.serverId) !== -1) {
            return;
        }

        this.uploadedFiles.push(file)
    }
}
