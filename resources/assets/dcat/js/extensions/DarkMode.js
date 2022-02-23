
export default class DarkMode {
    constructor(Dcat) {
        this.options = {
            sidebar_dark: Dcat.config.sidebar_dark,
            dark_mode: Dcat.config.dark_mode,
            class: {
                dark: 'dark-mode',
                sidebarLight: Dcat.config.sidebar_light_style || 'sidebar-light-primary',
                sidebarDark: 'sidebar-dark-white',
            }
        };

        Dcat.darkMode = this;
    }

    // 暗黑模式切换按钮
    initSwitcher (selector) {
        var storage = localStorage || {setItem:function () {}, getItem: function () {}},
            darkMode = this,
            key = 'dcat-admin-theme-mode',
            icon = '.dark-mode-switcher i';

        function switchMode(theme) {
            switch (theme) {
                case 'dark': {
                    $(icon).addClass('icon-sun').removeClass('icon-moon');
                    darkMode.display(true);
                    break;
                }
                case 'def': {
                    darkMode.display(false);
                    $(icon).removeClass('icon-sun').addClass('icon-moon');
                    break;
                }
                default: {
                    break;
                }
            }
        }

        switchMode(storage.getItem(key));

        $(document).off('click', selector).on('click', selector, function () {
            $(icon).toggleClass('icon-sun icon-moon');

            if ($(icon).hasClass('icon-moon')) {
                switchMode('def');

                storage.setItem(key, 'def');

            } else {
                storage.setItem(key, 'dark');

                switchMode('dark')
            }
        })

        window.addEventListener('storage', function (event) {
            if (event.key === key) {
                switchMode(event.newValue);
            }
        });
    }

    toggle() {
        if ($('body').hasClass(this.options.class.dark)) {
            this.display(false)
        } else {
            this.display(true)
        }
    }

    display(show) {
        let $document = $(document),
            $body = $('body'),
            $sidebar = $('.main-menu .main-sidebar'),
            options = this.options,
            cls = options.class;

        if (show) {
            $body.addClass(cls.dark);
            $sidebar.removeClass(cls.sidebarLight).addClass(cls.sidebarDark);

            $document.trigger('dark-mode.shown');

            return;
        }

        $body.removeClass(cls.dark);
        if (! options.sidebar_dark) {
            $sidebar.addClass(cls.sidebarLight).removeClass(cls.sidebarDark);
        }

        $document.trigger('dark-mode.hide');
    }
}