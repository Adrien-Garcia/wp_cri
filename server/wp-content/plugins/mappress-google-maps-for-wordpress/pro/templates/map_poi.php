<div class='mapp-iw'>
	<div class='mapp-title'>
		<?php echo $poi->get_title_link(); ?>
	</div>
	<div>
		<?php echo $poi->get_thumbnail(array('class' => 'mapp-thumb')); ?>
		<?php echo $poi->get_body(); ?>
	</div>
	<?php echo $poi->get_links(); ?>
</div>