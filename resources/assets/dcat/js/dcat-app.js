
/*=========================================================================================
  File Name: app.js
  Description: Dcat Admin JS脚本.
  ----------------------------------------------------------------------------------------
  Item Name: Dcat Admin
  Author: Jqh
  Author URL: https://github.com/jqhph
==========================================================================================*/

import Dcat from './Dcat'

import NProgress from './NProgress/NProgress.min'
import Ajax from './extensions/Ajax'
import Toastr from './extensions/Toastr'
import SweetAlert2 from './extensions/SweetAlert2'
import RowSelector from './extensions/RowSelector'
import Grid from './extensions/Grid'
import Form from './extensions/Form'
import DialogForm from './extensions/DialogForm'
import Loading from './extensions/Loading'
import AssetsLoader from './extensions/AssetsLoader'
import Slider from './extensions/Slider'
import Color from './extensions/Color'
import Validator from './extensions/Validator'
import DarkMode from './extensions/DarkMode'

import Menu from './bootstrappers/Menu'
import Footer from './bootstrappers/Footer'
import Pjax from './bootstrappers/Pjax'
import DataActions from './bootstrappers/DataActions'

let win = window,
    $ = jQuery;

// 扩展Dcat对象
function extend (Dcat) {
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
    // 静态资源加载器
    new AssetsLoader(Dcat);
    // 颜色管理
    new Color(Dcat);
    // 表单验证器
    new Validator(Dcat);
    // 黑色主题切换
    new DarkMode(Dcat);

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
        Dcat.NP.configure({parent: '.app-content'});

        // layer弹窗设置
        layer.config({maxmin: true, moveOut: true, shade: false});

        //////////////////////////////////////////////////////////

        // 菜单点击选中效果
        new Menu(Dcat);
        // 返回顶部按钮
        new Footer(Dcat);
        // data-action 动作绑定(包括删除、批量删除等操作)
        new DataActions(Dcat);
    });

    // 每个请求都初始化
    Dcat.bootingEveryRequest(() => {
        // ajax全局设置
        $.ajaxSetup({
            cache: true,
            error: Dcat.handleAjaxError,
            headers: {
                'X-CSRF-TOKEN': Dcat.token
            }
        });
        // pjax初始化功能
        new Pjax(Dcat);
    });
}

function prepare(Dcat) {
    extend(Dcat);
    listen(Dcat);

    return Dcat;
}

/**
 * @returns {Dcat}
 */
win.CreateDcat = function(config) {
    return prepare(new Dcat(config));
};
