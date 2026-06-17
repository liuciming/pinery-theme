<?php
if (!defined("ABSPATH")) exit;
?>
<form role="search" method="get" class="search-form" action="<?php echo esc_url(home_url('/')); ?>">
  <input type="search" name="s" class="search-field" placeholder="<?php esc_attr_e('Search...', 'pinery'); ?>" value="<?php echo esc_attr(get_search_query()); ?>" />
  <button type="submit" class="search-submit"><?php _e('Search', 'pinery'); ?></button>
</form>
