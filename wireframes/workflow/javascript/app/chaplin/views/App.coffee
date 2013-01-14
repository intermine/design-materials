Chaplin = require 'chaplin'

module.exports = class AppView extends Chaplin.View

    container:       'body'
    containerMethod: 'html'
    autoRender:      true

    getTemplateFunction: -> require 'chaplin/templates/app'

    afterRender: ->
        super

        @delegate 'click', '.button[data-action="history-toggle"]', @historyToggle

        @

    historyToggle: (e) ->
        # Change the button text.
        btn = $(e.target)
        btn.text(
            if btn.text()[0...4] is 'Show' then 'Hide history'
            else 'Show history'
        )
        
        # Send a message (to HistoryView).
        Chaplin.mediator.publish 'history', 'toggle'