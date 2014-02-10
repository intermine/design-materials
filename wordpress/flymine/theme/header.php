<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />
<title><?php if (is_search()) { $ser=$_GET['s']; ?><?php _e('Search'); ?><?php echo " &quot;".wp_specialchars($ser,1)."&quot;"; } ?><?php wp_title(''); ?><?php if (!is_home()) { ?> | <?php } ?><?php bloginfo('name'); ?></title>
<meta name="generator" content="WordPress <?php bloginfo('version'); ?>" />
<meta name="keywords" content="" />
<meta name="description" content="" />
<meta name="author" content="" />
<link rel="shortcut icon" href="<?php echo get_stylesheet_directory_uri(); ?>/images/favicon.ico" />
<link rel="stylesheet" href="<?php bloginfo('stylesheet_url'); ?>" type="text/css" media="screen, print" />
<link rel="alternate" type="application/rss+xml" title="<?php bloginfo('name'); ?> RSS Feed" href="<?php bloginfo('rss2_url'); ?>" />
<link rel="alternate" type="text/xml" title="RSS .92" href="<?php bloginfo('rss_url'); ?>" />
<link rel="alternate" type="application/atom+xml" title="Atom 0.3" href="<?php bloginfo('atom_url'); ?>" />
<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />

<?php wp_head(); ?>

</head>
<body>
<a name="top"></a>
<div id="nf_page">

	<!-- Begin the side bar. -->
  <div id="nf_pagesidebar">
    <div id="nf_pagesidebarcontainer">
      <?php include(TEMPLATEPATH.'/searchform.php'); ?>
      <br />
      <?php get_sidebar(); ?>
    </div>
  </div>
  <!-- End the side bar. -->

  <a href="<?php echo get_settings('home'); ?>/">
    <div id="title">
        <span><?php bloginfo('description'); ?></span>
		<p><?php bloginfo('name'); ?></p>
	</div>
  </a>

	<!-- Begin the main (content) column. -->
  <div id="nf_pagecontentcolumn">

    <div id="content">
        <h1>Content</h1>