
export default class Menu {
    constructor(Dcat) {
        this.bindClick();
    }

    // 菜单点击选中效果
    bindClick() {
        let $content = $('.main-menu-content'),
            $items = $content.find('li.nav-item'),
            $hasSubItems = $content.find('li.has-sub');

        $items.find('a').click(function () {
            let href = $(this).attr('href');
            if (! href || href === '#') {
                return;
            }

            $items.removeClass('active');
            $hasSubItems.removeClass('sidebar-group-active');

            $(this).parent().addClass('active')
        });
    }
}
