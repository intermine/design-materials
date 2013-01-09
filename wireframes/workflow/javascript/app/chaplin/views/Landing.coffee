Chaplin = require 'chaplin'

module.exports = class LandingView extends Chaplin.View

    container:       'body'
    containerMethod: 'html'
    autoRender:      true

    getTemplateFunction: -> require 'chaplin/templates/landing'