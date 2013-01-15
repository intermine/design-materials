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
            Chaplin.mediator.publish 'history', 'remove', @model
        # @delegate 'click', '.button[data-action="step-view"]', ->
        #     Chaplin.mediator.publish 'app', 'view', @model

        # Init "time ago" updater.
        @updateTime()

        @

    # Update our "time ago" and call again if we change often.
    updateTime: =>
        # Element to update.
        el = $(@el).find('em.ago')
        # The creation date.
        created = new Date @model.get 'created'
        # Previous time.
        c = null
        # Init timeout for fib sequence.
        [a, b] = [0, 1]
        # Run this now for the first time.
        do queue = ->
            d = moment(created).fromNow()
            # Call again?
            if c isnt d
                # Reset fib.
                [a, b] = [0, 1]
                # Save and show value.
                el.text c = d
            # Call later.
            else
                # Delay update by calling fib.
                [a, b] = [b, a + b]

            # Call us in this time.
            setTimeout queue, b * 1000