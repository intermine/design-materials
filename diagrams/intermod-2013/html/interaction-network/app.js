// Gene names.
var genes = [ 'GPS2', 'NCOR1', 'NCOR2', 'TBC1XR1', 'ANKRO11' ];

// The consequences in their order.
var consequences = [
    'splice site mutant/variant',
    'stop codon/gained',
    'frame shift',
    'non conservative/missense',
    'conservative substitution'
];

var w = window,
    d = document,
    e = d.documentElement,
    g = d.getElementsByTagName('body')[0],
    width = -50 + (w.innerWidth || e.clientWidth || g.clientWidth),
    height = -50 + (w.innerHeight|| e.clientHeight|| g.clientHeight);

// Use a set of 10 colors.
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
    json.nodes.push({ 'name': item, 'consequences': (function(min, max) {
        var i,
            temp = [],
            len = consequences.length;
        for (i = 0; i < len; i++) {
            // Not all.
            if (Math.floor(Math.random() * 3) == 0) continue;
            // Add the consequence.
            temp.push({
                'name': consequences[i],
                'count': Math.floor(Math.random() * (max - min + 1)) + min
            });
        }
        return temp
    })(15, 30) });
});

(function(min, max) {
    // Init the links.
    var i,
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
})(3, 7);

// Init force layout.
force
    .nodes(json.nodes)
    .links(json.links)
    .start();

// Onclick link event.
var onLink = (function() {
    // A currently active node.
    var active = null;

    return function() {
        var self = d3.select(this);

        // Are we clicking on the same link?
        if (active && self.node() == active.node()) {
            active.classed("active", false);
            active = null;
        } else {
            // Deselect the currently selected one?
            if (active) {
                active.classed("active", false);
            }
            // Select "this" one.
            active = self;
            active.classed("active", true);
        }
    };
})()

// The links.
var link = svg.selectAll(".link")
    .data(json.links)
    .enter().append("line")
    .attr("class", "link")
    .style("stroke-width", function(d) {
        return d.strength;
    })
    // Events.
    .on("click", onLink);

// The nodes.
var node = svg.selectAll("g.node")
    .data(json.nodes)
    // Wrapping element.
    .enter().append("svg:g")
    .attr("class", "node")
    .call(force.drag);

// Add the circles.
var innerR = 3, // inner radius.
    ringR =  3; // ring radius
node.forEach(function(list) {
    list.forEach(function(g, i) {
        var temp = json.nodes[i].consequences,
            len = temp.length;
        temp.reverse().forEach(function(consequence, j) {
            d3.select(g).append('circle')
                .attr("r", innerR + ((len - j) * ringR))
                .attr("fill", color(j))
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