
<div align="center">
    <img src="https://jqhph.github.io/dcat-admin/assets/img/logo-text.png" height="80"> 
</div>
<br>
<p align="center"><code>Dcat Admin</code>是一个基于<a href="https://www.laravel-admin.org/" target="_blank">laravel-admin</a>二次开发而成的后台构建工具，只需极少的代码即可构建出一个功能完善且颜值极高的后台系统。</p>

<p align="center">
<a href="http://www.dcatadmin.com/">文档</a> |
<a href="https://jqhph.github.io/dcat-admin/demo.html">Demo</a> |
<a href="https://github.com/jqhph/dcat-admin-demo">Demo源码</a> |
<a href="#extensions">扩展</a>
</p>

<p align="center">
    <a href="https://github.com/jqhph/dcat-admin/blob/master/LICENSE"><img src="https://img.shields.io/badge/license-MIT-7389D8.svg?style=flat" ></a>
    <a href="https://travis-ci.org/jqhph/dcat-admin">
        <img src="https://travis-ci.org/jqhph/dcat-admin.svg?branch=master" alt="Build Status">
    </a>
    <a href="https://styleci.io/repos/182349597">
        <img src="https://github.styleci.io/repos/182349597/shield" alt="StyleCI">
    </a>
    <a href="https://packagist.org/packages/dcat/laravel-admin" ><img src="https://poser.pugx.org/dcat/laravel-admin/v/stable" /></a> 
    <a href="https://packagist.org/packages/dcat/laravel-admin"><img src="https://img.shields.io/packagist/dt/dcat/laravel-admin.svg?color=" /></a> 
    <a><img src="https://img.shields.io/badge/php-7.1+-59a9f8.svg?style=flat" /></a> 
    <a><img src="https://img.shields.io/badge/laravel-5.5+-59a9f8.svg?style=flat" ></a>
</p>

## 声明
大家好，我是 Dcat Admin 的开发者 Jiangqh，非常感谢大家对这个项目的关注和支持，我会用心把这个项目做好，并且会一直维护下去！

现经过一些朋友的提醒，以及我自己的深思熟虑之后，决定对这个项目的前端框架再次进行重构，新的前端框架不再采用商业版的 vuexy，而是使用完全开源的 AdminLTE3.0 版本，当然也会保持高颜值的特点。预计重构时间将在一周左右，对大家带来的不便深感抱歉，如果喜欢这个项目，不妨继续关注，谢谢大家。

## 截图

![](https://jqhph.github.io/dcat-admin/assets/img/users1.png)

## 功能

- [x] 用户管理（可拆卸）
- [x] RBAC权限管理（可拆卸），支持无限极权限节点
- [x] 菜单管理（可拆卸）
- [x] 使用PJAX构建无刷新页面，并且支持按需加载静态资源
- [x] 松耦合的页面构建与数据操作设计，可轻松切换数据源
- [x] 插件功能
- [x] 可视化代码生成器，可根据数据表一键生成增删改查页面
- [x] 数据表格构建工具，内置丰富的表格常用功能（如组合表头、数据导出、搜索、快捷创建、批量操作等）
- [x] 树状表格功能构建工具，支持分页和点击加载
- [x] 数据表单构建工具，内置 50+ 种表单类型，支持异步提交
- [x] 分步表单构建工具
- [x] 弹窗表单构建工具
- [x] 数据详情页构建工具
- [x] 无限层级树状页面构建工具，支持用拖拽的方式实现数据的层级、排序等操作
- [x] 内置 40+ 种常用页面组件（如图表、下拉菜单、Tab卡片、提示工具、提示卡片等）
- [x] Section功能（类似`Wordpress`的过滤器Filter）
- [x] 异步文件上传表单，支持分块上传

## 环境
 - PHP >= 7.1.0
 - Laravel 5.5.0 ~ 7.*
 - Fileinfo PHP Extension

## 安装

> 如果安装过程中出现`composer`下载过慢或安装失败的情况，请运行命令`composer config -g repo.packagist composer https://mirrors.aliyun.com/composer/`把`composer`镜像更换为阿里云镜像。


首先请确保已经安装了`laravel`，如果没有安装`laravel`，则可以通过以下命令安装：
```
composer create-project --prefer-dist laravel/laravel 项目名称 5.8.*
# 或
composer create-project --prefer-dist laravel/laravel 项目名称
```

安装好了`laravel`，然后设置数据库连接设置正确。

```
composer require dcat/laravel-admin
```

然后运行下面的命令来发布资源：

```
php artisan admin:publish
```

在该命令会生成配置文件`config/admin.php`，可以在里面修改安装的地址、数据库连接、以及表名，建议都是用默认配置不修改。

然后运行下面的命令完成安装：

> 执行这一步命令可能会报以下错误`Specified key was too long ... 767 bytes`，如果出现这个报错，请在`app/Providers/AppServiceProvider.php`文件的`boot`方法中加上代码`\Schema::defaultStringLength(191);`，再重新运行一遍`php artisan admin:install`命令即可。

```
php artisan admin:install
```

启动服务后，在浏览器打开 `http://localhost/admin/` ,使用用户名 `admin` 和密码 `admin`登陆.

<a name="extensions"></a>
## 扩展

| 扩展                                        | 描述                              | dcat-admin 版本                             |
| ------------------------------------------------ | ---------------------------------------- |---------------------------------------- |
| [dcat-page](https://github.com/jqhph/dcat-page)    | 简洁的静态站点构建工具 | * |
| [ueditor](https://github.com/jqhph/dcat-admin-ueditor) | 百度在线编辑器     | * |
| [grid-sortable](https://github.com/jqhph/dcat-admin-grid-sortable) | 表格拖曳排序工具      | * |
| [gank](https://github.com/jqhph/dcat-admin-gank) | 干货集中营      |* |




## 鸣谢
`Dcat Admin` 基于以下组件:

+ [Laravel](https://laravel.com/)
+ [Laravel Admin](https://www.laravel-admin.org/)
+ [vuexy](https://pixinvent.com/demo/vuexy-vuejs-admin-dashboard-template/landing/)(此项目为商业收费项目，已购买正式版取得授权，且由于协议中未声明是否允许开源代码，所以本项目不包含[vuexy](https://pixinvent.com/demo/vuexy-vuejs-admin-dashboard-template/landing/)打包前的代码，只包含打包之后的)
+ [bootstrap4](https://getbootstrap.com/)
+ [jQuery3](https://jquery.com/)
+ [Eonasdan Datetimepicker](https://github.com/Eonasdan/bootstrap-datetimepicker/)
+ [font-awesome](http://fontawesome.io)
+ [jquery-form](https://github.com/jquery-form/form)
+ [moment](http://momentjs.com/)
+ [webuploader](http://fex.baidu.com/webuploader/)
+ [bootstrap-fileinput](https://github.com/kartik-v/bootstrap-fileinput)
+ [jquery-pjax](https://github.com/defunkt/jquery-pjax)
+ [Nestable](http://dbushell.github.io/Nestable/)
+ [toastr](http://codeseven.github.io/toastr/)
+ [editor-md](https://github.com/pandao/editor.md)
+ [fontawesome-iconpicker](https://github.com/itsjavi/fontawesome-iconpicker)
+ [layer弹出层](http://layer.layui.com/)
+ [waves](https://github.com/fians/Waves)
+ [bootstrap-duallistbox](https://www.virtuosoft.eu/code/bootstrap-duallistbox/)
+ [char.js](https://www.chartjs.org)
+ [nprogress](https://ricostacruz.com/nprogress/)
+ [bootstrap-validator](https://github.com/1000hz/bootstrap-validator)
+ [Google map](https://www.google.com/maps)
+ [Tencent map](http://lbs.qq.com/)

## License
------------
`dcat-admin` is licensed under [The MIT License (MIT)](LICENSE).
