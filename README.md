
<div align="center">

# DCAT ADMIN

</div>

<p align="center"><code>Dcat Admin</code>是一个基于<a href="https://www.laravel-admin.org/" target="_blank">laravel-admin</a>二次开发而成的后台构建工具，只需使用很少的代码即可快速构建出一个功能完善的漂亮的管理后台。</p>

<p align="center">
<a href="https://jqhph.github.io/dcat-admin">文档</a> |
<a href="https://jqhph.github.io/dcat-admin/demo.html">Demo</a> |
<a href="https://github.com/jqhph/dcat-admin-demo">Demo源码</a> |
<a href="#extensions">扩展</a>
</p>

<p align="center">
    <a href="https://github.com/jqhph/dcat-admin/blob/master/LICENSE"><img src="https://img.shields.io/badge/license-MIT-7389D8.svg?style=flat" ></a>
    <a href="https://styleci.io/repos/182349597">
        <img src="https://github.styleci.io/repos/182349597/shield" alt="StyleCI">
    </a>
    <a href="https://github.com/jqhph/dcat-admin/releases" ><img src="https://img.shields.io/github/release/jqhph/dcat-admin.svg?color=4099DE" /></a> 
    <a href="https://packagist.org/packages/dcat/laravel-admin"><img src="https://img.shields.io/packagist/dt/dcat/laravel-admin.svg?color=" /></a> 
    <a><img src="https://img.shields.io/badge/php-7.1+-59a9f8.svg?style=flat" /></a> 
    <a><img src="https://img.shields.io/badge/laravel-5.5+-59a9f8.svg?style=flat" ></a>
</p>

## 截图

![dcat-admin](https://jqhph.github.io/dcat-admin/assets/img/grid-1.png)

## 前言

我写这个项目的初衷只是为了补充和完善`Laravel Admin`，`Laravel Admin`是我使用过的最好用的后台构建工具，API简洁易用，上手简单，让开发者告别了冗杂的HTML代码，只需极少的代码就可以构建出一个完善的管理后台。

但是`Laravel Admin`也有许多让我不太满意的细节（例如：比较“陈旧”的UI界面、过度依赖`Eloquent model`、无法按需加载静态资源等等），正是对这些细节的不满意让我产生了重写`Laravel Admin`的想法。而我不在原项目提交`PR`是因为改动太大，并且改动后与原有的代码并不完全兼容，所以只好再开一坑，在此也非常感谢`Lavarel Admin`的开发者们免费为大家开发维护一个这么优秀的项目。

<details>
<summary>为什么不改造成前后端分离项目？</summary>


近几年，前后端分离方案已经发展成前端技术的主流，也是未来的发展趋势。那么我为什么不把Laravel Admin改造成前后端分离的模式呢？原因如下：

+ 改成前后端分离会增加项目复杂度，提高了使用门槛。
+ 保持`Laravel Admin`架构的前后端分离（类似`Laravel nova`）只是“技术角度”的前后端分离，从团队合作的角度而言并不能做到前后端分离，实际上不论是前端还是后端的工作都需要后端去做，所以这么做可能不但无法减轻开发者的工作量，反而会增加工作量。
+ 前后端分离技术固然已经是行业的主流和趋势，但是基于`jQuery`的非前后端分离项目也有自己的优势，比如门槛低、类库非常丰富等等，所以市场占有率依然极高。
+ 不是什么项目都适合采用前后端分离方案，显然对于希望控制人力成本的小公司和个人开发者而言，基于`jQuery`的非前后端分离项目显然更加的简单高效。

</details>


## 功能

- [x] 用户管理（可拆卸）
- [x] RBAC权限管理（可拆卸），支持无限极权限节点
- [x] 菜单管理（可拆卸）
- [x] 扩展管理
- [x] 按需加载静态资源，无需担心安装组件过多
- [x] 简单清晰的数据操作接口，可随意切换数据源
- [x] 基于`Bootstrap3`的栅格布局系统
- [x] 数据表格构建工具，内置 20+ 种字段类型，10+ 种表格常用功能（如双表头、数据导出、快捷搜索、快捷创建、批量操作等）
- [x] 数据表格搜索工具，内置 20+ 种过滤器，近 10 种表单类型
- [x] 数据表单构建工具，内置 50+ 种表单类型，支持异步提交
- [x] 分步表单构建工具
- [x] 弹窗表单构建工具
- [x] 数据详情页构建工具
- [x] 无限层级树状页面构建工具
- [x] 可视化代码生成器，支持生成增删改查代码、语言包、数据表等，可根据数据表生成增删改查页面
- [x] 内置 40+ 种常用页面组件（如图表、下拉菜单、Tab卡片、提示工具、提示卡片等）
- [x] Section功能（类似`Wordpress`的过滤器Filter）
- [x] 异步文件上传表单，支持分块上传
- [x] ide-helper文件生成工具

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
