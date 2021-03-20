
import debounce from './Debounce'

export default class Helpers {
    constructor(Dcat) {
        Dcat.helpers = this;

        this.dcat = Dcat;

        // 延迟触发，消除重复触发
        this.debounce = debounce;
    }

    /**
     * 获取json对象或数组的长度
     *
     * @param obj
     * @returns {number}
     */
    len(obj) {
        if (typeof obj !== 'object') {
            return 0;
        }
        let i, len = 0;

        for(i in obj) {
            len += 1;
        }

        return len;
    }

    /**
     * 判断变量或key是否存在
     *
     * @param _var
     * @param key
     * @returns {boolean}
     */
    isset(_var, key) {
        let isset = (typeof _var !== 'undefined' && _var !== null);

        if (typeof key === 'undefined') {
            return isset;
        }

        return isset && typeof _var[key] !== 'undefined';
    };

    empty(obj, key) {
        return !(this.isset(obj, key) && obj[key]);
    };

    /**
     * 根据key获取对象的值，支持获取多维数据
     *
     * @param arr
     * @param key
     * @param def
     * @returns {null|*}
     */
    get(arr, key, def) {
        def = null;

        if (this.len(arr) < 1) {
            return def;
        }

        key = String(key).split('.');

        for (var i = 0; i < key.length; i++) {
            if (this.isset(arr, key[i])) {
                arr = arr[key[i]];
            } else {
                return def;
            }
        }

        return arr;
    }

    /**
     * 判断key是否存在
     *
     * @param arr
     * @param key
     * @returns {def|boolean}
     */
    has(arr, key) {
        if (this.len(arr) < 1) return def;
        key = String(key).split('.');

        for (var i = 0; i < key.length; i++) {
            if (this.isset(arr, key[i])) {
                arr = arr[key[i]];
            } else {
                return false;
            }
        }

        return true;
    }

    /**
     * 判断元素是否在对象中存在
     *
     * @param arr
     * @param val
     * @param strict
     * @returns {boolean}
     */
    inObject(arr, val, strict) {
        if (this.len(arr) < 1) {
            return false;
        }

        for (var i in arr) {
            if (strict) {
                if (val === arr[i]) {
                    return true;
                }
                continue
            }

            if (val == arr[i]) {
                return true;
            }
        }
        return false;
    }

    // 判断对象是否相等
    equal(array, array2, strict) {
        if (!array || !array2) {
            return false;
        }

        let len1 = this.len(array),
            len2 = this.len(array2), i;

        if (len1 !== len2) {
            return false;
        }

        for (i in array) {
            if (! this.isset(array2, i)) {
                return false;
            }

            if (array[i] === null && array2[i] === null) {
                return true;
            }

            if (typeof array[i] === 'object' && typeof array2[i] === 'object') {
                if (! this.equal(array[i], array2[i], strict)) {
                    return false;
                }
                continue;
            }

            if (strict) {
                if (array[i] !== array2[i]) {
                    return false;
                }
            } else {
                if (array[i] != array2[i]) {
                    return false;
                }
            }

        }
        return true;
    }

    // 字符串替换
    replace(str, replace, subject) {
        if (!str) {
            return str;
        }

        return str.replace(
            new RegExp(replace, "g"),
            subject
        );
    }

    /**
     * 生成随机字符串
     *
     * @returns {string}
     */
    random(len) {
        return Math.random().toString(12).substr(2, len || 16)
    }

    // 预览图片
    previewImage(src, width, title) {
        let Dcat = this.dcat,
            img = new Image(),
            win = this.isset(window.top) ? top : window,
            clientWidth = Math.ceil(win.screen.width * 0.6),
            clientHeight = Math.ceil(win.screen.height * 0.8);

        img.style.display = 'none';
        img.style.height = 'auto';
        img.style.width = width || '100%';
        img.src = src;

        document.body.appendChild(img);

        Dcat.loading();
        img.onload = function () {
            Dcat.loading(false);
            let srcw = this.width,
                srch = this.height,
                width = srcw > clientWidth ? clientWidth : srcw,
                height = Math.ceil(width * (srch/srcw));

            height = height > clientHeight ? clientHeight : height;

            title = title || src.split('/').pop();

            if (title.length > 50) {
                title = title.substr(0, 50) + '...';
            }

            layer.open({
                type: 1,
                shade: 0.2,
                title: false,
                maxmin: false,
                shadeClose: true,
                closeBtn: 2,
                content: $(img),
                area: [width+'px', (height) + 'px'],
                skin: 'layui-layer-nobg',
                end: function () {
                    document.body.removeChild(img);
                }
            });
        };
        img.onerror = function () {
            Dcat.loading(false);
            Dcat.error(Dcat.lang.trans('no_preview'))
        };
    }

    // 异步加载
    asyncRender(url, done, error) {
        let Dcat = this.dcat;

        $.ajax(url).then(function (data) {
            done(
                Dcat.assets.resolveHtml(data, Dcat.triggerReady).render()
            );
        }, function (a, b, c) {
            if (error) {
                if (error(a, b, c) === false) {
                    return false;
                }
            }

            Dcat.handleAjaxError(a, b, c);
        })
    }

    /**
     * 联动多个字段.
     *
     * @param _this
     * @param options
     */
    loadFields(_this, options) {
        let refreshOptions = function(url, target) {
            Dcat.loading();

            $.ajax(url).then(function(data) {
                Dcat.loading(false);
                target.find("option").remove();

                $.map(data, function (d) {
                    target.append(new Option(d[options.textField], d[options.idField], false, false));
                });

                $(target).val(String(target.data('value')).split(',')).trigger('change');
            });
        };

        let promises = [],
            values = [];

        if (! options.values) {
            $(_this).find('option:selected').each(function () {
                if (String(this.value) === '0' || this.value) {
                    values.push(this.value)
                }
            });
        } else {
            values = options.values;
            if (typeof values === 'string') {
                values = [values];
            }
        }

        if (! values.length) {
            return;
        }

        options.fields.forEach(function(field, index){
            var target = $(_this).closest(options.group).find('.' + options.fields[index]);

            if (! values.length) {
                return;
            }
            promises.push(refreshOptions(options.urls[index] + (options.urls[index].match(/\?/)?'&':'?') + "q="+ values.join(','), target));
        });

        $.when(promises).then(function() {});
    }
}
