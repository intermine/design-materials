Chaplin = require 'chaplin'

LandingView = require 'chaplin/views/Landing'
AppView = require 'chaplin/views/App'
LeftSidebarView = require 'chaplin/views/LeftSidebar'
ToolView = require 'chaplin/views/Tool'
HistoryView = require 'chaplin/views/History'

History = require 'chaplin/models/History'
Tool = require 'chaplin/models/Tool'

module.exports = class FluxController extends Chaplin.Controller

    historyURL: (params) -> ''

    # Clean these up.
    views: []

    # Whitelist of tools we can use.
    tools: [ 'UploadList', 'CompareLists', 'UseSteps' ]

    initialize: ->
        ( view.dispose() for view in @views )

        super

    # Landing page.
    index: ->
        # Create the landing page view.
        new LandingView()

        # Reset the history.
        window.History.reset()

    # A specific tool to show.
    tool: (opts) ->
        # Get the tool name.
        tool = ( ( p[0].toUpperCase() + p[1...] if p ) for p in opts.name.split('-') ).join('')
        assert tool in @tools, "Unknown tool `#{tool}`"

        # Create the main app view.
        new AppView()

        # Init the history view.
        @views.push new HistoryView 'collection': window.History

        # A specific tool, show the sidebar.
        new LeftSidebarView()

        # ...and the actual tool.
        new ToolView 'model': new Tool('name': tool)