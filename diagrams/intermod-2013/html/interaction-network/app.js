var app = function() {

    // Gene names.
    var genes = [ 'GPS2', 'NCOR1', 'NCOR2', 'TBC1XR1', 'ANKRO11' ];

    // Organisms.
    var organisms = [
        'mouse',
        'rat',
        'human',
        'fly',
        'nematode',
        'mosquito'
    ];

    // The consequences in their order.
    var consequences = [
        'splice site',
        'nonsense',
        'frame shift',
        'non conservative missense',
        'conservative missense',
        'synonymous'
    ];

    // Colors.
    var colors = [
        '#B30000',
        '#FC4E2A',
        '#FEB24C',
        '#3F007D',
        '#4EB3D3',
        '#238443'
    ];

    // Load the templates.
    var templates = [ 'app', 'consequences', 'heatmap', 'organisms' ];
    return async.map(templates, function(name, cb) {
        d3.xhr('templates/' + name + '.mustache', function(err, res) {
            cb(err, res.responseText);
        });
    }, function(err, results) {
        if (err) throw err;

        var temp = {};
        results.forEach(function(template, i) {
            temp[templates[i]] = template;
        });
        templates = temp;

        // Render the body.
        d3.select('body').html(templates.app);

        // Render the heatmap.
        d3.select('#heatmap').html(
            Mustache.render(templates.heatmap, {
                // Population head.
                'population': (function() {
                    var arr = [],
                        i,
                        clr = d3.scale.category10();
                    for (i = 0; i < 10; i++) {
                        arr.push({ 'i': i + 1, 'color': clr(i) });
                    }
                    return arr;
                })(),
                // Random population data.
                'individuals': (function() {
                    var i,
                        iArr = [],
                        len = genes.length;
                    for (i = 0; i < len; i++) {
                        var j,
                            jArr = [];
                        for (j = 0; j < 10; j++) {
                            jArr.push({ 'expressed': Math.floor(Math.random() * 5) == 0 });
                        }
                        iArr.push({ 'gene': genes[i], 'expressions': jArr });
                    }
                    return iArr;
                })()
            })
        );

        // Size of a graph.
        var w = window,
            d = document,
            e = d.documentElement,
            g = d.getElementsByTagName('body')[0],
            width = 1000,//-50 + (w.innerWidth || e.clientWidth || g.clientWidth),
            height = 500;//-50 + (w.innerHeight|| e.clientHeight|| g.clientHeight);

        // Create a scale of colors for consequences.
        // var color = d3.scale.ordinal().range(colorbrewer.YlOrRd[consequences.length].reverse());

        var svg = d3.select("#graph").append("svg")
            .attr("width", width)
            .attr("height", height);

        var force = d3.layout.force()
            .charge(-1e4)
            .linkDistance(function(d) {
                return d.strength;
            })
            .size([ width, height ]);

        var json = { 'nodes': [], 'links': [] };

        // Init the nodes.
        genes.forEach(function(item) {
            json.nodes.push({ 'name': item, 'consequences': (function(min, max) {
                var i,
                    temp = [],
                    len = consequences.length;
                for (i = 0; i < len; i++) {
                    // Add the consequence.
                    temp.push({
                        'name': consequences[i],
                        'count': (function() {
                            // Zero.
                            if (Math.floor(Math.random() * 3) == 0) return 0;
                            // Random count.
                            return Math.floor(Math.random() * (max - min + 1)) + min
                        })(),
                        'color': colors[i]
                    });
                }
                return temp
            })(15, 30) });
        });

        // Init the links.
        (function(min, max) {
            var i,
                len = genes.length;
            for (i = 0; i < len; i++) {
                var j;
                for(j = i + 1; j < len; j++) {
                    // Not all to all.
                    if (Math.floor(Math.random() * 3) == 0) continue;
                    // Save into links.
                    json.links.push({
                        'source': i,
                        'target': j,
                        'strength': Math.floor(Math.random() * (max - min + 1)) + min,
                        // Add the organisms.
                        'organisms': _.clone(organisms).map(function(organism) {
                            return {
                                'organism': organism,
                                'supported': Math.floor(Math.random() * 3) == 1
                            };
                        })
                    });
                }
            }
        })(1, 8);

        //console.log(JSON.stringify(json, null, 2));

        // Init force layout.
        force
            .nodes(json.nodes)
            .links(json.links)
            .start();

        // The links.
        var link = svg.selectAll(".link")
            .data(json.links)
            .enter().append("line")
            .attr("class", "link")
            .style("stroke-width", function(d) {
                return d.strength;
            })
            // Events.
            .on("click", (function() {
                // A currently active link.
                var active = null;

                return function(d) {
                    var self = d3.select(this);

                    // Are we clicking on the same link?
                    if (active && self.node() == active.node()) {
                        active.classed("active", false);
                        active = null;
                        // Hide it.
                        d3.select('#organisms').style('display', 'none');
                    } else {
                        // Deselect the currently selected one?
                        if (active) active.classed("active", false);
                        
                        // Select "this" one.
                        active = self;
                        active.classed("active", true);

                        // Render the template.
                        d3.select('#organisms').html(
                            Mustache.render(templates.organisms, d)
                        ).style('display', 'block');
                    }
                };
            })());

        // The nodes.
        var node = svg.selectAll("g.node")
            .data(json.nodes)
            // Wrapping element.
            .enter().append("svg:g")
            .attr("class", "node")
            // Events.
            .on("mouseover", function(d) {
                // Onclick show consequences.
                d3.select('#consequences').html(
                    Mustache.render(templates.consequences, d)
                ).style('display', 'block');
            })
            .on("mouseout", function() {
                d3.select('#consequences').style('display', 'none');
            })
            .call(force.drag);

        // Add the circles.
        var innerR = 3, // inner radius.
            ringR =  3; // ring radius
        node.forEach(function(list) {
            list.forEach(function(g, i) {
                var i,
                    j = 0,
                    // Clone and reverse; starting from the outside.
                    arr = _.clone(json.nodes[i].consequences).reverse(),
                    // All the zero-count.
                    len = arr.length - _.filter(arr, { 'count': 0 }).length;

                // Loop it.
                arr.forEach(function(consequence) {
                    if (!consequence.count) return;
                    // Add it.
                    d3.select(g).append('circle')
                        .attr("r", innerR + ((len - j) * ringR))
                        .attr("fill", consequence.color)
                    // This one was included.
                    j++;
                });
            });
        });

        node.append('circle')
            .attr("class", "circle")
            .attr("r", innerR)

        // Add labels.
        node.append("svg:text")
            .attr("class", "label")
            .text(function(d) {
                return d.name;
            })
            .attr("x", 20);

        // Draw the links.
        force.on("tick", function() {
            // Position the links.
            link.attr("x1", function(d) {
                return d.source.x;
            })
            .attr("y1", function(d) {
                return d.source.y;
            })
            .attr("x2", function(d) {
                return d.target.x;
            })
            .attr("y2", function(d) {
                return d.target.y;
            });

            node.attr("cx", function(d) {
                return d.x;
            })
            .attr("cy", function(d) {
                return d.y;
            });

            // Position the nodes.
            node.attr("transform", function(d) { return "translate(" + d.x + "," + d.y + ")"; });
        });
    });
};