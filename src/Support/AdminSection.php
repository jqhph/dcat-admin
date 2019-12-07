<?php

class AdminSection
{
    // head标签区块，此区块可以往<head>标签内输入内容
    const HEAD = 'ADMIN_HEAD';

    // 往body标签内部输入内容
    const BODY_INNER_BEFORE = 'ADMIN_BODY_INNER_BEFORE';
    const BODY_INNER_AFTER = 'ADMIN_BODY_INNER_AFTER';

    // 往#app内部输入内容
    // #app div之前
    const APP_INNER_BEFORE = 'ADMIN_APP_INNER_BEFORE';
    // #app div结束之后
    const APP_INNER_AFTER = 'ADMIN_APP_INNER_AFTER';

    // 顶部导航栏用户面板区块
    const NAVBAR_USER_PANEL = 'ADMIN_NAVBAR_USER_PANEL';
    // 顶部导航栏用户面板之后区块
    const NAVBAR_AFTER_USER_PANEL = 'ADMIN_NAVBAR_AFTER_USER_PANEL';

    // 侧边栏顶部用户信息面板区块
    const LEFT_SIDEBAR_USER_PANEL = 'ADMIN_LEFT_SIDEBAR_USER_PANEL';
    // 菜单栏区块
    const LEFT_SIDEBAR_MENU = 'ADMIN_LEFT_SIDEBAR_MENU';
    // 菜单栏顶部区块
    const LEFT_SIDEBAR_MENU_TOP = 'ADMIN_LEFT_SIDEBAR_MENU_TOP';
    // 菜单栏底部区块
    const LEFT_SIDEBAR_MENU_BOTTOM = 'ADMIN_LEFT_SIDEBAR_MENU_BOTTOM';
    // 右侧导航栏内容区块
    const RIGHT_SIDEBAR = 'ADMIN_RIGHT_SIDEBAR';
    // 右侧导航栏样式，支持"control-sidebar-light" 和 "control-sidebar-dark"
    const RIGHT_SIDEBAR_CLASS = 'ADMIN_RIGHT_SIDEBAR_CLASS';
}
