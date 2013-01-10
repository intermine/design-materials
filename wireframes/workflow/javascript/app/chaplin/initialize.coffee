# Simple assertion class.
class AssertException

    constructor: (@message) ->

    toString: -> "AssertException: #{@message}"

# Set the assertion on the window object.
@.assert = (exp, message) -> throw new AssertException(message) unless exp


# -----------------------------------------------------------

# A Class for saving to localStorage.
class window.LocalStorage

    # Name of the store.
    constructor: (@name) ->
        item = window.localStorage.getItem @name
        @records = (item and item.split(',')) or []

    # Destroy all entries.
    reset: ->
        # New Array.
        @records = []
        # Save new state.
        @save()

    # Save the local `@records` into localStorage.
    save: -> window.localStorage.setItem @name, @records.join(',')

    # Generate four random hex digits.
    S4 = -> (((1 + Math.random()) * 0x10000) | 0).toString(16).substring 1

    # Generate a pseudo-GUID by concatenating random hexadecimal.
    guid = -> [ S4(), S4(), '-', S4(), '-', S4(), '-', S4(), '-', S4(), S4(), S4() ].join('')

# -----------------------------------------------------------

FluxMine = require 'chaplin/Application'
Workflow = require 'chaplin/models/Workflow'

$ ->
    # Init the workflow.
    window.Workflow = new Workflow()
    # Start the app.
    window.App = new FluxMine()