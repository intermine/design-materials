function loadTemplate() {
    IMBedding.loadTemplate(
    {
        // Search for GO annotations for a particular gene.
       'name':          "Gene_GO",

       // Show GO annotations for gene:
       'constraint1':   "Gene",
       'op1':           "LOOKUP",
       'value1':        "Arc1",
       'extra1':        "D. melanogaster",
       'code1':         "A"
    },
    '#template-query',
    {
        'baseUrl':      "http://www.flymine.org/release-30.0",
        'openOnLoad':   true
    }
    );
}
