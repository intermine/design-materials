Chaplin = require 'chaplin'

module.exports = class Tool extends Chaplin.Model

    initialize: ->
        super
        
        # Timestamp of creation date.
        @set 'created', Date.now() if @isNew()

        # Description for workflow steps.
        @set 'description', do =>
            switch @get 'name'
                when 'UploadList' then 'Produces a list.'