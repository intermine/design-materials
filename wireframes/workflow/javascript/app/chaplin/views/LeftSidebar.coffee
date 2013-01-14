Chaplin = require 'chaplin'

module.exports = class LeftSidebarView extends Chaplin.View

    container:       'aside#left'
    containerMethod: 'html'
    autoRender:      true

    getTemplateFunction: -> require 'chaplin/templates/sidebar-left'