<?php

/*******************************************************************************************************************************
 MENUS & NAVIGATION
*******************************************************************************************************************************/

/**
 * Affichage de la description pour menu principal (>= 1024)
 *
 */
class Menu_With_Description extends Walker_Nav_Menu {
	function start_el(&$output, $item, $depth = 0, $args = array(), $id = 0) {
		global $wp_query;
		$indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';
 
		$class_names = $value = '';
 
		$classes = empty( $item->classes ) ? array() : (array) $item->classes;
 
		$class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item ) );
		$class_names = ' class="' . esc_attr( $class_names ) . '"';
 
		$output .= $indent . '<li id="menu-item-'. $item->ID . '"' . $value . $class_names .'>';
 
		$attributes = ! empty( $item->attr_title ) ? ' title="' . esc_attr( $item->attr_title ) .'"' : '';
		$attributes .= ! empty( $item->target ) ? ' target="' . esc_attr( $item->target ) .'"' : '';
		$attributes .= ! empty( $item->xfn ) ? ' rel="' . esc_attr( $item->xfn ) .'"' : '';
		$attributes .= ! empty( $item->url ) ? ' href="' . esc_attr( $item->url ) .'"' : '';
 
		$item_output = $args->before;
		$item_output .= '<a'. $attributes .'>';
		$item_output .= $args->link_before . apply_filters( 'the_title', $item->title, $item->ID ) . $args->link_after;
		$item_output .= '</a>';
		$item_output .= $args->after;
		$item_output .= '<span class="sub">' . $item->description . '</span>';
 
		$output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
	}
}



/**
 * Noeud d'arborescence basé sur élément de menu actif / A insérer dans votre template (généralement sidebar.php) (voir plus bas pour utilisation)
 *
 */
add_filter( 'wp_nav_menu_objects', 'my_wp_nav_menu_objects_sub_menu', 10, 2 );
 
// filter_hook function to react on sub_menu flag
function my_wp_nav_menu_objects_sub_menu( $sorted_menu_items, $args ) {
  if ( isset( $args->sub_menu ) ) {
    $root_id = 0;
    
    // find the current menu item
    foreach ( $sorted_menu_items as $menu_item ) {
      if ( $menu_item->current ) {
        // set the root id based on whether the current menu item has a parent or not
        $root_id = ( $menu_item->menu_item_parent ) ? $menu_item->menu_item_parent : $menu_item->ID;
        break;
      }
    }
    
    // find the top level parent
    if ( ! isset( $args->direct_parent ) ) {
      $prev_root_id = $root_id;
      while ( $prev_root_id != 0 ) {
        foreach ( $sorted_menu_items as $menu_item ) {
          if ( $menu_item->ID == $prev_root_id ) {
            $prev_root_id = $menu_item->menu_item_parent;
            // don't set the root_id to 0 if we've reached the top of the menu
            if ( $prev_root_id != 0 ) $root_id = $menu_item->menu_item_parent;
            break;
          } 
        }
      }
    }
 
    $menu_item_parents = array();
    foreach ( $sorted_menu_items as $key => $item ) {
      // init menu_item_parents
      if ( $item->ID == $root_id ) $menu_item_parents[] = $item->ID;
 
      if ( in_array( $item->menu_item_parent, $menu_item_parents ) ) {
        // part of sub-tree: keep!
        $menu_item_parents[] = $item->ID;
      } else if ( ! ( isset( $args->show_parent ) && in_array( $item->ID, $menu_item_parents ) ) ) {
        // not part of sub-tree: away with it!
        unset( $sorted_menu_items[$key] );
      }
    }
    
    return $sorted_menu_items;

  } else {

    return $sorted_menu_items;

  }
  
}

/*
$sidebarmenu = wp_nav_menu(

	array(

		'menu'    => 'Menu principal',
		'sub_menu' => true,
        'echo' => FALSE,
    	'fallback_cb' => '__return_false',
    	'depth' => 5,
        'show_parent' => true

	)

);

if( !empty($sidebarmenu) ) :

?>

<aside class="sidebar portal-sub-nav">

	<?php echo $sidebarmenu; ?>

</aside>
*/





/*******************************************************************************************************************************
 BACKOFFICE
*******************************************************************************************************************************/

/**
 * Icones personnalisés pour les diff�rents custome posts
 * - Remplacer le selecteur ".menu-icon-xxxx" par la classe voulu (peut etre trouvé avec l'inspecteur)
 * - http://melchoyce.github.io/dashicons/ pour récupérer le code de l'icône (copy CSS)
 *
 */
function add_menu_icons_styles(){
?>
 
<style>
#adminmenu .menu-icon-event div.wp-menu-image:before { 
 	content: '\f145';
}
#adminmenu .menu-icon-slides div.wp-menu-image:before {
	content: "\f233";
}
#adminmenu #menu-posts-cookielawinfo div.wp-menu-image:before {
	content: "\f348";
}
#adminmenu .toplevel_page_mappress div.wp-menu-image:before {
	content: "\f231";
}
#adminmenu .toplevel_page_settings div.wp-menu-image:before {
	content: "\f108";
}

/***VIE CRIDON*/
#adminmenu .toplevel_page_mvc_vie_cridons div.wp-menu-image:before {
	content: "\f491";
}
/***FLASHES*/
#adminmenu .toplevel_page_mvc_flashes div.wp-menu-image:before {
	content: "\f491";
}

/***USER CRIDON*/
#adminmenu .toplevel_page_mvc_user_cridons div.wp-menu-image:before {
	content: "\f110";
}
/***VEILLES CRIDON*/
#adminmenu .toplevel_page_mvc_veilles div.wp-menu-image:before {
	content: "\f105";
}

/***QUESTIONS CRIDON*/
#adminmenu .toplevel_page_mvc_questions div.wp-menu-image:before {
	content: "\f105";
}

/***NOTAIRES CRIDON*/
#adminmenu .toplevel_page_mvc_notaires div.wp-menu-image:before {
	content: "\f338";
}

/***MATIERES CRIDON*/
#adminmenu .toplevel_page_mvc_matieres div.wp-menu-image:before {
	content: "\f318";
}

/***FORMATIONS CRIDON*/
#adminmenu .toplevel_page_mvc_formations div.wp-menu-image:before {
	content: "\f105";
}

/***DOCUMENTS CRIDON*/
#adminmenu .toplevel_page_mvc_documents div.wp-menu-image:before {
	content: "\f103";
}

/***COMPETENCES CRIDON*/
#adminmenu .toplevel_page_mvc_competences div.wp-menu-image:before {
	content: "\f118";
}

/***CAHIER CRIDON*/
#adminmenu .toplevel_page_mvc_cahier_cridons div.wp-menu-image:before {
	content: "\f331";
}

/***AFFECTATION*/
#adminmenu .toplevel_page_mvc_affectations div.wp-menu-image:before {
	content: "\f237";
}






























</style>
 
<?php
}
add_action( 'admin_head', 'add_menu_icons_styles' );


/**
 * Feuille de styles pour l'�diteur
 *
 */

function my_theme_add_editor_styles() {
	add_editor_style( 'library/css/front-back-styles.css' );
}
add_action( 'init', 'my_theme_add_editor_styles' );


/**
 * Modifie les éléments disponible de TinyMCE
 *
 */
function mce_mod( $init ) {
	$init['block_formats'] = 'Paragraph=p;Heading 2=h2;Heading 3=h3;Heading 4=h4';

	/*$style_formats = array (
			array( 'title' => 'Bold text', 'inline' => 'b' ),
			array( 'title' => 'Red text', 'inline' => 'span', 'styles' => array( 'color' => '#ff0000' ) ),
			array( 'title' => 'Red header', 'block' => 'h1', 'styles' => array( 'color' => '#ff0000' ) ),
			array( 'title' => 'Example 1', 'inline' => 'span', 'classes' => 'example1' ),
			array( 'title' => 'Example 2', 'inline' => 'span', 'classes' => 'example2' )
	);

	$init['style_formats'] = json_encode( $style_formats );

	$init['style_formats_merge'] = false;*/
	return $init;
}
add_filter('tiny_mce_before_init', 'mce_mod');

/*function mce_add_buttons( $buttons ){
	array_splice( $buttons, 1, 0, 'styleselect' );
	return $buttons;
}
add_filter( 'mce_buttons_2', 'mce_add_buttons' );*/



/**
 * Excerpt pour les pages
 *
 */
add_action( 'init', 'my_add_excerpts_to_pages' );
function my_add_excerpts_to_pages() {
	add_post_type_support( 'page', 'excerpt' );
}



/**
 * gravity form pour role éditeur
 *
 */
function add_grav_forms(){
	$role = get_role('editor');
	$role->add_cap('gform_full_access');
}
add_action('admin_init','add_grav_forms');



/**
 * Partage des modèles
 *
 */
function my_save_post($id)
{
	$p = get_post($id);
	if ($p->post_type === 'tinymcetemplates') {
		if (isset($_POST['_tinymcetemplates-share'])) {
			update_post_meta($id, '_tinymcetemplates-share', 1);
		}
	}
}
add_action('save_post', 'my_save_post', 11);




/**
 * Désactivation complète des commentaires
 *
 */
function df_disable_comments_post_types_support() {
	$post_types = get_post_types();
	foreach ($post_types as $post_type) {
		if(post_type_supports($post_type, 'comments')) {
			remove_post_type_support($post_type, 'comments');
			remove_post_type_support($post_type, 'trackbacks');
		}
	}
}
add_action('admin_init', 'df_disable_comments_post_types_support');

// Close comments on the front-end
function df_disable_comments_status() {
	return false;
}
add_filter('comments_open', 'df_disable_comments_status', 20, 2);
add_filter('pings_open', 'df_disable_comments_status', 20, 2);

// Hide existing comments
function df_disable_comments_hide_existing_comments($comments) {
	$comments = array();
	return $comments;
}
add_filter('comments_array', 'df_disable_comments_hide_existing_comments', 10, 2);

// Remove comments page in menu
function df_disable_comments_admin_menu() {
	remove_menu_page('edit-comments.php');
}
add_action('admin_menu', 'df_disable_comments_admin_menu');

// Redirect any user trying to access comments page
function df_disable_comments_admin_menu_redirect() {
	global $pagenow;
	if ($pagenow === 'edit-comments.php') {
		wp_redirect(admin_url()); exit;
	}
}
add_action('admin_init', 'df_disable_comments_admin_menu_redirect');

// Remove comments metabox from dashboard
function df_disable_comments_dashboard() {
	remove_meta_box('dashboard_recent_comments', 'dashboard', 'normal');
}
add_action('admin_init', 'df_disable_comments_dashboard');

// Remove comments links from admin bar
function df_disable_comments_admin_bar() {
	if (is_admin_bar_showing()) {
		remove_action('admin_bar_menu', 'wp_admin_bar_comments_menu', 60);
	}
}
add_action('init', 'df_disable_comments_admin_bar');




/**
 * Sticky pour custom posts
 * - Remplacer "$typenow == 'custom_type'" par le nom de votre custom post
 * - Possibilit� d'en rajouter : $typenow == 'custom_type' || $typenow == 'custom_type_2' || etc ...
 *
 */
add_action( 'admin_footer-post.php', 'gkp_add_sticky_post_support' );
add_action( 'admin_footer-post-new.php', 'gkp_add_sticky_post_support' );

function gkp_add_sticky_post_support()
{ global $post, $typenow; ?>
	
	<?php if ( $typenow == 'custom_type' && current_user_can( 'edit_others_posts' ) ) : ?>
	<script>
	jQuery(function($) {
		var sticky = "<br/><span id='sticky-span'><input id='sticky' name='sticky' type='checkbox' value='sticky' <?php checked( is_sticky( $post->ID ) ); ?> /> <label for='sticky' class='selectit'><?php _e( 'Stick this post to the front page','bonestheme' ); ?></label><br /></span>";	
		$('[for=visibility-radio-public]').append(sticky);	
	});
	</script>
	<?php endif; ?>
	
<?php

}





/**
 * Catégories de média différente de celles des posts/pages etc...
 *
 */
add_filter( 'wpmediacategory_taxonomy', function(){ return 'category_media'; }, 1 ); //requires PHP 5.3 or newer




/*******************************************************************************************************************************
 FRONTOFFICE
*******************************************************************************************************************************/

/**
 * Tronquage contenu
 * ATTENTION : Eviter d'utiliser sur the_content(). Privilégier l'excerpt ou tout autre contenu non mis en forme
 * via une �diteur WYSIWYG
 *
 */
function truncate($string, $max_length, $replacement = '', $trunc_at_space = false)
{
	$max_length -= strlen($replacement);
	$string_length = strlen($string);

	if($string_length <= $max_length)
		return $string;

	if( $trunc_at_space && ($space_position = strrpos($string, ' ', $max_length-$string_length)) )
		$max_length = $space_position;

	return substr_replace($string, $replacement, $max_length);
}





/**
 * Attribution du template de la catégorie parent aux catégories enfants (si modèle de catégorie spécifique)
 *
 */
function load_cat_parent_template()
{
    global $wp_query;

    if (!$wp_query->is_category)
        return true; // saves a bit of nesting

    // get current category object
    $cat = $wp_query->get_queried_object();

    // trace back the parent hierarchy and locate a template
    while ($cat && !is_wp_error($cat)) {
        $template = get_template_directory() . "/category-{$cat->slug}.php";

        if (file_exists($template)) {
            load_template($template);
            exit;
        }

        $cat = $cat->parent ? get_category($cat->parent) : false;
    }
}
add_action('template_redirect', 'load_cat_parent_template');



/**
 * Permet d'afficher les customs posts dans les template archives.php & category.php
 * - Remplacer 'custom_type' par le nom de vos customs posts
 */
function namespace_add_custom_types( $query ) {
  if( is_category() || is_tag() && empty( $query->query_vars['suppress_filters'] ) ) {
    $query->set( 'post_type', array(
     'post', 'nav_menu_item'
    ));
    return $query;
  }
}
add_filter( 'pre_get_posts', 'namespace_add_custom_types' );




/**
 * Permet d'afficher les customs posts dans les r�sulats de recherche
 * - Remplacer 'custom_type_x' par le nom de vos customs posts
 */
function searchAll( $query ) {
	if ( $query->is_search ) {
		$query->set( 'post_type', array( 'post', 'page', 'feed', 'custom_type', 'custom_type_2'));
	}
	return $query;
}

// The hook needed to search ALL content
add_filter( 'the_search_query', 'searchAll' );


/*
 Gestion des couleurs dans TinyMCE
 Remplacer les codes HEXA par ceux fournis sur la maquette
*/
function tiny_mce_custom_palette($init) {
	// Code � d�commenter lors du passage � Wordpress v4+

	$default_colours = '
      "000000", "Noir",
      "535353", "Gris fonc�",
      "989898", "Gris interm�diaire",
      "a1a1a1", "Gris clair",
      "fb9200", "Orange"
      ';
 	$custom_colours = '';

  	$init['textcolor_map'] = '['.$default_colours.','.$custom_colours.']';
  	$init['textcolor_rows'] = 6; // expand colour grid to 6 rows

  	return $init;
}

add_filter('tiny_mce_before_init', 'tiny_mce_custom_palette');


/*
 Supprime les entr�e 'Personnaliser' & 'Arri�re-plan' du menu apparence (tous les utilisateurs).
*/
function remove_customize() {
    $customize_url_arr = array();
    $customize_url_arr[] = 'customize.php'; // 3.x
    $customize_url = add_query_arg( 'return', urlencode( wp_unslash( $_SERVER['REQUEST_URI'] ) ), 'customize.php' );
    $customize_url_arr[] = $customize_url; // 4.0 & 4.1
    if ( current_theme_supports( 'custom-header' ) && current_user_can( 'customize') ) {
        $customize_url_arr[] = add_query_arg( 'autofocus[control]', 'header_image', $customize_url ); // 4.1
        $customize_url_arr[] = 'custom-header'; // 4.0
    }
    if ( current_theme_supports( 'custom-background' ) && current_user_can( 'customize') ) {
        $customize_url_arr[] = add_query_arg( 'autofocus[control]', 'background_image', $customize_url ); // 4.1
        $customize_url_arr[] = 'custom-background'; // 4.0
    }
    foreach ( $customize_url_arr as $customize_url ) {
        remove_submenu_page( 'themes.php', $customize_url );
    }
}
add_action( 'admin_menu', 'remove_customize', 999 );

?>