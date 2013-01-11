Chaplin = require 'chaplin'

StepView = require 'chaplin/views/Step'

module.exports = class SidebarView extends Chaplin.View

    'container':       '#workflow'
    'containerMethod': 'html'
    'autoRender':      true

    # Store all step Views here to garbage dump.
    views: []

    getTemplateFunction: -> require 'chaplin/templates/workflow'

    dispose: ->
        # Stop listening to these.
        Chaplin.mediator.unsubscribe 'workflow'

        super

    afterRender: ->
        super

        # Hide by default and set width to how much space we have on screen.
        $(@el).css('width', $(window).width() - $('footer#bottom').outerWidth())

        steps = $(@el).find('#steps')

        # Set the height of the steps based on the height of the viewport.
        do height = -> steps.css 'height', ($(window).height() / 2) - 52

        # On window resize, update height again.
        $(window).resize height

        # Make the workflow grid sortable.
        steps.sortable
            'stop': (event, ui) =>
                # Get the element that has just moved.
                item = $(ui.item)
                # What is the item to the left of me (to determine new position).
                next = item.next()
                # Move it in the Collection.
                if next.length is 0
                    @collection.move item.attr('data-id')
                else
                    @collection.move item.attr('data-id'), next.attr('data-id')
                # Update view.
                @updateView()

        # Listen to messages.
        Chaplin.mediator.subscribe 'workflow', (action, tool) =>
            # Which action?
            switch action
                # Toggle the view.
                when 'toggle'
                    # if $(@el).is(':hidden') then @updateView()
                    $(@el).parent().toggle()
                # Add a tool.
                when 'add'
                    # Update the tool's order (push on stack).
                    tool.set 'order', @collection.length
                    # Save on Collection.
                    @collection.add tool
                    @updateView()
                # Remove a tool.
                when 'remove'
                    @collection.remove tool
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

        # Populate with separate step views, on order they have set.
        @collection.each (model) =>
            @views.push step = new StepView 'model': model
            steps.append step.el

        # Make the workflow I/O droppable/draggable.
        steps.find('.step .io.output').draggable
            'helper': 'clone'
        steps.find('.step .io.input').droppable
            'hoverClass': 'over'
            'drop': (event, ui) =>
                # Get the source and target.
                a = $(ui.draggable).closest('.step').attr('data-id')
                b = $(event.target).closest('.step').attr('data-id')
                console.log a, b