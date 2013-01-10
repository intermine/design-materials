Chaplin = require 'chaplin'

module.exports = class SidebarView extends Chaplin.View

    'container':       '#workflow'
    'containerMethod': 'html'
    'autoRender':      true

    getTemplateFunction: -> require 'chaplin/templates/workflow'

    afterRender: ->
        super

        # Hide by default and set width to how much space we have on screen.
        $(@el).hide().css('width', $(window).width() - $('footer#bottom').outerWidth())

        # Listen to messages.
        Chaplin.mediator.subscribe 'workflow', (action, tool) =>
            # Which action?
            switch action
                # Toggle the view.
                when 'toggle'
                    # Are we going to be showing the Workflow? Then update its contents before showing it.
                    if $(@el).is(':hidden') then @updateView()
                    $(@el).toggle()
                # Add a tool.
                when 'add'
                    @collection.create tool

        @

    # Update the View rendering the Tools that have been used in the current session.
    updateView: ->