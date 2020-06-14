
<div align="center">
    <img src="https://jqhph.github.io/dcat-admin/assets/img/logo-text.png" height="80"> 
</div>
<br>
<p align="center"><code>Dcat Admin</code>是一个基于<a href="https://www.laravel-admin.org/" target="_blank">laravel-admin</a>二次开发而成的后台系统构建工具，只需很少的代码即可快速构建出一个功能完善的高颜值后台系统。内置丰富的后台常用组件，开箱即用，让开发者告别冗杂的HTML代码，对后端开发者非常友好。</p>

<p align="center">
<a href="https://learnku.com/docs/dcat-admin/1.x">learnku文档</a> |
<a href="http://www.dcatadmin.com/">文档</a> |
<a href="https://jqhph.github.io/dcat-admin/demo.html">Demo</a> |
<a href="https://github.com/jqhph/dcat-admin-demo">Demo源码</a> |
<a href="#extensions">扩展</a>
</p>

<p align="center">
    <a href="https://github.com/jqhph/dcat-admin/blob/master/LICENSE"><a href="https://opencollective.com/dcat-admin" alt="Financial Contributors on Open Collective"><img src="https://opencollective.com/dcat-admin/all/badge.svg?label=financial+contributors" /></a> <img src="https://img.shields.io/badge/license-MIT-7389D8.svg?style=flat" ></a>
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

## 截图

![](https://cdn.learnku.com/uploads/images/202005/24/38389/fnbgQWK115.png!large)

![](https://cdn.learnku.com/uploads/images/202006/13/38389/LMpJ0aqPEv.png!large)

![](https://cdn.learnku.com/uploads/images/202004/24/38389/GBkt9jYnW0.png!large)

![](https://cdn.learnku.com/uploads/images/202004/17/38389/MUGJxDwrSn.png!large)

![](https://cdn.learnku.com/uploads/images/202004/24/38389/bP75OeDbWH.png!large)

## 功能特性

- [x] 简洁优雅、灵活可扩展的API
- [x] 用户管理
- [x] RBAC权限管理，支持无限极权限节点
- [x] 菜单管理
- [x] 使用pjax构建无刷新页面，支持**按需加载**静态资源，可以无限扩展组件而不影响整体性能
- [x] 松耦合的页面构建与数据操作设计，可轻松切换数据源
- [x] 自定义页面
- [x] 自定义主题配色
- [x] 多主题切换功能，内置多种主题色
- [x] 可轻松构建无菜单栏的独立页面（如可用于构建弹窗选择器等功能）
- [x] 插件功能
- [x] 可视化代码生成器，可根据数据表一键生成增删改查页面
- [x] 数据表格构建工具，内置丰富的表格常用功能（如组合表头、数据导出、搜索、快捷创建、批量操作等）
- [x] 树状表格功能构建工具，支持分页和点击加载
- [x] 数据表单构建工具，内置丰富的表单类型，支持表单异步提交
- [x] 分步表单构建工具
- [x] 弹窗表单构建工具
- [x] 数据详情页构建工具
- [x] 无限层级树状页面构建工具，支持用拖拽的方式实现数据的层级、排序等操作
- [x] 内置丰富的常用页面组件（如图表、数据统计卡片、下拉菜单、Tab卡片、提示工具等）
- [x] `Section`功能（类似`Wordpress`的`Filter`和`blade`模板的`section`标签）
- [x] 异步文件上传表单，支持分块多线程上传
- [x] 多应用
- [ ] 插件市场，只需在管理页面轻轻点击鼠标即可完成插件的安装、更新和卸载等操作


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

> 执行这一步命令可能会报以下错误`Specified key was too long ... 767 bytes`，如果出现这个报错，请在`app/Providers/AppServiceProvider.php`文件的`boot`方法中加上代码`\Schema::defaultStringLength(191);`，然后删除掉数据库中的所有数据表，再重新运行一遍`php artisan admin:install`命令即可。
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


## 鸣谢
`Dcat Admin` 基于以下组件:

+ [Laravel](https://laravel.com/)
+ [Laravel Admin](https://www.laravel-admin.org/)
+ [AdminLTE3](https://github.com/ColorlibHQ/AdminLTE)
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

## Contributors

### Code Contributors

This project exists thanks to all the people who contribute. [[Contribute](CONTRIBUTING.md)].
<a href="https://github.com/jqhph/dcat-admin/graphs/contributors"><img src="https://opencollective.com/dcat-admin/contributors.svg?width=890&button=false" /></a>

### Financial Contributors

Become a financial contributor and help us sustain our community. [[Contribute](https://opencollective.com/dcat-admin/contribute)]

#### Individuals

<a href="https://opencollective.com/dcat-admin"><img src="https://opencollective.com/dcat-admin/individuals.svg?width=890"></a>

#### Organizations

Support this project with your organization. Your logo will show up here with a link to your website. [[Contribute](https://opencollective.com/dcat-admin/contribute)]

<a href="https://opencollective.com/dcat-admin/organization/0/website"><img src="https://opencollective.com/dcat-admin/organization/0/avatar.svg"></a>
<a href="https://opencollective.com/dcat-admin/organization/1/website"><img src="https://opencollective.com/dcat-admin/organization/1/avatar.svg"></a>
<a href="https://opencollective.com/dcat-admin/organization/2/website"><img src="https://opencollective.com/dcat-admin/organization/2/avatar.svg"></a>
<a href="https://opencollective.com/dcat-admin/organization/3/website"><img src="https://opencollective.com/dcat-admin/organization/3/avatar.svg"></a>
<a href="https://opencollective.com/dcat-admin/organization/4/website"><img src="https://opencollective.com/dcat-admin/organization/4/avatar.svg"></a>
<a href="https://opencollective.com/dcat-admin/organization/5/website"><img src="https://opencollective.com/dcat-admin/organization/5/avatar.svg"></a>
<a href="https://opencollective.com/dcat-admin/organization/6/website"><img src="https://opencollective.com/dcat-admin/organization/6/avatar.svg"></a>
<a href="https://opencollective.com/dcat-admin/organization/7/website"><img src="https://opencollective.com/dcat-admin/organization/7/avatar.svg"></a>
<a href="https://opencollective.com/dcat-admin/organization/8/website"><img src="https://opencollective.com/dcat-admin/organization/8/avatar.svg"></a>
<a href="https://opencollective.com/dcat-admin/organization/9/website"><img src="https://opencollective.com/dcat-admin/organization/9/avatar.svg"></a>

## License
------------
`dcat-admin` is licensed under [The MIT License (MIT)](LICENSE).
