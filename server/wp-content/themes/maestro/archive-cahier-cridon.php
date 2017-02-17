<?php get_header(); ?>

	<div id="content" class="archive archive-cahier">

        <div class="breadcrumbs">
            <div class="wrap cf">
                <?php if (function_exists('CriBreadcrumb')) CriBreadcrumb(); ?>
            </div>
        </div>

		<div id="main" class="cf" role="main">
			<div id="inner-content" class="wrap cf">

				<h1 class="h1">Les Cahiers du CRIDON LYON</h1>
				<div class="listing object">
					
                <?php
                $current_date = null;
                foreach ($objects as $key => $object) :
                ?>
                <?php criWpPost($object); ?>
                <?php //var_dump($object) ?>
                <?php //var_dump($object->documents) ?>

					<article id="post-<?php the_ID(); ?>" <?php post_class( 'cf' ); ?> role="article">
                        <?php
                        if( $current_date != get_the_date('d-M-Y')) :
                        $current_date = get_the_date('d-M-Y');
                        ?>
                        <div class="date sel-object-date">
                            <div class="sep"></div>
                            <span class="jour sel-date_day"><?php echo get_the_date( 'd') ?></span>
                            <span class="mois sel-date_month"><?php echo get_the_date( 'M') ?></span>
                            <span class="annee sel-date_year"><?php echo get_the_date( 'Y') ?></span>
                        </div>
                        <?php endif; ?>

						
						<div class="details">							
							<div class="block_right sel-object-content" >
								<h2><?php the_title() ?></h2>
                                <?php
                                $class = $object->__model_name;

                                ?>
                                <?php if (method_exists($class, "getDocuments")) : ?>
                                    <?php
                                    $documents = $class::getDocuments($object);
                                    ?>
                                    <?php foreach ($documents as $index => $document) : ?>
                                        <?php
                                        $options = array(
                                            'controller' => 'documents',
                                            'action'     => 'download',
                                            'id'         => $document->id
                                        );
                                        $publicUrl  = MvcRouter::public_url($options);
                                        ?>
                                        <a href="<?php echo $publicUrl; ?>" title="télécharger le document pdf" target="_blank">Télécharger le sommaire</a>
                                    <?php endforeach; ?>

                                <?php endif; ?>
								<ul>
                                    <?php
                                    $subcahiers = $object->cahier_cridons;
                                    foreach ($subcahiers as $subcahier) :
                                    ?>
                                        <?php criWpPost($subcahier); ?>
									<li class="">
										<div class="img-cat">
											<img class="" src="<?php echo $subcahier->matiere->picto ?>" alt="<?php echo $subcahier->matiere->label ?>" />
										</div>
										<div class="matiere"><?php echo $subcahier->matiere->label ?></div>
										<h3><?php the_title() ; ?></h3>
                                        <?php
                                        $class = $subcahier->__model_name;
                                        ?>
                                        <?php if (method_exists($class, "getDocuments")) : ?>
                                            <?php
                                            $documents = $class::getDocuments($subcahier);
                                            ?>
                                            <?php foreach ($documents as $index => $document) : ?>
                                                <?php
                                                $options = array(
                                                    'controller' => 'documents',
                                                    'action'     => 'download',
                                                    'id'         => $document->id
                                                );
                                                $publicUrl  = MvcRouter::public_url($options);
                                                ?>
                                                <a href="<?php echo $publicUrl ; ?>" title="Télécharger le document pdf" target="_blank"></a>
                                            <?php endforeach; ?>

                                        <?php endif; ?>

									</li>
                                    <?php endforeach; ?>
                                    <?php criWpPost($object); ?>
								</ul>
							</div>
						</div>
						
					</article>	

                    <?php endforeach; ?>
									
                    <div class="pagination">
                    	<?php echo $this->pagination(); ?>
                    </div>
                    
                </div>

			</div>					

		</div>

		
	</div>

<?php get_footer(); ?>
