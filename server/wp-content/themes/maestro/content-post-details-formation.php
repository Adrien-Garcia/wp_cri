<?php criWpPost($object); ?>
<article id="post-<?php the_ID(); ?>" <?php post_class('cf'); ?> role="article">
    <!-- gestion de l'affichage sans session -->
	<?php if (empty($sessions)): ?>
        <div class="session">
            <p class="horaire">Pas de session de programmée</p>
        </div>
        <a href="<?php echo MvcRouter::public_url(array('controller'=> 'formations','action' => 'demande','id' => $object->id))?>" class="bt inscription-session"><?php _e('Contacter le Cridon Lyon') ?></a>
        <!-- gestion de l'affichage avec session(s) -->
    <?php else :
        $nextSession = !empty($highlight) ? $highlight : reset($sessions);
    ?>
    	<div class="date sel-object-date">
    		<span class="jour"><?php echo strftime('%d',strtotime($nextSession->date)) ?></span>
    		<span class="mois"><?php echo strftime('%b',strtotime($nextSession->date)) ?></span>
    		<span class="annee"><?php echo strftime('%Y',strtotime($nextSession->date)) ?></span>
    	</div>

        <div class="session">

            <p class="organisme">
                <?php echo $nextSession->entite->office_name ?>
            </p>

                <?php if ($nextSession->contact_organisme): ?>
                    <?php if (!empty(trim($nextSession->entite->tel))): ?>
                    <p class="telephone">
                        <a href="tél:<?php echo $nextSession->entite->tel ?>"><?php echo $nextSession->entite->tel ?></a>
                    </p>
                    <?php endif; ?>
                    <?php if (!empty(trim($nextSession->entite->office_email_adress_1))): ?>
                    <p class="email">
                        <a href="mailto:<?php echo $nextSession->entite->office_email_adress_1 ?>"><?php echo $nextSession->entite->office_email_adress_1 ?></a>
                    </p>
                    <?php endif; ?>
                <?php endif; ?>
            <p class="horaire <?php echo $nextSession->is_full ? ' complet ' :'' ; ?> "><?php echo $nextSession->is_full ? 'Complet' : $nextSession->timetable ?></p>

            <p class="place"><?php echo $nextSession->place ?></p>
            <?php 
                $duree = mvc_model('Session')->getDuration($nextSession);
            ?>
            <p class="duree"><?php echo $duree ?></p>
            <p class="price"><?php echo $nextSession->price ?>€ HT / personne</p>

        </div>

        <?php if (!empty($nextSession->action) && !empty($nextSession->action_label)): ?>
            <a
            <?php if (!$nextSession->is_full) : ?>
                href="<?php echo $nextSession->action ?>" class="bt inscription-session"
            <?php else: ?>
                class="bt bt-disabled inscription-session"
            <?php endif; ?>><?php _e($nextSession->action_label) ?></a>
        <?php endif; ?>
    <?php endif; ?>

	<div class="details <?php if(!empty($niveau)){echo $niveau;} ?>">
		<?php if (isset($object->matieres) && !empty($object->matieres)) : ?>

		<div class="block_left">
			<div class="img-cat">
                <?php foreach ($object->matieres as $index => $matiere) : ?>
                    <img class="sel-object-picto" src="<?php echo $matiere->picto ?>" alt="<?php echo $matiere->label ?>" />
                <?php endforeach; ?>
			</div>
            <?php if (!empty($object->millesimes)): ?>
                <div class="millesime-wrapper">
                    <div class="millesime">
                        Catalogue
                        <span>
                            <?php
                            $millesimes = assocToKeyVal($object->millesimes, 'id', 'year');
                            echo implode(',', $millesimes);
                            ?>
                        </span>
                    </div>
                </div>
            <?php endif; ?>
		</div>
		<?php endif; ?>



		<div class="block_right sel-object-content">
		<?php if (isset($object->matieres) && !empty($object->matieres)) : ?>
			<div class="matiere">
            <?php foreach ($object->matieres as $index => $matiere) : ?>
                <span><?php echo $matiere->label ?></span>
            <?php endforeach; ?>
            </div>
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
            <?php
            $class = $object->__model_name;
            ?>
            <?php if (property_exists($object, 'documents') || method_exists($class, "getDocuments")) : ?>
            <?php
            if (property_exists($object, 'documents')){
                $documents = $object->documents;
            }else{
                $documents = $class::getDocuments($object);
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
                        <li>
                            <a href="<?php echo $publicUrl ; ?>" target="_blank">
                                <?php echo $document->name ; ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
				</ul>
			</div>
                <?php endif; ?>
            <?php endif; ?>

		</div>

        <div class="block_certification">
            <img src="/wp-content/themes/maestro/library/images/logo-CSN_2017.jpg" alt="">
            <div class="num">
                Certification CSN
                <?php if (!empty($object->csn)) : ?>
                    <span><?php echo $object->csn ?></span>
                <?php endif; ?>
            </div>
        </div>


	</div>

    <?php if (!empty($sessions)): ?>
        <div class="liste-sessions">
            <p class="titre"><?php _e('Sessions suivantes :'); ?></p>
            <ul>
                <?php foreach ($sessions as $session): ?>
                    <li class="<?php echo $session->is_full ? ' session-complet ' : '' ; ?>">
                        <div class="session-item">
                            <p class="session-date"><?php echo strftime('%d %b %G',strtotime($session->date)) ?></p>
                            <p class="session-organisme"><?php echo $session->entite->office_name ?></p>
                            <p class="session-horaire <?php echo $session->is_full ? ' complet ' :'' ; ?> "><?php echo $session->is_full ? 'Complet' : $session->timetable ?></p>
                            <p class="session-place"><?php echo $session->place ?></p>
                            <?php 
                                $duree = mvc_model('Session')->getDuration($session);
                            ?>
                            <p class="session-duree"><?php echo $duree ?></p>
                            <p class="session-price"><?php echo $session->price ?>€ HT / personne</p>
                        </div>
                        <?php if ($session->contact_organisme): ?>
                            <?php if (!empty(trim($session->entite->tel)) || !empty(trim($session->entite->office_email_adress_1))): ?>
                            <div class="wrapper-session-contact">
                            Contact
                                <?php if (!empty(trim($session->entite->tel))): ?>
                                    <p class="session-telephone">Tél. : <a href="tel:<?php echo $session->entite->tel ?>"><?php echo $session->entite->tel ?></a></p>
                                <?php endif; ?>
                                <?php if (!empty(trim($session->entite->office_email_adress_1))): ?>
                                    <p class="session-mail">Email : <a href="mailto:<?php echo $session->entite->office_email_adress_1 ?>"><?php echo $session->entite->office_email_adress_1 ?></a></p>
                                <?php endif; ?>
                            </div>
                            <?php endif; ?>
                        <?php endif; ?>

                        <?php if (!empty($session->action) && !empty($session->action_label)): ?>
                            <a
                            <?php if (!$session->is_full) : ?>
                                href="<?php echo $session->action ?>"
                            <?php endif; ?>
                                class="bt preinscrire <?php echo $session->is_full ? ' bt-disabled ' : '' ?><?php echo (strlen($session->action_label) > 16) ? " large" : "" ?>"><?php _e($session->action_label) ?></a>
                        <?php endif; ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>
</article>
