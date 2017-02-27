<?php get_header(); ?>
    <div id="content" class="page">

        <div class="breadcrumbs">
            <div id="inner-content" class="wrap cf">
                <?php // if (function_exists('custom_breadcrumbs')) custom_breadcrumbs(); ?>
                <a href="#" title="">Accueil</a> + <span>Catalogue <?php echo date('Y') ?></span>
            </div>
        </div>


        <div id="main" class="cf" role="main">

            <div id="inner-content" class="wrap cf">
                <?php if ($catalogPublished): ?>
                    <a href="<?php echo mvc_public_url(array('controller' => 'formations', 'action' => 'catalognextyear')); ?>">Prochain catalogue</a>
                <?php endif; ?>
            </div>

            <?php /*get_sidebar();*/ ?>

        </div>

    </div>
<?php get_footer(); ?>