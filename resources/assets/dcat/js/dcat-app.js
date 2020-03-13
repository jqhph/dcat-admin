
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
import Ajax from './extensions/Ajax'
import Toastr from './extensions/Toastr'
import SweetAlert2 from './extensions/SweetAlert2'
import RowSelector from './extensions/RowSelector'
import Grid from './extensions/Grid'
import Form from './extensions/Form'
import DialogForm from './extensions/DialogForm'
import Debouncen from './extensions/Debounce'

import Footer from './bootstrappers/Footer'
import Pjax from './bootstrappers/Pjax'

let win = window,
    $ = jQuery;

// 扩展Dcat对象
function extend (Dcat) {
    new Ajax(Dcat);
    new Toastr(Dcat);
    new SweetAlert2(Dcat);
    new Grid(Dcat);

    // NProgress
    Dcat.NP = NProgress;

    // 行选择器
    Dcat.RowSelector = function (options) {
        return new RowSelector(options)
    };

    // 弹窗表单
    Dcat.DialogForm = function (options) {
        return new DialogForm(Dcat, options);
    };

    Dcat.debounce = Debouncen;
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

