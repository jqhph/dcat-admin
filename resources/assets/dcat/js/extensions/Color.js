
export default class Color {
    constructor(Dcat) {
        let colors = Dcat.config.colors || {},
            newInstance = $.extend(colors),
            _this = this;

        // 获取颜色
        newInstance.get = function (color) {
            return colors[color] || color;
        };

        // 颜色转亮
        newInstance.lighten = function (color, amt) {
            return _this.lighten(newInstance.get(color), amt)
        };

        // 颜色转暗
        newInstance.darken = (color, amt) => {
            return newInstance.lighten(color, -amt)
        };

        // 颜色透明度设置
        newInstance.alpha = (color, alpha) => {
            let results = newInstance.toRBG(color);

            return `rgba(${results[0]}, ${results[1]}, ${results[2]}, ${alpha})`;
        };

        // 16进制颜色转化成10进制
        newInstance.toRBG = (color, amt) => {
            if (color.indexOf('#') === 0) {
                color = color.slice(1);
            }

            return _this.toRBG(newInstance.get(color), amt);
        };

        // 获取所有颜色
        newInstance.all = function () {
            return colors;
        };

        Dcat.color = newInstance;
    }

    lighten(color, amt) {
        let hasPrefix = false;

        if (color.indexOf('#') === 0) {
            color = color.slice(1);

            hasPrefix = true;
        }

        let colors = this.toRBG(color, amt);

        return (hasPrefix ? '#' : '') + (colors[2] | (colors[1] << 8) | (colors[0] << 16)).toString(16);
    }

    toRBG(color, amt) {
        let format = (value) => {
            if (value > 255) {
                return 255;
            }
            if (value < 0) {
                return 0;
            }

            return value;
        };

        amt = amt || 0;

        let num = parseInt(color, 16),
            red = format((num >> 16) + amt),
            blue = format(((num >> 8) & 0x00FF) + amt),
            green = format((num & 0x0000FF) + amt);

        return [red, blue, green]
    }
}
