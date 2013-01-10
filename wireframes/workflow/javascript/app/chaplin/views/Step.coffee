Chaplin = require 'chaplin'

module.exports = class StepView extends Chaplin.View

    'containerMethod': 'html'
    'autoRender':      true

    getTemplateFunction: -> require 'chaplin/templates/step'

    getTemplateData: -> _.extend @model.toJSON(),
        'order': @options.order

    afterRender: ->
        super

        $(@el).attr 'class', 'step'

        # Events on buttons.
        @delegate 'click', '.button[data-action="step-remove"]', ->
            Chaplin.mediator.publish 'workflow', 'remove', @model

        @