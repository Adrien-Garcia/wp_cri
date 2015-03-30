<?php
function custom_breadcrumbs() {
	
	$chevron = '<span class="chevron">&#8250;</span><span itemprop="child" itemscope itemtype="http://data-vocabulary.org/Breadcrumb">';
	$chevron_home ='<span class="chevron">&#8250;</span><span itemprop="child" itemscope itemtype="http://data-vocabulary.org/Breadcrumb">';
	$home = __('Home','bonestheme'); // text for the 'Home' link
	$before = '<span class="breadcrumb-current" itemprop="title">'; // tag before the current crumb
	$after = '</span>'; // tag after the current crumb
	//$start ='<div itemprop="child" itemscope itemtype="http://data-vocabulary.org/Breadcrumb">';
	//$end = '</div>';
	
	if ( !is_home() && !is_front_page() || is_paged() ) {
	
		echo '<div id="crumbs" itemscope itemtype="http://data-vocabulary.org/Breadcrumb">';
	
		//Hem - skall inte vara child
		global $post;
		$homeLink = home_url();
		echo '<a href="' . $homeLink . '" itemprop="url"><span itemprop="title">' . $home . '</span></a> ' . $chevron_home . ' ';
	
		if ( is_category() ) {
			global $wp_query;
			$cat_obj = $wp_query->get_queried_object();
			$thisCat = $cat_obj->term_id;
			$thisCat = get_category($thisCat);
			$parentCat = get_category($thisCat->parent);
			if ($thisCat->parent != 0) echo(get_category_parents($parentCat, TRUE, ' ' . $chevron . ' '));
			echo $before . __('Archive for ','responsive') . single_cat_title('', false) . $after;
	
		} elseif ( is_day() ) {
			echo '<a href="' . get_year_link(get_the_time('Y')) . '">' . get_the_time('Y') . '</a> ' . $chevron . ' ';
			echo '<a href="' . get_month_link(get_the_time('Y'),get_the_time('m')) . '">' . get_the_time('F') . '</a> ' . $chevron . ' ';
			echo $before . get_the_time('d') . $after;
	
		} elseif ( is_month() ) {
			echo '<a href="' . get_year_link(get_the_time('Y')) . '">' . get_the_time('Y') . '</a> ' . $chevron . ' ';
			echo $before . get_the_time('F') . $after;
	
		} elseif ( is_year() ) {
			echo $before . get_the_time('Y') . $after;
	
		} elseif ( is_single() && !is_attachment() ) { //KÃ¶rs inte vid jobman
			if ( get_post_type() != 'post' ) {
				$post_type = get_post_type_object(get_post_type());
				$slug = $post_type->rewrite;
				echo '<a href="' . $homeLink . '/' . $slug['slug'] . '/"><span itemprop="title">' . $post_type->labels->singular_name . '</span></a> ' . $chevron ;
				echo $before . get_the_title() . $after;
			} else {
				$cat = get_the_category(); $cat = $cat[0];
				echo get_category_parents($cat, TRUE, ' ' . $chevron . ' ');
				echo $before . get_the_title() . $after; //En Post som Ã¤r den aktuella
			}
	
		} elseif ( !is_single() && !is_page() && get_post_type() != 'post' && !is_404() ) {
			$post_type = get_post_type_object(get_post_type());
			echo $before . $post_type->labels->singular_name . $after;
	
		} elseif ( is_attachment() ) {
			$parent = get_post($post->post_parent);
			$cat = get_the_category($parent->ID); $cat = $cat[0];
			echo get_category_parents($cat, TRUE, ' ' . $chevron . ' ');
			echo '<a href="' . get_permalink($parent) . '">' . $parent->post_title . '</a> ' . $chevron . ' ';
			echo $before . get_the_title() . $after;
	
		} elseif ( is_page() && !$post->post_parent ) { //If the page is one level below home
			echo $before . get_the_title(). $after;
	
		} elseif ( is_page() && $post->post_parent ) {
			$parent_id  = $post->post_parent;
			$breadcrumbs = array();
			while ($parent_id) {
				$page = get_post($parent_id);
				// Check if page should be accessible in breancrumbs
				$in_crumbs = get_post_meta($parent_id, 'in_crumbs', true);

				//Ordernary bread crumb
				if( $in_crumbs == "Non" ){
					$breadcrumbs[] = '<a class="no-link"><span itemprop="title">' . get_the_title($page->ID) . '</span></a>';
				}
				else{
					$breadcrumbs[] = '<a href="' . get_permalink($page->ID) . '" itemprop="url"><span itemprop="title">' . get_the_title($page->ID) . '</span></a>';
				}
				$parent_id  = $page->post_parent;
			}
			//The current page
			$breadcrumbs = array_reverse($breadcrumbs);
			/* The orgiginal code
			 foreach ($breadcrumbs as $crumb) echo $crumb . '' . $chevron . ' ';
			echo  $before  . get_the_title() . 'yy' . $after;
			*/
	
			//start job manager - Modifiaction to make plugin Job Manager to work - Remove this part if you don't use Job Manager
			foreach ($breadcrumbs as $crumb){
				echo $crumb . ' ' . $chevron . ' ';
			}
			$my_post = get_post($post->ID);
			$my_post_type = $my_post->post_type;
	
			if($my_post_type = "jobman_job"){
				echo $before . $my_post->post_title. $after;
			}else{
				echo  $before  . get_the_title() . 'yy' . $after;
			}
			// end job manager
	
		} elseif ( is_search() ) {
			echo $before . __('Search results for ','responsive') . get_search_query() . $after;
	
		} elseif ( is_tag() ) {
			echo $before . __('Posts tagged ','responsive') . single_tag_title('', false) . $after;
	
		} elseif ( is_author() ) {
			global $author;
			$userdata = get_userdata($author);
			echo $before . __('All posts by ','responsive') . $userdata->display_name . $after;
	
		} elseif ( is_404() ) {
			echo $before . __('Error 404 ','responsive') . $after;
		}
	
		if ( get_query_var('paged') ) {
			if ( is_category() || is_day() || is_month() || is_year() || is_search() || is_tag() || is_author() ) echo ' (';
			echo __('Page','responsive') . ' ' . get_query_var('paged');
			if ( is_category() || is_day() || is_month() || is_year() || is_search() || is_tag() || is_author() ) echo ')';
		}
	
		echo '</div>';
	
	}
	
} // end qt_custom_breadcrumbs()
?>