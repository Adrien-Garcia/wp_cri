<div class="breadcrumbs">
    <div id="<?php echo $containerId; ?>" class="<?php echo $containerClass; ?>">
        <?php if (is_array($breadcrumb) && count($breadcrumb) > 0):
                $lastKey = key( array_slice( $breadcrumb, -1, 1, TRUE ) );
            ?>
            <?php foreach ($breadcrumb as $key => $items): ?>
                <?php if($key !== $lastKey): ?>
                    <a href="<?php echo $items->url; ?>" title="<?php echo $items->title; ?>"><?php echo $items->title; ?></a> <?php echo $separator; ?>
                <?php else: ?>
                    <span><?php echo $items->title; ?></span>
                <?php endif ?>
            <?php endforeach; ?>
        <?php endif ?>
    </div>
</div>