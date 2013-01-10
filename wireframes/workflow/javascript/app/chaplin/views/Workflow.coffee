Chaplin = require 'chaplin'

StepView = require 'chaplin/views/Step'

module.exports = class SidebarView extends Chaplin.View

    'container':       '#workflow'
    'containerMethod': 'html'
    'autoRender':      true

    # Store all step Views here to garbage dump.
    views: []

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
                    # if $(@el).is(':hidden') then @updateView()
                    $(@el).toggle()
                # Add a tool.
                when 'add'
                    @collection.create tool
                    @updateView()

        # Call initial view update.
        @updateView()

        @

    # Update the View rendering the Tools that have been used in the current session.
    updateView: ->
        # Show/hide info message if no steps taken.
        $(@el).find('p.message').hide @collection.length is 0
        # Remove any and all step views.
        ( view.dispose() for view in @views )
        # Clear all of the views.
        (steps = $(@el).find('#steps')).html('')

        i = 0
        # Populate with separate step views.
        @collection.each (model) =>
            @views.push step = new StepView 'model': model, 'order': ++i
            steps.append step.el