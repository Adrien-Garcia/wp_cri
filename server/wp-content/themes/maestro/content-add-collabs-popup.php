
<div id="layer-add-collabs" style="display:none;">

    <div class="titre">
        <span class="close_layer layer-cridonline_close"></span>
        <span class="texte">
            <?php _e('Ajouter'); ?>
            <span><?php _e('un collaborateur'); ?></span>
        </span>
    </div>

    <?php
    if (!empty($alertEmailChanged)) : ?>
        <div class="error"><?php echo $alertEmailChanged ?></div>
    <?php endif; ?>

    <form action="<?php echo mvc_public_url(array('controller' => 'notaires', 'action' => 'collaborateur')); ?>" method="post">
        <div>
            <!-- <label for="collaborator_last_name">Nom</label> -->
            <input type="text" name="collaborator_last_name" placeholder="<?php _e('Nom *'); ?>" id="collaborator_last_name" required>
        </div>
        <div>
            <!-- <label for="collaborator_first_name">Prénom</label> -->
            <input type="text" name="collaborator_first_name" placeholder="<?php _e('Prénom *'); ?>" id="collaborator_first_name" required>
        </div>
        <div>
            <!-- <label for="collaborator_tel">Télephone fixe</label> -->
            <input type="text" name="collaborator_tel" placeholder="<?php _e('Téléphone fixe'); ?>" id="collaborator_tel">
        </div>
        <div>
            <!-- <label for="collaborator_tel_portable">Télephone portable</label> -->
            <input type="text" name="collaborator_tel_portable" placeholder="<?php _e('Téléphone mobile'); ?>" id="collaborator_tel_portable">
        </div>
        <div>
            <!-- <label for="collaborator_function">Fonction</label> -->
            <select name="collaborator_function" placeholder="<?php _e('Fonction'); ?>" id="collaborator_function" required>
                <option value="" disabled selected><?php _e('Fonction *'); ?></option>
            <?php if(is_array($collaborator_functions) && count($collaborator_functions) > 0): ?>
                <?php foreach($collaborator_functions as $item): ?>
                <option value="<?php echo $item->id; ?>"><?php echo $item->label; ?></option>
                <?php endforeach; ?>
            <?php endif ?>
            </select>
        </div>
        <div>
            <!-- <label for="collaborator_email">E-mail</label> -->
            <input type="text" name="collaborator_email" placeholder="<?php _e('Email *'); ?>" id="collaborator_email" pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,4}$">
        </div>
        <div class="droit-collaborateur">
            <p>Droits du collaborateur</p>
            <label class="select js-account-cridonline-validation-checkbox">
                <input type="checkbox" name="compta" class="js-account-cridonline-validation-cgv" value="value">
                <span><?php _e("Accès aux pages «compta» (finance,factures, relevée de consommation)"); ?></span>
            </label>

            <label class="unselect js-account-cridonline-validation-checkbox">
                <input type="checkbox" name="question_ecrite" class="js-account-cridonline-validation-cgv" value="value">
                <span><?php _e("Poser des questions écrites"); ?></span>
            </label>

            <label class="unselect js-account-cridonline-validation-checkbox">
                <input type="checkbox" name="question_tel" class="js-account-cridonline-validation-cgv" value="value">
                <span><?php _e("Poser des questions téléphoniques)"); ?></span>
            </label>

            <label class="unselect js-account-cridonline-validation-checkbox">
                <input type="checkbox" name="question_tel" class="js-account-cridonline-validation-cgv" value="value">
                <span><?php _e("Accès aux bases de connaissance"); ?></span>
            </label>
        </div>
        <div class="submit">
            <input type="submit" value="Enregistrer">
        </div>
        <p class="chps_obli"><?php _e("* Champs obligatoires"); ?></p>
    </form>

    
</div>

