<?php if (!empty($message)) : ?>
    <div class="message-erreur"><?php echo $message ?></div>
<?php endif; ?>
<h2><?php _e("Ma liste de collaborateur"); ?></h2>

<div class="bt-ajout js-account-collaborateur-add-button" data-js-ajax-add-url="<?php echo mvc_public_url(array('controller' => 'notaires', 'action' => 'gestioncollaborateur')); ?>">
    <?php _e("Ajouter un collaborateur"); ?>
</div>


<ul class="list-collab">
    <?php
        foreach ($liste as $key => $member) :

            ?>
            <li>
                <div class="trash">
                    <form accept-charset="utf-8" class="js-account-collaborateur-delete-form" data-js-ajax-delete-url="<?php echo mvc_public_url(array('controller' => 'notaires','action' =>'contentdeletecollaborateur'));?>">
                        <input type="hidden" value="<?php echo $member->id; ?>" class="js-account-collaborateur-delete-id">
                        <input type="submit" value="submit">
                    </form>
                </div>
                <div class="block_01 js-account-collaborateur-modify"
                     data-js-ajax-id="<?php echo $member->id; ?>"
                     data-js-ajax-lastname="<?php echo $member->last_name; ?>"
                     data-js-ajax-firstname="<?php echo $member->first_name; ?>"
                     data-js-ajax-phone="<?php echo $member->tel; ?>"
                     data-js-ajax-mobilephone="<?php echo $member->tel_portable; ?>"
                     data-js-ajax-notairefunction="<?php echo $member->id_fonction; ?>"
                     data-js-ajax-collaboratorfunction="<?php echo $member->id_fonction_collaborateur; ?>"
                     data-js-ajax-emailaddress="<?php echo $member->email_adress; ?>"
                     data-js-ajax-modify-url="<?php echo mvc_public_url(array('controller' => 'notaires', 'action' => 'gestioncollaborateur')); ?>">
                        <span class="nom"><?php echo $member->last_name ?> <?php echo $member->first_name ?></span><br />
                        <span class="fonction"><?php echo $member->id_fonction == CONST_NOTAIRE_COLLABORATEUR ? $member->collaborator_fonction_label : $member->notaire_fonction_label  ?></span>
                </div>
                <div class="block_02">
                    <?php echo $member->email_adress ?>
                </div>
                <div class="block_03">
                    <?php if (!empty($member->tel)): ?>
                        <span class="tel"><?php echo $member->tel ?></span><br />
                    <?php endif ?>    
                    <span class="tel"><?php echo $member->tel_portable ?></span>
                </div>
            </li>

        <?php 
            
                //var_dump($member);
        endforeach;
    ?>
</ul>

<div class="add-collabs">
    <div id="layer-collaborateur-add" style="display:none;">
    </div>
</div>
<div class="supp-collabs">
    <?php echo get_template_part("content","sup-collabs-popup"); ?>
</div>

<form class="bt-ajout js-account-collaborateur-add-button" data-js-ajax-add-url="<?php echo mvc_public_url(array('controller' => 'notaires', 'action' => 'gestioncollaborateur')); ?>">
    <?php _e("Ajouter un collaborateur"); ?>
</form>
