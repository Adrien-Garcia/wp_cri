<?php if(count($matieres) > 0):
        $i = 0;
    ?>
<div id="veille_nav_menu" class="posttypediv">
    <?php foreach($matieres as $items):
        if (count($items->veilles) > 0) :
        ?>
            <div id="tabs-panel-wishlist-veille_nav_menu" class="tabs-panel tabs-panel-active">
                <p><strong><?php echo $items->label; ?></strong></p>
                <ul id ="wishlist-veille_nav_menu-checklist" class="categorychecklist form-no-clear">
                    <?php
                        foreach($items->veilles as $item):
                            $i--;
                        ?>
                            <li>
                                <label class="menu-item-title">
                                    <input type="checkbox" class="menu-item-checkbox" name="menu-item[<?php echo $i; ?>][menu-item-object-id]" value="<?php echo $item->id; ?>"> <?php echo $item->post->post_title; ?>
                                </label>
                                <input type="hidden" class="menu-item-type" name="menu-item[<?php echo $i; ?>][menu-item-type]" value="custom">
                                <input type="hidden" class="menu-item-title" name="menu-item[<?php echo $i; ?>][menu-item-title]" value="<?php echo $item->post->post_title; ?>">
                                <input type="hidden" class="menu-item-url" name="menu-item[<?php echo $i; ?>][menu-item-url]" value="<?php echo mvc_public_url(array('controller' => 'veilles', 'id' => $item->post->post_name)) ?>">
                            </li>
                    <?php
                        endforeach;
                    ?>
                </ul>
            </div>
    <?php
        endif;
    endforeach; ?>
    <p class="button-controls">
        <span class="add-to-menu">
            <input type="submit" class="button-secondary submit-add-to-menu right" value="Ajouter au menu" name="add-post-type-menu-item" id="submit-veille_nav_menu">
            <span class="spinner"></span>
        </span>
    </p>
</div>
<?php endif ?>