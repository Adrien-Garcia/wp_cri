<?php criWpPost($object); ?>
<article id="post-<?php the_ID(); ?>" <?php post_class('cf'); ?> role="article">
	<div class="date-object sel-object-date">
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
			<div class="chapeau">
				<?php echo get_the_excerpt() ?>
			</div>
			
		</div>
		<div class="block_full">
			
			<div class="content">									

				<?php the_content(); ?>

				<!--h2>sous titre 2</h2>

				<p>Lenditaque rere dolupti orendi comniti niendunt quatem idernam conse nim ni coneste nes ellest aut inulpa dem. Minisciundae et, sunt pa nis sant ulluptius ditatendam eiusam quaeceatus.</p>
				
				<ul>
					<li>doluptatur apedigenet eligen</li>
					<li>qui officit voluptatis doluptatur</li>
				    <li>ut modi re, cus dolest porio volorempore sandame</li>
				    <li><a href="">doluptatur apedigenet eligen</a></li>
				</ul>

				<p>Natem volo illuptatus, ut modi re, cus dolest porio volorempore sandame cullant qui re, <s>ex essi blaccum fuga</s>. Sed qui velendi cationsequis vitio quam, volectam hariossit qui officit voluptatis doluptatur am velignis aceat et escilig endicto ipsandi tatiis nonsectenda dit as aut labo. Ut voluptatur? Maximoles doluptatur apedigenet eligent ea vit pliquo totatur sunt ea cum di dipsus iuscient ad es dolupicid ut aut mi, imusanditat.</p>

				<h2>sous titre 2</h2>

				<p>Lenditaque rere dolupti orendi comniti niendunt quatem idernam conse nim ni coneste nes ellest aut inulpa dem. Minisciundae et, sunt pa nis sant ulluptius ditatendam eiusam quaeceatus.</p>

					<h3>Sous titre 3</h3>
					<p>Texte couleur 2 Lenditaque rere dolupti orendi comniti niendunt quatem idernam conse 
					nim ni coneste nes ellest aut inulpa dem. Minisciundae et, sunt pa nis sant ulluptius 
					ditatendam eiusam quaeceatus.</p>

				<p>Natem volo illuptatus, ut modi re, cus dolest porio volorempore sandame cullant qui re, ex essi blaccum fuga. Sed qui velendi cationsequis vitio quam, volectam hariossit qui officit voluptatis doluptatur am velignis aceat et escilig endicto ipsandi tatiis nonsectenda dit as aut labo. Ut voluptatur? Maximoles doluptatur apedigenet eligent ea vit pliquo totatur sunt ea cum di dipsus iuscient ad es dolupicid ut aut mi, imusanditat.</p>

					<h3>Sous titre 3</h3>
					<p>Texte couleur 2 Lenditaque rere dolupti orendi comniti niendunt quatem idernam conse 
					nim ni coneste nes ellest aut inulpa dem. Minisciundae et, sunt pa nis sant ulluptius 
					ditatendam eiusam quaeceatus.</p!-->
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
            <?php if (method_exists($class, "getDocuments")) : ?>

			<div class="documents-liees">
				<ul>
                    <?php
                    $documents = $class::getDocuments($object->id);
                    ?>
                    <?php foreach ($documents as $index => $document) : ?>
                        <li><a href="<?php echo $document->file_path ; ?>" target="_blank"><?php echo $document->name ; ?></a></li>
                    <?php endforeach; ?>
				</ul>
			</div>

            <?php endif; ?>
			
		</div>

		
	</div>

 	

    	
  	

</article>