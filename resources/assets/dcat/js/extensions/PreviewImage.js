
export default class PreviewImage {
    constructor(Dcat) {
        this.dcat = Dcat;

        Dcat.previewImage = this.preview
    }

    preview(src, width, title) {
        let Dcat = this.dcat,
            img = new Image(),
            win = Dcat.helpers.isset(window.top) ? top : window,
            clientWidth = Math.ceil(win.screen.width * 0.6),
            clientHeight = Math.ceil(win.screen.height * 0.8);

        img.style.display = 'none';
        img.style.height = 'auto';
        img.style.width = width || '100%';
        img.src = src;

        document.body.appendChild(img);

        Dcat.loading();
        img.onload = function () {
            Dcat.loading(false);
            let srcw = this.width,
                srch = this.height,
                width = srcw > clientWidth ? clientWidth : srcw,
                height = Math.ceil(width * (srch/srcw));

            height = height > clientHeight ? clientHeight : height;

            title = title || src.split('/').pop();

            if (title.length > 50) {
                title = title.substr(0, 50) + '...';
            }

            win.layer.open({
                type: 1,
                shade: 0.2,
                title: false,
                maxmin: false,
                shadeClose: true,
                closeBtn: 2,
                content: $(img),
                area: [width+'px', (height) + 'px'],
                skin: 'layui-layer-nobg',
                end: function () {
                    document.body.removeChild(img);
                }
            });
        };
        img.onerror = function () {
            Dcat.loading(false);
            Dcat.warning('预览失败');
        };
    }
}