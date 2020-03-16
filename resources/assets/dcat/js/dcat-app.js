
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
import Helpers from './extensions/Helpers'
import Ajax from './extensions/Ajax'
import Toastr from './extensions/Toastr'
import SweetAlert2 from './extensions/SweetAlert2'
import RowSelector from './extensions/RowSelector'
import Grid from './extensions/Grid'
import Form from './extensions/Form'
import DialogForm from './extensions/DialogForm'
import Loading from './extensions/Loading'
import PreviewImage from './extensions/PreviewImage'
import AssetsLoader from './extensions/AssetsLoader'
import Slider from './extensions/Slider'

import Menu from './bootstrappers/Menu'
import Footer from './bootstrappers/Footer'
import Pjax from './bootstrappers/Pjax'
import DataActions from './bootstrappers/DataActions'

let win = window,
    $ = jQuery;

// 扩展Dcat对象
function extend (Dcat) {
    // 工具函数
    new Helpers(Dcat);
    // ajax处理相关扩展函数
    new Ajax(Dcat);
    // Toastr简化使用函数
    new Toastr(Dcat);
    // SweetAlert2简化使用函数
    new SweetAlert2(Dcat);
    // Grid相关功能函数
    new Grid(Dcat);
    // loading效果
    new Loading(Dcat);
    // 图片预览功能
    new PreviewImage(Dcat);
    // 静态资源加载器
    new AssetsLoader(Dcat);

    // 加载进度条
    Dcat.NP = NProgress;

    // 行选择器
    Dcat.RowSelector = function (options) {
        return new RowSelector(options)
    };

    // ajax表单提交
    Dcat.Form = function (options) {
        return new Form(options)
    };

    // 弹窗表单
    Dcat.DialogForm = function (options) {
        return new DialogForm(Dcat, options);
    };

    // 滑动面板
    Dcat.Slider = function (options) {
        return new Slider(Dcat, options)
    };
}

// 初始化
function listen(Dcat) {
    // 只初始化一次
    Dcat.booting(() => {
        // 菜单点击选中效果
        new Menu(Dcat);
        // 返回顶部按钮
        new Footer(Dcat);

        // layer弹窗设置
        layer.config({maxmin: true, moveOut: true, shade: false});

        // ajax全局设置
        $.ajaxSetup({
            cache: true,
            error: Dcat.handleAjaxError
        });

        Dcat.NP.configure({parent: '.app-content'});
    });

    // 每个请求都初始化
    Dcat.bootingEveryRequest(() => {
        // pjax初始化功能
        new Pjax(Dcat);
        // data-action 动作绑定(包括删除、批量删除等操作)
        new DataActions(Dcat);

    });
}

// 开始初始化
function boot(Dcat) {
    extend(Dcat);
    listen(Dcat);

    $(Dcat.boot.bind(Dcat));

    return Dcat;
}

win.CreateDcat = function(config) {
    return boot(new Dcat(config));
};

