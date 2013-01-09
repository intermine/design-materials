Chaplin = require 'chaplin'

AppView = require 'chaplin/views/App'
SidebarView = require 'chaplin/views/Sidebar'
ToolView = require 'chaplin/views/Tool'

Tool = require 'chaplin/models/Tool'

module.exports = class FluxMine

    constructor: ->
        # Create the main app view.
        app = new AppView()

        # A specific tool, show the sidebar.
        sidebar = new SidebarView()

        # ...and the actual tool.
        tool = new ToolView 'model': new Tool('name': 'Upload')