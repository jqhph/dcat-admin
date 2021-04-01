
export default class Menu {
    constructor(Dcat) {
        this.init();
        this.initHorizontal();
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

    initHorizontal() {
        let selectors = {
            item: '.horizontal-menu .main-menu-content li.nav-item',
            link: '.horizontal-menu .main-menu-content li.nav-item .nav-link',
        };

        $(selectors.item).on('mouseover', function () {
            $(this).addClass('open')
        }).on('mouseout', function () {
            $(this).removeClass('open')
        });

        $(selectors.link).on('click', function () {
            let $this = $(this);

            $(selectors.link).removeClass('active');

            $this.addClass('active');

            $this.parents('.dropdown').find('.nav-link').eq(0).addClass('active');
            $this.parents('.dropdown-submenu').find('.nav-link').eq(0).addClass('active')
        });

        // 自动计算高度
        let $horizontalMenu = $('.horizontal-menu .main-horizontal-sidebar'),
            defaultHorizontalMenuHeight = 0,
            horizontalMenuTop = 0;

        // 重新计算高度
        let resize = function () {
            if (! $('.horizontal-menu').length) {
                return;
            }

            if (! defaultHorizontalMenuHeight) {
                defaultHorizontalMenuHeight = $horizontalMenu.height()
            }

            if (! horizontalMenuTop) {
                horizontalMenuTop = $horizontalMenu.offset().top + 15;
            }

            let height = $horizontalMenu.height(),
                diff = height - defaultHorizontalMenuHeight,
                $wrapper = $('.horizontal-menu.navbar-fixed-top .content-wrapper');

            if (height <= defaultHorizontalMenuHeight) {
                return $wrapper.css({'padding-top': horizontalMenuTop + 'px'});
            }

            $wrapper.css({'padding-top': (horizontalMenuTop + diff) + 'px'});
        };
        window.onresize = resize;

        resize();
    }
}
