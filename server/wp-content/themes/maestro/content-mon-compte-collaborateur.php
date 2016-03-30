<div>
    <form action="<?php echo mvc_public_url(array('controller' => 'notaires', 'action' => 'collaborateur')); ?>" method="post">
        <p>
            <label for="collaborator_last_name">Nom</label>
            <input type="text" name="collaborator_last_name" id="collaborator_last_name" required>
        </p>
        <p>
            <label for="collaborator_first_name">Prénom</label>
            <input type="text" name="collaborator_first_name" id="collaborator_first_name" required>
        </p>
        <p>
            <label for="collaborator_tel">Télephone fixe</label>
            <input type="text" name="collaborator_tel" id="collaborator_tel">
        </p>
        <p>
            <label for="collaborator_tel_portable">Télephone portable</label>
            <input type="text" name="collaborator_tel_portable" id="collaborator_tel_portable">
        </p>
        <p>
            <label for="collaborator_function">Fonction</label>
            <select name="collaborator_function" id="collaborator_function" required>
                <option value=""> --- </option>
            <?php if(is_array($collaborator_functions) && count($collaborator_functions) > 0): ?>
                <?php foreach($collaborator_functions as $item): ?>
                <option value="<?php echo $item->id; ?>"><?php echo $item->label; ?></option>
                <?php endforeach; ?>
            <?php endif ?>
            </select>
        </p>
        <p>
            <label for="collaborator_email">E-mail</label>
            <input type="text" name="collaborator_email" id="collaborator_email" pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,4}$">
        </p>
        <p>
            <input type="submit" value="Ajouter le Collaborateur">
        </p>
    </form>
</div>