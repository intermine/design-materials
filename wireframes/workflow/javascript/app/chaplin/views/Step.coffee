Chaplin = require 'chaplin'

module.exports = class StepView extends Chaplin.View

    'containerMethod': 'html'
    'autoRender':      true
    'tagName':         'li'

    getTemplateFunction: -> require 'chaplin/templates/step'

    getTemplateData: -> @model.toJSON()

    afterRender: ->
        super

        # Add class and add order, 0-indexed!
        $(@el).attr('class', "step #{@model.get('type')}").attr('data-id', @model.id)

        # Events on buttons.
        @delegate 'click', '.button[data-action="step-remove"]', ->
            Chaplin.mediator.publish 'workflow', 'remove', @model
        @delegate 'click', '.button[data-action="step-view"]', ->
            Chaplin.mediator.publish 'app', 'view', @model

        @