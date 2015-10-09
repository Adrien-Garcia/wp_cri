<h2>Edit Formation</h2>

<?php echo $this->form->create($model->name); ?>
<?php echo $this->form->belongs_to_dropdown('Post', $posts, array('style' => 'width: 200px;', 'empty' => true, 'id' => 'drpPost', 'label' => 'Article à associée')); ?>
<?php echo $this->form->end('Update'); ?>