<div class="mvc-title">
    <h2>Démarche</h2>
</div>
<div class="mvc-form">
    <?php echo $this->form->create($model->name,array('action'=>'edit')); ?>
    <?php echo $this->form->input('type',array( 'type'=>'hidden','value' => $object->type,'disabled' => true )); ?>
    <div>
        <label>Type de démarche</label>
        <span><?php echo Config::$labelWorflowFormation[$object->type] ?></span>
    </div>
    <?php echo $this->form->input('date',array( 'type'=>'hidden','value' => $object->date,'disabled' => true )); ?>
    <div>
        <label>Date</label>
        <span>
        <?php
        $date = DateTime::createFromFormat('Y-m-d', $object->date);
        echo $date->format('d/m/Y');
        ?>
        </span>
    </div>
    <?php echo $this->form->input('notaire_id',array( 'type'=>'hidden','value' => $object->notaire_id,'disabled' => true )); ?>
    <div>
        <label>Notaire</label>
        <span>
        <?php
        $email = trim($object->notaire->email_adress);
        $email = !empty($email) ? ' (<a href="mailto:' . $email . '">' . $email . '</a>)' :  '';
        echo $object->notaire->first_name . ' ' . $object->notaire->last_name . $email;
        ?>
        </span>
    </div>
    <div>
        <label>CPRCEN</label>
        <span>
        <?php
        echo trim($object->notaire->crpcen);
        ?>
        </span>
    </div>
    <?php if (isset($object->session) && !empty($object->session->date)): ?>
    <div>
        <label>Session</label>
        <span>
        <?php
        $date = DateTime::createFromFormat('Y-m-d', $object->session->date);
        echo 'Le ' . $date->format('d/m/Y');
        ?>
        </span>
    </div>
    <?php endif; ?>
    <?php if (isset($object->formation) || (isset($object->session) && isset($object->session->formation))): ?>
        <?php
        $formation = isset($object->formation) ? $object->formation : $object->session->formation;
        ?>
        <?php if (!empty($formation->post)): ?>
    <div>
        <label>Formation</label>
        <span>
        <?php
        echo $formation->post->post_title;
        ?>
        </span>
    </div>
        <?php endif; ?>
    <?php endif; ?>
    <?php
    $details = CONST_FORMATION_GENERIQUE == $object->type ? 'Thème de la formation' : 'Nombre de participants';
    ?>
    <?php echo $this->form->input('session_id',array( 'type'=>'hidden','value' => $object->session_id,'disabled' => true )); ?>
    <?php echo $this->form->input('formation_id',array( 'type'=>'hidden','value' => $object->formation_id,'disabled' => true )); ?>
    <?php echo $this->form->input('details',array( 'type'=>'text', 'label'=> $details, 'value' => $object->details,'disabled' => true )); ?>
    <?php echo $this->form->input('commentaire_client',array( 'type'=>'textarea', 'label'=> 'Commentaire', 'value' => $object->commentaire_client,'disabled' => true )); ?>
    <?php echo $this->form->input('commentaire_cridon',array( 'type'=>'textarea', 'label'=> 'Notes', 'value' => $object->commentaire_cridon )); ?>
    <?php echo $this->form->end('Valider'); ?>
</div>
