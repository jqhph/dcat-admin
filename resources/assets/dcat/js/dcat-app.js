
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
import RowSelector from './extensions/RowSelector'
import Grid from './extensions/Grid'
import Debounce from './extensions/Debounce'

import Footer from './bootstrappers/Footer'
import Pjax from './bootstrappers/Pjax'

let win = window,
    $ = jQuery;

win.NProgress = NProgress;

// 扩展Dcat对象
function extend (Dcat) {
    new AjaxExtension(Dcat);
    new DialogExtension(Dcat);
    new Grid(Dcat);

    Dcat.NP = NProgress;
    Dcat.RowSelector = function (options) {
        return new RowSelector(options)
    };
    Dcat.debounce = Debounce;
}

// 初始化事件监听
function on(Dcat) {
    new Footer(Dcat);
    new Pjax(Dcat);

    Dcat.booting(function () {
        Dcat.NP.configure({parent: '.app-content'});
    });
}

// 初始化
function boot(Dcat) {
    extend(Dcat);
    on(Dcat);

    $(Dcat.boot);

    return Dcat;
}

win.CreateDcat = function(config) {
    return boot(new Dcat(config));
};

