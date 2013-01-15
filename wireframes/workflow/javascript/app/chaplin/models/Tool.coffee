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
                @set 'type', 'green'
            
            when 'CompareLists'
                @set 'description', 'Compares <em>n</em> lists.'
                @set 'type', 'orange'

            when 'UseSteps'
                @set 'description', 'Saved Steps.'
                @set 'type', 'dark'