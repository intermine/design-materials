Chaplin = require 'chaplin'

module.exports = class UploadListToolView extends Chaplin.View

    container:       'div#widget'
    containerMethod: 'html'
    autoRender:      true

    getTemplateFunction: -> require 'chaplin/templates/tools/upload'