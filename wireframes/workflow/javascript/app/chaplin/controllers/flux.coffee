Chaplin = require 'chaplin'

LandingView = require 'chaplin/views/Landing'
AppView = require 'chaplin/views/App'
SidebarView = require 'chaplin/views/Sidebar'
ToolView = require 'chaplin/views/Tool'
WorkflowView = require 'chaplin/views/Workflow'

Workflow = require 'chaplin/models/Workflow'
Tool = require 'chaplin/models/Tool'

module.exports = class FluxController extends Chaplin.Controller

    historyURL: (params) -> ''

    # Clean these up.
    views: []

    initialize: ->
        ( view.dispose() for view in @views )

        super

    # Landing page.
    index: ->
        # Create the landing page view.
        new LandingView()

        # Reset the workflow history.
        window.Workflow.reset()

    # A specific tool to show.
    tool: (opts) ->
        # Get the tool name.
        tool = ( ( p[0].toUpperCase() + p[1...] if p ) for p in opts.name.split('-') ).join('')
        assert tool in [ 'UploadList', 'CompareLists' ], "Unknown tool `#{tool}`"

        # Create the main app view.
        new AppView()

        # Init the workflow view.
        @views.push new WorkflowView 'collection': window.Workflow

        # A specific tool, show the sidebar.
        new SidebarView()

        # ...and the actual tool.
        new ToolView 'model': new Tool('name': tool)