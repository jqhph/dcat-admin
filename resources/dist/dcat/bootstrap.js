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
/******/ 	return __webpack_require__(__webpack_require__.s = 2);
/******/ })
/************************************************************************/
/******/ ({

/***/ "./resources/assets/dcat/dcat-bootstrap.js":
/*!*************************************************!*\
  !*** ./resources/assets/dcat/dcat-bootstrap.js ***!
  \*************************************************/
/*! no exports provided */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _dcat__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./dcat */ "./resources/assets/dcat/dcat.js");


window.CreateDcat = function (config) {
  return new _dcat__WEBPACK_IMPORTED_MODULE_0__["default"](config);
};

/***/ }),

/***/ "./resources/assets/dcat/dcat.js":
/*!***************************************!*\
  !*** ./resources/assets/dcat/dcat.js ***!
  \***************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return Dcat; });
function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } }

function _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); return Constructor; }

var $ = jQuery,
    _pjaxResponded = false,
    bootingCallbacks = [],
    formCallbacks = {
  before: [],
  success: [],
  error: []
};

var Dcat = /*#__PURE__*/function () {
  function Dcat(config) {
    _classCallCheck(this, Dcat);

    this.withConfig(config);
  }

  _createClass(Dcat, [{
    key: "booting",
    value: function booting(callback) {
      bootingCallbacks.push(callback);
      return this;
    }
  }, {
    key: "boot",
    value: function boot() {
      bootingCallbacks.forEach(function (callback) {
        return callback(Vue, router, store);
      });
      bootingCallbacks = [];
    }
  }, {
    key: "liftOff",
    value: function liftOff() {
      this.boot();
    }
  }, {
    key: "ready",
    value: function ready(callback, _window) {
      if (!_window || _window === window) {
        if (!_pjaxResponded) {
          return $(callback);
        }

        return $(document).one('pjax:done', callback);
      }

      var proxy = function proxy(e) {
        _window.$(_window.$('#pjax-container')).one('pjax:done', proxy);

        callback(e);
      };

      _window.Dcat.ready(proxy);
    }
  }, {
    key: "withConfig",
    value: function withConfig(config) {
      this.config = config;
      this.withLang(config['lang']);
      this.withToken(config['token']);
      return this;
    }
  }, {
    key: "withToken",
    value: function withToken(token) {
      token && (this.token = token);
      return this;
    }
  }, {
    key: "withLang",
    value: function withLang(lang) {
      lang && (this.lang = lang);
      return this;
    }
  }, {
    key: "pjaxResponded",
    value: function pjaxResponded() {
      _pjaxResponded = true;
      return this;
    }
  }, {
    key: "submiting",
    value: function submiting(callback) {
      typeof callback == 'function' && formCallbacks.before.push(callback);
      return this;
    }
  }, {
    key: "submitted",
    value: function submitted(success, error) {
      typeof success == 'function' && formCallbacks.success.push(success);
      typeof error == 'function' && formCallbacks.error.push(error);
      return this;
    }
  }]);

  return Dcat;
}();



/***/ }),

/***/ 2:
/*!*******************************************************!*\
  !*** multi ./resources/assets/dcat/dcat-bootstrap.js ***!
  \*******************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__(/*! D:\php-project\laravel\laraveladmin\github-test\pck-dcat-admin\dcat-admin\resources\assets\dcat\dcat-bootstrap.js */"./resources/assets/dcat/dcat-bootstrap.js");


/***/ })

/******/ });