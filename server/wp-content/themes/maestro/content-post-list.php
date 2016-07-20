<?php $current_date = null; ?>
<?php $last_date = null; ?>

<?php
foreach ($objects as $key => $object) :
?>

<?php criWpPost($object); ?>

<article id="post-<?php the_ID(); ?>" <?php post_class( 'cf' ); ?> role="article">

	<!-- POUR LES FORMATIONS LA DATE CORRESPOND A CELLE DU JOUR DE LA FORMATION ET NON A CELLE DE LA CREATION DE LA FORMATION EN BDD -->
	<?php
	    if ( !empty($object->__model_name) && $object->__model_name == 'Formation' && !empty($object->custom_post_date) ){
            $current_date = $object->custom_post_date;
        } else {
            if ($current_date != get_the_date('Y-m-d')) {
                $current_date = get_the_date('Y-m-d');
            }
        }
	 ?>
	<?php if ($last_date != $current_date) : ?>
	    <div class="date sel-object-date">
	        <div class="sep"></div>
	        <span class="jour"><?php echo strftime('%d',strtotime($current_date)) ?></span>
	        <span class="mois"><?php echo mb_substr(strftime('%b',strtotime($current_date)),0,4) ?></span>
	        <span class="annee"><?php echo strftime('%Y',strtotime($current_date)) ?></span>
	    </div>
    <?php endif; ?>
    <?php $last_date = $current_date ?>

    <?php if ( !empty($object->__model_name) && $object->__model_name == 'Veille' && !empty($object->level) ){
    	$niveau = 'niveau'.$object->level;
    }
    ?>

	<div class="details <?php if(!empty($niveau)){echo $niveau;} ?>">
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
			<!-- <div class="adresse">
				La Joliette<br />
				20A Boulevard du Plomb<br />
				13581 Marseille Cedex 20<br />
				France
			</div> -->
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