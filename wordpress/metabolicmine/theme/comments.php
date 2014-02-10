<?php
if ('comments.php'==basename($_SERVER['SCRIPT_FILENAME'])) die ('ACCESS DENIED.');
if (!empty($post->post_password)) {
  if ($_COOKIE['wp-postpass_'.COOKIEHASH]!=$post->post_password) { ?>
    <p class="nocomments"><?php _e('Enter your password to view comments.'); ?></p>
  <?php
    return;
  }
}
$oddcomment='alt';
?>
<?php if ($comments): ?>
  <h2 id="comments"><?php comments_number(__(''),__('Talkback'),__('Talkback x %')); ?></h2>
  <ol class="commentlist">
  <?php foreach ($comments as $comment): ?>
    <li class="<?php echo $oddcomment; ?>" id="comment-<?php comment_ID() ?>">
      <span class="gravatar">
      <?php if (function_exists('get_avatar')) { ?>
      <?php echo get_avatar(get_comment_author_email(),'40'); ?>
      <?php } ?>
      </span>
      <big><?php comment_author_link() ?></big>
      <br />
      <span class="comment_data"><a href="#comment-<?php comment_ID() ?>" title=""><?php comment_date('j M Y') ?> @ <?php comment_time() ?></a> <?php edit_comment_link(__("edit this"),'# ',''); ?></span>
      <?php if ($comment->comment_approved=='0'): ?>
        <span class="await_mod">(awaiting moderation)</span>
      <?php endif; ?>
      <?php comment_text() ?>
    </li>
    <?php
      if ('alt'==$oddcomment) $oddcomment='';
      else $oddcomment='alt';
    ?>
  <?php endforeach; ?>
  </ol>
<?php else : ?>
  <?php if ('open'==$post->comment_status): ?>
  <?php else: ?>
    <p class="nocomments"><?php _e(' '); ?></p>
  <?php endif; ?>
<?php endif; ?>
<?php if ('open'==$post->comment_status): ?>
<h2 id="respond"><?php _e("Add Comments"); ?></h2>
  <p id="respond_title">Re: <?php the_title(); ?></p>
  <?php if (get_option('comment_registration')&&!$user_ID): ?>
  <p><a href="<?php echo get_option('siteurl'); ?>/wp-login.php?redirect_to=<?php the_permalink(); ?>">Sign in</a></p>
  <?php else: ?>
  <form action="<?php echo get_option('siteurl'); ?>/wp-comments-post.php" method="post" id="commentform">
    <?php if ($user_ID): ?>
    <p>You are signed in as <a href="<?php echo get_option('siteurl'); ?>/wp-admin/profile.php"><?php echo $user_identity; ?></a>. (<a href="<?php echo get_option('siteurl'); ?>/wp-login.php?action=logout" title="<?php _e('Sign out of this account') ?>">Sign out</a>)</p>
    <?php else: ?>
    <label for="author"><small>Name <?php if ($req) _e('(required)'); ?></small></label>
    <br />
    <input type="text" name="author" id="author" value="<?php echo $comment_author; ?>" size="36" tabindex="1" />
    <br />
    <label for="email"><small>Email (hidden) <?php if ($req) _e('(required)'); ?></small></label>
    <br />
    <input type="text" name="email" id="email" value="<?php echo $comment_author_email; ?>" size="36" tabindex="2" />
    <br />
    <label for="url"><small>Web site</small></label>
    <br />
    <input type="text" name="url" id="url" value="<?php echo $comment_author_url; ?>" size="36" tabindex="3" /><br />
    <?php endif; ?>
    <p><textarea name="comment" id="comment" cols="100%" rows="8" tabindex="4"></textarea></p>
    <p><input name="submit" type="submit" id="submit" tabindex="5" value="<?php _e("Submit"); ?>"/>
    <input type="hidden" name="comment_post_ID" value="<?php echo $id; ?>" /></p>
    <p class="allowed-tags"><small><strong>Tags you can use (optional):</strong><br /><?php echo allowed_tags(); ?></small></p>
    <?php do_action('comment_form',$post->ID); ?>
  </form>
  <?php endif; ?>
<?php endif; ?>

