<?php 

$page_data = get_page( 1515 ); 
$content = $page_data->post_content;
?>

<div>
	<?php echo $content; ?>
</div>