<?php get_header(); ?>
    <div id="content" class="page page-catalogue-formation">

        <div class="breadcrumbs">
            <div class="wrap cf">
                <?php // if (function_exists('CriBreadcrumb')) CriBreadcrumb(); ?>
                <a href="#" title="">Accueil</a> + <span>Catalogue des formations <?php echo date('Y', strtotime('+1 year')) ?></span>
            </div>
        </div>

        <div id="inner-content" class="wrap cf">
            <div id="main" class="cf" role="main">
                <article id="post-<?php the_ID(); ?>" <?php post_class( 'cf' ); ?> role="article">
                    <header class="article-header">
                        <h1 class="page-title h1" itemprop="headline"><?php _e('Catalogue des formations '. date('Y', strtotime('+1 year'))); ?></h1>
                    </header> <?php // end article header ?>

                    <?php if (!$catalogPublished): ?>
                        <p>Votre catalogue pour l'année <?php echo date('Y', strtotime('+1 year')) ?> n'est pas encore disponible</p>
                        <a href="<?php echo mvc_public_url(array('controller' => 'formations', 'action' => 'catalog')); ?>">Catalogue actuel</a>
                    <?php else: ?>
                        <?php set_query_var( 'currentCatalog', false ); ?>
                        <?php set_query_var( 'catalogPublished', $catalogPublished ); ?>
                        <?php set_query_var( 'sortedFormations', $sortedFormations ); ?>
                        <?php echo get_template_part("page","catalog-detail"); ?>
                    <?php endif; ?>
                </article>
            </div>
            <?php /*get_sidebar();*/ ?>
        </div>
    </div>

<?php get_footer(); ?>