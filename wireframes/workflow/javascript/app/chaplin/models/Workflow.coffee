Chaplin = require 'chaplin'

Tool = require 'chaplin/models/Tool'

module.exports = class Workflow extends Chaplin.Collection

    'model': Tool

    'localStorage': new Backbone.LocalStorage 'Workflow'