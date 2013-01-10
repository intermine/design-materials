Chaplin = require 'chaplin'

Tool = require 'chaplin/models/Tool'

module.exports = class Workflow extends Chaplin.Collection

    'model': Tool

    'localStorage': new Backbone.LocalStorage 'Workflow'

    initialize: ->
        super

        # Re-init all Models saved in localStorage.
        for obj in @localStorage.findAll()
            @push new Tool obj

        @

    create: (model) ->
        # Save in localStorage.
        super

        # Save on us too.
        @push model