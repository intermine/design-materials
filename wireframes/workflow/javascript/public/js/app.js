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
  var AppView, Chaplin, FluxMine, LandingView, SidebarView, Tool, ToolView, Workflow, WorkflowView;

  Chaplin = require('chaplin');

  LandingView = require('chaplin/views/Landing');

  AppView = require('chaplin/views/App');

  SidebarView = require('chaplin/views/Sidebar');

  ToolView = require('chaplin/views/Tool');

  WorkflowView = require('chaplin/views/Workflow');

  Workflow = require('chaplin/models/Workflow');

  Tool = require('chaplin/models/Tool');

  module.exports = FluxMine = (function() {

    function FluxMine() {
      var p, path, tool;
      path = window.location.pathname;
      if (path === '/') {
        new LandingView();
      } else {
        tool = ((function() {
          var _i, _len, _ref, _results;
          _ref = path.split('/').pop().split('-');
          _results = [];
          for (_i = 0, _len = _ref.length; _i < _len; _i++) {
            p = _ref[_i];
            _results.push(p[0].toUpperCase() + p.slice(1));
          }
          return _results;
        })()).join('');
        assert(tool === 'UploadList', "Unknown tool `" + tool + "`");
        new AppView();
        new WorkflowView({
          'collection': new Workflow()
        });
        new SidebarView();
        new ToolView({
          'model': new Tool({
            'name': tool
          })
        });
      }
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

window.require.define({"chaplin/models/Tool": function(exports, require, module) {
  var Chaplin, Tool,
    __hasProp = {}.hasOwnProperty,
    __extends = function(child, parent) { for (var key in parent) { if (__hasProp.call(parent, key)) child[key] = parent[key]; } function ctor() { this.constructor = child; } ctor.prototype = parent.prototype; child.prototype = new ctor(); child.__super__ = parent.prototype; return child; };

  Chaplin = require('chaplin');

  module.exports = Tool = (function(_super) {

    __extends(Tool, _super);

    function Tool() {
      return Tool.__super__.constructor.apply(this, arguments);
    }

    return Tool;

  })(Chaplin.Model);
  
}});

window.require.define({"chaplin/models/Workflow": function(exports, require, module) {
  var Chaplin, Workflow,
    __hasProp = {}.hasOwnProperty,
    __extends = function(child, parent) { for (var key in parent) { if (__hasProp.call(parent, key)) child[key] = parent[key]; } function ctor() { this.constructor = child; } ctor.prototype = parent.prototype; child.prototype = new ctor(); child.__super__ = parent.prototype; return child; };

  Chaplin = require('chaplin');

  module.exports = Workflow = (function(_super) {

    __extends(Workflow, _super);

    function Workflow() {
      return Workflow.__super__.constructor.apply(this, arguments);
    }

    return Workflow;

  })(Chaplin.Collection);
  
}});

window.require.define({"chaplin/templates/app": function(exports, require, module) {
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
      
        __out.push('<div id="wrapper">\n    <header id="top">\n        <div class="inner">\n            <div class="account right">\n                Monsieur Tout-le-Monde <span>&#8226;</span> <a href="#">Logout</a>\n            </div>\n            <h1><strong>Flux:Mine</strong> - a workflow functionality prototype</h1>\n        </div>\n    </header>\n\n    <section id="middle">\n        <div id="widget"></div>\n        <aside id="sidebar"></aside>\n    </section>\n</div>\n\n<div id="workflow"></div>\n\n<footer id="bottom">\n    <div class="wrap">\n        <div id="history">\n            <a class="button" data-action="workflow-toggle">Show workflow view</a>\n        </div>\n    </div>\n</footer>');
      
      }).call(this);
      
    }).call(__obj);
    __obj.safe = __objSafe, __obj.escape = __escape;
    return __out.join('');
  }
}});

window.require.define({"chaplin/templates/landing": function(exports, require, module) {
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
      
        __out.push('<div id="wrapper">\n    <header id="top">\n        <div class="inner">\n            <div class="account right">\n                Monsieur Tout-le-Monde <span>&#8226;</span> <a href="#">Logout</a>\n            </div>\n            <h1><strong>Flux:Mine</strong> - a workflow functionality prototype</h1>\n        </div>\n    </header>\n\n    <section id="middle">\n        <div id="landing" class="container row">\n            <div class="four columns">\n                <h2>Popular Tools</h2>\n                <ul>\n                    <li><a href="/tool/upload-list">Upload List</a></li>\n                    <li><a href="/tool/compare-lists">Compare Lists</a></li>\n                    <li><a href="/tool/saved-workflow">Use a Saved Workflow</a></li>\n                </ul>\n            </div>\n            <div class="four columns">\n                <h2>Popular Workflows</h2>\n                <ul>\n                    <li>Lorem ipsum dolor</li>\n                    <li>Sed ut perspiciatis</li>\n                    <li>At vero eos et accusamus</li>\n                </ul>\n            </div>\n            <div class="four columns">\n                <h2>Help</h2>\n                <ul>\n                    <li>Et iusto odio dignissimos</li>\n                    <li>Ducimus qui blanditiis</li>\n                    <li>Praesentium voluptatum deleniti</li>\n                </ul>\n            </div>\n        </div>\n    </section>\n</div>\n\n<footer id="wide">\n    <p>&copy; 2000-2013 InterMine, University of Cambridge</p>\n</footer>');
      
      }).call(this);
      
    }).call(__obj);
    __obj.safe = __objSafe, __obj.escape = __escape;
    return __out.join('');
  }
}});

window.require.define({"chaplin/templates/sidebar": function(exports, require, module) {
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
      
        __out.push('<div class="wrap">\n    <ul>\n        <li><a href="#">Global search</a></li>\n        <li><a href="#">View a public list</a></li>\n        <li><a href="#">Compare lists</a></li>\n    </ul>\n</div>');
      
      }).call(this);
      
    }).call(__obj);
    __obj.safe = __objSafe, __obj.escape = __escape;
    return __out.join('');
  }
}});

window.require.define({"chaplin/templates/tools/upload": function(exports, require, module) {
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
      
        __out.push('<div class="wrap">\n    <div class="row">\n        <div class="twelve columns">\n            <h1>Upload a List</h1>\n            <p>Select the type of list to create and either enter in a list\n                of identifiers or upload identifiers from a file. A search will\n                be performed for all the identifiers in your list.</p>\n        </div>\n    </div>\n    <form class="row custom">\n        <div class="six columns">\n            <label>List of identifiers</label>\n            <textarea></textarea>\n        </div>\n        <div class="six columns">\n            <label>Identifier type</label>\n            <select class="three">\n                <option>Genes</option>\n                <option>Proteins</option>\n            </select>\n        </div>\n    </form>\n    <div class="row twelve columns">\n        <a class="button" href="#">Upload a list</span></a>\n    </div>\n</div>');
      
      }).call(this);
      
    }).call(__obj);
    __obj.safe = __objSafe, __obj.escape = __escape;
    return __out.join('');
  }
}});

window.require.define({"chaplin/templates/workflow": function(exports, require, module) {
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
      
        __out.push('<div class="row">\n    <div class="twelve columns">\n        <h1>Workflow</h1>\n        <p>The workflow boxes will be populated here as you work with this app.</p>\n    </div>\n</div>');
      
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
      return require('chaplin/templates/app');
    };

    AppView.prototype.afterRender = function() {
      AppView.__super__.afterRender.apply(this, arguments);
      this.delegate('click', '.button[data-action="workflow-toggle"]', this.workflowToggle);
      return this;
    };

    AppView.prototype.workflowToggle = function(e) {
      var btn;
      btn = $(e.target);
      btn.text(btn.text().slice(0, 4) === 'Show' ? 'Hide workflow view' : 'Show workflow view');
      return Chaplin.mediator.publish('workflow', 'toggle');
    };

    return AppView;

  })(Chaplin.View);
  
}});

window.require.define({"chaplin/views/Landing": function(exports, require, module) {
  var Chaplin, LandingView,
    __hasProp = {}.hasOwnProperty,
    __extends = function(child, parent) { for (var key in parent) { if (__hasProp.call(parent, key)) child[key] = parent[key]; } function ctor() { this.constructor = child; } ctor.prototype = parent.prototype; child.prototype = new ctor(); child.__super__ = parent.prototype; return child; };

  Chaplin = require('chaplin');

  module.exports = LandingView = (function(_super) {

    __extends(LandingView, _super);

    function LandingView() {
      return LandingView.__super__.constructor.apply(this, arguments);
    }

    LandingView.prototype.container = 'body';

    LandingView.prototype.containerMethod = 'html';

    LandingView.prototype.autoRender = true;

    LandingView.prototype.getTemplateFunction = function() {
      return require('chaplin/templates/landing');
    };

    return LandingView;

  })(Chaplin.View);
  
}});

window.require.define({"chaplin/views/Sidebar": function(exports, require, module) {
  var Chaplin, SidebarView,
    __hasProp = {}.hasOwnProperty,
    __extends = function(child, parent) { for (var key in parent) { if (__hasProp.call(parent, key)) child[key] = parent[key]; } function ctor() { this.constructor = child; } ctor.prototype = parent.prototype; child.prototype = new ctor(); child.__super__ = parent.prototype; return child; };

  Chaplin = require('chaplin');

  module.exports = SidebarView = (function(_super) {

    __extends(SidebarView, _super);

    function SidebarView() {
      return SidebarView.__super__.constructor.apply(this, arguments);
    }

    SidebarView.prototype.container = 'aside#sidebar';

    SidebarView.prototype.containerMethod = 'html';

    SidebarView.prototype.autoRender = true;

    SidebarView.prototype.getTemplateFunction = function() {
      return require('chaplin/templates/sidebar');
    };

    return SidebarView;

  })(Chaplin.View);
  
}});

window.require.define({"chaplin/views/Tool": function(exports, require, module) {
  var Chaplin, ToolView,
    __hasProp = {}.hasOwnProperty,
    __extends = function(child, parent) { for (var key in parent) { if (__hasProp.call(parent, key)) child[key] = parent[key]; } function ctor() { this.constructor = child; } ctor.prototype = parent.prototype; child.prototype = new ctor(); child.__super__ = parent.prototype; return child; };

  Chaplin = require('chaplin');

  module.exports = ToolView = (function(_super) {

    __extends(ToolView, _super);

    function ToolView() {
      return ToolView.__super__.constructor.apply(this, arguments);
    }

    ToolView.prototype.initialize = function() {
      var ToolClass, tool;
      ToolClass = require("chaplin/views/tools/" + (this.model.get('name')) + "Tool");
      tool = new ToolClass({
        'model': this.model
      });
      return this;
    };

    return ToolView;

  })(Chaplin.View);
  
}});

window.require.define({"chaplin/views/Workflow": function(exports, require, module) {
  var Chaplin, SidebarView,
    __hasProp = {}.hasOwnProperty,
    __extends = function(child, parent) { for (var key in parent) { if (__hasProp.call(parent, key)) child[key] = parent[key]; } function ctor() { this.constructor = child; } ctor.prototype = parent.prototype; child.prototype = new ctor(); child.__super__ = parent.prototype; return child; };

  Chaplin = require('chaplin');

  module.exports = SidebarView = (function(_super) {

    __extends(SidebarView, _super);

    function SidebarView() {
      return SidebarView.__super__.constructor.apply(this, arguments);
    }

    SidebarView.prototype.container = '#workflow';

    SidebarView.prototype.containerMethod = 'html';

    SidebarView.prototype.autoRender = true;

    SidebarView.prototype.getTemplateFunction = function() {
      return require('chaplin/templates/workflow');
    };

    SidebarView.prototype.afterRender = function() {
      var _this = this;
      SidebarView.__super__.afterRender.apply(this, arguments);
      $(this.el).hide().css('width', $(window).width() - $('footer#bottom').outerWidth());
      Chaplin.mediator.subscribe('workflow', function(action) {
        switch (action) {
          case 'toggle':
            return $(_this.el).toggle();
        }
      });
      return this;
    };

    return SidebarView;

  })(Chaplin.View);
  
}});

window.require.define({"chaplin/views/tools/UploadListTool": function(exports, require, module) {
  var Chaplin, UploadListToolView,
    __hasProp = {}.hasOwnProperty,
    __extends = function(child, parent) { for (var key in parent) { if (__hasProp.call(parent, key)) child[key] = parent[key]; } function ctor() { this.constructor = child; } ctor.prototype = parent.prototype; child.prototype = new ctor(); child.__super__ = parent.prototype; return child; };

  Chaplin = require('chaplin');

  module.exports = UploadListToolView = (function(_super) {

    __extends(UploadListToolView, _super);

    function UploadListToolView() {
      return UploadListToolView.__super__.constructor.apply(this, arguments);
    }

    UploadListToolView.prototype.container = 'div#widget';

    UploadListToolView.prototype.containerMethod = 'html';

    UploadListToolView.prototype.autoRender = true;

    UploadListToolView.prototype.getTemplateFunction = function() {
      return require('chaplin/templates/tools/upload');
    };

    return UploadListToolView;

  })(Chaplin.View);
  
}});

