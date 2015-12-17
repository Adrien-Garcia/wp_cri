<?php
/*
 Template Name: iFrame
*/
?>
<?php
if ( !CriIsNotaire() ) : ?>
    <?php CriRefuseAccess(); ?>
<?php else : ?>
    <?php get_header(); ?>
    <?php
        $notaire = CriNotaireData();
    ?>
        <div id="content" class="page page-iframe">

            <div class="breadcrumbs">
                <div id="inner-content" class="wrap cf">
                    <?php // if (function_exists('custom_breadcrumbs')) custom_breadcrumbs(); ?>
                    <a href="#" title="">Accueil</a> + <span>Rechercher dans les bases de connaissances</span>
                </div>
            </div>

            <div id="inner-content" class="wrap cf">

                <div id="main" class="cf" role="main">

                    <h1 class="h1" style="visibility:hidden;">SINEQUA</h1>
                    <iframe style="height: 700px; width: 1200px;" src="http://10.115.100.32/search?profile=profil.CL_externe&amp;user=<?php echo $notaire->id ?>&amp;password=<?php echo $notaire->web_password ?>" width="300" height="150"></iframe>

                </div>

                <?php /*get_sidebar();*/ ?>

            </div>

        </div>

    <?php get_footer(); ?>
<?php endif; ?>
