Chaplin = require 'chaplin'

module.exports = class Tool extends Chaplin.Model

    initialize: ->
        super
        
        # Timestamp of creation date.
        @set 'created', Date.now() if @isNew()

        # Logic for different types of tools.
        switch @get 'name'
            when 'UploadList'
                @set 'description', 'Produces a list.'
                @set 'io',
                    'output': 'List'
                @set 'type', 'green'
            
            when 'CompareLists'
                @set 'description', 'Compares <em>n</em> lists.'
                @set 'io',
                    'input': 'Lists'
                @set 'type', 'orange'
