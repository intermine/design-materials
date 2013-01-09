Chaplin = require 'chaplin'

module.exports = class SidebarView extends Chaplin.View

    container:       '#workflow'
    containerMethod: 'html'
    autoRender:      true

    getTemplateFunction: -> require 'chaplin/templates/workflow'

    afterRender: ->
        super

        # Hide by default and set width to how much space we have on screen.
        $(@el).hide().css('width', $(window).width() - $('footer#bottom').outerWidth())

        # Listen to messages, we show them.
        Chaplin.mediator.subscribe 'workflow', (action) =>
            switch action
                when 'toggle'
                    $(@el).toggle()

        @