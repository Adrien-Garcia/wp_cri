<?php
/* Welcome to Bones :)
This is the core Bones file where most of the
main functions & features reside. If you have
any custom functions, it's best to put them
in the functions.php file.

Developed by: Eddie Machado
URL: http://themble.com/bones/

  - head cleanup (remove rsd, uri links, junk css, ect)
  - enqueueing scripts & styles
  - theme support functions
  - custom menu output & fallbacks
  - related post function
  - page-navi function
  - removing <p> from around images
  - customizing the post excerpt
  - custom google+ integration
  - adding custom fields to user profiles

*/

/*********************
WP_HEAD GOODNESS
The default wordpress head is
a mess. Let's clean it up by
removing all the junk we don't
need.
*********************/

function bones_head_cleanup() {
	// category feeds
	// remove_action( 'wp_head', 'feed_links_extra', 3 );
	// post and comment feeds
	// remove_action( 'wp_head', 'feed_links', 2 );
	// EditURI link
	remove_action( 'wp_head', 'rsd_link' );
	// windows live writer
	remove_action( 'wp_head', 'wlwmanifest_link' );
	// index link
	remove_action( 'wp_head', 'index_rel_link' );
	// previous link
	remove_action( 'wp_head', 'parent_post_rel_link', 10, 0 );
	// start link
	remove_action( 'wp_head', 'start_post_rel_link', 10, 0 );
	// links for adjacent posts
	remove_action( 'wp_head', 'adjacent_posts_rel_link_wp_head', 10, 0 );
	// WP version
	remove_action( 'wp_head', 'wp_generator' );
	// remove WP version from css
	add_filter( 'style_loader_src', 'bones_remove_wp_ver_css_js', 9999 );
	// remove Wp version from scripts
	add_filter( 'script_loader_src', 'bones_remove_wp_ver_css_js', 9999 );

} /* end bones head cleanup */

// remove WP version from RSS
function bones_rss_version() { return ''; }

// remove WP version from scripts
function bones_remove_wp_ver_css_js( $src ) {
	if ( strpos( $src, 'ver=' ) )
		$src = remove_query_arg( 'ver', $src );
	return $src;
}

// remove injected CSS for recent comments widget
function bones_remove_wp_widget_recent_comments_style() {
	if ( has_filter( 'wp_head', 'wp_widget_recent_comments_style' ) ) {
		remove_filter( 'wp_head', 'wp_widget_recent_comments_style' );
	}
}

// remove injected CSS from recent comments widget
function bones_remove_recent_comments_style() {
	global $wp_widget_factory;
	if (isset($wp_widget_factory->widgets['WP_Widget_Recent_Comments'])) {
		remove_action( 'wp_head', array($wp_widget_factory->widgets['WP_Widget_Recent_Comments'], 'recent_comments_style') );
	}
}

// remove injected CSS from gallery
function bones_gallery_style($css) {
	return preg_replace( "!<style type='text/css'>(.*?)</style>!s", '', $css );
}


/*********************
SCRIPTS & ENQUEUEING
*********************/

// loading modernizr and jquery, and reply script
function bones_scripts_and_styles() {

  global $wp_styles; // call global $wp_styles variable to add conditional wrapper around ie stylesheet the WordPress way

  if (!is_admin()) {

  		wp_deregister_script( 'jquery' );
  		wp_deregister_script( 'jquery-migrate' );
  		
  		// jquery
  		wp_register_script( 'jquery', get_stylesheet_directory_uri() . '/library/js/min/jquery.min.js', array(), '1.11.1', false );
  		
  		// jquery migrate
  		wp_register_script( 'jquery-migrate', get_stylesheet_directory_uri() . '/library/js/min/jquery-migrate.min.js', array('jquery'), '1.2.1', false );
  	
		// modernizr (without media query polyfill)
		wp_register_script( 'bones-modernizr', get_stylesheet_directory_uri() . '/library/js/min/modernizr.custom.min.js', array(), '2.5.3', false );

		// Owl-carousel
		wp_register_script( 'owl-carousel', get_stylesheet_directory_uri() . '/library/js/min/owl.carousel.min.js', array(), '', false );

		// popup overlay
		wp_register_script( 'popupoverlay', get_stylesheet_directory_uri() . '/library/js/jquery.popupoverlay.js', array(), '', false );

		// register main stylesheet
		wp_register_style( 'bones-stylesheet', get_stylesheet_directory_uri() . '/library/css/style.css', array(), '', 'all' );

		// bxslider stylesheet
		wp_register_style( 'bxslider-stylesheet', get_stylesheet_directory_uri() . '/library/css/modules/jquery.bxslider.css', array(), '', 'all' );

		// GARAMOND
		wp_register_style('wpb-googleFontsGaramont', 'http://fonts.googleapis.com/css?family=EB+Garamond');

		// DOSIS
		wp_register_style('wpb-googleFontsDosis', 'http://fonts.googleapis.com/css?family=Dosis:400,200,300,500,600,800,700');


		// ie-only style sheet
		wp_register_style( 'bones-ie-only', get_stylesheet_directory_uri() . '/library/css/ie.css', array(), '' );

	    // comment reply script for threaded comments
	    if ( is_singular() AND comments_open() AND (get_option('thread_comments') == 1)) {
			  wp_enqueue_script( 'comment-reply' );
	    }

		//adding scripts file in the footer
	    wp_register_script( 'bxslider', get_stylesheet_directory_uri() . '/library/js/min/jquery.bxslider.min.js', array('jquery'), '4.1.2', true );
	    wp_register_script( 'picturefill', get_stylesheet_directory_uri() . '/library/js/min/picturefill.min.js', array(), '2.1.0-beta', true );
	    wp_register_script( 'slider', get_stylesheet_directory_uri() . '/library/js/min/slider.min.js', array( 'jquery' ), '', true );
	    wp_register_script( 'enquire', get_stylesheet_directory_uri() . '/library/js/min/enquire.min.js', array( 'jquery' ), '', true );
	    wp_register_script( 'bones-js', get_stylesheet_directory_uri() . '/library/js/min/scripts.min.js', array( 'jquery' ), '', true );

	    wp_register_script( 'app-js', get_stylesheet_directory_uri() . '/library/js/min/app.min.js', array( 'jquery' ), '', true );

		// enqueue scripts
		wp_enqueue_script( 'jquery' );
		wp_enqueue_script( 'jquery-migrate' );
		wp_enqueue_script( 'bones-modernizr' );
		wp_enqueue_script( 'owl-carousel' );
		wp_enqueue_script( 'popupoverlay' );
		wp_enqueue_script( 'bxslider' );
		wp_enqueue_script( 'picturefill' );
		wp_enqueue_script( 'bones-js' );
		wp_enqueue_script( 'enquire' );
		wp_enqueue_script( 'slider' );
		wp_enqueue_script( 'app-js' );
		
		// enqueue styles
		wp_enqueue_style( 'wpb-googleFontsGaramont' );
		wp_enqueue_style( 'wpb-googleFontsDosis' );
		wp_enqueue_style( 'bones-stylesheet' );
		wp_enqueue_style( 'bxslider-stylesheet' );
		wp_enqueue_style( 'bones-ie-only' );


		$wp_styles->add_data( 'bones-ie-only', 'conditional', 'lte IE 9' ); // add conditional wrapper around ie stylesheet


	}
}

/*********************
THEME SUPPORT
*********************/

// Adding WP 3+ Functions & Theme Support
function bones_theme_support() {

	// wp thumbnails (sizes handled in functions.php)
	add_theme_support( 'post-thumbnails' );

	// default thumb size
	set_post_thumbnail_size(125, 125, true);

	// wp custom background (thx to @bransonwerner for update)
	/*add_theme_support( 'custom-background',
	    array(
	    'default-image' => '',    // background image default
	    'default-color' => '',    // background color default (dont add the #)
	    'wp-head-callback' => '_custom_background_cb',
	    'admin-head-callback' => '',
	    'admin-preview-callback' => ''
	    )
	);*/

	//add_theme_support( 'title-tag' );

	// rss thingy
	add_theme_support('automatic-feed-links');

	// to add header image support go here: http://themble.com/support/adding-header-background-image-support/

	// adding post format support
	/*add_theme_support( 'post-formats',
		array(
			'aside',             // title less blurb
			'gallery',           // gallery of images
			'link',              // quick link to other site
			'image',             // an image
			'quote',             // a quick quote
			'status',            // a Facebook like status update
			'video',             // video
			'audio',             // audio
			'chat'               // chat transcript
		)
	);*/

	// wp menus
	add_theme_support( 'menus' );

	// registering wp3+ menus
	register_nav_menus(
		array(
			'main-nav' => __( 'The Main Menu', 'bonestheme' ),   // main nav in header
			'footer-links' => __( 'Footer Links', 'bonestheme' ) // secondary nav in footer
		)
	);
} /* end bones theme support */

function spi_setup() {
	add_theme_support( 'title-tag' );
}
add_action( 'after_setup_theme', 'spi_setup' );

/*********************
 MENUS & NAVIGATION
*********************/
/**
 *  Menu principal
 *
 */
function nav_principal() {
	// Affiche le menu wp3 si possible (sinon, fallback)
	wp_nav_menu(array(
	'container' => false,                           // Supprime le conteneur par défaut de la navigation
	'container_class' => 'menu clearfix',           // Classe du conteneur
	'menu' => 'Menu principal',                     // Nom du menu
	'menu_class' => 'nav top-nav clearfix',         // Classe du menu
	'theme_location' => 'main-nav',           // Localisation du menu dans le thème
	'before' => '',                                 // Balisage avant le menu
	'after' => '',                                  // Balisage après le menu
	'link_before' => '',                            // Balisage avant chaque lien
	'link_after' => '',                             // Balisage après chaque lien
	'depth' => 0,                                   // Profondeur du menu (0 : aucune)
	'fallback_cb' => 'ao_nav_principale_fallback',  // fallback fonction (si pas de support du menu)
	//'walker' => new Menu_With_Description			// Utilisation de la description
	));
}

/**
 * Menu secondaire (footer)
 *
 */
function nav_pied_de_page() {
	// Affiche le menu wp3 si possible (sinon, fallback)
	wp_nav_menu(array(
	'container' => '',                              // Supprime le conteneur par défaut de la navigation
	'container_class' => 'footer-menu clearfix',    // Classe du conteneur
	'menu' => 'Menu pied de page',                  // Nom du menu
	'menu_class' => 'nav footer-nav clearfix',      // Classe du menu
	'theme_location' => 'footer-links',         // Localisation du menu dans le thème
	'before' => '',                                 // Balisage avant le menu
	'after' => '',                                  // Balisage après le menu
	'link_before' => '',                            // Balisage avant chaque lien
	'link_after' => '',                             // Balisage après chaque lien
	'depth' => 0,                                   // Profondeur du menu (0 : aucune)
	'fallback_cb' => 'ao_nav_pied_de_page_fallback' // fallback fonction (si pas de support du menu)
	));
}

/**
 * Fallback du menu princiapl
 *
 */
function nav_principale_fallback( $args ) {
	wp_page_menu( 'show_home=Accueil' );
}

/**
 * Fallback pour le menu pied de page
 *
 */
function nav_pied_de_page_fallback() {

}

/*********************
RELATED POSTS FUNCTION
*********************/

// Related Posts Function (call using bones_related_posts(); )
function bones_related_posts() {
	echo '<ul id="bones-related-posts">';
	global $post;
	$tags = wp_get_post_tags( $post->ID );
	if($tags) {
		foreach( $tags as $tag ) {
			$tag_arr .= $tag->slug . ',';
		}
		$args = array(
			'tag' => $tag_arr,
			'numberposts' => 5, /* you can change this to show more */
			'post__not_in' => array($post->ID)
		);
		$related_posts = get_posts( $args );
		if($related_posts) {
			foreach ( $related_posts as $post ) : setup_postdata( $post ); ?>
				<li class="related_post"><a class="entry-unrelated" href="<?php the_permalink() ?>" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></li>
			<?php endforeach; }
		else { ?>
			<?php echo '<li class="no_related_post">' . __( 'No Related Posts Yet!', 'bonestheme' ) . '</li>'; ?>
		<?php }
	}
	wp_reset_postdata();
	echo '</ul>';
} /* end bones related posts function */

/*********************
PAGE NAVI
*********************/

// Numeric Page Navi (built into the theme by default)
function bones_page_navi() {
  global $wp_query;
  $bignum = 999999999;
  if ( $wp_query->max_num_pages <= 1 )
    return;
  echo '<nav class="pagination">';
  echo paginate_links( array(
    'base'         => str_replace( $bignum, '%#%', esc_url( get_pagenum_link($bignum) ) ),
    'format'       => '',
    'current'      => max( 1, get_query_var('paged') ),
    'total'        => $wp_query->max_num_pages,
    'prev_text'    => '&larr;',
    'next_text'    => '&rarr;',
    'type'         => 'list',
    'end_size'     => 3,
    'mid_size'     => 3
  ) );
  echo '</nav>';
} /* end page navi */

/*********************
RANDOM CLEANUP ITEMS
*********************/

// remove the p from around imgs (http://css-tricks.com/snippets/wordpress/remove-paragraph-tags-from-around-images/)
function bones_filter_ptags_on_images($content){
	return preg_replace('/<p>\s*(<a .*>)?\s*(<img .* \/>)\s*(<\/a>)?\s*<\/p>/iU', '\1\2\3', $content);
}

// This removes the annoying […] to a Read More link
function bones_excerpt_more($more) {
	global $post;
	// edit here if you like
	return '...  <a class="excerpt-read-more" href="'. get_permalink($post->ID) . '" title="'. __( 'Read ', 'bonestheme' ) . get_the_title($post->ID).'">'. __( 'Read more &raquo;', 'bonestheme' ) .'</a>';
}



?>
