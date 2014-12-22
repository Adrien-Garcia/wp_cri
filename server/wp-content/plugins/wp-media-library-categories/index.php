<?php
/**
 * Plugin Name: Media Library Categories
 * Plugin URI: http://wordpress.org/plugins/wp-media-library-categories/
 * Description: Adds the ability to use categories in the media library.
 * Version: 1.4.15
 * Author: Jeffrey-WP
 * Author URI: http://codecanyon.net/user/jeffrey-wp/?ref=jeffrey-wp
 */

/** Custom update_count_callback */
function wpmediacategory_update_count_callback( $terms = array(), $taxonomy = 'category' ) {
	global $wpdb;

	// default taxonomy
	$taxonomy = 'category';
	// add filter to change the default taxonomy
	$taxonomy = apply_filters( 'wpmediacategory_taxonomy', $taxonomy );

	// select id & count from taxonomy
	$query = "SELECT term_taxonomy_id, MAX(total) AS total FROM ((
	SELECT tt.term_taxonomy_id, COUNT(*) AS total FROM $wpdb->term_relationships tr, $wpdb->term_taxonomy tt WHERE tr.term_taxonomy_id = tt.term_taxonomy_id AND tt.taxonomy = %s GROUP BY tt.term_taxonomy_id
	) UNION ALL (
	SELECT term_taxonomy_id, 0 AS total FROM $wpdb->term_taxonomy WHERE taxonomy = %s
	)) AS unioncount GROUP BY term_taxonomy_id";
	$rsCount = $wpdb->get_results( $wpdb->prepare( $query, $taxonomy, $taxonomy ) );
	// update all count values from taxonomy
	foreach ( $rsCount as $rowCount ) {
		$wpdb->update( $wpdb->term_taxonomy, array( 'count' => $rowCount->total ), array( 'term_taxonomy_id' => $rowCount->term_taxonomy_id ) );
	}
}


/** register taxonomy for attachments */
function wpmediacategory_init() {
	// Default taxonomy
	$taxonomy = 'category';
	// Add filter to change the default taxonomy
	$taxonomy = apply_filters( 'wpmediacategory_taxonomy', $taxonomy );

	if ( $taxonomy != 'category' ) {
		$args = array(
			'hierarchical'          => true,  // hierarchical: true = display as categories, false = display as tags
			'show_admin_column'     => true,
			'update_count_callback' => 'wpmediacategory_update_count_callback'
		);
		register_taxonomy( $taxonomy, array( 'attachment' ), $args );
	} else {
		register_taxonomy_for_object_type( $taxonomy, 'attachment' );
	}
}
add_action( 'init', 'wpmediacategory_init' );


/** change default update_count_callback for category taxonomy */
function wpmediacategory_change_category_update_count_callback() {
	global $wp_taxonomies;

	// Default taxonomy
	$taxonomy = 'category';
	// Add filter to change the default taxonomy
	$taxonomy = apply_filters( 'wpmediacategory_taxonomy', $taxonomy );

	if ( $taxonomy == 'category' ) {
		if ( ! taxonomy_exists( 'category' ) )
			return false;

		$new_arg = &$wp_taxonomies['category']->update_count_callback;
		$new_arg = 'wpmediacategory_update_count_callback';
	}
}
add_action( 'init', 'wpmediacategory_change_category_update_count_callback', 100 );


/** custom gallery shortcode */
function wpmediacategory_gallery_atts( $result, $defaults, $atts ) {

    if ( isset( $atts['category'] ) ) {

		// Default taxonomy
		$taxonomy = 'category';
		// Add filter to change the default taxonomy
		$taxonomy = apply_filters( 'wpmediacategory_taxonomy', $taxonomy );

		$category = $atts['category'];

		// category slug?
		if ( ! is_numeric( $category ) ) {

			if ( $taxonomy != 'category' ) {

				$term = get_term_by( 'slug', $category, $taxonomy );
				if ( false !== $term ) {
					$category = $term->term_id;
				} else {
					// not existing category slug
					$category = '';
				}

			} else {

				$categoryObject = get_category_by_slug( $category );
				if ( false !== $categoryObject ) {
					$category = $categoryObject->term_id;
				} else {
					// not existing category slug
					$category = '';
				}
			}

		}

		if ( $category != '' ) {

			$ids_new = array();

			if ( $taxonomy != 'category' ) {

				$args = array(
					'post_type'   => 'attachment',
					'numberposts' => -1,
					'post_status' => null,
					'tax_query'   => array(
						array(
							'taxonomy' => $taxonomy,
							'field'    => 'id',
							'terms'    => $category
						)
					)
				);

			} else {

				$args = array(
					'post_type'   => 'attachment',
					'numberposts' => -1,
					'post_status' => null,
					'category'    => $category
				);

			}
			$attachments = get_posts( $args );

			if ( ! empty( $attachments ) ) {

				// ids attribute already present?
				if ( isset( $atts['ids'] ) ) {
					$ids_old = explode( ',', $atts['ids'] );
					foreach ( $attachments as $attachment ) {
						// preserve id if in the selected category
						if ( in_array( $attachment->ID, $ids_old ) ) {
							$ids_new[] = $attachment->ID;
						}
					}
				} else {
					foreach ( $attachments as $attachment ) {
						$ids_new[] = $attachment->ID;
					}
				}

				$atts['ids'] = $ids_new;
			} else {
				$atts['ids'] = -1; // don't display images if category is empty
			}

		}

		$result['include'] = implode( ',', $atts['ids'] );
		$result['category'] = $atts['category'];

	}

	return $result;
 
}
add_filter( 'shortcode_atts_gallery', 'wpmediacategory_gallery_atts', 10, 3 );


// load code that is only needed in the admin section
if ( is_admin() ) {

	/** Handle default category of attachments without category */
	function wpmediacategory_set_attachment_category( $post_ID ) {

		// default taxonomy
		$taxonomy = 'category';
		// add filter to change the default taxonomy
		$taxonomy = apply_filters( 'wpmediacategory_taxonomy', $taxonomy );

		// if attachment already have categories, stop here
		if ( wp_get_object_terms( $post_ID, $taxonomy ) )
			return;

		// no, then get the default one
		$post_category = array( get_option('default_category') );

		// then set category if default category is set on writting page
		if ( $post_category )
			wp_set_post_categories( $post_ID, $post_category );
	}
	add_action( 'add_attachment', 'wpmediacategory_set_attachment_category' );
	add_action( 'edit_attachment', 'wpmediacategory_set_attachment_category' );


	/** Custom walker for wp_dropdown_categories, based on https://gist.github.com/stephenh1988/2902509 */
	class wpmediacategory_walker_category_filter extends Walker_CategoryDropdown{

		function start_el( &$output, $category, $depth = 0, $args = array(), $id = 0 ) {
			$pad = str_repeat( '&nbsp;', $depth * 3 );
			$cat_name = apply_filters( 'list_cats', $category->name, $category );

			if( ! isset( $args['value'] ) ) {
				$args['value'] = ( $category->taxonomy != 'category' ? 'slug' : 'id' );
			}

			$value = ( $args['value']=='slug' ? $category->slug : $category->term_id );

			$output .= '<option class="level-' . $depth . '" value="' . $value . '"';
			if ( $value === (string) $args['selected'] ) {
				$output .= ' selected="selected"';
			}
			$output .= '>';
			$output .= $pad . $cat_name;
			if ( $args['show_count'] )
				$output .= '&nbsp;&nbsp;(' . $category->count . ')';

			$output .= "</option>\n";
		}

	}


	/** Add a category filter */
	function wpmediacategory_add_category_filter() {
		global $pagenow;
		if ( 'upload.php' == $pagenow ) {
			// Default taxonomy
			$taxonomy = 'category';
			// Add filter to change the default taxonomy
			$taxonomy = apply_filters( 'wpmediacategory_taxonomy', $taxonomy );
			if ( $taxonomy != 'category' ) {
				$dropdown_options = array(
					'taxonomy'        => $taxonomy,
					'name'            => $taxonomy,
					'show_option_all' => __( 'View all categories' ),
					'hide_empty'      => false,
					'hierarchical'    => true,
					'orderby'         => 'name',
					'show_count'      => true,
					'walker'          => new wpmediacategory_walker_category_filter(),
					'value'           => 'slug'
				);
			} else {
				$dropdown_options = array(
					'taxonomy'        => $taxonomy,
					'show_option_all' => __( 'View all categories' ),
					'hide_empty'      => false,
					'hierarchical'    => true,
					'orderby'         => 'name',
					'show_count'      => false,
					'walker'          => new wpmediacategory_walker_category_filter(),
					'value'           => 'id'
				);
			}
			wp_dropdown_categories( $dropdown_options );
		}
	}
	add_action( 'restrict_manage_posts', 'wpmediacategory_add_category_filter' );


	/** Add custom Bulk Action to the select menus */
	function wpmediacategory_custom_bulk_admin_footer() {
		// default taxonomy
		$taxonomy = 'category';
		// add filter to change the default taxonomy
		$taxonomy = apply_filters( 'wpmediacategory_taxonomy', $taxonomy );
		$terms = get_terms( $taxonomy, 'hide_empty=0' );
		if ( $terms && ! is_wp_error( $terms ) ) :

			echo '<script type="text/javascript">';
			echo 'jQuery(window).load(function() {';
			echo 'jQuery(\'<optgroup id="wpmediacategory_optgroup1" label="' .  html_entity_decode( __( 'Categories' ), ENT_QUOTES, 'UTF-8' ) . '">\').appendTo("select[name=\'action\']");';
			echo 'jQuery(\'<optgroup id="wpmediacategory_optgroup2" label="' .  html_entity_decode( __( 'Categories' ), ENT_QUOTES, 'UTF-8' ) . '">\').appendTo("select[name=\'action2\']");';

			// add categories
			foreach ( $terms as $term ) {
				$sTxtAdd = esc_js( __( 'Add' ) . ': ' . $term->name );
				echo "jQuery('<option>').val('wpmediacategory_add_" . $term->term_taxonomy_id . "').text('" . $sTxtAdd . "').appendTo('#wpmediacategory_optgroup1');";
				echo "jQuery('<option>').val('wpmediacategory_add_" . $term->term_taxonomy_id . "').text('" . $sTxtAdd . "').appendTo('#wpmediacategory_optgroup2');";
			}
			// remove categories
			foreach ( $terms as $term ) {
				$sTxtRemove = esc_js( __( 'Remove' ) . ': ' . $term->name );
				echo "jQuery('<option>').val('wpmediacategory_remove_" . $term->term_taxonomy_id . "').text('" . $sTxtRemove . "').appendTo('#wpmediacategory_optgroup1');";
				echo "jQuery('<option>').val('wpmediacategory_remove_" . $term->term_taxonomy_id . "').text('" . $sTxtRemove . "').appendTo('#wpmediacategory_optgroup2');";
			}
			// remove all categories
			echo "jQuery('<option>').val('wpmediacategory_remove_0').text('" . esc_js(  __( 'Delete all' ) ) . "').appendTo('#wpmediacategory_optgroup1');";
			echo "jQuery('<option>').val('wpmediacategory_remove_0').text('" . esc_js(  __( 'Delete all' ) ) . "').appendTo('#wpmediacategory_optgroup2');";
			echo "});";
			echo "</script>";

		endif;
	}
	add_action( 'admin_footer-upload.php', 'wpmediacategory_custom_bulk_admin_footer' );


	/** Handle the custom Bulk Action */
	function wpmediacategory_custom_bulk_action() {
		global $wpdb;

		if ( ! isset( $_REQUEST['action'] ) ) {
			return;
		}

		// is it a category?
		$sAction = ( $_REQUEST['action'] != -1 ) ? $_REQUEST['action'] : $_REQUEST['action2'];
		if ( substr( $sAction, 0, 16 ) != 'wpmediacategory_' ) {
			return;
		}

		// security check
		check_admin_referer( 'bulk-media' );

		// make sure ids are submitted.  depending on the resource type, this may be 'media' or 'post'
		if( isset( $_REQUEST['media'] ) ) {
			$post_ids = array_map( 'intval', $_REQUEST['media'] );
		}

		if( empty( $post_ids ) ) {
			return;
		}

		$sendback = admin_url( "upload.php?editCategory=1" );

		// remember pagenumber
		$pagenum = isset( $_REQUEST['paged'] ) ? absint( $_REQUEST['paged'] ) : 0;
		$sendback = add_query_arg( 'paged', $pagenum, $sendback );

		// remember orderby
		if ( isset( $_REQUEST['orderby'] ) ) {
			$sOrderby = $_REQUEST['orderby'];
			$sendback = add_query_arg( 'orderby', $sOrderby, $sendback );
		}
		// remember order
		if ( isset( $_REQUEST['order'] ) ) {
			$sOrder = $_REQUEST['order'];
			$sendback = add_query_arg( 'order', $sOrder, $sendback );
		}
		// remember author
		if ( isset( $_REQUEST['author'] ) ) {
			$sOrderby = $_REQUEST['author'];
			$sendback = add_query_arg( 'author', $sOrderby, $sendback );
		}

		foreach( $post_ids as $post_id ) {

			if ( is_numeric( str_replace( 'wpmediacategory_add_', '', $sAction ) ) ) {
				$nCategory = str_replace( 'wpmediacategory_add_', '', $sAction );

				// update or insert category
				$wpdb->replace( $wpdb->term_relationships,
					array(
						'object_id'        => $post_id,
						'term_taxonomy_id' => $nCategory
					),
					array(
						'%d',
						'%d'
					)
				);

			} else if ( is_numeric( str_replace( 'wpmediacategory_remove_', '', $sAction ) ) ) {
				$nCategory = str_replace( 'wpmediacategory_remove_', '', $sAction );

				// remove all categories
				if ( $nCategory == 0 ) {
					$wpdb->delete( $wpdb->term_relationships,
						array(
							'object_id' => $post_id
						),
						array(
							'%d'
						)
					);
				// remove category
				} else {
					$wpdb->delete( $wpdb->term_relationships,
						array(
							'object_id'        => $post_id,
							'term_taxonomy_id' => $nCategory
						),
						array(
							'%d',
							'%d'
						)
					);
				}

			}
		}

		wpmediacategory_update_count_callback();

		wp_redirect( $sendback );
		exit();
	}
	add_action( 'load-upload.php', 'wpmediacategory_custom_bulk_action' );


	/** Display an admin notice on the page after changing category */
	function wpmediacategory_custom_bulk_admin_notices() {
		global $post_type, $pagenow;

		if ( $pagenow == 'upload.php' && $post_type == 'attachment' && isset( $_GET['editCategory'] ) ) {
			echo '<div class="updated"><p>' . __( 'Settings saved.' ) . '</p></div>';
		}
	}
	add_action( 'admin_notices', 'wpmediacategory_custom_bulk_admin_notices' );


	/** Add a link to media categories on the plugin page */
	function wpmediacategory_add_plugin_action_links( $links ) {
		// default taxonomy
		$taxonomy = 'category';
		// add filter to change the default taxonomy
		$taxonomy = apply_filters( 'wpmediacategory_taxonomy', $taxonomy );
		return array_merge(
			array(
				'settings' => '<a href="' . get_bloginfo( 'wpurl' ) . '/wp-admin/edit-tags.php?taxonomy=' . $taxonomy . '&amp;post_type=attachment">' . __( 'Categories' ) . '</a>',
				'premium' => '<a href="http://codecanyon.net/item/media-library-categories-premium/6691290?ref=jeffrey-wp">' . __( 'Get Premium Version' ) . '</a>'
			),
			$links
		);
	}
	add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'wpmediacategory_add_plugin_action_links' );


	/** Changing categories in the 'grid view' */
	function wpmediacategory_ajax_query_attachments() {

		if ( ! current_user_can( 'upload_files' ) ) {
			wp_send_json_error();
		}

		$taxonomies = get_object_taxonomies( 'attachment', 'names' );

		$query = isset( $_REQUEST['query'] ) ? (array) $_REQUEST['query'] : array();

		$defaults = array(
			's', 'order', 'orderby', 'posts_per_page', 'paged', 'post_mime_type',
			'post_parent', 'post__in', 'post__not_in'
		);
		$query = array_intersect_key( $query, array_flip( array_merge( $defaults, $taxonomies ) ) );

		$query['post_type'] = 'attachment';
		$query['post_status'] = 'inherit';
		if ( current_user_can( get_post_type_object( 'attachment' )->cap->read_private_posts ) )
			$query['post_status'] .= ',private';
			
		$query['tax_query'] = array( 'relation' => 'AND' );

		foreach ( $taxonomies as $taxonomy ) {				
			if ( isset( $query[$taxonomy] ) && is_numeric( $query[$taxonomy] ) ) {
				array_push( $query['tax_query'], array(
					'taxonomy' => $taxonomy,
					'field'    => 'id',
					'terms'    => $query[$taxonomy]
				));	
			}
			unset ( $query[$taxonomy] );
		}

		$query = apply_filters( 'ajax_query_attachments_args', $query );
		$query = new WP_Query( $query );

		$posts = array_map( 'wp_prepare_attachment_for_js', $query->posts );
		$posts = array_filter( $posts );

		wp_send_json_success( $posts );
	}
	add_action( 'wp_ajax_query-attachments', 'wpmediacategory_ajax_query_attachments', 0 );


	/** Enqueue admin scripts and styles */
	function wpmediacategory_enqueue_media_action() {
		global $pagenow;
		if ( wp_script_is( 'media-editor' ) && 'upload.php' == $pagenow ) {

			// Default taxonomy
			$taxonomy = 'category';
			// Add filter to change the default taxonomy
			$taxonomy = apply_filters( 'wpmediacategory_taxonomy', $taxonomy );

			if ( $taxonomy != 'category' ) {
				$dropdown_options = array(
					'taxonomy'        => $taxonomy,
					'hide_empty'      => false,
					'hierarchical'    => true,
					'orderby'         => 'name',
					'show_count'      => true,
					'walker'          => new wpmediacategory_walker_category_mediagridfilter(),
					'value'           => 'id',
					'echo'            => false
				);
			} else {
				$dropdown_options = array(
					'taxonomy'        => $taxonomy,
					'hide_empty'      => false,
					'hierarchical'    => true,
					'orderby'         => 'name',
					'show_count'      => false,
					'walker'          => new wpmediacategory_walker_category_mediagridfilter(),
					'value'           => 'id',
					'echo'            => false
				);
			}
			$attachment_terms = wp_dropdown_categories( $dropdown_options );
			$attachment_terms = preg_replace( array( "/<select([^>]*)>/", "/<\/select>/" ), "", $attachment_terms );

			echo '<script type="text/javascript">';
			echo '/* <![CDATA[ */';
			echo 'var wpmediacategory_taxonomies = {"' . $taxonomy . '":{"list_title":"' . html_entity_decode( __( 'View all categories' ), ENT_QUOTES, 'UTF-8' ) . '","term_list":[' . substr( $attachment_terms, 2 ) . ']}};';
			echo '/* ]]> */';
			echo '</script>';

			wp_enqueue_script( 'wpmediacategory-media-views', plugins_url( 'js/wpmediacategory-media-views.min.js', __FILE__ ), array( 'media-views' ), '1.4.15', true );
		}
	}
	add_action( 'admin_enqueue_scripts', 'wpmediacategory_enqueue_media_action' );


	/** Custom walker for wp_dropdown_categories for media grid view filter */
	class wpmediacategory_walker_category_mediagridfilter extends Walker_CategoryDropdown {

		function start_el( &$output, $category, $depth = 0, $args = array(), $id = 0 ) {
			$pad = str_repeat( '&nbsp;', $depth * 3 );

			$cat_name = apply_filters( 'list_cats', $category->name, $category );

			// {"term_id":"1","term_name":"no category"}
			$output .= ',{"term_id":"' . $category->term_id . '",';

			$output .= '"term_name":"' . $pad . esc_attr( $cat_name );
			if ( $args['show_count'] ) {
				$output .= '&nbsp;&nbsp;('. $category->count .')';
			}
			$output .= '"}';
		}

	}

}
?>