<div id="slider">
  
	<?php if( have_rows('slide') ): ?>

		<?php  /* Slider */  ?>
		<ul class="bxslider">

		<?php while( have_rows('slide') ): the_row(); ?>

			<li>

			<span>
				<?php the_sub_field("titre"); ?>
			</span>

			<?php if( get_sub_field("image") ): ?>

				<?php $slider_img_src = get_sub_field("image","slider-accueil-mobile"); ?>
				<?php $slider_img_2_src = get_sub_field("image","slider-accueil-tablette" ); ?>
				<?php $slider_img_3_src = get_sub_field("image","slider-accueil-pc" ); ?>

				<?php /* Activer picturefill si projet responsive */ ?>
				<picture>
					<!--[if IE 9]><video style="display: none;"><![endif]-->
					<source srcset="<?php echo $slider_img_src['url']; ?>" media="(max-width: 760px)">
					<source srcset="<?php echo $slider_img_2_src['url']; ?>" media="(max-width: 1030px)">
					<source srcset="<?php echo $slider_img_3_src['url']; ?>" media="(min-width: 1360px)">
					<!--[if IE 9]></video><![endif]-->
					<img srcset="<?php echo $slider_img_src['url']; ?>" alt="<?php the_sub_field('titre'); ?> : <?php the_sub_field('description'); ?>">
				</picture>

			<?php endif; ?>

			<div>
				<?php the_sub_field("description"); ?>
			</div>

			<a href="<?php the_sub_field("lien_bouton"); ?>" title="<?php the_sub_field("libelle_bouton"); ?>"><?php the_sub_field("libelle_bouton"); ?></a>

			</li>

		<?php endwhile; ?>

		</ul>
		<?php  /* Fin slider */  ?>

		<?php  /* Pagination (si besoin d'une pagination customisée, sinon utiliser la pagination générée par bxSlider */  ?>
		<!--div id="bx-pager">

			<div>

			<?php $cont = 0 ; ?>

			<?php while( have_rows('slide') ): the_row(); ?>

				<a data-slide-index="<?php echo $cont; ?>" title="<?php the_title(); ?>"></a>

			<?php $cont++; endwhile; ?>

			<?php wp_reset_query(); ?>

			</div>

		</div-->
		<?php  /* Fin pgination */  ?>

	<?php endif; ?>
            
  </div>