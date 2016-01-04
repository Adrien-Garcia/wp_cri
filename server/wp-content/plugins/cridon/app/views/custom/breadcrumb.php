<?php //echo '<pre>'; die(print_r($beadcrumb)); ?>
<div class="breadcrumbs">
    <div id="<?php echo $containerId; ?>" class="<?php echo $containerClass; ?>">
        <?php if (is_array($beadcrumb) && count($beadcrumb) > 0):
                $lastKey = key( array_slice( $beadcrumb, -1, 1, TRUE ) );
            ?>
            <?php foreach ($beadcrumb as $key => $items): ?>
                <?php if($key !== $lastKey): ?>
                    <a href="<?php echo $items->url; ?>" title="<?php echo $items->title; ?>"><?php echo $items->title; ?></a> <?php echo $separator; ?>
                <?php else: ?>
                    <span><?php echo $items->title; ?></span>
                <?php endif ?>
            <?php endforeach; ?>
        <?php endif ?>
    </div>
</div>