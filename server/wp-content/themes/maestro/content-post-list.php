<?php $current_date = null; ?>

<?php
foreach ($objects as $key => $object) :
?>

<?php criWpPost($object); ?>

<article id="post-<?php the_ID(); ?>" <?php post_class( 'cf' ); ?> role="article">
	<?php 
		if( $current_date != get_the_date('d-M-Y')) :
			$current_date = get_the_date('d-M-Y');
	 ?>
		<div class="date sel-object-date">
			<div class="sep"></div>
			<span class="jour"><?php echo get_the_date( 'd') ?></span>
	      	<span class="mois"><?php echo get_the_date( 'M') ?></span>
	      	<span class="annee"><?php echo get_the_date( 'Y') ?></span> 				
		</div>
	<?php endif; ?>
	<div class="details">
		<?php if ( isset($object->matiere) ): ?>
			
		<div class="block_left">
			<div class="img-cat">
				<img class="sel-object-picto" src="<?php echo $object->matiere->picto ?>" alt="<?php echo $object->matiere->label ?>" />
			</div>
		</div>
		<?php endif ?>
		<div class="block_right sel-object-content js-home-block-link" >
		<?php //var_dump($this) ?>
			<?php if ( isset($object->matiere) ): ?>
			<div class="matiere"><?php echo $object->matiere->label ?></div>
			<?php endif ?>
			<h2><?php the_title() ?></h2>
		<?php if (!empty($post->post_excerpt)): ?>	
			<div class="chapeau">
				<?php echo get_the_excerpt() ?>
			</div>
		<?php endif; ?>
			<div class="extrait">
				<?php echo wp_trim_words( wp_strip_all_tags( get_the_content(), true ), 35, "..." ) ?>
			</div>
			<ul class="mots_cles">
			<?php 
				$tags = get_the_tags();
				if( $tags ) : foreach ($tags as $tag) :
			 ?>
				<li><?php echo $tag->name; ?></li>
			<?php endforeach; endif; ?>
			</ul>
			<a href="<?php the_permalink(); ?>" title="<?php the_title() ?>">Lire</a>
		</div>
	</div>
	
</article>

<?php endforeach; ?>

                    