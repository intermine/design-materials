(function() {
    
    var jsPlumbDemo = function() {
        jsPlumb.importDefaults({
            // default drag options
            DragOptions : { cursor: 'pointer', zIndex:2000 },
            // default to blue at one end and green at the other
            EndpointStyles : [{ fillStyle:'#666' }, { fillStyle:'#666' }],
            // blue endpoints 7 px; green endpoints 11.
            Endpoints : [ [ "Dot", { radius: 4 } ], [ "Dot", { radius: 6 } ]],
            // the overlays to decorate each connection with.  note that the label overlay uses a function to generate the label text; in this
            // case it returns the 'labelText' member that we set on each connection in the 'init' method below.
            ConnectionOverlays : [
                [ "Arrow", { location:1, cssClass:"anArrow" } ],
                [ "Label", { 
                    location:0.1,
                    id:"label",
                    cssClass:"aLabel"
                }]
            ],
            ConnectorZIndex:5
        });         

        // this is the paint style for the connecting lines..
        var connectorPaintStyle = {
            lineWidth: 1,
            strokeStyle: "#b2b3b5",
            joinstyle: "round",
            outlineColor: "white",
            outlineWidth: 2
        },
        // .. and this is the hover style. 
        connectorHoverStyle = {
            lineWidth:1,
            strokeStyle:"#666"
        },
        // the definition of source endpoints (the small blue ones)
        sourceEndpoint = {
            endpoint:"Dot",
            paintStyle:{ fillStyle:"#666",radius:4 },
            isSource:true,
            connector:[ "Flowchart", { stub:[40, 60], gap:10 } ],                               
            connectorStyle:connectorPaintStyle,
            dragOptions:{},
            overlays:[
                [ "Label", { 
                    location:[0.5, 1.5], 
                    label:"Output",
                    cssClass:"endpointSourceLabel" 
                } ]
            ]
        },
        // a source endpoint that sits at BottomCenter
        //  bottomSource = jsPlumb.extend( { anchor:"BottomCenter" }, sourceEndpoint),
        // the definition of target endpoints (will appear when the user drags a connection) 
        targetEndpoint = {
            endpoint:"Dot",                 
            paintStyle:{ fillStyle:"#666",radius:6 },
            maxConnections:-1,
            dropOptions:{ hoverClass:"hover", activeClass:"active" },
            isTarget:true,          
            overlays:[
                [ "Label", { location:[0.5, -0.5], label:"Input", cssClass:"endpointTargetLabel" } ]
            ]
        },          
        init = function(connection) {
            connection.getOverlay("label").setLabel(connection.sourceId.substring(6) + "-" + connection.targetId.substring(6));
        };          

        var allSourceEndpoints = [], allTargetEndpoints = [];
            _addEndpoints = function(toId, sourceAnchors, targetAnchors) {
                for (var i = 0; i < sourceAnchors.length; i++) {
                    var sourceUUID = toId + sourceAnchors[i];
                    allSourceEndpoints.push(jsPlumb.addEndpoint(toId, sourceEndpoint, { anchor:sourceAnchors[i], uuid:sourceUUID }));                       
                }
                for (var j = 0; j < targetAnchors.length; j++) {
                    var targetUUID = toId + targetAnchors[j];
                    allTargetEndpoints.push(jsPlumb.addEndpoint(toId, targetEndpoint, { anchor:targetAnchors[j], uuid:targetUUID }));                       
                }
            };

        _addEndpoints("window4", ["TopCenter", "BottomCenter"], ["LeftMiddle", "RightMiddle"]);         
        _addEndpoints("window2", ["LeftMiddle", "BottomCenter"], ["TopCenter", "RightMiddle"]);
        _addEndpoints("window3", ["RightMiddle", "BottomCenter"], ["LeftMiddle", "TopCenter"]);
        _addEndpoints("window1", ["LeftMiddle", "RightMiddle"], ["TopCenter", "BottomCenter"]);
                    
        // listen for new connections; initialise them the same way we initialise the connections at startup.
        jsPlumb.bind("jsPlumbConnection", function(connInfo, originalEvent) { 
            init(connInfo.connection);
        });         
                    
        // make all the window divs draggable                       
        //jsPlumb.draggable(jsPlumb.getSelector(".window"), { grid: [20, 20] });
        // THIS DEMO ONLY USES getSelector FOR CONVENIENCE. Use your library's appropriate selector method!
        jsPlumb.draggable(jsPlumb.getSelector(".window"));

        //* connect a few up
        jsPlumb.connect({uuids:["window2BottomCenter", "window3TopCenter"]});
        jsPlumb.connect({uuids:["window2LeftMiddle", "window4LeftMiddle"]});
        jsPlumb.connect({uuids:["window4TopCenter", "window4RightMiddle"]});
        jsPlumb.connect({uuids:["window3RightMiddle", "window2RightMiddle"]});
        jsPlumb.connect({uuids:["window4BottomCenter", "window1TopCenter"]});
        jsPlumb.connect({uuids:["window3BottomCenter", "window1BottomCenter"]});
                
        //
        // listen for clicks on connections, and offer to delete connections on click.
        //
        jsPlumb.bind("click", function(conn, originalEvent) {
            if (confirm("Delete connection from " + conn.sourceId + " to " + conn.targetId + "?"))
                jsPlumb.detach(conn); 
        }); 
        
        jsPlumb.bind("connectionDrag", function(connection) {
            console.log("connection " + connection.id + " is being dragged");
        });     
        
        jsPlumb.bind("connectionDragStop", function(connection) {
            console.log("connection " + connection.id + " was dragged");
        });
    };

    /*
     *  This file contains the JS that handles the first init of each jQuery demonstration, and also switching
     *  between render modes.
     */
    jsPlumb.bind("ready", function() {
        (function(desiredMode) {
            var newMode = jsPlumb.setRenderMode(desiredMode);
            $(".rmode").removeClass("selected");
            $(".rmode[mode='" + newMode + "']").addClass("selected");       
            $(".rmode[mode='canvas']").attr("disabled", !jsPlumb.isCanvasAvailable());
            $(".rmode[mode='svg']").attr("disabled", !jsPlumb.isSVGAvailable());
            $(".rmode[mode='vml']").attr("disabled", !jsPlumb.isVMLAvailable());
            jsPlumbDemo();
        })(jsPlumb.SVG);       
    });
})();