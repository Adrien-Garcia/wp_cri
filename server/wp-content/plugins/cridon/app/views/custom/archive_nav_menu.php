<div id="archives_nav_menu" class="posttypediv">
    <div id="tabs-panel-wishlist-archives_nav_menu" class="tabs-panel tabs-panel-active">
        <ul id ="wishlist-archives_nav_menu-checklist" class="categorychecklist form-no-clear">
            <li>
                <label class="menu-item-title">
                    <input type="checkbox" class="menu-item-checkbox" name="menu-item[-1][menu-item-object-id]" value="flashes"> <?php echo $flashes['title']; ?>
                </label>
                <input type="hidden" class="menu-item-type" name="menu-item[-1][menu-item-type]" value="custom">
                <input type="hidden" class="menu-item-title" name="menu-item[-1][menu-item-title]" value="<?php echo $flashes['title']; ?>">
                <input type="hidden" class="menu-item-url" name="menu-item[-1][menu-item-url]" value="<?php echo $flashes['link']; ?>">
            </li>
            <li>
                <label class="menu-item-title">
                    <input type="checkbox" class="menu-item-checkbox" name="menu-item[-2][menu-item-object-id]" value="veilles"> <?php echo $veilles['title']; ?>
                </label>
                <input type="hidden" class="menu-item-type" name="menu-item[-2][menu-item-type]" value="custom">
                <input type="hidden" class="menu-item-title" name="menu-item[-2][menu-item-title]" value="<?php echo $veilles['title']; ?>">
                <input type="hidden" class="menu-item-url" name="menu-item[-2][menu-item-url]" value="<?php echo $veilles['link']; ?>">
            </li>
            <li>
                <label class="menu-item-title">
                    <input type="checkbox" class="menu-item-checkbox" name="menu-item[-3][menu-item-object-id]" value="flash"> <?php echo $formations['title']; ?>
                </label>
                <input type="hidden" class="menu-item-type" name="menu-item[-3][menu-item-type]" value="custom">
                <input type="hidden" class="menu-item-title" name="menu-item[-3][menu-item-title]" value="<?php echo $formations['title']; ?>">
                <input type="hidden" class="menu-item-url" name="menu-item[-3][menu-item-url]" value="<?php echo $formations['link']; ?>">
            </li>
            <li>
                <label class="menu-item-title">
                    <input type="checkbox" class="menu-item-checkbox" name="menu-item[-4][menu-item-object-id]" value="vie_cridons"> <?php echo $vie_cridons['title']; ?>
                </label>
                <input type="hidden" class="menu-item-type" name="menu-item[-4][menu-item-type]" value="custom">
                <input type="hidden" class="menu-item-title" name="menu-item[-4][menu-item-title]" value="<?php echo $vie_cridons['title']; ?>">
                <input type="hidden" class="menu-item-url" name="menu-item[-4][menu-item-url]" value="<?php echo $vie_cridons['link']; ?>">
            </li>
            <li>
                <label class="menu-item-title">
                    <input type="checkbox" class="menu-item-checkbox" name="menu-item[-5][menu-item-object-id]" value="cahier_cridons"> <?php echo $cahier_cridons['title']; ?>
                </label>
                <input type="hidden" class="menu-item-type" name="menu-item[-5][menu-item-type]" value="custom">
                <input type="hidden" class="menu-item-title" name="menu-item[-5][menu-item-title]" value="<?php echo $cahier_cridons['title']; ?>">
                <input type="hidden" class="menu-item-url" name="menu-item[-5][menu-item-url]" value="<?php echo $cahier_cridons['link']; ?>">
            </li>
        </ul>
    </div>
    <p class="button-controls">
        <span class="add-to-menu">
            <input type="submit" class="button-secondary submit-add-to-menu right" value="Ajouter au menu" name="add-post-type-menu-item" id="submit-archives_nav_menu">
            <span class="spinner"></span>
        </span>
    </p>
</div>