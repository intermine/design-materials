Chaplin = require 'chaplin'

module.exports = class SidebarView extends Chaplin.View

    container:       'aside#sidebar'
    containerMethod: 'html'
    autoRender:      true

    getTemplateFunction: -> require 'chaplin/templates/sidebar'