<?php 

$page_data = get_post( 1515 );
$content = $page_data->post_content;
?>

<div>
	<?php echo apply_filters( 'the_content', $content ); ?>
</div>