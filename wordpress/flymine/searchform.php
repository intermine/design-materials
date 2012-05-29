<form method="get" id="searchform" action="<?php bloginfo('home'); ?>/">
  <div id="nf_searchform_searchbox">
  	<div id="nf_searchform_searchbox_c1"><input type="text" size="14" value="<?php echo attribute_escape($s); ?>" name="s" id="s" /></div>
    <div id="nf_searchform_searchbox_c2">&nbsp;<input type="image" src="<?php bloginfo('template_directory'); ?>/images/search_16x16.png" id="searchsubmit" title="Search" /></div>
    <div id="nf_searchform_searchbox_c3">&nbsp;<a href="<?php bloginfo('rss2_url'); ?>"><img src="<?php bloginfo('template_directory'); ?>/images/rss_16x16.png" alt="RSS Feed" title="RSS Feed" /></a></div>
  </div>
</form>
