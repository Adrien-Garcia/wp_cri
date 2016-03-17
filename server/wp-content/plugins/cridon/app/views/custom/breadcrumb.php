<nav itemscope itemtype="http://schema.org/BreadcrumbList">
    <?php if (is_array($breadcrumbs) && count($breadcrumbs) > 0):
            $lastKey = key( array_slice( $breadcrumbs, -1, 1, TRUE ) );
        ?>
        <?php foreach ($breadcrumbs as $key => $items): ?>
        <div itemprop="itemListElement" itemscope
             itemtype="http://schema.org/ListItem">
            <?php if($key !== $lastKey): ?>
                <a itemprop="item" href="<?php echo $items->url; ?>" title="<?php echo $items->title; ?>">
                    <span itemprop="name"><?php echo $items->title; ?></span>
                </a>
                <?php echo $separator; ?>
            <?php else: ?>
                <span itemprop="name"><?php echo $items->title; ?></span>
            <?php endif ?>
            <meta itemprop="position" content="<?php echo ($key + 1); ?>" />
        </div>
        <?php endforeach; ?>
    <?php endif ?>
</nav>
