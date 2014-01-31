<div id="sidebar">
	<h1>Sidebar</h1>
	<ul>
        <li><a target="new" href="http://www.flymine.org"><h2>FlyMine</h2></a></li>
		<?php if (function_exists('dynamic_sidebar')&&dynamic_sidebar(1)): else: ?>
			<li><h2><?php _e('Pages:'); ?></h2>
				<ul>
				<?php wp_list_pages('title_li=&depth=-1'); ?>
				</ul>
			</li>
			<li><h2><?php _e('Archives:'); ?></h2>
				<ul>
				<?php wp_get_archives('type=monthly&limit=10'); ?>
				</ul>
			</li>
			<li><h2><?php _e('Categories:'); ?></h2>
				<ul>
				<?php
					if (function_exists( wp_list_categories)) {
						wp_list_categories('title_li=&orderby=name&hierarchical=1');
					}
					else {
						wp_list_cats('sort_column=name&optioncount=0&title_li=');
					}
				?>
				</ul>
			</li>
            <!--
			<li><h2><?php _e('Meta:'); ?></h2>
				<ul>
					<?php wp_register(); ?>
					<li><?php wp_loginout(); ?></li>
					<li><a href="http://validator.w3.org/check/referer" title="<?php _e('This page validates as XHTML 1.0 Transitional'); ?>"><?php _e('Valid <abbr title="eXtensible HyperText Markup Language">XHTML</abbr>'); ?></a></li>
					<li><a href="<?php bloginfo('rss2_url'); ?>" title="<?php _e('Syndicate this site using RSS 2.0'); ?>"><?php _e('Entries <abbr title="Really Simple Syndication">RSS</abbr>'); ?></a></li>
					<li><a href="<?php bloginfo('comments_rss2_url'); ?>" title="<?php _e('The latest comments to all posts in RSS'); ?>"><?php _e('Comments <abbr title="Really Simple Syndication">RSS</abbr>'); ?></a></li>
					<?php wp_meta(); ?>
				</ul>
			</li>
            -->
		<?php endif; ?>
	</ul>
</div>
