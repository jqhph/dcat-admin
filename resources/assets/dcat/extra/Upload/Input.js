
export default class Input {
    constructor(Uploder) {
        this.uploader = Uploder;

        this.$selector = Uploder.$selector.find(Uploder.options.inputSelector)
    }

    // 获取上传的文件名
    get() {
        let val = this.$selector.val();

        return val ? val.split(',') : [];
    }

    // 增加文件名
    add(id) {
        let val = this.get();

        val.push(id);

        this.set(val);
    }

    // 设置表单值
    set(arr) {
        arr = arr.filter(function (v, k, self) {
            return self.indexOf(v) === k;
        }).filter(function (v) {
            return v ? true : false;
        });

        // 手动触发change事件，方便监听文件变化
        this.$selector.val(arr.join(',')).trigger('change');
    }

    // 删除表单值
    delete(id) {
        let _this = this;

        _this.deleteUploadedFile(id);

        if (!id) {
            return _this.$selector.val('');
        }

        _this.set(_this.get().filter(function (v) {
            return v != id;
        }));
    }

    deleteUploadedFile(fileId) {
        let addUploadedFile = this.uploader.addUploadedFile;

        addUploadedFile.uploadedFiles = addUploadedFile.uploadedFiles.filter(function (v) {
            return v.serverId != fileId;
        });
    }

    // 移除字段验证错误提示信息
    removeValidatorErrors() {
        this.$selector.parents('.form-group,.form-label-group,.form-field').find('.with-errors').html('')
    }
}
