var app = function() {

    // Globally available.
    var data = {},
        templates = {};

    // Load all of the data.
    async.each([
        [ 'app', 'template' ],
        [ 'consequences', 'template' ],
        [ 'heatmap', 'template' ],
        [ 'consequences', 'template' ],
        [ 'organisms', 'template' ],
        [ 'colors', 'data' ],
        [ 'consequences', 'data' ],
        [ 'genes', 'data' ],
        [ 'organisms', 'data' ],
        [ 'phylogeny', 'data' ]
    ], function(item, cb) {
        var name = item[0];
        if (item[1] == 'template') {
            d3.text('templates/' + name + '.mustache', function(err, res) {
                templates[name] = res;
                cb(err);
            });
        } else {
            d3.json('data/' + name + '.json', function(err, res) {
                data[name] = res;
                cb(err);
            });
        }
    }, function(err) {
        if (err) throw err;

        // Render the body.
        d3.select('body').html(templates.app);

        // Render the heatmap.
        d3.select('#heatmap').html(
            Mustache.render(
                templates.heatmap,
                (function(size) {
                
                    return {
                        // Population head.
                        'population': (function() {
                            var arr = [],
                                i;
                            for (i = 0; i < size; i++) {
                                arr.push({ 'i': i + 1 });
                            }
                            return arr;
                        })(),
                        
                        // Random population data.
                        'individuals': (function() {
                            var i,
                                result = null,
                                len    = data.genes.length,
                                found  = false,
                                total  = null;
                            // Find a nice "line" through.
                            while (!found) {
                                result = [];
                                total  = 0;
                                // For all genes.
                                for (i = 0; i < len; i++) {
                                    // Have between 1 - 4 subjects expressed in each gene.
                                    var bound = Math.floor(Math.random() * (6 - 2)) + 1,
                                        gene = [];
                                    // For all the population.
                                    for (j = 0; j < size; j++) {
                                        // Is this gene expressed in this subject?
                                        var expressed;
                                        // Increase the total then.
                                        if (expressed = (j >= total && !!bound)) {
                                            total += 1;
                                            bound -= 1;
                                        }
                                        gene.push({ 'expressed': expressed });
                                    }       
                                    // Save it.
                                    result.push({ 'gene': data.genes[i], 'expressions': gene });
                                }
                                // Nicely filled in and no expressions left?
                                if (total == size && !bound) found = true;
                            }

                            return result;
                        })
                    }
                
                })(13) // amount of individuals
            )
        );

        // Size of a graph.
        var w = window,
            d = document,
            e = d.documentElement,
            g = d.getElementsByTagName('body')[0],
            width = 1000,
            height = 500;

        var svgN = d3.select("#graph").append("svg")
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
        _.forEach(data.genes, function(item) {
            json.nodes.push({
                'name': item,
                'consequences': _.cloneDeep(data.consequences).map(
                    function(consequence, i) {
                        consequence.present = Math.random() < 0.5;
                        consequence.color = data.colors[i];
                        return consequence;
                    }
                )
            });
        });

        // Init the links.
        (function(min, max) {
            var i,
                len = data.genes.length;
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
                        'organisms': (function() {
                            // How far will the conservation reach?
                            var conservation = Math.floor(Math.random() * data.organisms.length);
                            // Make sure we are present in human.
                            var conservation = (conservation) ? conservation : 1;
                            // Make the array.
                            return _.clone(data.organisms).map(function(organism, i) {
                                organism = { 'organism': organism };
                                organism.supported = (i <= conservation);
                                return organism;
                            });
                        })()
                    });
                }
            }
        })(1, 8);

        // Add fake nodes to have links from (all) boxes.
        // Will keep the chart in a nice webby centre.
        var i;
        for (i = 0; i < 4; i++) {
            json.nodes.push([
                { 'fixed': true, 'x': 100, 'y': 100 },
                { 'fixed': true, 'x': 100, 'y': 400 },
                { 'fixed': true, 'x': 900, 'y': 100 },
                { 'fixed': true, 'x': 900, 'y': 400 }
            ][i]);

            var j,
                len = json.nodes.length - (i * 1) - 1;
            for (j = 0; j < len; j++) {
                json.links.push({
                    'source': j,
                    'target': json.nodes.length - 1,
                    'strength': 1,
                    'fake': true
                });
            }
        }

        // Init force layout.
        force
            .nodes(json.nodes)
            .links(json.links)
            .start();

        // The links.
        var link = svgN.selectAll(".link")
            .data(json.links)
            .enter().append("line")
            .attr("class", function(d) {
                return (d.fake) ? "fake link" : "link";
            })
            .attr("data-connection", function(d) {
                return d.source.index + '-' + d.target.index;
            })
            .style("stroke-width", function(d) {
                return (d.fake) ? 3 : d.strength;
            })
            // Events.
            .on("click", (function() {
                // A currently active link.
                var active = null;

                return function(dR) {
                    if (dR.fake) return;

                    var self = d3.select(this),
                        hide = function() {
                            active.classed("active", false);

                            // Hide the box.
                            d3.select('#organisms').style('display', 'none');
                        };

                    // Are we clicking on the same link?
                    if (active && self.node() == active.node()) {
                        hide();
                        active = null;
                    } else {
                        // Deselect the currently selected one?
                        if (active) hide();
                        
                        // Select "this" one.
                        active = self;
                        active.classed("active", true);

                        // Render the template.
                        d3.select('#organisms').html(
                            Mustache.render(templates.organisms, dR)
                        ).style('display', 'block');

                        // Append the phylogenic tree.
                        (function() {

                            var width = 270,
                                height = 250,
                                top = 70,
                                left = 10,
                                text = 80;

                            var cluster = d3.layout.cluster()
                                .size([ height - top, width - left - text ]);

                            var svgT = d3.select("#organisms .tree").append("svg")
                                .attr("width", width)
                                .attr("height", height)
                                .append("g")
                                .attr("transform", "translate(" + left + "," + top + ")");

                            var nodes = cluster.nodes(data.phylogeny);

                            var link = svgT.selectAll("path.link")
                            .data(cluster.links(nodes))
                            .enter().append("path")
                            .attr("class", "link")
                            .attr("d", function(d, i) {
                                return "M" + d.source.y + "," + d.source.x
                                + "V" + d.target.x + "H" + d.target.y;
                            });

                            var node = svgT.selectAll("g.node")
                            .data(nodes)
                            .enter().append("g")
                            .attr("class", "node")
                            .attr("transform", function(d) {
                                return "translate(" + d.y + "," + d.x + ")";
                            });

                            node.append("circle")
                            .attr("r", 4.5)
                            .attr("class", function(d) {
                                return (d.show) ? (
                                    _.find(dR.organisms, { 'organism': d.name }).supported ? 'supported' : 'unsupported'
                                ) : '';
                            })
                            .attr("transform", "translate(3,0)");

                            node.append("text")
                            .attr("dx", 13)
                            .attr("dy", 6)
                            .attr("text-anchor", function(d) {
                                return d.children ? "end" : "start";
                            })
                            .text(function(d) {
                                return (d.show) ? d.name : '';
                            });

                        })();
                    }
                };
            })());

        // The nodes.
        var node = svgN.selectAll("g.node")
            .data(json.nodes)
            // Wrapping element.
            .enter().append("svg:g")
            .attr("class", function(d) {
                return (d.fixed) ? "fake" : "node";
            })
            // Events.
            .on("mouseover", function(d) {
                if (d.fixed) return;
                
                // Onclick show consequences.
                d3.select('#consequences').html(
                    Mustache.render(templates.consequences, d)
                ).style('display', 'block');

                // Show the connector to the box.
                var from = d.index,
                    to = json.nodes[json.nodes.length - 2].index;

                svgN.select('line.fake.link[data-connection="' +
                    from + '-' + to + '"]').classed('active', true);
            })
            .on("mouseout", function(d) {
                d3.select('#consequences').style('display', 'none');

                // Hide the connector to the box.
                var from = d.index,
                    to = json.nodes[json.nodes.length - 2].index;

                svgN.select('line.fake.link[data-connection="' +
                    from + '-' + to + '"]').classed('active', false);
            })
            .call(force.drag);

        // Add the circles.
        var innerR = 3, // inner radius.
            ringR =  3; // ring radius
        node.forEach(function(list) {
            list.forEach(function(g, i) {
                // Skip fake fixed nodes.
                if (json.nodes[i].fixed) {
                    return d3.select(g).append('circle').attr("r", 0)
                }

                var i,
                    j = 0,
                    // Clone and reverse; starting from the outside.
                    arr = json.nodes[i].consequences,
                    // All the zero-count.
                    len = _.filter(arr, { 'present': true }).length;

                // Loop it.
                arr.forEach(function(consequence) {
                    if (!consequence.present) return;
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