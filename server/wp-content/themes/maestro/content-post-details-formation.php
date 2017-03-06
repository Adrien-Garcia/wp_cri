<?php criWpPost($object); ?>
<article id="post-<?php the_ID(); ?>" <?php post_class('cf'); ?> role="article">
    <!-- gestion de l'affichage sans session -->
	<?php if (empty($sessions)): ?>
        <div class="date sel-object-date"></div>
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
                    <p class="telephone">
                        <a href="tel:<?php echo $nextSession->entite->tel ?>"><?php echo $nextSession->organisme->tel ?></a>
                    </p>
                    <p class="email">
                        <a href="mailto:<?php echo $nextSession->entite->office_email_adress_1 ?>"><?php echo $nextSession->organisme->office_email_adress_1 ?></a>
                    </p>
                <?php endif; ?>

            <p class="horaire"><?php echo $nextSession->timetable ?></p>

        </div>

        <?php if (!empty($nextSession->action) && !empty($nextSession->action_label)): ?>
            <a href="<?php echo $nextSession->action ?>" class="bt inscription-session"><?php _e($nextSession->action_label) ?></a>
        <?php endif; ?>
    <?php endif; ?>

	<div class="details <?php if(!empty($niveau)){echo $niveau;} ?>">
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


	</div>

    <?php if (!empty($sessions)): ?>
        <div class="liste-sessions">
            <p class="titre"><?php _e('Sessions suivantes :'); ?></p>
            <ul>
                <?php foreach ($sessions as $session): ?>
                    <li>
                        <div class="session-item">
                            <p class="session-date"><?php echo strftime('%d %b %G',strtotime($session->date)) ?></p>
                            <p class="session-organisme"><?php echo $session->entite->office_name ?></p>
                            <p class="session-horaire"><?php echo $session->timetable ?></p>
                        </div>
                        <?php if ($session->contact_organisme): ?>
                            <div class="wrapper-session-contact">
                            Contact
                                <p class="session-telephone">Tél. : <a href="tel:<?php echo $session->entite->tel ?>"><?php echo $session->entite->tel ?></a></p>
                                <p class="session-mail">Email : <a href="mailto:<?php echo $session->entite->office_email_adress_1 ?>"><?php echo $session->entite->office_email_adress_1 ?></a></p>
                            </div>
                        <?php endif; ?>
                        <?php if (!empty($session->action) && !empty($session->action_label)): ?>
                            <a href="<?php echo $session->action ?>" class="bt preinscrire<?php echo (strlen($session->action_label) > 16) ? " large" : "" ?>"><?php _e($session->action_label) ?></a>
                        <?php endif; ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>
</article>
