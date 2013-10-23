// Gene names.
var genes = [ 'GPS2', 'NCOR1', 'NCOR2', 'TBC1XR1', 'ANKRO11' ];

var w = window,
    d = document,
    e = d.documentElement,
    g = d.getElementsByTagName('body')[0],
    width = -50 + (w.innerWidth || e.clientWidth || g.clientWidth),
    height = -50 + (w.innerHeight|| e.clientHeight|| g.clientHeight);

var color = d3.scale.category10();

var svg = d3.select("body").append("svg")
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
    json.nodes.push({ 'name': item });
});

(function(min, max) {
    // Init the links.
    var i = 0,
        len = genes.length;
    for (i = 0; i < len; i++) {
        var j;
        for(j = i + 1; j < len; j++) {
            // Not all to all.
            if (Math.floor(Math.random() * 3) == 0) continue;
            // Save into links.
            var rand = Math.floor(Math.random() * (max - min + 1)) + min;
            json.links.push({ 'source': i, 'target': j, 'strength': rand });
        }
    }
})(1, 10);

// Find a node by its name.
var find = _.memoize(function(name) {
    return _.find(json.nodes, { 'name': name });
});

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
        var active = false;

        return function() {
            active = !active;
            d3.select(this).classed("active", active);
        };
    })());

// The nodes.
var node = svg.selectAll("g.node")
    .data(json.nodes)
    // Wrapping element.
    .enter().append("svg:g")
    .attr("class", "node")
    .call(force.drag);

// Add a circle.
node.append('circle')
    .attr("class", "circle")
    .attr("r", 10)

// Add labels.
node.append("svg:text")
    .attr("class", "label")
    .text(function(d) {
        return d.name;
    })
    .attr("x", 10);

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