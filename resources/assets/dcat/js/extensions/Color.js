
export default class Color {
    constructor(Dcat) {
        let colors = Dcat.config.colors || {},
            _this = this;

        // 颜色转亮
        colors.lighten = _this.lighten.bind(_this);

        // 颜色转暗
        colors.darken = (color, amt) => {
            return _this.lighten(color, -amt)
        };

        // 颜色透明度设置
        colors.alpha = (color, alpha) => {
            let results = colors.toRBG(color);

            return `rgba(${results[0]}, ${results[1]}, ${results[2]}, ${alpha})`;
        };

        // 16进制颜色转化成10进制
        colors.toRBG = (color, amt) => {
            if (color[0] === '#') {
                color = color.slice(1);
            }

            return _this.toRBG(color, amt);
        };

        Dcat.color = colors;
    }

    lighten(color, amt) {
        let hasPrefix = false;

        if (color[0] === '#') {
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
