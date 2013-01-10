Chaplin = require 'chaplin'

Tool = require 'chaplin/models/Tool'

module.exports = class Workflow extends Chaplin.Collection

    'model': Tool

    'comparator': 'order'

    initialize: ->
        super

        # Get the localStorage data.
        @store = new window.LocalStorage 'Workflow'
        for record in @store.records
            @push new Tool record

        @

    # Reset the workflow.
    reset: ->
        @store.reset()

        # Destroy locally.
        super

    # Move a tool in its position in the workflow.
    move: (itemIndex, nextIndex) ->
        item = @at itemIndex
        # Popush at the very end.
        unless nextIndex
            # Every following item will have an order reduced by 1.
            for i in [ (itemIndex + 1) ... @length ]
                console.log i
                model = @at i
                model.set 'order', model.get('order') - 1
            # And finally ours is at the end.
            item.set 'order', @length - 1
        else
            console.log 'nay'

        # Sort the collection as we have updated the `order` attr.
        @sort()