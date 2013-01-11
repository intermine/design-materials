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
        @keys = (item and item.split(',')) or []

    # Destroy all entries.
    reset: ->
        # Remove the object.
        ( window.localStorage.removeItem(@name + '-' + key) for key in @keys )
        # Remove keys.
        @keys = []
        # Save new state.
        @save()

    # Add a Model.
    add: (model) ->
        # Need to generate an id?
        unless model.id
            model.id = guid()
            model.set model.idAttribute, model.id

        # Add to storage.
        window.localStorage.setItem @name + '-' + model.id, JSON.stringify model
        # Save the key.
        key = model.id.toString()
        @keys.push key unless key in @keys
        @save()

    # Save the local `@keys` into localStorage.
    save: -> window.localStorage.setItem @name, @keys.join(',')

    # Remove the model.
    remove: (model) ->
        # Object.
        window.localStorage.removeItem @name + '-' + model.id
        # Keys.
        @keys.splice @keys.indexOf(model.id), 1
        @save()

    # Return all items.
    findAll: -> ( JSON.parse(window.localStorage.getItem(@name + '-' + key)) for key in @keys )

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