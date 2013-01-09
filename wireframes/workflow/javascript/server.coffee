#!/usr/bin/env coffee

flatiron = require 'flatiron'
union    = require 'union'
connect  = require 'connect'
send     = require 'send'
fs       = require 'fs'

csv      = require './lib/csv.coffee'
exec     = require './lib/exec.coffee'

# Export for Brunch.
exports.startServer = (port, dir) ->
    app = flatiron.app
    app.use flatiron.plugins.http,
        'before': [
            # Have a nice favicon.
            connect.favicon()
            # Static file serving.
            connect.static "./#{dir}"
        ]
        'onError': (err, req, res) ->
            if err.status is 404
                # Silently serve the root of the client app.
                send(req, 'index.html')
                    .root('./public')
                    .on('error', union.errorHandler)
                    .pipe(res)
            else
                # Go Union!
                union.errorHandler err, req, res

    app.router.path "/api/docs", ->
        @get ->
            fs.readdir './docs', (err, files) =>
                rule = (file) ->
                    [ name, ext ] = file.split('.')
                    # Extension, matching spreadsheet and url allowed.
                    if ext is 'coffee' and name + '.csv' in files and encodeURIComponent(name) is name then [ true, name ]
                    else [ false, null ]

                names = ( 'name': name for f in files when ( [ res, name ] = rule(f) ; res ) )

                @res.writeHead 200, 'application/json'
                @res.write JSON.stringify names
                @res.end()

    app.router.path "/api/docs/:name", ->
        @post (name) ->
            code = @req.body.code
            sheet = @req.body.sheet
            exec code, sheet, (err, newSheet) =>
                if err
                    @res.writeHead 500, 'application/json'
                    @res.write JSON.stringify 'message': err
                    @res.end()
                else
                    # Save the (pre-exec) spreadsheet.
                    sheet = csv.save sheet
                    fs.writeFile "./docs/#{name}.csv", sheet, 'utf-8', (err) =>
                        if err
                            @res.writeHead 500, 'application/json'
                            @res.write JSON.stringify 'message': err
                            @res.end()
                        else
                            # Save the code.
                            fs.writeFile "./docs/#{name}.coffee", code, 'utf-8', (err) =>
                                if err
                                    @res.writeHead 500, 'application/json'
                                    @res.write JSON.stringify 'message': err
                                    @res.end()
                                else
                                    # Respond with the latest version.
                                    @res.writeHead 200, 'application/json'
                                    @res.write JSON.stringify
                                        'code': code
                                        'sheet': newSheet
                                    @res.end()

        @get (name) ->
            fs.readFile "./docs/#{name}.coffee", 'utf-8', (err, docCoffee) =>
                if err
                    @res.writeHead 500, 'application/json'
                    @res.write JSON.stringify 'message': err
                    @res.end()
                else
                    fs.readFile "./docs/#{name}.csv", 'utf-8', (err, docCSV) =>
                        if err
                            @res.writeHead 500, 'application/json'
                            @res.write JSON.stringify 'message': err
                            @res.end()
                        else
                            # Read the sheet.
                            oldSheet = csv.read docCSV

                            # Exec.
                            exec docCoffee, oldSheet, (err, sheet) =>
                                if err
                                    @res.writeHead 500, 'application/json'
                                    @res.write JSON.stringify
                                        'message': err
                                        'code': docCoffee
                                        'sheet': oldSheet
                                    @res.end()
                                else
                                    @res.writeHead 200, 'application/json'
                                    @res.write JSON.stringify
                                        'code': docCoffee
                                        'sheet': sheet
                                    @res.end()

    app.start port, (err) ->
        throw err if err