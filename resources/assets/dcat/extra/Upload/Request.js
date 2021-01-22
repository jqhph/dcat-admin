
export default class Request {
    constructor(Uploader) {
        this.uploader = Uploader;
    }

    delete(file, callback) {
        let _this = this,
            parent = _this.uploader,
            options = parent.options,
            uploader = parent.uploader;

        Dcat.confirm(parent.lang.trans('confirm_delete_file'), file.serverId, function () {
            var post = options.deleteData;

            post.key = file.serverId;

            if (! post.key) {
                parent.input.delete(file.serverId);

                return uploader.removeFile(file);
            }

            post._column = parent.getColumn();
            post._relation = parent.relation;

            Dcat.loading();

            $.post({
                url: options.deleteUrl,
                data: post,
                success: function (result) {
                    Dcat.loading(false);

                    if (result.status) {
                        callback(result);

                        return;
                    }

                    parent.helper.showError(result)
                }
            });

        });
    }

    // 保存已上传的文件名到服务器
    update() {
        let _this = this,
            parent = _this.uploader,
            uploader = parent.uploader,
            options = parent.options,
            updateColumn = parent.getColumn(),
            relation = _this.relation,
            values = parent.input.get(), // 获取表单值
            num = uploader.getStats().successNum,
            form = $.extend({}, options.formData);

        if (!num || !values || !options.autoUpdateColumn) {
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
            url: options.updateServer,
            data: form,
        });
    }
}