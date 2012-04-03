<?php


if ( ! class_exists( 'TD_Themes_List_Table' ) ) :

if ( ! class_exists( 'WP_List_Table' ) )
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );

/**
 * Reports list table class
 */
class TD_Themes_List_Table extends WP_List_Table {

	function __construct() {
		parent::__construct( array(
			'singular' => 'td_theme',
			'plural'   => 'td_themes'
		) );
	}

	function get_columns() {
		return array();
	}

	function prepare_items() {
		global $wpdb;

		$this->_column_headers = array( $this->get_columns(), array(), array() );

		$per_page = 8;
		$current_page = $this->get_pagenum();

		$args = array(
			'paged'          => $current_page,
			'post_type'      => 'td_theme',
			'posts_per_page' => $per_page
		);

		$args['post_status'] = isset( $_REQUEST['post_status'] ) ? $_REQUEST['post_status'] : '';

		$query = new WP_query( $args );

		$this->items = $query->get_posts();

		$this->set_pagination_args(array(
			'total_items' => $query->found_posts,
			'per_page' => $per_page
		));
	}

	function display() {
?>
		<div class="tablenav top themes">
			<?php $this->pagination( 'top' ); ?>
			<br class="clear" />
		</div>

		<div id="availablethemes">
			<?php $this->display_rows_or_placeholder(); ?>
		</div>

		<div class="tablenav bottom themes">
			<?php $this->pagination( 'bottom' ); ?>
			<br class="clear" />
		</div>
<?php
	}

	function display_rows() {
		$themes = $this->items;

		$post_type_object = get_post_type_object( 'td_theme' );
?>
<?php foreach ( $themes as $theme ) : ?>
	<?php $title = get_the_title( $theme->ID ); ?>
	<?php $edit_link = get_edit_post_link( $theme->ID ); ?>
	<?php $can_edit_post = current_user_can( $post_type_object->cap->edit_post, $theme->ID ); ?>

	<div class="available-theme">
		<a href="<?php echo $edit_link; ?>" class="screenshot">
			<?php echo td_get_screenshot( $theme->ID ); ?>
		</a>

		<h4>
			<?php echo $title; ?>
			<?php echo td_get_current_version( $theme->ID ); ?>
		</h4>

		<span class="action-links">
			<?php
			if ( $can_edit_post && 'trash' != $theme->post_status )
				$actions['edit'] = '<a href="' . get_edit_post_link( $theme->ID, true ) . '" title="' . esc_attr( __( 'Edit this item page' ) ) . '">' . __( 'Edit Page' ) . '</a>';

			if ( current_user_can( $post_type_object->cap->delete_post, $theme->ID ) ) {
				if ( 'trash' == $theme->post_status )
					$actions['untrash'] = "<a title='" . esc_attr( __( 'Restore this item from the Trash' ) ) . "' href='" . wp_nonce_url( admin_url( sprintf( $post_type_object->_edit_link . '&amp;action=untrash', $theme->ID ) ), 'untrash-' . $theme->post_type . '_' . $theme->ID ) . "'>" . __( 'Restore' ) . "</a>";
				elseif ( EMPTY_TRASH_DAYS )
					$actions['trash'] = "<a class='submitdelete' title='" . esc_attr( __( 'Move this item to the Trash' ) ) . "' href='" . get_delete_post_link( $theme->ID ) . "'>" . __( 'Trash' ) . "</a>";
				if ( 'trash' == $theme->post_status || ! EMPTY_TRASH_DAYS )
					$actions['delete'] = "<a class='submitdelete' title='" . esc_attr( __( 'Delete this item permanently' ) ) . "' href='" . get_delete_post_link( $theme->ID, '', true ) . "'>" . __( 'Delete Permanently' ) . "</a>";
			}

			if ( in_array( $theme->post_status, array( 'pending', 'draft', 'future' ) ) ) {
				if ( $can_edit_post )
					$actions['view'] = '<a href="' . esc_url( add_query_arg( 'preview', 'true', get_permalink( $theme->ID ) ) ) . '" title="' . esc_attr( sprintf( __( 'Preview &#8220;%s&#8221;' ), $title ) ) . '" rel="permalink">' . __( 'Preview' ) . '</a>';
			} elseif ( 'trash' != $theme->post_status ) {
				$actions['view'] = '<a href="' . get_permalink( $theme->ID ) . '" title="' . esc_attr( sprintf( __( 'View &#8220;%s&#8221;' ), $title ) ) . '" rel="permalink">' . __( 'View' ) . '</a>';
			}

			echo implode( ' | ', $actions );
			?>
		</span>
	</div>
<?php endforeach; ?>
<?php
	}

	function get_views() {
		$status_links = array();
		$num_posts = wp_count_posts( 'td_theme', 'readable' );

		$total_posts = array_sum( (array) $num_posts );

		foreach ( get_post_stati( array('show_in_admin_all_list' => false) ) as $state )
			$total_posts -= $num_posts->$state;

		$class = empty( $_REQUEST['post_status'] ) ? ' class="current"' : '';
		$status_links['all'] = "<a href='admin.php?page=td-admin'$class>" . sprintf( _nx( 'All <span class="count">(%s)</span>', 'All <span class="count">(%s)</span>', $total_posts, 'posts' ), number_format_i18n( $total_posts ) ) . '</a>';

		foreach ( get_post_stati(array('show_in_admin_status_list' => true), 'objects') as $status ) {
			$class = '';

			$status_name = $status->name;

			if ( empty( $num_posts->$status_name ) )
				continue;

			if ( isset($_REQUEST['post_status']) && $status_name == $_REQUEST['post_status'] )
				$class = ' class="current"';

			$status_links[$status_name] = "<a href='admin.php?page=td-admin&amp;post_status=$status_name'$class>" . sprintf( translate_nooped_plural( $status->label_count, $num_posts->$status_name ), number_format_i18n( $num_posts->$status_name ) ) . '</a>';
		}

		return $status_links;
	}
}
endif;

