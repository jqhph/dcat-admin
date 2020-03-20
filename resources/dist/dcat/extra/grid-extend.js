/******/ (function(modules) { // webpackBootstrap
/******/ 	// The module cache
/******/ 	var installedModules = {};
/******/
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/
/******/ 		// Check if module is in cache
/******/ 		if(installedModules[moduleId]) {
/******/ 			return installedModules[moduleId].exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = installedModules[moduleId] = {
/******/ 			i: moduleId,
/******/ 			l: false,
/******/ 			exports: {}
/******/ 		};
/******/
/******/ 		// Execute the module function
/******/ 		modules[moduleId].call(module.exports, module, module.exports, __webpack_require__);
/******/
/******/ 		// Flag the module as loaded
/******/ 		module.l = true;
/******/
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/
/******/
/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = modules;
/******/
/******/ 	// expose the module cache
/******/ 	__webpack_require__.c = installedModules;
/******/
/******/ 	// define getter function for harmony exports
/******/ 	__webpack_require__.d = function(exports, name, getter) {
/******/ 		if(!__webpack_require__.o(exports, name)) {
/******/ 			Object.defineProperty(exports, name, { enumerable: true, get: getter });
/******/ 		}
/******/ 	};
/******/
/******/ 	// define __esModule on exports
/******/ 	__webpack_require__.r = function(exports) {
/******/ 		if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 			Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 		}
/******/ 		Object.defineProperty(exports, '__esModule', { value: true });
/******/ 	};
/******/
/******/ 	// create a fake namespace object
/******/ 	// mode & 1: value is a module id, require it
/******/ 	// mode & 2: merge all properties of value into the ns
/******/ 	// mode & 4: return value when already ns object
/******/ 	// mode & 8|1: behave like require
/******/ 	__webpack_require__.t = function(value, mode) {
/******/ 		if(mode & 1) value = __webpack_require__(value);
/******/ 		if(mode & 8) return value;
/******/ 		if((mode & 4) && typeof value === 'object' && value && value.__esModule) return value;
/******/ 		var ns = Object.create(null);
/******/ 		__webpack_require__.r(ns);
/******/ 		Object.defineProperty(ns, 'default', { enumerable: true, value: value });
/******/ 		if(mode & 2 && typeof value != 'string') for(var key in value) __webpack_require__.d(ns, key, function(key) { return value[key]; }.bind(null, key));
/******/ 		return ns;
/******/ 	};
/******/
/******/ 	// getDefaultExport function for compatibility with non-harmony modules
/******/ 	__webpack_require__.n = function(module) {
/******/ 		var getter = module && module.__esModule ?
/******/ 			function getDefault() { return module['default']; } :
/******/ 			function getModuleExports() { return module; };
/******/ 		__webpack_require__.d(getter, 'a', getter);
/******/ 		return getter;
/******/ 	};
/******/
/******/ 	// Object.prototype.hasOwnProperty.call
/******/ 	__webpack_require__.o = function(object, property) { return Object.prototype.hasOwnProperty.call(object, property); };
/******/
/******/ 	// __webpack_public_path__
/******/ 	__webpack_require__.p = "/";
/******/
/******/
/******/ 	// Load entry module and return exports
/******/ 	return __webpack_require__(__webpack_require__.s = 1);
/******/ })
/************************************************************************/
/******/ ({

/***/ "./resources/assets/dcat/extra/grid-extend.js":
/*!****************************************************!*\
  !*** ./resources/assets/dcat/extra/grid-extend.js ***!
  \****************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

(function (w, $) {
  var Dcat = w.Dcat;

  function Tree(opts) {
    this.options = $.extend({
      button: null,
      table: null,
      url: '',
      perPage: '',
      showNextPage: '',
      pageQueryName: '',
      parentIdQueryName: '',
      tierQueryName: '',
      showIcon: 'fa-angle-right',
      hideIcon: 'fa-angle-down',
      loadMoreIcon: '<i class="feather icon-more-horizontal"></i>'
    }, opts);
    this.key = this.tier = this.row = this.data = this._req = null;

    this._init();
  }

  Tree.prototype = {
    _init: function _init() {
      this._bindClick();
    },
    _bindClick: function _bindClick() {
      var _this = this,
          opts = _this.options;

      $(opts.button).off('click').click(function () {
        if (_this._req) {
          return;
        }

        var $this = $(this),
            _i = $("i", this),
            shown = _i.hasClass(opts.showIcon);

        _this.key = $this.data('key');
        _this.tier = $this.data('tier');
        _this.row = $this.closest('tr');

        if ($this.data('inserted') == '0') {
          _this._request(1);

          $this.data('inserted', 1);
        }

        _i.toggleClass(opts.showIcon + ' ' + opts.hideIcon);

        var children = [];
        getChildren(_this.row.nextAll(), _this.row).forEach(function (v) {
          if (getTier(v) !== _this.tier + 1) {
            return;
          }

          children.push(v);
          shown ? $(v).show() : $(v).hide();
        });
        children.forEach(function (v) {
          if (shown) {
            return;
          }

          var icon = $(v).find('a[data-tier=' + getTier(v) + '] i');

          if (icon.hasClass(opts.hideIcon)) {
            icon.parent().click();
          }
        });
      });
    },
    _request: function _request(page, after) {
      var _this = this,
          row = _this.row,
          key = _this.key,
          tier = _this.tier,
          tableSelector = _this.options.table;

      if (_this._req) {
        return;
      }

      _this._req = 1;
      Dcat.loading();
      var data = {
        _token: Dcat.token
      };
      data[_this.options.parentIdQueryName] = key;
      data[_this.options.tierQueryName] = tier + 1;
      data[_this.options.pageQueryName.replace(':key', key)] = page;
      $.ajax({
        url: _this.options.url,
        type: 'GET',
        data: data,
        headers: {
          'X-PJAX': true
        },
        success: function success(resp) {
          after && after();
          Dcat.loading(false);
          _this._req = 0; // 获取最后一行

          var children = getChildren(row.nextAll(), row);
          row = children.length ? $(children.pop()) : row;

          var _body = $('<div>' + resp + '</div>'),
              _tbody = _body.find(tableSelector + ' tbody'),
              lastPage = _body.find('last-page').text(),
              nextPage = _body.find('next-page').text(); // 标记子节点行


          _tbody.find('tr').each(function (_, v) {
            $(v).attr('data-tier', tier + 1);
          });

          if (_this.options.showNextPage && _tbody.find('tr').length == _this.options.perPage && lastPage >= page) {
            // 加载更多
            var loadMore = $("<tr data-tier=\"".concat(tier + 1, "\" data-page=\"").concat(nextPage, "\">\n                                <td colspan=\"").concat(row.find('td').length, "\" align=\"center\" style=\"cursor: pointer\"> \n                                    <a href=\"#\" style=\"font-size: 1.5rem\">").concat(_this.options.loadMoreIcon, "</a> \n                                </td>\n                            </tr>"));
            row.after(loadMore); // 加载更多

            loadMore.click(function () {
              var _t = $(this);

              _this._request(_t.data('page'), function () {
                _t.remove();
              });
            });
          } // 附加子节点


          row.after(_tbody.html()); // 附加子节点js脚本以及触发子节点js脚本执行

          _body.find('script').each(function (_, v) {
            row.after(v);
          }); // 主动触发ready事件，执行子节点附带的js脚本


          Dcat.triggerReady();
        },
        error: function error(a, b, c) {
          after && after();
          Dcat.loading(false);
          _this._req = 0;

          if (a.status != 404) {
            Dcat.handleAjaxError(a, b, c);
          }
        }
      });
    }
  };

  function Orderable(opts) {
    this.options = $.extend({
      button: null,
      url: ''
    }, opts);
    this.direction = this.key = this.tier = this.row = this._req = null;

    this._init();
  }

  Orderable.prototype = {
    _init: function _init() {
      this._bindClick();
    },
    _bindClick: function _bindClick() {
      var _this = this;

      $(_this.options.button).off('click').click(function () {
        if (_this._req) {
          return;
        }

        _this._req = 1;
        Dcat.loading();
        var $this = $(this);
        _this.key = $this.data('id');
        _this.direction = $this.data('direction');
        _this.row = $this.closest('tr');
        _this.tier = getTier(_this.row);

        _this._request();
      });
    },
    _request: function _request() {
      var _this = this,
          key = _this.key,
          row = _this.row,
          tier = _this.tier,
          direction = _this.direction,
          prevAll = row.prevAll(),
          nextAll = row.nextAll(),
          prev = row.prevAll('tr').first(),
          next = row.nextAll('tr').first();

      $.ajax({
        type: 'POST',
        url: _this.options.url.replace(':key', key),
        data: {
          _method: 'PUT',
          _token: Dcat.token,
          _orderable: direction
        },
        success: function success(data) {
          Dcat.loading(false);
          _this._req = 0;

          if (!data.status) {
            return data.message && Dcat.warning(data.message);
          }

          Dcat.success(data.message);

          if (direction) {
            var prevRow = sibling(prevAll, tier);

            if (swapable(prevRow, tier) && prev.length && getTier(prev) >= tier) {
              prevRow.before(row); // 把所有子节点上移

              getChildren(nextAll, row).forEach(function (v) {
                prevRow.before(v);
              });
            }
          } else {
            var nextRow = sibling(nextAll, tier),
                nextRowChildren = nextRow ? getChildren(nextRow.nextAll(), nextRow) : [];

            if (swapable(nextRow, tier) && next.length && getTier(next) >= tier) {
              nextAll = row.nextAll();

              if (nextRowChildren.length) {
                nextRow = $(nextRowChildren.pop());
              } // 把所有子节点下移


              var all = [];
              getChildren(nextAll, row).forEach(function (v) {
                all.unshift(v);
              });
              all.forEach(function (v) {
                nextRow.after(v);
              });
              nextRow.after(row);
            }
          }
        },
        error: function error(a, b, c) {
          _this._req = 0;
          Dcat.loading(false);
          Dcat.handleAjaxError(a, b, c);
        }
      });
    }
  };

  function isTr(v) {
    return $(v).prop('tagName').toLocaleLowerCase() === 'tr';
  }

  function getTier(v) {
    return parseInt($(v).data('tier') || 0);
  }

  function isChildren(parent, child) {
    return getTier(child) > getTier(parent);
  }

  function getChildren(all, parent) {
    var arr = [],
        isBreak = false,
        firstTr;
    all.each(function (_, v) {
      // 过滤非tr标签
      if (!isTr(v) || isBreak) return;
      firstTr || (firstTr = $(v)); // 非连续的子节点

      if (firstTr && !isChildren(parent, firstTr)) {
        return;
      }

      if (isChildren(parent, v)) {
        arr.push(v);
      } else {
        isBreak = true;
      }
    });
    return arr;
  }

  function swapable(_o, tier) {
    if (_o && _o.length && tier === getTier(_o)) {
      return true;
    }
  }

  function sibling(all, tier) {
    var next;
    all.each(function (_, v) {
      if (getTier(v) === tier && !next && isTr(v)) {
        next = $(v);
      }
    });
    return next;
  }

  Dcat.grid.Tree = function (opts) {
    return new Tree(opts);
  };

  Dcat.grid.Orderable = function (opts) {
    return new Orderable(opts);
  };
})(window, jQuery);

/***/ }),

/***/ 1:
/*!**********************************************************!*\
  !*** multi ./resources/assets/dcat/extra/grid-extend.js ***!
  \**********************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__(/*! F:\p\dcat-admin-github\dcat-admin\resources\assets\dcat\extra\grid-extend.js */"./resources/assets/dcat/extra/grid-extend.js");


/***/ })

/******/ });