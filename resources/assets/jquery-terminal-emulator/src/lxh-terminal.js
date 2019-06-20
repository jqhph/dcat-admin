(function (w) {
    var translator,
        lastLineClass = '.terminal-last-line',
        windowClass = '.terminal-window',
        helpText = 'Type "help" to get a supporting command list.',
        unkownText = 'Unknown Command.',
        success = 'success',
        info = 'info',
        system = 'system',
        error = 'error',
        warning = 'warning',
        primary = 'primary',
        purple = 'purple',
        content = 'content',
        label = 'label',
        style = 'style',
        undefined = 'undefined';

    function Terminal(options) {
        var _t = this,
            def = {
                title: 'Lxh Terminal',
                locale: 'en',
                langs: {en: {}, 'zh-CN': {}},
                start: 'Welcome to %s.',
                end: [
                    {content: helpText, style: system}
                ],
                messages: [],
                commands: {},
                element: '.terminal-container',
                loadingTime: 500,
                width: '100%',
                height: '500px',
                histories: []
            },
            defCommands = {
                help: {
                    handle: function (input, resolve, reject) {
                        var commands = _t.commands(), i, content = '', builder = _t.builder;

                        for (i in commands) {
                            if (i == 'help') continue;
                            content += _t.builder.line('---> ' + translator.trans(commands[i].description), success, i)
                        }

                        resolve(builder.row(builder.cmd(translator.trans('Here is a list of supporting command.'))) + content);
                    }
                },
                readme: {
                    description: 'About this project.',
                    handle: [
                        { content: 'This is a component that emulates a command terminal in JQuery' }
                    ]
                },
                document: {
                    description: 'Document of this project.',
                    handle: [
                        {content: '<a href="https://github.com/jqhph/jquery-terminal-emulator" target="_blank">https://github.com/jqhph/jquery-terminal-emulator</a>', style: primary}
                    ]
                },
                version: {
                    description: 'Return this project version.',
                    handle: [
                        {content: 'v1.0.5', style: system}
                    ]
                }
            },
            historyIndex = -1;

        /**
         * 初始化操作
         */
        function init() {
            options = options || {};

            // 合并数组
            options = merge(def, options);
            options.commands = merge(defCommands, options.commands);

            this.$el = this.element();

            this.translator = translator;

            // 翻译器
            translator.set(options.langs[options.locale]);

            this.builder = new Builder(this);

            var supportingCommands = {};
            for (var i in options.commands) {
                supportingCommands[i] = 1;
            }
            this.supportingCommands = supportingCommands;

            this.render();
        }

        /**
         *
         * @returns {*|string}
         */
        this.element = function () {
            var e = options.element || def.element;

            return typeof e == 'object' ? e : $(e);
        };

        this.addHistory = function (command) {
            if (command) {
                options.histories.unshift(command)
            }
        };

        this.prevHistory = function () {
            if (historyIndex >= options.histories.length - 1) {
                historyIndex = options.histories.length - 2;
            }

            historyIndex ++;
            return options.histories[historyIndex] || '';
        };

        this.nextHistory = function () {
            if (historyIndex < 1) {
                historyIndex = -1;
                return '';
            }
            historyIndex --;
            return options.histories[historyIndex] || '';
        };

        this.resetHistoryIndex = function () {
            historyIndex = -1;
        };

        /**
         * 消息
         *
         * @returns {Array|Terminal.messages|*|$.validator.defaults.messages|{}|defaults.messages}
         */
        this.messages = function () {
            return options.messages;
        };

        /**
         * 获取命令数据
         *
         * @returns {*}
         */
        this.commands = function () {
            return options.commands;
        };

        /**
         * 增加一条命令
         *
         * @returns {*}
         */
        this.command = function (name, description, handle) {
            options.commands[name] = {description: description, handle: handle};

            _t.supportingCommands[name] = 1;
        };

        /**
         * 获取配置
         *
         * @param key
         * @param def
         * @returns {*}
         */
        this.option = function (key, def) {
            if (! key) return options;
            return options[key] || def;
        };

        function merge(_old, _new) {
            for (var i in _old) {
                if (typeof _new[i] == undefined) {
                    _new[i] = _old[i];
                }
            }

            return _new;
        }

        // 初始化
        init.call(this);
    }

    function terminal_expand() {
        var _t;

        Terminal.prototype = {
            /**
             * 渲染terminal界面
             */
            render: function () {
                var headerStart = '<div class="terminal"><div style="position:relative">', // style="position:relative"
                    footerEnd = '</div></div>',
                    bodyStart = '<div class="terminal-w-c" style="position:absolute;top:0;left:0;right:0;overflow:auto;margin-top:25px;z-index:1;max-height:' // position:absolute;
                        + this.option('height') + '" ><div class="terminal-window" >',
                    bodyEnd = '</div></div>';
                _t = this;

                this.html = headerStart
                    + this.builder.header()
                    + bodyStart
                    + this.builder.body()
                    + bodyEnd
                    + footerEnd;

                this.$el.html(this.html);
                bind(this);

                setTimeout(function () {
                    var deg = 'rotate(720deg)';
                    _t.$el.find('.terminal').css({
                        width: _t.option('width')
                    });
                    _t.$el.find('.terminal-window').css({'min-height': _t.option('height')});
                },2);

                render_rows(_t.messages(), function () {
                    render_rows(_t.option('end'), function (has) {
                        if (has) {
                            _t.loading(function (_t) {
                                _t.append(_t.builder.lastLine());
                            });
                        } else {
                            _t.append(_t.builder.lastLine());
                        }
                    });
                }, true);

                this.afterRender();
            },

            afterRender: function () {
                _t.$el.find('.terminal').css({
                    "height": _t.option('height'),
                    "margin-bottom": "25px"
                });
            },

            scrollTop: function () {
                var cls = '.end-input', t = this;
                t.$el.find(cls).remove();
                t.$win.append('<input class="end-input" value="test" style="opacity:0;height:0;line-height:0">');
                // t.$el.find(cls).focus();
            },

            /**
             * loading效果
             *
             */
            loading: function (callback) {
                var _t = this, builder = this.builder, text = '...', counter = 0;
                _t.append(
                    builder.row(
                        builder.span('loading', text)
                    )
                );
                if (!callback) {
                    return;
                }
                setTimeout(function () {
                    _t.done();
                    callback(_t);
                }, _t.option('loadingTime'));
            },

            // 移除loading效果
            done: function () {
                this.$el.find('.loading').remove();
            },

            /**
             * 追加内容
             *
             * @param content
             * @param focus 是否自动选中光标
             */
            append: function (content, focus) {
                this.$win.append(content);
                this.scrollTop();

                bind(this, focus);
            },

            /**
             * 输入内容
             *
             * @param content
             */
            input: function (content) {
                var builder = this.builder;
                content = this.run(content);

                if (content === null) return;

                if (content !== false) {
                    content = builder.line(content);
                } else {
                    content = ''
                }

                this.append(
                    content + builder.lastLine(), true
                );
            },

            /**
             * 运行命令
             *
             * @param input
             */
            run: function (input) {
                var inputs = input.split(' ');
                var _n = [], command, i, builder = this.builder, commands = this.commands();
                for (i in inputs) {
                    if (inputs[i]) {
                        _n.push(inputs[i]);
                    }
                }

                command = _n.shift();
                if (! command) {
                    return '';
                }

                if (typeof this.supportingCommands[command] == undefined) {
                    this.append(
                        builder.line(input) +
                        builder.line(translator.trans(unkownText), error) +
                        builder.line(translator.trans(helpText), system)
                    );

                    return false;
                }

                var res = builder.line(input);
                command = commands[command];

                switch (typeof command.handle) {
                    case undefined:
                        return res;
                    case 'object':
                        return res + build_lines(command.handle);
                    case 'function':
                        async_run(_n, command.handle).then(function (success) {
                            _t.done();
                            _t.append(
                                builder.line(res + build_lines(success)) + builder.lastLine(), true
                            );
                        }, function (err) {
                            _t.done();
                            _t.append(
                                builder.line(res) +
                                builder.line(translator.trans('Something went wrong!'), error) +
                                builder.systemline(build_lines(err)) +
                                builder.lastLine(),
                                true
                            );
                        });
                        return null;

                    default:
                        return res + command.handle;
                }

                function build_lines(rows) {
                    if (typeof rows != 'object') return rows;
                    var contents = '';
                    for (i in rows) {
                        contents += builder.systemline(rows[i][content], rows[i][style], rows[i][label]);
                    }
                    return contents;
                }
            }
        };

        /**
         * 基于$.Deferred的异步任务处理
         *
         * @param handle
         * @returns {*}
         */
        function async_run(input, handle) {
            var p = $.Deferred();

            _t.loading();

            setTimeout(function () {
                handle(input, p.resolve, p.reject)
            }, 1);

            return p.promise();
        }

        function scrollBottom(terminal)
        {
            var offset = $('.end-input').offset();
            if (offset) {
                terminal.$el.find('.terminal-w-c').animate({
                    scrollTop: offset.top
                }, {duration: 1});
            }
        }

        /**
         * @param terminal
         * @param focus 是否自动选中光标
         */
        function bind(terminal, focus) {
            var events = {
                // 光标选中以及移动到最后
                focus: function (e) {
                    var $input = terminal.$el.find('.input-box'),
                        $last = terminal.$el.find(lastLineClass);

                    focus && $input.focus();
                    scrollBottom(terminal);

                    $input.off('click').click(function () {
                        var input = this;
                        scrollBottom(terminal);
                        focus && $input.focus();

                        setTimeout(function () {
                            move_cursor(input, 1000);
                        }, 30);
                    });
                    $input.off('keyup').on('keyup', function (e) {
                        var val = this.value;

                        switch (e.keyCode) {
                            case 13:
                                // 输出内容
                                $last.remove();
                                terminal.addHistory(val);
                                terminal.input(val);
                                break;
                            case 38:
                                val = terminal.prevHistory();
                                $input.val(val);
                                return show_for_input($input, val);
                                break;
                            case 40:
                                val = terminal.nextHistory();
                                $input.val(val);
                                return show_for_input($input, val);
                                break;
                            default:
                                // 显示内容
                                return show_for_input($input, val);
                        }
                    });
                }
            };
            ///////////////////////////////////////////////////
            terminal.$win = terminal.$el.find(windowClass);

            // 光标选中以及移动到最后
            terminal.$el.off('click').on('click', events.focus.bind(this)).click();
        }

        function show_for_input($input, val) {
            return $input.parent().find('.__content').html(val);
        }

        // 移动光标
        function move_cursor(input, len) {
            if (document.selection) {
                var sel = input.createTextRange();
                sel.moveStart('character', len);
                sel.collapse();
                sel.select();
            } else if (typeof input.selectionStart == 'number'
                && typeof input.selectionEnd == 'number') {
                input.selectionStart = input.selectionEnd = len;
            }
        }


        /**
         * 渲染消息
         *
         * @param rows
         * @param next
         * @param unprefix
         */
        function render_rows(rows, next, unprefix) {
            var message = rows.shift();

            if (message) {
                _t.loading(render);
            } else {
                next();
            }

            function render() {
                _t.append(
                    _t.builder.line(translator.trans(message[content]), message[style], message[label], unprefix)
                );

                if (message = rows.shift()) {
                    _t.loading(render);
                } else {
                    next(true);
                }
            }
        }
    }

    /**
     * 翻译器
     *
     * @constructor
     */
    function Translator() {
        var packages = {};

        /**
         * 初始化操作
         */
        function init() {
            packages = packages || {};
        }

        /**
         * 获取语言包
         *
         * @param key
         * @returns {*}
         */
        this.get = function (key) {
            return String(packages[key] || key);
        };

        /**
         *
         * @param _packages
         */
        this.set = function (_packages) {
            packages = _packages || packages;
        };

        // 初始化
        init.call(this);
    }

    Translator.prototype = {
        trans: function (text, replaces) {
            if (typeof text == 'object') return text;

            replaces = replaces && typeof replaces.splice == 'function' ? replaces : [replaces];

            var i = -1;
            return this.get(text).replace(/%s/ig, function ($match, position) {
                i++;
                return replaces[i] || ''
            });
        }
    };

    function Builder(terminal) {
        var title = terminal.option('title');

        function is_color(value) {
            if (! value) return value;
            if (value.indexOf('#') == -1 && value.indexOf('rgb') == -1) {
                return 'class="'+ value +'"';
            }

            return 'style="color:'+value+'"';
        }

        return {
            header: function () {
                return '<div class="header"><h4>' + title + '</h4><ul class="shell-dots"><li class="red"></li><li class="yellow"></li><li class="green"></li></ul></div>';
            },

            body: function () {
                var text = translator.trans(terminal.option('start'), title);

                return this.row(text)
                    + this.row(
                        this.prompt() + this.cmd('cd ' + title)
                    );
            },

            // 最后一行
            lastLine: function () {
                return this.row(function (builder) {
                    return builder.prompt(title+'/ ') + builder.span('content') + builder.span('cursor', '&nbsp;') + builder.input();
                }, lastLineClass.replace('.', ''));
            },

            /**
             * 渲染列表
             *
             * @param list
             */
            list: function (list, title) {
                if (!list || typeof list.push == undefined) {
                    return list;
                }

                title = title || '';
                var lis = '', i;

                for (i in list) {
                    lis += '<li><pre>' + list[i] + '</pre></li>';
                }

                if (title) {
                    title = this.span('', title);
                }

                return title + '<ul>' + lis + '</ul>';
            },

            /**
             * 显示一行数据
             *
             * @param content 要输出的内容
             * @param type  success info warning error system
             * @param label
             * @returns {*}
             */
            line: function (content, type, label, unprefix) {
                var prompt = '';
                if (! type && ! label && ! unprefix) {
                    prompt = this.prompt() + ' ';
                }

                // if (unprefix) prompt = time() + ' ' + prompt;

                if (type && !label) label = type;

                return this.row(
                    prompt + this.span(type, label) + ' ' + this.cmd(content)
                );
            },

            systemline: function (content, type, label) {
                if (type && !label) label = type;

                return this.row(
                    this.span(type, label) + ' ' + this.cmd(content)
                );
            },

            input: function () {
                return '<input type="text" class="input-box" />';
            },

            row: function (content, cls) {
                return '<p class="' + (cls||'') + '">' + value.call(this, content) + '</p>';
            },

            prompt: function (content) {
                return this.span('prompt', content);
            },

            cmd: function (content) {
                if (typeof content == 'object' && typeof content.list == 'object') {
                    if (typeof content.list.push != undefined) {
                        content = this.list(content.list, content.title);
                    }
                }

                return this.span('cmd', content);
            },

            success: function (content) {
                return this.span(success, content);
            },

            info: function (content) {
                return this.span(info, content);
            },

            warning: function (content) {
                return this.span(warning, content);
            },

            error: function (content) {
                return this.span(error, content);
            },

            system: function (content) {
                return this.span(system, content);
            },

            span: function (cls, content) {
                if (cls == 'content') {
                    cls = '__'+cls
                }
                return '<span '+ is_color(cls || '') +'>'+translator.trans(value.call(this, content))+'</span>'
            }

        };
    }

    function value(value) {
        if (typeof value == 'function') {
            return value(this);
        }
        return value || '';
    }

    function time() {
        return new Date().toLocaleTimeString().split('').splice(2).join('')
    }


    function init() {
        translator = new Translator();

        terminal_expand();

        w.LxhTerminal = Terminal;

        $.fn.lxhTerminal = function (options) {
            options = options || {};
            options.element = $(this);

            return new Terminal(options);
        };
    }

    init();

})(window);