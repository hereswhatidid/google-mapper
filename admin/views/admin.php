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

<div class="wrap bootstrap-wrapper" id="googleMapperApp">

	<div class="container">
		<div class="page-header">
			<h1><?php echo esc_html( get_admin_page_title() ); ?> <small data-bind="text: appMode">Subtext for header</small></h1>
		</div>
		<div data-bind="page: { id: 'start', title: '<?php _e( 'View All', $this->plugin_slug ); ?>' }">
			<table class="table table-bordered table-striped">
				<thead>
					<tr>
						<th><?php _e( 'ID', $this->plugin_slug ); ?></th>
						<th><?php _e( 'Title', $this->plugin_slug ); ?></th>
						<th><?php _e( 'Address', $this->plugin_slug ); ?></th>
						<th>&nbsp;</th>
					</tr>
				</thead>
				<tbody data-bind="foreach: locations">
					<tr>
						<td data-bind="text: id"></td>
						<td data-bind="title"></td>
						<td data-bind="address"></td>
						<td>
							<div class="btn-group">
								<a class="btn btn-success" data-bind="page-href: '/edit/'"><?php _e( 'Edit', $this->plugin_slug ); ?></a>
								<a class="btn btn-error" data-bind="click: deleteLocation"><?php _e( 'Delete', $this->plugin_slug ); ?></a>
							</div>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
</div>
