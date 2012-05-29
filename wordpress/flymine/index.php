  <?php get_header(); ?>
  <!--
  <div id="nf_sitetitle"><a href="<?php echo get_settings('home'); ?>/" title="<?php bloginfo('name'); ?>"><?php bloginfo('name'); ?></a></div>
  <div id="nf_sitedescription"><?php bloginfo('description'); ?></div>
  <div id="nf_topimage"></div>
  -->

  <?php if (have_posts()): ?>

    <?php $howManyEntriesToShowInFull=10;
    // Change this value as you please. However, it's usually a good idea
    // to keep it the same as the "Blog pages show at most" setting
    // under WP Admin's "Reading Settings".
    ?>

    <?php $post=$posts[0]; ?>
    <?php if (is_category()) { ?>
      <h2 class="pagetitle"><?php echo single_cat_title(); ?></h2>
    <?php } elseif (is_day()) { ?>
      <h2 class="pagetitle"><?php the_time('j F Y'); ?></h2>
    <?php } elseif (is_month()) { ?>
      <h2 class="pagetitle"><?php the_time('F Y'); ?></h2>
    <?php } elseif (is_year()) { ?>
      <h2 class="pagetitle"><?php the_time('Y'); ?></h2>
    <?php } elseif (is_search()) { ?>
      <h2 class="pagetitle">Search results for "<?php echo $_GET['s']; ?>"</h2>
    <?php } elseif (is_tag()) { ?>
      <h2 class="pagetitle">Tagged "<?php echo single_tag_title(); ?>"</h2>
    <?php } elseif ((isset($_GET['paged']))&&(!empty($_GET['paged']))) { ?>
    <?php } ?>

    <?php while (have_posts()): the_post(); $loopcounter++; ?>
      <div class="post <?php if ($loopcounter == 1) echo 'latest'; ?>" id="post-<?php the_ID(); ?>">
      <?php if ((is_single())||(is_page())||((is_home())&&($loopcounter<=$howManyEntriesToShowInFull))) { ?>
        <h2>
          <a href="<?php the_permalink() ?>" rel="bookmark" title="Permalink: <?php the_title(); ?>"><?php the_title(); ?></a>
        </h2>
      <?php } else { ?>
        <div class="listentrytitle">
          <h3><a href="<?php the_permalink(); ?>" rel="bookmark" title="<?php the_title(); ?>"><?php the_title(); ?></a></h3>
          <?php the_time('j M Y') ?>
          <span>|</span> in <?php the_category(' + ') ?> by <?php the_author(); ?>
          <br />
        <?php if (is_home()) { ?>
          <br />
          <span class="listentrycomments">
            <?php comments_popup_link(__(''),__('+ 1 comment'),__('+ % comments')); ?>
          </span>
        <?php } ?>
        </div>
      <?php } ?>
      <?php
      if ((is_home())&&($loopcounter>$howManyEntriesToShowInFull)) {
        //the_excerpt();
      }
      ?>
      <?php if (!is_page()) { ?>
        <?php if ((is_home())&&($loopcounter>$howManyEntriesToShowInFull)) { ?>
        <?php } else if ((is_single())||((is_home())&&($loopcounter<=$howManyEntriesToShowInFull))) { ?>
        <p class="details_small">
            <?php the_time('j M y'); ?> <span>|</span> in <?php the_category(' + ') ?> by <?php the_author(); ?>
        </p>
        <?php } ?>
      <?php } ?>
      <?php
      if ((is_home())||(is_single())||(is_page()) AND $loopcounter<=$howManyEntriesToShowInFull) {
        the_content(__('(more...)'));
      ?>
        <?php wp_link_pages('before=<div id="page-links">&after=</div>'); ?>
        <div class="listentrytags">
        <?php the_tags('Tagged: ',' &raquo; ',''); ?>
        </div>
      <?php
      } else {
        if (is_home()) {
        } else {
          the_excerpt();
        }
      ?>
        <div class="listentrytags">
        <?php the_tags('Tagged: ',' &raquo; ',''); ?>
        </div>
      <?php
      }
      ?>
      <?php if ((is_home())&&($loopcounter>$howManyEntriesToShowInFull)) { ?>
      <?php } else { ?>
        <p class="date">
        <?php if (!((is_single())||(is_page()))) { ?>
          <?php if (is_home()) { ?>
            <?php comments_popup_link(__(''), __('1 comment'), __('% comments')); ?>
            <span>|</span>
            <a href="<?php the_permalink() ?>" style="font-weight: normal;">Share or discuss</a>
            <!--
            <span>|</span>
            <?php the_time('Y-m-d'); ?>
            <span>|</span>
            <?php the_author(); ?>
            -->
          <?php } else { ?>
            <?php comments_popup_link(__(''), __('1 comment'), __('% comments')); ?>
            <span>|</span>
            <a href="<?php the_permalink() ?>">Read the rest</a>
          <?php } ?>
        <?php } ?>
        <?php if ((!is_home())&&(is_single())) { ?>
          <!--
          <?php the_time('Y-m-d'); ?>
          <span>|</span>
          <?php the_author(); ?>
          -->
        <?php } ?>
        <?php edit_post_link(__('Edit'),' - ',''); ?>
        </p>
      <?php } ?>
      </div>

      <?php if (is_single()) { ?>
      <div id="single_browse">
        <ul>
            <li><h4>Browse in category: <?php foreach((get_the_category()) as $cat) { echo $cat->cat_name; } ?></h4></li>
            <li class="previous"><?php previous_post_link('%link','%title',TRUE); ?></li>
            <li class="next"><?php next_post_link('%link','%title',TRUE); ?></li>
        </ul>
        <ul>
            <li><h4>Browse in timeline</h4></li>
            <li class="previous"><?php previous_post_link('%link'); ?></li>
            <li class="next"><?php next_post_link('%link'); ?></li>
        </ul>
      </div>
      <?php } ?>

      <?php if ((is_single())||(is_page())) comments_template(); ?>
    <?php endwhile; ?>

    <?php if ((!(is_single()))&&(!(is_home()))&&(!(is_page()))) { ?>
      <div class="navigation"><?php posts_nav_link('','<span class="alignright">'.__('Newer entries').'</span>','<span class="alignleft">'.__('Older entries').'</span>'); ?></div>
    <?php } ?>
  <?php else: ?>
      <h4>Your lucky number is 404.</h4>
      <p><?php _e('It means the stuff you are looking for is not there.<br />Why not try one of the categories or tags instead?'); ?></p>
  <?php endif; ?>

    </div><!-- End content. -->

    <div id="links_end_of_content_paddingtop">
    </div>
    <div id="links_end_of_content">
    <?php if (is_home()) { ?>
      <?php next_posts_link('<span class="left">Older entries</span>') ?>
      <br />
      <?php previous_posts_link('<span class="right">Newer entries</span>') ?>
    <?php } else if (is_single()) { ?>
      <a href="<?php echo get_settings('home'); ?>/" title="Home">Home</a>
      &nbsp;|&nbsp;
      <a href="<?php bloginfo('rss2_url'); ?>" title="<?php _e('RSS feed'); ?>">RSS Feed</a>
      &nbsp;|&nbsp;
      <a href="javascript:window.print();" title="Print">Print</a>
      &nbsp;|&nbsp;
      <a href="#top" title="Search">Search</a>
      &nbsp;|&nbsp;
      <a href="#top" title="Top">Top</a>
    <?php } ?>
    </div>

<?php get_footer(); ?>