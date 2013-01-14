Chaplin = require 'chaplin'

module.exports = class RightSidebarView extends Chaplin.View

    container:       'aside#right'
    containerMethod: 'html'
    autoRender:      true

    getTemplateFunction: -> require @options.template