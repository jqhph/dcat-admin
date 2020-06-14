
export default class DarkMode {
    constructor(Dcat) {
        this.options = {
            sidebar_dark: Dcat.config.sidebar_dark,
            dark_mode: Dcat.config.dark_mode,
            class: {
                dark: 'dark-mode',
                sidebarLight: 'sidebar-light-primary',
                sidebarDark: 'sidebar-dark-white',
            }
        };

        Dcat.darkMode = this;

        if (this.options.dark_mode) {
            this.switch(true)
        }
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