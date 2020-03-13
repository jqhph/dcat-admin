
/*=========================================================================================
  File Name: app.js
  Description: Dcat Admin JS脚本.
  ----------------------------------------------------------------------------------------
  Item Name: Dcat Admin
  Author: Jqh
  Author URL: https://github.com/jqhph
==========================================================================================*/

import Dcat from './Dcat'

import NProgress from './nprogress/NProgress.min'
import AjaxExtension from './extensions/Ajax'
import DialogExtension from './extensions/Dialog'
import RowSelectorExtension from './extensions/RowSelector'
import GridExtension from './extensions/Grid'
import DebounceExtension from './extensions/Debounce'

import Footer from './bootstrappers/Footer'
import Pjax from './bootstrappers/Pjax'

let win = window,
    $ = jQuery;

win.NProgress = NProgress;

// 扩展Dcat对象
function extend (Dcat) {
    new AjaxExtension(Dcat);
    new DialogExtension(Dcat);
    new GridExtension(Dcat);

    Dcat.NP = NProgress;
    Dcat.RowSelector = function (options) {
        return new RowSelectorExtension(options)
    };
    Dcat.debounce = DebounceExtension;
}

// 初始化事件监听
function listen(Dcat) {
    Dcat.booting(function () {
        new Footer(Dcat);
        new Pjax(Dcat);

        Dcat.NP.configure({parent: '.app-content'});
    });
}

// 初始化
function boot(Dcat) {
    extend(Dcat);
    listen(Dcat);

    $(Dcat.boot);

    return Dcat;
}

win.CreateDcat = function(config) {
    return boot(new Dcat(config));
};

