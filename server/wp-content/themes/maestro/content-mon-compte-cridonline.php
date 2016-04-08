<h2><?php _e("L'offre crid’online"); ?></h2>

<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum. Sed ut perspiciatis unde omnis iste natus</p>

<div class="cridonline-offres">
    <?php set_query_var( 'notaire', $notaire ); ?>
    <?php set_query_var( 'priceVeilleLevel2', $priceVeilleLevel2 ); ?>
    <?php set_query_var( 'priceVeilleLevel3', $priceVeilleLevel3 ); ?>
    <?php echo get_template_part("content","cridonline-offres"); ?>
</div>




<div class="wrap-link">
   <a href="" title="CGV" target="_blank"><?php _e("Télécharger les conditions générales de vente (CGV)"); ?></a> 
</div>
