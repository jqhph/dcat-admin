
import Dcat from './Dcat'

import AjaxExtension from './extensions/Ajax'
import DialogExtension from './extensions/Dialog'

import Footer from './bootstrappers/Footer'
import Pjax from './bootstrappers/Pjax'

let $ = jQuery,
    extend = function (Dcat) {
        // 扩展Dcat对象
        new AjaxExtension(Dcat);
        new DialogExtension(Dcat);
    },
    on = function (Dcat) {
        // 初始化
        new Footer(Dcat);
        new Pjax(Dcat);
    },
    boot = function (Dcat) {
        extend(Dcat);
        on(Dcat);

        $(Dcat.boot);

        return Dcat;
    };

(function () {
    this.CreateDcat = function(config) {
        return boot(new Dcat(config));
    };
}.call(window));

