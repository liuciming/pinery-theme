<?php
if (!defined("ABSPATH")) exit;

if (post_password_required()) return;
?>

<div id="comments" class="comments-area">

<?php if (have_comments()): ?>
  <h2 class="comments-title">
    <?php
    printf(
      _nx('One thought on "%2$s"', '%1$s thoughts on "%2$s"', get_comments_number(), 'comments title', 'pinery'),
      number_format_i18n(get_comments_number()),
      '<span>' . get_the_title() . '</span>'
    );
    ?>
  </h2>

  <ol class="comment-list">
    <?php
    wp_list_comments([
      'style'       => 'ol',
      'avatar_size' => 48,
      'short_ping'  => true,
    ]);
    ?>
  </ol>

  <?php if (get_comment_pages_count() > 1 && get_option('page_comments')): ?>
    <nav class="comment-navigation">
      <div class="nav-previous"><?php previous_comments_link(__('Older Comments', 'pinery')); ?></div>
      <div class="nav-next"><?php next_comments_link(__('Newer Comments', 'pinery')); ?></div>
    </nav>
  <?php endif; ?>

  <?php if (!comments_open() && get_comments_number()): ?>
    <p class="no-comments"><?php _e('Comments are closed.', 'pinery'); ?></p>
  <?php endif; ?>

<?php endif; ?>

<?php
comment_form([
  'title_reply'          => __('Leave a Comment', 'pinery'),
  'title_reply_before'   => '<h3 class="comment-reply-title">',
  'title_reply_after'    => '</h3>',
  'label_submit'         => __('Post Comment', 'pinery'),
  'comment_notes_before' => '',
]);
?>

</div>
