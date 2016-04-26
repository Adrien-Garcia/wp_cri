    <div class="titre">
        <span class="close_layer layer-collaborateur_close"></span>
        <span class="texte">
            <?php empty($collaborator['id']) ? _e('Ajouter') : _e('Modifier') ; ?>
            <span><?php _e('un collaborateur'); ?></span>
        </span>
    </div>

    <form class="js-account-collaborateur-add-form" data-js-ajax-add-url="<?php echo mvc_public_url(array('controller' => 'notaires', 'action' => 'gestioncollaborateur')); ?>">
        <input type="hidden" name="collaborator_id" class="js-account-collaborateur-modify-id" value="<?php echo empty($collaborator['id']) ? '' : $collaborator['id'] ?>">
        <input type="hidden" name="action" class="js-account-collaborateur-action" value="<?php echo empty($collaborator['action']) ? '' : $collaborator['action'] ?>">
        <div>
            <!-- <label for="collaborator_last_name">Nom</label> -->
            <input type="text" name="collaborator_last_name" placeholder="<?php _e('Nom *'); ?>" id="collaborator_last_name" class="js-account-collaborateur-add-lastname" value="<?php echo empty($collaborator['lastname']) ? '' : $collaborator['lastname'] ?>" required>
        </div>
        <div>
            <!-- <label for="collaborator_first_name">Prénom</label> -->
            <input type="text" name="collaborator_first_name" placeholder="<?php _e('Prénom *'); ?>" id="collaborator_first_name" class="js-account-collaborateur-add-firstname" value="<?php echo empty($collaborator['firstname']) ? '' : $collaborator['firstname'] ?>" required>
        </div>
        <div>
            <!-- <label for="collaborator_tel">Télephone fixe</label> -->
            <input type="text" name="collaborator_tel" placeholder="<?php _e('Téléphone fixe'); ?>" id="collaborator_tel" class="js-account-collaborateur-add-phone" value="<?php echo empty($collaborator['phone']) ? '' : $collaborator['phone'] ?>">
        </div>
        <div>
            <!-- <label for="collaborator_tel_portable">Télephone portable</label> -->
            <input type="text" name="collaborator_tel_portable" placeholder="<?php _e('Téléphone mobile'); ?>" id="collaborator_tel_portable" class="js-account-collaborateur-add-mobilephone" value="<?php echo empty($collaborator['mobilephone']) ? '' : $collaborator['mobilephone'] ?>">
        </div>
        <div>
            <!-- <label for="notaire_function">Fonction</label> -->
            <select name="notaire_function" id="notaire_function" class="js-account-collaborateur-add-function" required>
                <option value="" disabled selected><?php _e('Fonction *'); ?></option>
                <?php if(is_array($notaire_functions) && count($notaire_functions) > 0): ?>
                    <?php foreach($notaire_functions as $item) : ?>
                        <?php $selected = !empty($collaborator['notairefunction']) && $item->id_fonction_notaire == $collaborator['notairefunction'] ? 'selected' : ''; ?>
                        <option value="<?php echo $item->id_fonction_notaire; ?>" <?php echo $selected ?>>
                            <?php echo $item->notaire_fonction_label; ?>
                        </option>
                    <?php endforeach; ?>
                <?php endif ?>
            </select>
        </div>
        <!-- <label for="collaborator_function">Fonction</label> -->
        <select name="collaborator_function" id="collaborator_function" class="js-account-collaborateur-add-functioncollaborator <?php echo empty($collaborator['collaboratorfunction']) ? 'hidden' : '' ?>">
            <option value="" disabled selected><?php _e('Fonction Collaborateur'); ?></option>
            <?php if(is_array($collaborateur_functions) && count($collaborateur_functions) > 0): ?>
                <?php foreach($collaborateur_functions as $item): ?>
                    <?php $selected = !empty($collaborator['collaboratorfunction']) && $item->id_fonction_collaborateur == $collaborator['collaboratorfunction'] ? 'selected' : ''; ?>
                    <option value="<?php echo $item->id_fonction_collaborateur; ?>" <?php echo $selected ?>>
                        <?php echo $item->collaborateur_fonction_label; ?>
                    </option>
                <?php endforeach; ?>
            <?php endif ?>
        </select>
        <div>

        </div>
        <div>
            <!-- <label for="collaborator_email">E-mail</label> -->
            <input type="email" name="collaborator_email" placeholder="<?php _e('Email *'); ?>" id="collaborator_email" class="js-account-collaborateur-add-email" value="<?php echo empty($collaborator['emailaddress']) ? '' : $collaborator['emailaddress'] ?>" required>
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

