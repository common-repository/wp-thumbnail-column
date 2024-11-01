<?php
/*
  Plugin Name: WP Thumbnail Column
  Plugin URI: http://wordpress.org/extend/plugins/wp-thumbnail-column/
  Description: This plugin adds column with featured images for selected post types
  Author: Spectraweb s.r.o.
  Author URI: http://www.spectraweb.cz
  Version: 1.0.0
 */

// load plugin translation
load_plugin_textdomain('wp-thumbcolumn', false, dirname(plugin_basename(__FILE__)) . '/languages');

// activation hook
register_activation_hook(__FILE__, 'wp_thumbcolumn_activation');
// deactivation hook
register_deactivation_hook(__FILE__, 'wp_thumbcolumn_deactivation');

//
add_action('init', 'wp_thumbcolumn_init');

require_once 'include/CustomColumns.php';

/**
 *
 */
function wp_thumbcolumn_activation()
{

}

/**
 *
 */
function wp_thumbcolumn_deactivation()
{

}

/**
 *
 */
function wp_thumbcolumn_init()
{
	if (is_admin())
	{
		add_action('admin_init', 'wp_thumbcolumn_admin_init');
		add_action('admin_menu', 'wp_thumbcolumn_admin_menu');
	}
}

/**
 *
 */
function wp_thumbcolumn_admin_init()
{
	$post_types = get_post_types(array('show_ui' => true), 'names');
	foreach ($post_types as $post_type)
	{
		register_setting('wp_thumbcolumn', 'wp_thumbcolumn_' . $post_type);
		if (get_option('wp_thumbcolumn_' . $post_type))
		{
			CustomColumns::addColumn($post_type, 1, 'thumbnail', __('Image'));
		}
	}
}

/**
 *
 */
function wp_thumbcolumn_admin_menu()
{
	add_options_page(__('Thumbnail Column', 'wp-thumbcolumn'), __('Thumbnail Column', 'wp-thumbcolumn'), 'manage_options', basename(__FILE__), 'wp_thumbcolumn_settings');
}

/**
 *
 */
function wp_thumbcolumn_settings()
{
	$plugin_path = plugin_dir_url(__FILE__);
	?>
	<div class="wrap">
		<h2><?php _e('Thumbnail Column Settings', 'wp-thumbcolumn') ?></h2>

		<?php if ($message != '') : ?>
			<div class="updated fade below-h2">
				<p><?php echo $message ?></p>
			</div>
		<?php endif; ?>

		<form method="post" action="options.php">
			<?php settings_fields('wp_thumbcolumn'); ?>
			<table class="form-table">
				<?php $post_types = get_post_types(array('show_ui' => true), 'names'); ?>
				<tr valign="top">
					<th scope="row"><?php _e('Enable for the following post types', 'wp-thumbcolumn') ?></th>
					<td>
						<?php foreach ($post_types as $post_type) : ?>
							<div>
								<label>
									<input type="checkbox" name="wp_thumbcolumn_<?php echo $post_type ?>"
										   <?php if (get_option('wp_thumbcolumn_' . $post_type)) echo 'checked="checked"' ?>/>
										   <?php echo $post_type ?>
								</label>
							</div>
						<?php endforeach; ?>
					</td>
				</tr>
			</table>
			<!-- Submit form -->
			<p class="submit">
				<input type="submit" class="button-primary" value="<?php _e('Save Changes', 'wp-instadigest') ?>" />
			</p>
		</form>
	</div>
	<?php
}