Chaplin = require 'chaplin'

module.exports = class Tool extends Chaplin.Model

    initialize: ->
        super
        
        @set 'created', Date.now() if @isNew()