# Simple assertion class.
class AssertException

    constructor: (@message) ->

    toString: -> "AssertException: #{@message}"

# Set the assertion on the window object.
@.assert = (exp, message) -> throw new AssertException(message) unless exp

FluxMine = require 'chaplin/Application'
Workflow = require 'chaplin/models/Workflow'

$ ->
    # Init the workflow.
    window.Workflow = new Workflow()
    # Start the app.
    window.App = new FluxMine()