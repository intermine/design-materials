Chaplin = require 'chaplin'

module.exports = class FluxMine extends Chaplin.Application

    title: 'Flux:Mine'

    initialize: ->
        super

        # Initialize core components.
        @initDispatcher
            'controllerPath':   'chaplin/controllers/'
            'controllerSuffix': ''

        # So that nice Controller switching works...
        @layout = new Chaplin.Layout {@title}

        # Register all routes and start routing.
        @initRouter (match) ->
            match '',           'flux#index'
            match 'tool/:name', 'flux#tool'

        # Freeze the application instance to prevent further changes.
        Object.freeze? @