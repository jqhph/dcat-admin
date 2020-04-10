
let idPrefix = 'dcat-slider-',
    template = `<div id="{id}" class="slider-panel {class}">
    <div class="slider-content position-fixed p-1 ps ps--active-y"></div>
</div>`;

export default class Slider {
    constructor(Dcat, options) {
        let _this = this;

        _this.options = $.extend({
            target: null,
            class: null,
            autoDestory: true,
        }, options);

        _this.id = idPrefix + Dcat.helpers.random();
        _this.$target = $(_this.options.target);
        _this.$container = $(
            template
                .replace('{id}', _this.id)
                .replace('{class}', _this.options.class || '')
        );

        _this.$container.appendTo('body');
        _this.$container.find('.slider-content').append(_this.$target);

        // 滚动条
        new PerfectScrollbar(`#${_this.id} .slider-content`);

        if (_this.options.autoDestory) {
            // 刷新或跳转页面时移除面板
            Dcat.onPjaxComplete(() => {
                _this.destroy();
            });
        }
    }

    open() {
        this.$container.addClass('open');
    }

    close() {
        this.$container.removeClass('open');
    }

    toggle() {
        this.$container.toggleClass('open');
    }

    destroy() {
        this.$container.remove()
    }
}
