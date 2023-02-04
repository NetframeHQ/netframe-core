(function (global, factory) {
  typeof exports === 'object' && typeof module !== 'undefined' ? module.exports = factory() :
  typeof define === 'function' && define.amd ? define(factory) :
  (global = typeof globalThis !== 'undefined' ? globalThis : global || self, global.autoComplete = factory());
}(this, (function () { 'use strict';

  function ownKeys(object, enumerableOnly) {
    var keys = Object.keys(object);

    if (Object.getOwnPropertySymbols) {
      var symbols = Object.getOwnPropertySymbols(object);

      if (enumerableOnly) {
        symbols = symbols.filter(function (sym) {
          return Object.getOwnPropertyDescriptor(object, sym).enumerable;
        });
      }

      keys.push.apply(keys, symbols);
    }

    return keys;
  }

  function _objectSpread2(target) {
    for (var i = 1; i < arguments.length; i++) {
      var source = arguments[i] != null ? arguments[i] : {};

      if (i % 2) {
        ownKeys(Object(source), true).forEach(function (key) {
          _defineProperty(target, key, source[key]);
        });
      } else if (Object.getOwnPropertyDescriptors) {
        Object.defineProperties(target, Object.getOwnPropertyDescriptors(source));
      } else {
        ownKeys(Object(source)).forEach(function (key) {
          Object.defineProperty(target, key, Object.getOwnPropertyDescriptor(source, key));
        });
      }
    }

    return target;
  }

  function _typeof(obj) {
    "@babel/helpers - typeof";

    if (typeof Symbol === "function" && typeof Symbol.iterator === "symbol") {
      _typeof = function (obj) {
        return typeof obj;
      };
    } else {
      _typeof = function (obj) {
        return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj;
      };
    }

    return _typeof(obj);
  }

  function _defineProperty(obj, key, value) {
    if (key in obj) {
      Object.defineProperty(obj, key, {
        value: value,
        enumerable: true,
        configurable: true,
        writable: true
      });
    } else {
      obj[key] = value;
    }

    return obj;
  }

  function _toConsumableArray(arr) {
    return _arrayWithoutHoles(arr) || _iterableToArray(arr) || _unsupportedIterableToArray(arr) || _nonIterableSpread();
  }

  function _arrayWithoutHoles(arr) {
    if (Array.isArray(arr)) return _arrayLikeToArray(arr);
  }

  function _iterableToArray(iter) {
    if (typeof Symbol !== "undefined" && iter[Symbol.iterator] != null || iter["@@iterator"] != null) return Array.from(iter);
  }

  function _unsupportedIterableToArray(o, minLen) {
    if (!o) return;
    if (typeof o === "string") return _arrayLikeToArray(o, minLen);
    var n = Object.prototype.toString.call(o).slice(8, -1);
    if (n === "Object" && o.constructor) n = o.constructor.name;
    if (n === "Map" || n === "Set") return Array.from(o);
    if (n === "Arguments" || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(n)) return _arrayLikeToArray(o, minLen);
  }

  function _arrayLikeToArray(arr, len) {
    if (len == null || len > arr.length) len = arr.length;

    for (var i = 0, arr2 = new Array(len); i < len; i++) arr2[i] = arr[i];

    return arr2;
  }

  function _nonIterableSpread() {
    throw new TypeError("Invalid attempt to spread non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method.");
  }

  function _createForOfIteratorHelper(o, allowArrayLike) {
    var it = typeof Symbol !== "undefined" && o[Symbol.iterator] || o["@@iterator"];

    if (!it) {
      if (Array.isArray(o) || (it = _unsupportedIterableToArray(o)) || allowArrayLike && o && typeof o.length === "number") {
        if (it) o = it;
        var i = 0;

        var F = function () {};

        return {
          s: F,
          n: function () {
            if (i >= o.length) return {
              done: true
            };
            return {
              done: false,
              value: o[i++]
            };
          },
          e: function (e) {
            throw e;
          },
          f: F
        };
      }

      throw new TypeError("Invalid attempt to iterate non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method.");
    }

    var normalCompletion = true,
        didErr = false,
        err;
    return {
      s: function () {
        it = it.call(o);
      },
      n: function () {
        var step = it.next();
        normalCompletion = step.done;
        return step;
      },
      e: function (e) {
        didErr = true;
        err = e;
      },
      f: function () {
        try {
          if (!normalCompletion && it.return != null) it.return();
        } finally {
          if (didErr) throw err;
        }
      }
    };
  }

  var select$1 = function select(element) {
    return typeof element === "string" ? document.querySelector(element) : element();
  };
  var create = function create(tag, options) {
    var el = typeof tag === "string" ? document.createElement(tag) : tag;
    for (var key in options) {
      var val = options[key];
      if (key === "inside") {
        val.append(el);
      } else if (key === "dest") {
        select$1(val[0]).insertAdjacentElement(val[1], el);
      } else if (key === "around") {
        var ref = val;
        ref.parentNode.insertBefore(el, ref);
        el.append(ref);
        if (ref.getAttribute("autofocus") != null) ref.focus();
      } else if (key in el) {
        el[key] = val;
      } else {
        el.setAttribute(key, val);
      }
    }
    return el;
  };
  var getQuery = function getQuery(field) {
    return field instanceof HTMLInputElement || field instanceof HTMLTextAreaElement ? field.value : field.innerHTML;
  };
  var format = function format(value, diacritics) {
    value = value.toString().toLowerCase();
    return diacritics ? value.normalize("NFD").replace(/[\u0300-\u036f]/g, "").normalize("NFC") : value;
  };
  var debounce = function debounce(callback, duration) {
    var timer;
    return function () {
      clearTimeout(timer);
      timer = setTimeout(function () {
        return callback();
      }, duration);
    };
  };
  var checkTrigger = function checkTrigger(query, condition, threshold) {
    return condition ? condition(query) : query.length >= threshold;
  };
  var mark = function mark(value, cls) {
    return create("mark", _objectSpread2({
      innerHTML: value
    }, typeof cls === "string" && {
      "class": cls
    })).outerHTML;
  };

  var configure = (function (ctx) {
    var name = ctx.name,
        options = ctx.options,
        resultsList = ctx.resultsList,
        resultItem = ctx.resultItem;
    for (var option in options) {
      if (_typeof(options[option]) === "object") {
        if (!ctx[option]) ctx[option] = {};
        for (var subOption in options[option]) {
          ctx[option][subOption] = options[option][subOption];
        }
      } else {
        ctx[option] = options[option];
      }
    }
    ctx.selector = ctx.selector || "#" + name;
    resultsList.destination = resultsList.destination || ctx.selector;
    resultsList.id = resultsList.id || name + "_list_" + ctx.id;
    resultItem.id = resultItem.id || name + "_result";
    ctx.input = select$1(ctx.selector);
  });

  var eventEmitter = (function (name, ctx) {
    ctx.input.dispatchEvent(new CustomEvent(name, {
      bubbles: true,
      detail: ctx.feedback,
      cancelable: true
    }));
  });

  var search = (function (query, record, options) {
    var _ref = options || {},
        mode = _ref.mode,
        diacritics = _ref.diacritics,
        highlight = _ref.highlight;
    var nRecord = format(record, diacritics);
    record = record.toString();
    query = format(query, diacritics);
    if (mode === "loose") {
      query = query.replace(/ /g, "");
      var qLength = query.length;
      var cursor = 0;
      var match = Array.from(record).map(function (character, index) {
        if (cursor < qLength && nRecord[index] === query[cursor]) {
          character = highlight ? mark(character, highlight) : character;
          cursor++;
        }
        return character;
      }).join("");
      if (cursor === qLength) return match;
    } else {
      var _match = nRecord.indexOf(query);
      if (~_match) {
        query = record.substring(_match, _match + query.length);
        _match = highlight ? record.replace(query, mark(query, highlight)) : record;
        return _match;
      }
    }
  });

  var getData = function getData(ctx, query) {
    return new Promise(function ($return, $error) {
      var data;
      data = ctx.data;
      if (data.cache && data.store) return $return();
      return new Promise(function ($return, $error) {
        if (typeof data.src === "function") {
          return data.src(query).then($return, $error);
        }
        return $return(data.src);
      }).then(function ($await_4) {
        try {
          ctx.feedback = data.store = $await_4;
          eventEmitter("response", ctx);
          return $return();
        } catch ($boundEx) {
          return $error($boundEx);
        }
      }, $error);
    });
  };
  var findMatches = function findMatches(query, ctx) {
    var data = ctx.data,
        searchEngine = ctx.searchEngine;
    var matches = [];
    data.store.forEach(function (value, index) {
      var find = function find(key) {
        var record = key ? value[key] : value;
        var match = typeof searchEngine === "function" ? searchEngine(query, record) : search(query, record, {
          mode: searchEngine,
          diacritics: ctx.diacritics,
          highlight: ctx.resultItem.highlight
        });
        if (!match) return;
        var result = {
          match: match,
          value: value
        };
        if (key) result.key = key;
        matches.push(result);
      };
      if (data.keys) {
        var _iterator = _createForOfIteratorHelper(data.keys),
            _step;
        try {
          for (_iterator.s(); !(_step = _iterator.n()).done;) {
            var key = _step.value;
            find(key);
          }
        } catch (err) {
          _iterator.e(err);
        } finally {
          _iterator.f();
        }
      } else {
        find();
      }
    });
    if (data.filter) matches = data.filter(matches);
    var results = matches.slice(0, ctx.resultsList.maxResults);
    ctx.feedback = {
      query: query,
      matches: matches,
      results: results
    };
    eventEmitter("results", ctx);
  };

  var Expand = "aria-expanded";
  var Active = "aria-activedescendant";
  var Selected = "aria-selected";
  var feedback = function feedback(ctx, index) {
    ctx.feedback.selection = _objectSpread2({
      index: index
    }, ctx.feedback.results[index]);
  };
  var render = function render(ctx) {
    var resultsList = ctx.resultsList,
        list = ctx.list,
        resultItem = ctx.resultItem,
        feedback = ctx.feedback;
    var matches = feedback.matches,
        results = feedback.results;
    ctx.cursor = -1;
    list.innerHTML = "";
    if (matches.length || resultsList.noResults) {
      var fragment = new DocumentFragment();
      results.forEach(function (result, index) {
        var element = create(resultItem.tag, _objectSpread2({
          id: "".concat(resultItem.id, "_").concat(index),
          role: "option",
          innerHTML: result.match,
          inside: fragment
        }, resultItem["class"] && {
          "class": resultItem["class"]
        }));
        if (resultItem.element) resultItem.element(element, result);
      });
      list.append(fragment);
      if (resultsList.element) resultsList.element(list, feedback);
      open(ctx);
    } else {
      close(ctx);
    }
  };
  var open = function open(ctx) {
    if (ctx.isOpen) return;
    (ctx.wrapper || ctx.input).setAttribute(Expand, true);
    ctx.list.removeAttribute("hidden");
    ctx.isOpen = true;
    eventEmitter("open", ctx);
  };
  var close = function close(ctx) {
    if (!ctx.isOpen) return;
    (ctx.wrapper || ctx.input).setAttribute(Expand, false);
    ctx.input.setAttribute(Active, "");
    ctx.list.setAttribute("hidden", "");
    ctx.isOpen = false;
    eventEmitter("close", ctx);
  };
  var goTo = function goTo(index, ctx) {
    var resultItem = ctx.resultItem;
    var results = ctx.list.getElementsByTagName(resultItem.tag);
    var cls = resultItem.selected ? resultItem.selected.split(" ") : false;
    if (ctx.isOpen && results.length) {
      var _results$index$classL;
      var state = ctx.cursor;
      if (index >= results.length) index = 0;
      if (index < 0) index = results.length - 1;
      ctx.cursor = index;
      if (state > -1) {
        var _results$state$classL;
        results[state].removeAttribute(Selected);
        if (cls) (_results$state$classL = results[state].classList).remove.apply(_results$state$classL, _toConsumableArray(cls));
      }
      results[index].setAttribute(Selected, true);
      if (cls) (_results$index$classL = results[index].classList).add.apply(_results$index$classL, _toConsumableArray(cls));
      ctx.input.setAttribute(Active, results[ctx.cursor].id);
      ctx.list.scrollTop = results[index].offsetTop - ctx.list.clientHeight + results[index].clientHeight + 5;
      ctx.feedback.cursor = ctx.cursor;
      feedback(ctx, index);
      eventEmitter("navigate", ctx);
    }
  };
  var next = function next(ctx) {
    goTo(ctx.cursor + 1, ctx);
  };
  var previous = function previous(ctx) {
    goTo(ctx.cursor - 1, ctx);
  };
  var select = function select(ctx, event, index) {
    index = index >= 0 ? index : ctx.cursor;
    if (index < 0) return;
    ctx.feedback.event = event;
    feedback(ctx, index);
    eventEmitter("selection", ctx);
    close(ctx);
  };
  var click = function click(event, ctx) {
    var itemTag = ctx.resultItem.tag.toUpperCase();
    var items = Array.from(ctx.list.querySelectorAll(itemTag));
    var item = event.target.closest(itemTag);
    if (item && item.nodeName === itemTag) {
      select(ctx, event, items.indexOf(item));
    }
  };
  var navigate = function navigate(event, ctx) {
    switch (event.keyCode) {
      case 40:
      case 38:
        event.preventDefault();
        event.keyCode === 40 ? next(ctx) : previous(ctx);
        break;
      case 13:
        if (!ctx.submit) event.preventDefault();
        if (ctx.cursor >= 0) select(ctx, event);
        break;
      case 9:
        if (ctx.resultsList.tabSelect && ctx.cursor >= 0) select(ctx, event);
        break;
      case 27:
        ctx.input.value = "";
        close(ctx);
        break;
    }
  };

  function start (ctx, q) {
    var _this = this;
    return new Promise(function ($return, $error) {
      var queryVal, condition;
      queryVal = q || getQuery(ctx.input);
      queryVal = ctx.query ? ctx.query(queryVal) : queryVal;
      condition = checkTrigger(queryVal, ctx.trigger, ctx.threshold);
      if (condition) {
        return getData(ctx, queryVal).then(function ($await_2) {
          try {
            if (ctx.feedback instanceof Error) return $return();
            findMatches(queryVal, ctx);
            if (ctx.resultsList) render(ctx);
            return $If_1.call(_this);
          } catch ($boundEx) {
            return $error($boundEx);
          }
        }, $error);
      } else {
        close(ctx);
        return $If_1.call(_this);
      }
      function $If_1() {
        return $return();
      }
    });
  }

  var eventsManager = function eventsManager(events, callback) {
    for (var element in events) {
      for (var event in events[element]) {
        callback(element, event);
      }
    }
  };
  var addEvents = function addEvents(ctx) {
    var events = ctx.events;
    var run = debounce(function () {
      return start(ctx);
    }, ctx.debounce);
    var publicEvents = ctx.events = _objectSpread2({
      input: _objectSpread2({}, events && events.input)
    }, ctx.resultsList && {
      list: events ? _objectSpread2({}, events.list) : {}
    });
    var privateEvents = {
      input: {
        input: function input() {
          run();
        },
        keydown: function keydown(event) {
          navigate(event, ctx);
        },
        blur: function blur() {
          close(ctx);
        }
      },
      list: {
        mousedown: function mousedown(event) {
          event.preventDefault();
        },
        click: function click$1(event) {
          click(event, ctx);
        }
      }
    };
    eventsManager(privateEvents, function (element, event) {
      if (!ctx.resultsList && event !== "input") return;
      if (publicEvents[element][event]) return;
      publicEvents[element][event] = privateEvents[element][event];
    });
    eventsManager(publicEvents, function (element, event) {
      ctx[element].addEventListener(event, publicEvents[element][event]);
    });
  };
  var removeEvents = function removeEvents(ctx) {
    eventsManager(ctx.events, function (element, event) {
      ctx[element].removeEventListener(event, ctx.events[element][event]);
    });
  };

  function init (ctx) {
    var _this = this;
    return new Promise(function ($return, $error) {
      var placeHolder, resultsList, parentAttrs;
      placeHolder = ctx.placeHolder;
      resultsList = ctx.resultsList;
      parentAttrs = {
        role: "combobox",
        "aria-owns": resultsList.id,
        "aria-haspopup": true,
        "aria-expanded": false
      };
      create(ctx.input, _objectSpread2(_objectSpread2({
        "aria-controls": resultsList.id,
        "aria-autocomplete": "both"
      }, placeHolder && {
        placeholder: placeHolder
      }), !ctx.wrapper && _objectSpread2({}, parentAttrs)));
      if (ctx.wrapper) ctx.wrapper = create("div", _objectSpread2({
        around: ctx.input,
        "class": ctx.name + "_wrapper"
      }, parentAttrs));
      if (resultsList) ctx.list = create(resultsList.tag, _objectSpread2({
        dest: [resultsList.destination, resultsList.position],
        id: resultsList.id,
        role: "listbox",
        hidden: "hidden"
      }, resultsList["class"] && {
        "class": resultsList["class"]
      }));
      addEvents(ctx);
      if (ctx.data.cache) {
        return getData(ctx).then(function ($await_2) {
          try {
            return $If_1.call(_this);
          } catch ($boundEx) {
            return $error($boundEx);
          }
        }, $error);
      }
      function $If_1() {
        eventEmitter("init", ctx);
        return $return();
      }
      return $If_1.call(_this);
    });
  }

  function extend (autoComplete) {
    var prototype = autoComplete.prototype;
    prototype.init = function () {
      init(this);
    };
    prototype.start = function (query) {
      start(this, query);
    };
    prototype.unInit = function () {
      if (this.wrapper) {
        var parentNode = this.wrapper.parentNode;
        parentNode.insertBefore(this.input, this.wrapper);
        parentNode.removeChild(this.wrapper);
      }
      removeEvents(this);
    };
    prototype.open = function () {
      open(this);
    };
    prototype.close = function () {
      close(this);
    };
    prototype.goTo = function (index) {
      goTo(index, this);
    };
    prototype.next = function () {
      next(this);
    };
    prototype.previous = function () {
      previous(this);
    };
    prototype.select = function (index) {
      select(this, null, index);
    };
    prototype.search = function (query, record, options) {
      return search(query, record, options);
    };
  }

  function autoComplete(config) {
    this.options = config;
    this.id = autoComplete.instances = (autoComplete.instances || 0) + 1;
    this.name = "autoComplete";
    this.wrapper = 1;
    this.threshold = 1;
    this.debounce = 0;
    this.resultsList = {
      position: "afterend",
      tag: "ul",
      maxResults: 5
    };
    this.resultItem = {
      tag: "li"
    };
    configure(this);
    extend.call(this, autoComplete);
    init(this);
  }

  return autoComplete;

})));

var t,e;t=this,e=function(){"use strict";function t(t,e){var n=Object.keys(t);if(Object.getOwnPropertySymbols){var r=Object.getOwnPropertySymbols(t);e&&(r=r.filter((function(e){return Object.getOwnPropertyDescriptor(t,e).enumerable}))),n.push.apply(n,r)}return n}function e(e){for(var n=1;n<arguments.length;n++){var i=null!=arguments[n]?arguments[n]:{};n%2?t(Object(i),!0).forEach((function(t){r(e,t,i[t])})):Object.getOwnPropertyDescriptors?Object.defineProperties(e,Object.getOwnPropertyDescriptors(i)):t(Object(i)).forEach((function(t){Object.defineProperty(e,t,Object.getOwnPropertyDescriptor(i,t))}))}return e}function n(t){return(n="function"==typeof Symbol&&"symbol"==typeof Symbol.iterator?function(t){return typeof t}:function(t){return t&&"function"==typeof Symbol&&t.constructor===Symbol&&t!==Symbol.prototype?"symbol":typeof t})(t)}function r(t,e,n){return e in t?Object.defineProperty(t,e,{value:n,enumerable:!0,configurable:!0,writable:!0}):t[e]=n,t}function i(t){return function(t){if(Array.isArray(t))return s(t)}(t)||function(t){if("undefined"!=typeof Symbol&&null!=t[Symbol.iterator]||null!=t["@@iterator"])return Array.from(t)}(t)||o(t)||function(){throw new TypeError("Invalid attempt to spread non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method.")}()}function o(t,e){if(t){if("string"==typeof t)return s(t,e);var n=Object.prototype.toString.call(t).slice(8,-1);return"Object"===n&&t.constructor&&(n=t.constructor.name),"Map"===n||"Set"===n?Array.from(t):"Arguments"===n||/^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(n)?s(t,e):void 0}}function s(t,e){(null==e||e>t.length)&&(e=t.length);for(var n=0,r=new Array(e);n<e;n++)r[n]=t[n];return r}var u=function(t){return"string"==typeof t?document.querySelector(t):t()},a=function(t,e){var n="string"==typeof t?document.createElement(t):t;for(var r in e){var i=e[r];if("inside"===r)i.append(n);else if("dest"===r)u(i[0]).insertAdjacentElement(i[1],n);else if("around"===r){var o=i;o.parentNode.insertBefore(n,o),n.append(o),null!=o.getAttribute("autofocus")&&o.focus()}else r in n?n[r]=i:n.setAttribute(r,i)}return n},c=function(t,e){return t=t.toString().toLowerCase(),e?t.normalize("NFD").replace(/[\u0300-\u036f]/g,"").normalize("NFC"):t},l=function(t,n){return a("mark",e({innerHTML:t},"string"==typeof n&&{class:n})).outerHTML},f=function(t,e){e.input.dispatchEvent(new CustomEvent(t,{bubbles:!0,detail:e.feedback,cancelable:!0}))},p=function(t,e,n){var r=n||{},i=r.mode,o=r.diacritics,s=r.highlight,u=c(e,o);if(e=e.toString(),t=c(t,o),"loose"===i){var a=(t=t.replace(/ /g,"")).length,f=0,p=Array.from(e).map((function(e,n){return f<a&&u[n]===t[f]&&(e=s?l(e,s):e,f++),e})).join("");if(f===a)return p}else{var d=u.indexOf(t);if(~d)return t=e.substring(d,d+t.length),d=s?e.replace(t,l(t,s)):e}},d=function(t,e){return new Promise((function(n,r){var i;return(i=t.data).cache&&i.store?n():new Promise((function(t,n){return"function"==typeof i.src?i.src(e).then(t,n):t(i.src)})).then((function(e){try{return t.feedback=i.store=e,f("response",t),n()}catch(t){return r(t)}}),r)}))},h=function(t,e){var n=e.data,r=e.searchEngine,i=[];n.store.forEach((function(s,u){var a=function(n){var o=n?s[n]:s,u="function"==typeof r?r(t,o):p(t,o,{mode:r,diacritics:e.diacritics,highlight:e.resultItem.highlight});if(u){var a={match:u,value:s};n&&(a.key=n),i.push(a)}};if(n.keys){var c,l=function(t,e){var n="undefined"!=typeof Symbol&&t[Symbol.iterator]||t["@@iterator"];if(!n){if(Array.isArray(t)||(n=o(t))||e&&t&&"number"==typeof t.length){n&&(t=n);var r=0,i=function(){};return{s:i,n:function(){return r>=t.length?{done:!0}:{done:!1,value:t[r++]}},e:function(t){throw t},f:i}}throw new TypeError("Invalid attempt to iterate non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method.")}var s,u=!0,a=!1;return{s:function(){n=n.call(t)},n:function(){var t=n.next();return u=t.done,t},e:function(t){a=!0,s=t},f:function(){try{u||null==n.return||n.return()}finally{if(a)throw s}}}}(n.keys);try{for(l.s();!(c=l.n()).done;)a(c.value)}catch(t){l.e(t)}finally{l.f()}}else a()})),n.filter&&(i=n.filter(i));var s=i.slice(0,e.resultsList.maxResults);e.feedback={query:t,matches:i,results:s},f("results",e)},m="aria-expanded",b="aria-activedescendant",y="aria-selected",v=function(t,n){t.feedback.selection=e({index:n},t.feedback.results[n])},g=function(t){t.isOpen||((t.wrapper||t.input).setAttribute(m,!0),t.list.removeAttribute("hidden"),t.isOpen=!0,f("open",t))},w=function(t){t.isOpen&&((t.wrapper||t.input).setAttribute(m,!1),t.input.setAttribute(b,""),t.list.setAttribute("hidden",""),t.isOpen=!1,f("close",t))},O=function(t,e){var n=e.resultItem,r=e.list.getElementsByTagName(n.tag),o=!!n.selected&&n.selected.split(" ");if(e.isOpen&&r.length){var s,u,a=e.cursor;t>=r.length&&(t=0),t<0&&(t=r.length-1),e.cursor=t,a>-1&&(r[a].removeAttribute(y),o&&(u=r[a].classList).remove.apply(u,i(o))),r[t].setAttribute(y,!0),o&&(s=r[t].classList).add.apply(s,i(o)),e.input.setAttribute(b,r[e.cursor].id),e.list.scrollTop=r[t].offsetTop-e.list.clientHeight+r[t].clientHeight+5,e.feedback.cursor=e.cursor,v(e,t),f("navigate",e)}},A=function(t){O(t.cursor+1,t)},k=function(t){O(t.cursor-1,t)},L=function(t,e,n){(n=n>=0?n:t.cursor)<0||(t.feedback.event=e,v(t,n),f("selection",t),w(t))};function j(t,n){var r=this;return new Promise((function(i,o){var s,u;return s=n||((u=t.input)instanceof HTMLInputElement||u instanceof HTMLTextAreaElement?u.value:u.innerHTML),function(t,e,n){return e?e(t):t.length>=n}(s=t.query?t.query(s):s,t.trigger,t.threshold)?d(t,s).then((function(n){try{return t.feedback instanceof Error?i():(h(s,t),t.resultsList&&function(t){var n=t.resultsList,r=t.list,i=t.resultItem,o=t.feedback,s=o.matches,u=o.results;if(t.cursor=-1,r.innerHTML="",s.length||n.noResults){var c=new DocumentFragment;u.forEach((function(t,n){var r=a(i.tag,e({id:"".concat(i.id,"_").concat(n),role:"option",innerHTML:t.match,inside:c},i.class&&{class:i.class}));i.element&&i.element(r,t)})),r.append(c),n.element&&n.element(r,o),g(t)}else w(t)}(t),c.call(r))}catch(t){return o(t)}}),o):(w(t),c.call(r));function c(){return i()}}))}var S=function(t,e){for(var n in t)for(var r in t[n])e(n,r)},T=function(t){var n,r,i,o=t.events,s=(n=function(){return j(t)},r=t.debounce,function(){clearTimeout(i),i=setTimeout((function(){return n()}),r)}),u=t.events=e({input:e({},o&&o.input)},t.resultsList&&{list:o?e({},o.list):{}}),a={input:{input:function(){s()},keydown:function(e){!function(t,e){switch(t.keyCode){case 40:case 38:t.preventDefault(),40===t.keyCode?A(e):k(e);break;case 13:e.submit||t.preventDefault(),e.cursor>=0&&L(e,t);break;case 9:e.resultsList.tabSelect&&e.cursor>=0&&L(e,t);break;case 27:e.input.value="",w(e)}}(e,t)},blur:function(){w(t)}},list:{mousedown:function(t){t.preventDefault()},click:function(e){!function(t,e){var n=e.resultItem.tag.toUpperCase(),r=Array.from(e.list.querySelectorAll(n)),i=t.target.closest(n);i&&i.nodeName===n&&L(e,t,r.indexOf(i))}(e,t)}}};S(a,(function(e,n){(t.resultsList||"input"===n)&&(u[e][n]||(u[e][n]=a[e][n]))})),S(u,(function(e,n){t[e].addEventListener(n,u[e][n])}))};function E(t){var n=this;return new Promise((function(r,i){var o,s,u;if(o=t.placeHolder,u={role:"combobox","aria-owns":(s=t.resultsList).id,"aria-haspopup":!0,"aria-expanded":!1},a(t.input,e(e({"aria-controls":s.id,"aria-autocomplete":"both"},o&&{placeholder:o}),!t.wrapper&&e({},u))),t.wrapper&&(t.wrapper=a("div",e({around:t.input,class:t.name+"_wrapper"},u))),s&&(t.list=a(s.tag,e({dest:[s.destination,s.position],id:s.id,role:"listbox",hidden:"hidden"},s.class&&{class:s.class}))),T(t),t.data.cache)return d(t).then((function(t){try{return c.call(n)}catch(t){return i(t)}}),i);function c(){return f("init",t),r()}return c.call(n)}))}function x(t){var e=t.prototype;e.init=function(){E(this)},e.start=function(t){j(this,t)},e.unInit=function(){if(this.wrapper){var t=this.wrapper.parentNode;t.insertBefore(this.input,this.wrapper),t.removeChild(this.wrapper)}var e;S((e=this).events,(function(t,n){e[t].removeEventListener(n,e.events[t][n])}))},e.open=function(){g(this)},e.close=function(){w(this)},e.goTo=function(t){O(t,this)},e.next=function(){A(this)},e.previous=function(){k(this)},e.select=function(t){L(this,null,t)},e.search=function(t,e,n){return p(t,e,n)}}return function t(e){this.options=e,this.id=t.instances=(t.instances||0)+1,this.name="autoComplete",this.wrapper=1,this.threshold=1,this.debounce=0,this.resultsList={position:"afterend",tag:"ul",maxResults:5},this.resultItem={tag:"li"},function(t){var e=t.name,r=t.options,i=t.resultsList,o=t.resultItem;for(var s in r)if("object"===n(r[s]))for(var a in t[s]||(t[s]={}),r[s])t[s][a]=r[s][a];else t[s]=r[s];t.selector=t.selector||"#"+e,i.destination=i.destination||t.selector,i.id=i.id||e+"_list_"+t.id,o.id=o.id||e+"_result",t.input=u(t.selector)}(this),x.call(this,t),E(this)}},"object"==typeof exports&&"undefined"!=typeof module?module.exports=e():"function"==typeof define&&define.amd?define(e):(t="undefined"!=typeof globalThis?globalThis:t||self).autoComplete=e();

const SearchBar = new autoComplete({
    data: {
      src: async () => {
        var query = document.querySelector("#search-input").value;
        var headers = new Headers();
        headers.append("Accept", "application/json");
        var init = { method: 'GET', headers: headers };
        var source = await fetch(`/search?query=${query}`, init);
        var data = await source.json();
        return data;
      },
      key: ["label"],
      cache: false
    },
    selector: "#search-input",
    observer: true,
    threshold: 1,
    debounce: 300,
    searchEngine: (query, record) => { return record; },
    resultsList: {
        destination: "#search-input",
        position: "afterend",
        element: "ul",
        className: "nf-autocomplete"
    },
    maxResults: 5,
    resultItem: {
        content: (data, source) => {
            source.innerHTML = `
              ${data.value.thumb}
              <span class="text">${data.match}</span>
            `;
        },
        element: "li"
    },
    noResults: (dataFeedback, generateList) => {
        generateList(autoCompleteJS, dataFeedback, dataFeedback.results);
        const result = document.createElement("li");
        result.setAttribute("class", "no_result");
        result.setAttribute("tabindex", "1");
        result.innerHTML = `<span>Found No Results for "${dataFeedback.query}"</span>`;
        document.querySelector(`#${autoCompleteJS.resultsList.idName}`).appendChild(result);
    },
    onSelection: feedback => {
        window.document.location = feedback.selection.value.value; 
    }
});