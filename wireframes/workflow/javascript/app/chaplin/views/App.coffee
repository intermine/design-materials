Chaplin = require 'chaplin'

module.exports = class AppView extends Chaplin.View

    container:       'body'
    containerMethod: 'html'
    autoRender:      true

    getTemplateFunction: -> require 'chaplin/templates/app'

    afterRender: ->
        super

        @delegate 'click', '.button[data-action="workflow-toggle"]', @workflowToggle

        @

    workflowToggle: (e) ->
        # Change the button text.
        btn = $(e.target)
        btn.text(
            if btn.text()[0...4] is 'Show' then 'Hide workflow view'
            else 'Show workflow view'
        )
        
        # Send a message (to WorkflowView).
        Chaplin.mediator.publish 'workflow', 'toggle'