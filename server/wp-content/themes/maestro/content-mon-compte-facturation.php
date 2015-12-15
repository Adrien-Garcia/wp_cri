<?php 

$page_data = get_page( 253 ); 
$content = $page_data->post_content;
?>

<div>
	<?php echo $content; ?>
</div>