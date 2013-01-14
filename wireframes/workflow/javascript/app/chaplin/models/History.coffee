Chaplin = require 'chaplin'

Tool = require 'chaplin/models/Tool'

module.exports = class History extends Chaplin.Collection

    'model': Tool

    initialize: ->
        super

        # Get the localStorage data.
        @store = new window.LocalStorage 'History'
        for record in @store.findAll()
            # Add it only in memory, we already have it in localStorage.
            @add new Tool record, false

        @

    # Sort on order.
    comparator: (item) ->  item.get('order')

    # Reset the history.
    reset: ->
        # In localStorage.
        @store.reset()

        # Destroy locally.
        super

    # Add model to the Collection.
    add: (model, localStorage=true) ->
        # Locally.
        super

        # In localStorage.
        if localStorage? then @store.add model

    # Remove a model from a Collection.
    remove: (model) ->
        # Get the order to update all elements after it.
        order = model.get 'order'
        @each (item) =>
            iOrder = item.get 'order'
            if iOrder > order
                item.set 'order', iOrder - 1
                @store.add item

        # In localStorage.
        @store.remove model

        # Locally.
        super

    # Move a tool in its position in the history.
    move: (a, b) ->
        assert a, 'The `id` of the element needs to be provided'

        # Traverse the current state wo/ us.
        arr = [] ; @each (item) -> arr.push(item.id) unless item.id is a

        # Add to the end.
        unless b then arr.push a
        # Inject.
        else arr.splice(arr.indexOf(b), 0, a)

        # Save the new order.
        @each (item) =>
            item.set 'order', arr.indexOf item.id
            @store.add item

        # Sort.
        @sort()