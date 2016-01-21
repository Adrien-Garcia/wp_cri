<?php criWpPost($object); ?>
<article id="post-<?php the_ID(); ?>" <?php post_class('cf'); ?> role="article">
	<div class="date sel-object-date">
		<span class="jour"><?php echo get_the_date( 'd') ?></span>
      	<span class="mois"><?php echo get_the_date( 'M') ?></span>
      	<span class="annee"><?php echo get_the_date( 'Y') ?></span> 				
	</div>

	<div class="details">
		<?php if (isset($object->matiere)) : ?>						
		
		<div class="block_left">
			<div class="img-cat">
				<img class="sel-object-picto" src="<?php echo $object->matiere->picto ?>" alt="<?php echo $object->matiere->label ?>" />
			</div>
		</div>
		<?php endif; ?>

		

		<div class="block_right sel-object-content">
		<?php if (isset($object->matiere)) : ?>						
			<div class="matiere"><?php echo $object->matiere->label ?></div>
		<?php endif; ?>
			<h1 class="entry-title single-title"><?php the_title() ?></h1>
		<?php if (!empty($post->post_excerpt)): ?>
			<div class="chapeau">
				<?php echo get_the_excerpt() ?>
			</div>
		<?php endif; ?>
		</div>
		<div class="block_full">
			
			<div class="content">		
				<?php the_content(); ?>
			</div>
			<ul class="mots_cles">
			<?php 
				$tags = get_the_tags();
				if( $tags ) : foreach ($tags as $tag) :
			 ?>
				<li><?php echo $tag->name; ?></li>
			<?php endforeach; endif; ?>
			</ul>

            <?php
            $class = $object->__model_name;
            ?>
            <?php if (property_exists($object, 'documents') || method_exists($class, "getDocuments")) : ?>
            <?php
            if (property_exists($object, 'documents')){
                $documents = $object->documents;
            }else{
                $documents = $class::getDocuments($object->id);                
            }
                if (! empty($documents)) :
            ?>
			<div class="documents-liees">
				<ul>

                    <?php foreach ($documents as $index => $document) : ?>
                        <?php
                        $options = array(
                            'controller' => 'documents',
                            'action'     => 'download',
                            'id'         => $document->id
                        );
                        $publicUrl  = MvcRouter::public_url($options);
                        ?>
                        <li><a href="<?php echo $publicUrl ; ?>" target="_blank"><?php echo $document->name ; ?></a></li>
                    <?php endforeach; ?>
				</ul>
			</div>
                <?php endif; ?>


            <?php endif; ?>
			
		</div>

		
	</div>

 	

    	
  	

</article>