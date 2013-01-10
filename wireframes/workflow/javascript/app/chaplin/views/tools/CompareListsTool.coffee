Chaplin = require 'chaplin'

module.exports = class CompareListsToolView extends Chaplin.View

    container:       'div#widget'
    containerMethod: 'html'
    autoRender:      true

    # Begin at this internal step.
    step: 1

    getTemplateFunction: ->
        switch @step
            when 1 then require 'chaplin/templates/tools/compare-input'
            when 2 then require 'chaplin/templates/tools/compare-done'

    afterRender: ->
        super

        @delegate 'click', '#submit', @submit

        @

    # Submit list upload, ask for next step.
    submit: ->
        # Create a step in workflow by emitting a message.
        Chaplin.mediator.publish 'workflow', 'add', @model

        # Change the step and re-render.
        @step += 1
        @render()