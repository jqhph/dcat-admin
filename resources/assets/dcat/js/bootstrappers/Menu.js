
export default class Menu {
    constructor(Dcat) {
        this.init();
    }

    // 菜单点击选中效果
    init() {
        if (! $('.main-sidebar .sidebar').length) {
            return;
        }

        // 滚动条优化
        new PerfectScrollbar('.main-sidebar .sidebar');

        let $content = $('.main-menu-content'),
            $items = $content.find('li'),
            $hasSubItems = $content.find('li.has-treeview');

        $items.find('a').click(function () {
            let href = $(this).attr('href');
            if (! href || href === '#') {
                return;
            }

            $items.find('.nav-link').removeClass('active');
            // $hasSubItems.removeClass('menu-open');

            $(this).addClass('active')
        });
    }
}
