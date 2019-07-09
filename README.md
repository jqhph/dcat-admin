
<h2>
    Dcat Admin
</h2>

<p align="center"><code>Dcat Admin</code>是一个基于<a href="https://www.laravel-admin.org/" target="_blank">laravel-admin</a>二次开发而成的后台构建工具，只需使用很少的代码即可快速构建出一个功能完善的漂亮的管理后台。</p>

<p align="center">
<a href="https://jqhph.gitee.io/dcatadmin/">文档</a> |
<a href="https://jqhph.gitee.io/dcatadmin/demo.html">Demo</a> |
<a href="https://github.com/jqhph/dcat-admin-demo">Demo源码</a> |
<a href="#extensions">扩展</a>
</p>

<p align="center">
    <a href="https://github.com/jqhph/dcat-admin/blob/master/LICENSE"><img src="https://img.shields.io/badge/license-MIT-7389D8.svg?style=flat" ></a>
    <a href="https://github.com/jqhph/dcat-admin/releases" ><img src="https://img.shields.io/github/release/jqhph/dcat-admin.svg?color=4099DE" /></a> 
    <a href="https://packagist.org/packages/dcat/laravel-admin"><img src="https://img.shields.io/packagist/dt/dcat/laravel-admin.svg?color=" /></a> 
    <a><img src="https://img.shields.io/badge/php-7.1+-59a9f8.svg?style=flat" /></a> 
    <a><img src="https://img.shields.io/badge/laravel-5.5+-59a9f8.svg?style=flat" ></a>
</p>

## 截图

![dcat-admin](https://raw.githubusercontent.com/jqhph/dcat-admin/master/docs/screenshots/2.gif)

## 前言

就我个人的感受而言，`Laravel Admin`是我使用过的最好用的后台构建工具，API简洁易用，入门也很容易，没有那么多花里胡哨的东西。而我之所以要开发这个项目，主要是想对`Laravel Admin`的一些细节做一些补充调整，增加一些比较常用的功能，优化开发体验（比如增加前端静态资源按需加载支持、美化界面和布局、增加表单弹窗、双表头表格等等比较实用的功能），总的来说可以把这个项目看做`Laravel Admin`“2.0”，更详细的异同点查看请[点击这里](https://jqhph.gitee.io/dcatadmin/docs-master-new.html)。

> 有的同学可能想问：现在都流行前后端分离这么久了，还搞这种后端渲染的项目有意义吗？答案是当然有意义。因为开发一个前后端分离项目也是需要一定成本和资源的（例如你得有个熟悉前端的开发人员），实际项目中也需要考量一下为一个管理后台耗费这些成本资源值不值得，并不是所有项目用前后端分离就更好。当然如果条件允许的话，用前后端分离的架构会更好一些。

## 环境
 - PHP >= 7.1.0
 - Laravel >= 5.5.0
 - Fileinfo PHP Extension

## 安装

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
php artisan vendor:publish --provider="Dcat\Admin\AdminServiceProvider"
```

在该命令会生成配置文件`config/admin.php`，可以在里面修改安装的地址、数据库连接、以及表名，建议都是用默认配置不修改。

然后运行下面的命令完成安装：
```
php artisan admin:install
```

启动服务后，在浏览器打开 `http://localhost/admin/` ,使用用户名 `admin` 和密码 `admin`登陆.


<a name="extensions"></a>
## 扩展

| 扩展                                        | 描述                              | dcat-admin 版本                             |
| ------------------------------------------------ | ---------------------------------------- |---------------------------------------- |
| [dcat-page](https://github.com/jqhph/dcat-page)             | 一个简洁的静态站点构建工具 | * |
| [ueditor](https://github.com/jqhph/dcat-admin-ueditor) | 百度在线编辑器          | * |
| [gank](https://github.com/jqhph/dcat-admin-gank) | 干货集中营          |* |


## 贡献



## 其他
`Dcat Admin` 基于以下组件:

+ [Laravel](https://laravel.com/)
+ [Laravel Admin](https://www.laravel-admin.org/)
+ [AdminLTE](https://almsaeedstudio.com/)
+ [Datetimepicker](http://eonasdan.github.io/bootstrap-datetimepicker/)
+ [font-awesome](http://fontawesome.io)
+ [jquery-form](https://github.com/jquery-form/form)
+ [moment](http://momentjs.com/)
+ [Google map](https://www.google.com/maps)
+ [Tencent map](http://lbs.qq.com/)
+ [bootstrap-fileinput](https://github.com/kartik-v/bootstrap-fileinput)
+ [jquery-pjax](https://github.com/defunkt/jquery-pjax)
+ [Nestable](http://dbushell.github.io/Nestable/)
+ [toastr](http://codeseven.github.io/toastr/)
+ [X-editable](http://github.com/vitalets/x-editable)
+ [bootstrap-number-input](https://github.com/wpic/bootstrap-number-input)
+ [fontawesome-iconpicker](https://github.com/itsjavi/fontawesome-iconpicker)
+ [layer弹出层](http://layer.layui.com/)
+ [waves](https://github.com/fians/Waves)


## License
------------
`dcat-admin` is licensed under [The MIT License (MIT)](LICENSE).
