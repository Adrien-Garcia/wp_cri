<?php if (!$currentCatalog && !$catalogPublished): ?>

    <p>Votre catalogue pour l'année <?php echo date('Y', strtotime('+1 year')) ?> n'est pas encore disponible</p>
    <a href="<?php echo mvc_public_url(array('controller' => 'formations', 'action' => 'catalog')); ?>">Catalogue actuel</a>

<?php else : ?>

<section class="entry-content cf" itemprop="articleBody">
    <div class="top">
        <div class="wrapper-img">
            <img src="<?php echo get_template_directory_uri(); ?>/library/images/formation-catalogue-couverture.jpg" alt="" width="228" height="324" />
            <span>Catalogue de formations 2017 CRIDON LYON</span>
        </div>
        <div class="wrapper-text">
            <p>Le CRIDON LYON participe à la formation permanente des Notaires de son ressort et de leur personnel en proposant aux Chambres départementales ou interdépartementales et aux Conseils régionaux des journées d’études dont les thèmes sont déterminés en fonction de la fréquence des questions posées ou des réformes législatives.</p>
            <p>Le CRIDON LYON met également cette prestation directement à disposition des études qui souhaitent organiser des journées de formation à la demande en leur sein ou dans nos locaux.</p>

            <a href="<?php echo mvc_public_url(array('controller' => 'formations', 'action' => 'calendar')) ?>" title="Consulter l'agenda des formations" class="bt-agenda">Consulter l'agenda des formations</a>
        </div>
    </div>

    <div class="wrapper-catalogue">
        <?php if ($currentCatalog && $catalogPublished): ?>
        
        <a href="<?php echo mvc_public_url(array('controller' => 'formations', 'action' => 'catalognextyear')); ?>" title="Catalogue des formations <?php echo date('Y', strtotime('+1 year')) ?>" class="bt-agenda catalogue">Catalogue des formations <?php echo date('Y', strtotime('+1 year')) ?></a>
        <br />
        <?php endif; ?>

        <ul>
            <?php foreach ($sortedFormations as $formations): ?>

                <?php // var_dump($formations) ?>
                <li class="matiere">
                    <div class="nom-matiere js-bt-matiere">
                        <span><?php echo $formations[0]->matieres[0]->label ?></span>
                    </div>
                    <ul class="formations">
                        <?php foreach ($formations as $formation): ?>
                        <li>
                            <div class="nom-formation"><a href="<?php echo get_permalink($formation->post->ID); ?>"><?php echo $formation->post->post_title ?></a></div>
                            <a href="<?php echo MvcRouter::public_url(array('controller'=> 'formations','action' => 'demande','id' => $formation->id)) ?>" class="demande"></a>
                            <a href="<?php echo $formation->document->download_url ?>" target="_blank" class="pdf"></a>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>

</section>

<?php endif; ?>