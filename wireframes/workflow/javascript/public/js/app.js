(function(/*! Brunch !*/) {
  'use strict';

  var globals = typeof window !== 'undefined' ? window : global;
  if (typeof globals.require === 'function') return;

  var modules = {};
  var cache = {};

  var has = function(object, name) {
    return ({}).hasOwnProperty.call(object, name);
  };

  var expand = function(root, name) {
    var results = [], parts, part;
    if (/^\.\.?(\/|$)/.test(name)) {
      parts = [root, name].join('/').split('/');
    } else {
      parts = name.split('/');
    }
    for (var i = 0, length = parts.length; i < length; i++) {
      part = parts[i];
      if (part === '..') {
        results.pop();
      } else if (part !== '.' && part !== '') {
        results.push(part);
      }
    }
    return results.join('/');
  };

  var dirname = function(path) {
    return path.split('/').slice(0, -1).join('/');
  };

  var localRequire = function(path) {
    return function(name) {
      var dir = dirname(path);
      var absolute = expand(dir, name);
      return globals.require(absolute);
    };
  };

  var initModule = function(name, definition) {
    var module = {id: name, exports: {}};
    definition(module.exports, localRequire(name), module);
    var exports = cache[name] = module.exports;
    return exports;
  };

  var require = function(name) {
    var path = expand(name, '.');

    if (has(cache, path)) return cache[path];
    if (has(modules, path)) return initModule(path, modules[path]);

    var dirIndex = expand(path, './index');
    if (has(cache, dirIndex)) return cache[dirIndex];
    if (has(modules, dirIndex)) return initModule(dirIndex, modules[dirIndex]);

    throw new Error('Cannot find module "' + name + '"');
  };

  var define = function(bundle) {
    for (var key in bundle) {
      if (has(bundle, key)) {
        modules[key] = bundle[key];
      }
    }
  }

  globals.require = require;
  globals.require.define = define;
  globals.require.brunch = true;
})();

window.require.define({"chaplin/Application": function(exports, require, module) {
  var AppView, Chaplin, FluxMine;

  Chaplin = require('chaplin');

  AppView = require('chaplin/views/App');

  module.exports = FluxMine = (function() {

    function FluxMine() {
      var app;
      app = new AppView();
    }

    return FluxMine;

  })();
  
}});

window.require.define({"chaplin/initialize": function(exports, require, module) {
  var AssertException, FluxMine;

  AssertException = (function() {

    function AssertException(message) {
      this.message = message;
    }

    AssertException.prototype.toString = function() {
      return "AssertException: " + this.message;
    };

    return AssertException;

  })();

  this.assert = function(exp, message) {
    if (!exp) {
      throw new AssertException(message);
    }
  };

  FluxMine = require('chaplin/Application');

  $(function() {
    return window.App = new FluxMine();
  });
  
}});

window.require.define({"chaplin/templates/body": function(exports, require, module) {
  module.exports = function (__obj) {
    if (!__obj) __obj = {};
    var __out = [], __capture = function(callback) {
      var out = __out, result;
      __out = [];
      callback.call(this);
      result = __out.join('');
      __out = out;
      return __safe(result);
    }, __sanitize = function(value) {
      if (value && value.ecoSafe) {
        return value;
      } else if (typeof value !== 'undefined' && value != null) {
        return __escape(value);
      } else {
        return '';
      }
    }, __safe, __objSafe = __obj.safe, __escape = __obj.escape;
    __safe = __obj.safe = function(value) {
      if (value && value.ecoSafe) {
        return value;
      } else {
        if (!(typeof value !== 'undefined' && value != null)) value = '';
        var result = new String(value);
        result.ecoSafe = true;
        return result;
      }
    };
    if (!__escape) {
      __escape = __obj.escape = function(value) {
        return ('' + value)
          .replace(/&/g, '&amp;')
          .replace(/</g, '&lt;')
          .replace(/>/g, '&gt;')
          .replace(/"/g, '&quot;');
      };
    }
    (function() {
      (function() {
      
        __out.push('<div id="wrapper">\n    <header id="top">\n        <div class="inner">\n            <div class="account right">\n                Monsieur Tout-le-Monde <span>&#8226;</span> <a href="#">Logout</a>\n            </div>\n            <h1><strong>Flux:Mine</strong> - a workflow functionality prototype</h1>\n        </div>\n    </header>\n\n    <section id="middle">\n        <div id="widget">\n            <div class="wrap">\n                <div class="row">\n                    <div class="twelve columns">\n                        <h1>Upload a List</h1>\n                        <p>Select the type of list to create and either enter in a list\n                            of identifiers or upload identifiers from a file. A search will\n                            be performed for all the identifiers in your list.</p>\n                    </div>\n                </div>\n                <form class="row custom">\n                    <div class="six columns">\n                        <label>List of identifiers</label>\n                        <textarea></textarea>\n                    </div>\n                    <div class="six columns">\n                        <label>Identifier type</label>\n                        <select class="three">\n                            <option>Genes</option>\n                            <option>Proteins</option>\n                        </select>\n                    </div>\n                </form>\n                <div class="row twelve columns">\n                    <a class="button" href="#">Upload a list</span></a>\n                </div>\n            </div>\n        </div>\n\n        <aside id="sidebar">\n            <div class="wrap">\n                <ul>\n                    <li><a href="#">Global search</a></li>\n                    <li><a href="#">View a public list</a></li>\n                    <li><a href="#">Compare lists</a></li>\n                </ul>\n            </div>\n        </aside>\n    </section>\n</div>\n\n<footer id="bottom">\n    <div class="wrap">\n        <div id="history">\n            <a class="button" href="#">Show workflow view</a>\n        </div>\n    </div>\n</footer>');
      
      }).call(this);
      
    }).call(__obj);
    __obj.safe = __objSafe, __obj.escape = __escape;
    return __out.join('');
  }
}});

window.require.define({"chaplin/views/App": function(exports, require, module) {
  var AppView, Chaplin,
    __hasProp = {}.hasOwnProperty,
    __extends = function(child, parent) { for (var key in parent) { if (__hasProp.call(parent, key)) child[key] = parent[key]; } function ctor() { this.constructor = child; } ctor.prototype = parent.prototype; child.prototype = new ctor(); child.__super__ = parent.prototype; return child; };

  Chaplin = require('chaplin');

  module.exports = AppView = (function(_super) {

    __extends(AppView, _super);

    function AppView() {
      return AppView.__super__.constructor.apply(this, arguments);
    }

    AppView.prototype.container = 'body';

    AppView.prototype.containerMethod = 'html';

    AppView.prototype.autoRender = true;

    AppView.prototype.getTemplateFunction = function() {
      return require('chaplin/templates/body');
    };

    return AppView;

  })(Chaplin.View);
  
}});

