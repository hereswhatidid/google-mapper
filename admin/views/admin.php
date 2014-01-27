<?php
/**
 * Represents the view for the administration dashboard.
 *
 * This includes the header, options, and other information that should provide
 * The User Interface to the end user.
 *
 * @package   Google_Mapper
 * @author    Gabe Shackle <gabe@hereswhatidid.com>
 * @license   GPL-2.0+
 * @link      http://hereswhatidid.com
 * @copyright 2014 Gabe Shackle
 */
?>

<div class="wrap">

	<h2><?php echo esc_html( get_admin_page_title() ); ?></h2>
	<form action="#">
		<input type="text" name="address" id="address"><br>
		Lat: <input type="text" id="latitude"> x Long: <input type="text" id="longitude">
		<button class="mapper-find"><?php _e( 'Check Address', $this->plugin_slug ); ?></button>

	</form>
	<!-- @TODO: Provide markup for your options page here. -->

</div>
