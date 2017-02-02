<?php criWpPost($object); ?>
<article id="post-<?php the_ID(); ?>" <?php post_class('cf'); ?> role="article">
	<?php
    if (!empty($sessions)){
        $nextSession = array_shift($sessions);
    ?>
    	<div class="date sel-object-date">
    		<span class="jour"><?php echo strftime('%d',strtotime($nextSession->date)) ?></span>
    		<span class="mois"><?php echo mb_substr(strftime('%b',strtotime($nextSession->date)),0,4) ?></span>
    		<span class="annee"><?php echo strftime('%Y',strtotime($nextSession->date)) ?></span>
    	</div>

        <div class="session">
            <p class="lieu"><?php echo $nextSession->lieu->name ?></p><?php if ($nextSession->contact_lieu): ?>
                <p class="telephone"><?php echo $nextSession->lieu->phone_number ?></p>
                <p class="email"><?php echo $nextSession->lieu->email ?></p>
            <?php endif; ?>
            <p class="horaire"><?php echo $nextSession->timetable ?></p>
            
        </div>

        <?php if (!empty($nextSession->action) && !empty($nextSession->action_label)): ?>
            <a href="<?php echo $nextSession->action ?>" class="bt inscription-session"><?php _e($nextSession->action_label) ?></a>
        <?php endif; ?>
    <?php } ?>

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
                            <p class="session-lieu"><?php echo $session->lieu->name ?></p>
                            <p class="session-horaire"><?php echo $session->timetable ?></p>
                        </div>
                        <?php if ($session->contact_lieu): ?>
                            <div class="wrapper-session-contact">
                            Contact :
                                <p class="session-telephone"><?php echo $session->lieu->phone_number ?></p>
                                <p class="session-mail"><?php echo $session->lieu->email ?></p>
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
