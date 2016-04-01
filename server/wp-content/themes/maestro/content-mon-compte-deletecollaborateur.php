<div id="content" class="page page-mon-compte">
    <div id="main" class="cf" role="main">
        <h1>Suppression d'un collaborateur</h1>
        <form action="<?php echo mvc_public_url(array('controller' => 'notaires', 'action' => 'deletecollaborateur', 'id' => $collaborator_id)); ?>" method="post">
            <input type="hidden" id="confirmdelete" name="confirmdelete" value="" />
            <p><?php echo $flash_message; ?></p>
            <input type="submit" value="Supprimer">
        </form>
    </div>
</div>