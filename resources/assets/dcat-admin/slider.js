
(function ($) {
    function Slider(cnf) {
        this.opts = {
            direction: cnf.direction || "left",
            wight: cnf.width || null,
            height: cnf.height || null,
            dom: $(cnf.dom),
            time: cnf.time || null,
            shade: cnf.shade,
            closeShade: (typeof cnf.closeShade == 'undefined') ? true : cnf.closeShade,
            callback: cnf.callback || null,
            background: cnf.background || null,
            top: cnf.top || null,
            right: cnf.right || null,
            bottom: cnf.bottom || null,
            left: cnf.left || null,
            zIndex: cnf.zIndex || 97,
            hasTopNavbar: (typeof cnf.hasTopNavbar == 'undefined') ? true : cnf.hasTopNavbar,
        };
        this.id = this.randomString();
        this.dom = this.opts.dom[0];
        this.container = null;
        this.inner = null;
        this.shade = null;
        this.opened = false;
        this.init()
    }

    Slider.prototype = {
        isMobile: function () {
            return navigator.userAgent.match(/(iPhone|iPod|Android|ios)/i) ? true : false
        },

        isSmallScreen: function () {
            return screen.width <= 767
        },

        addEvent: function (f, e, d) {
            if (f.attachEvent) {
                f.attachEvent("on" + e, d)
            } else {
                f.addEventListener(e, d, false)
            }
        },

        randomString: function () {
            return Math.random().toString(36).substr(2, 6)
        },

        init: function () {
            var self = this;
            if (! self.dom) {
                throw new Error('Invalid dom');
            }
            var mainDiv = document.createElement("div");
            var innderDiv = document.createElement("div");
            var shadeDiv = document.createElement("div");

            mainDiv.setAttribute("class", "da-slider-main da-slider-" + self.id);
            innderDiv.setAttribute("class", "da-slider-inner");
            shadeDiv.setAttribute("class", "da-slider-shade");

            innderDiv.appendChild(self.dom);
            mainDiv.appendChild(innderDiv);
            mainDiv.appendChild(shadeDiv);

            $("body")[0].appendChild(mainDiv);

            self.container = mainDiv;
            self.inner = innderDiv;
            self.shade = shadeDiv;

            switch (self.opts.direction) {
                case "t":
                case "top":
                    self.top = self.opts.top || 0;
                    self.left = self.opts.left || 0;
                    self.width = self.opts.width || "100%";
                    self.height = self.opts.height || "30%";
                    self.translate = "0,-100%,0";
                    break;
                case "b":
                case "bottom":
                    self.bottom = self.opts.bottom || 0;
                    self.left = self.opts.left || 0;
                    self.width = self.opts.width || "100%";
                    self.height = self.opts.height || "30%";
                    self.translate = "0,100%,0";
                    break;
                case "r":
                case "right":
                    self.bottom = self.opts.bottom || 0;
                    self.right = self.opts.right || 0;
                    self.width = self.opts.width || "30%";
                    self.height = self.opts.height || self.autoHeight() + "px";
                    self.translate = "100%,0,0";
                    break;
                default:
                    self.bottom = self.opts.bottom || 0;
                    self.left = self.opts.left || 0;
                    self.width = self.opts.width || "30%";
                    self.height = self.opts.height || self.autoHeight() + "px";
                    self.translate = "-100%,0,0"
            }

            mainDiv.style.display = "none";
            mainDiv.style.position = "fixed";
            mainDiv.style.top = "0";
            mainDiv.style.left = "0";
            mainDiv.style.width = "100%";
            mainDiv.style.height = "100%";
            mainDiv.style.zIndex = self.opts.zIndex + 1;

            innderDiv.style.position = "absolute";
            innderDiv.style.top = self.top;
            innderDiv.style.bottom = self.bottom;
            innderDiv.style.left = self.left;
            innderDiv.style.right = self.right;
            innderDiv.style.backgroundColor = self.opts.background;
            innderDiv.style.transform = "translate3d(" + self.translate + ")";
            innderDiv.style.webkitTransition = "all .2s ease-out";
            innderDiv.style.transition = "all .2s ease-out";
            innderDiv.style.zIndex = self.opts.zIndex + 2;
            innderDiv.style.boxShadow = '1px 1px 5px #ccc';
            innderDiv.style.overflowY = 'auto';
            innderDiv.style.padding = '10px';

            shadeDiv.style.width = "100%";
            shadeDiv.style.height = "100%";
            shadeDiv.style.opacity = "0";
            if (self.opts.shade !== false) {
                shadeDiv.style.backgroundColor = self.opts.shade || "rgb(0, 0, 0, 0.3)";
            }
            shadeDiv.style.zIndex = self.opts.zIndex;
            shadeDiv.style.webkitTransition = "all .2s ease-out";
            shadeDiv.style.transition = "all .2s ease-out";
            shadeDiv.style.webkitBackfaceVisibility = "hidden";

            self.resize();
            self.addListeners();
        },

        resize: function () {
            var self = this,
                d = this.opts.direction,
                map = {'t': 1, 'top': 1, 'b': 1, 'bottom': 1},
                dom = this.inner;

            if (! this.opts.height && ! (d in map)) {
                self.height = this.autoHeight() + "px";
                dom.style.height = '100%'
            }
            if (this.isSmallScreen() && ! (d in map)) {
                self.width = '100%';
                dom.style.width = '100%'
            }

            $(dom).slimScroll({
                width: '99.7%',
                height: self.height,
            });

            $(this.container).find('.slimScrollDiv').css({
                bottom: self.bottom,
                top: self.top,
                right: self.right,
                left: self.left,
                position: 'absolute',
                width: self.width,
                height: self.height,
                // 'box-shadow': '1px 1px 5px #ccc',
            });
        },

        autoHeight: function () {
            return document.documentElement.clientHeight
                - (this.opts.hasTopNavbar ? (this.isSmallScreen() ? 120 : 60) : 0)
        },

        toggle: function () {
            this.opened ? this.close() : this.open();
        },

        open: function () {
            var self = this;
            self.container.style.display = "block";
            self.opened = true;
            setTimeout(function () {
                self.inner.style.transform = "translate3d(0,0,0)";
                self.inner.style.webkitTransform = "translate3d(0,0,0)";
                self.shade.style.opacity = 0.5
            }, 30);
            if (self.opts.time) {
                self.timer = setTimeout(function () {
                    self.close()
                }, self.opts.time)
            }
        },

        close: function () {
            var self = this;
            self.timer && clearTimeout(self.timer);
            self.inner.style.webkitTransform = "translate3d(" + self.translate + ")";
            self.inner.style.transform = "translate3d(" + self.translate + ")";
            self.shade.style.opacity = 0;
            self.opened = false;

            setTimeout(function () {
                self.container.style.display = "none";
                self.timer = null;
                self.opts.callback && self.opts.callback()
            }, 300)
        },

        destroy: function () {
            this.container.remove();
        },

        onClick: function (dom, callback) {
            this.addEvent(dom, (this.isMobile() ? "touchend" : "click"), callback)
        },

        addListeners: function () {
            var self = this;
            self.addEvent(self.shade, "touchmove", function (f) {
                f.preventDefault()
            });
            self.onClick(self.shade, function (f) {
                if (self.opts.closeShade) {
                    self.close()
                }
            });

            $(window).resize(function () {
                self.resize()
            })
        }
    };

    LA.Slider = Slider
})(jQuery);