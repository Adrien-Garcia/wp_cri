<?php $current_date = null; ?>
<?php $last_date = null; ?>

<?php if (empty ($sessions) || count($sessions) == 0): ?>
	<p>Aucune formation n'est prévue actuellement</p>
<?php endif; ?>

<?php foreach ($sessions as $key => $session) :
	$formation = $formations[$session->formation->id]
?>

<?php criWpPost($session); ?>

<article id="post-<?php $formation->post->ID; ?>" <?php post_class( 'cf', $formation->post->ID ); ?> role="article">
	<?php
		$current_date = $session->date;
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

	<div class="details">
		<?php if ( isset($formation->matiere) ): ?>

		<div class="block_left">
			<div class="img-cat">
				<img class="sel-object-picto" src="<?php echo $formation->matiere->picto ?>" alt="<?php echo $formation->matiere->label ?>" />
                <img class="sel-object-picto" src="<?php echo $formation->matiere->picto ?>" alt="<?php echo $formation->matiere->label ?>" />
			</div>
		</div>
		<?php endif ?>
		<div class="block_right sel-object-content js-home-block-link" >
            <?php if ( isset($formation->matiere) ): ?>
                <div class="matiere">
                    <span><?php echo $formation->matiere->label ?></span>
                    <!-- <span><?php //echo $formation->matiere->label ?></span> -->
                </div>
            <?php endif ?>
            <h2><?php echo $formation->post->post_title ?></h2>
            <?php if (!empty($formation->post_excerpt)): ?>
                <div class="chapeau">
                    <?php echo $formation->post->post_excerpt ?>
                </div>
            <?php endif; ?>
            <div class="extrait">
                <?php echo wp_trim_words( wp_strip_all_tags( $formation->post->post_content, true ), 35, "..." ) ?>
            </div>
            <div class="lieux-formation">
                <p class="organisme"><?php echo $session->entite->office_name ?></p>
                <p class="horaire"><?php echo $session->timetable ?></p>
                <p class="place"><?php echo $session->place ?></p>
                <?php 
                    $duree = mvc_model('Session')->getDuration($session);
                ?>
                <p class="duree"><?php echo $duree ?></p>
                <p class="price"><?php echo $session->price ?>€ HT / Personne</p>
            </div>
            <a href="<?php echo get_permalink($formation->post->ID); ?>" title="<?php $formation->post->post_title ?>">Lire</a>
		</div>
	</div>	
</article>

<?php endforeach; ?>
