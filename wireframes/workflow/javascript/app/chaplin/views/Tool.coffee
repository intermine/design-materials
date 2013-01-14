Chaplin = require 'chaplin'

module.exports = class ToolView extends Chaplin.View

    initialize: ->
        # Render the actual tool.
        ToolClass = require "chaplin/views/tools/#{@model.get('name')}Tool"
        tool = new ToolClass('model': @model, 'sidebar': @options.sidebar)

        @