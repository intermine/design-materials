Chaplin = require 'chaplin'

RightSidebarView = require 'chaplin/views/RightSidebar'

module.exports = class UploadListToolView extends Chaplin.View

    container:       'div#widget'
    containerMethod: 'html'
    autoRender:      true

    # Begin at this internal step.
    step: 1

    # Render a specific template on each step.
    getTemplateFunction: ->
        switch @step
            when 1 then require 'chaplin/templates/tools/upload-input'
            when 2 then require 'chaplin/templates/tools/upload-done'

    afterRender: ->
        super

        switch @step
            when 1
                # Handle submit clicks.
                @delegate 'click', '#submit', @submit
            when 2
                # Show next steps.
                sidebar = new RightSidebarView 'template': 'chaplin/templates/tools/upload-done-next'
                # Attach events.
                sidebar.delegate 'click', 'a', (e) ->
                    console.log $(e.target).attr('data-step')

        @

    # Submit list upload, ask for next step.
    submit: ->
        # Create a step in a history by emitting a message.
        Chaplin.mediator.publish 'history', 'add', @model

        # Change the step and re-render.
        @step += 1
        @render()