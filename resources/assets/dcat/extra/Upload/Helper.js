
export default class Helper {
    constructor(Uploder) {
        this.uploader = Uploder;

        this.isSupportBase64 = this.supportBase64();
    }

    // 判断是否支持base64
    supportBase64() {
        let data = new Image(),
            support = true;

        data.onload = data.onerror = function () {
            if (this.width != 1 || this.height != 1) {
                support = false;
            }
        };
        data.src = "data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///ywAAAAAAQABAAACAUwAOw==";

        return support;
    }

    // 显示api响应的错误信息
    showError(response) {
        var message = 'Unknown error!';
        if (response && response.data) {
            message = response.data.message || message;
        }

        Dcat.error(message)
    }

    // 文件排序
    orderFiles($this) {
        var _this = this,
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
            _this.swrapUploadedFile(fileId, order);
            _this.uploader.reRenderUploadedFiles();

            return;
        }

        if (!$next.length) {
            return;
        }

        _this.swrapUploadedFile(fileId, order);
        _this.uploader.reRenderUploadedFiles();
    }

    // 交换文件排序
    swrapUploadedFile(fileId, order) {
        let _this = this,
            parent = _this.uploader,
            uploadedFiles = parent.addUploadedFile.uploadedFiles,
            index = parseInt(_this.searchUploadedFile(fileId)),
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

        _this.setUploadedFilesToInput();
    }

    setUploadedFilesToInput() {
        let _this = this,
            parent = _this.uploader,
            uploadedFiles = parent.addUploadedFile.uploadedFiles,
            files = [],
            i;

        for (i in uploadedFiles) {
            if (uploadedFiles[i]) {
                files.push(uploadedFiles[i].serverId);
            }
        }

        parent.input.set(files);
    }

    // 查找文件位置
    searchUploadedFile(fileId) {
        let _this = this,
            parent = _this.uploader,
            uploadedFiles = parent.addUploadedFile.uploadedFiles;

        for (var i in uploadedFiles) {
            if (uploadedFiles[i].serverId === fileId) {
                return i;
            }
        }

        return -1;
    }
}
