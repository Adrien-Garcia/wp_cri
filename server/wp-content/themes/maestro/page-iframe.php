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
                <div class="wrap cf">
                    <?php if (function_exists('CriBreadcrumb')) CriBreadcrumb(); ?>
                </div>
            </div>

            <div id="inner-content" class="wrap cf">

                <div id="main" class="cf" role="main">

                    <!-- <h1 class="h1" style="display: none;">SINEQUA</h1> -->
                    <iframe style="height: 700px; width: 1200px;" src="/sinequa/search?profile=profil.CL_externe&amp;login=<?php echo $notaire->crpcen ?>&amp;password=<?php echo $notaire->web_password ; ?>&amp;watch_level=<?php echo $notaire->etude->subscription_level ; ?>" width="300" height="150"></iframe>

                </div>

                <?php /*get_sidebar();*/ ?>

            </div>

        </div>

    <?php get_footer(); ?>
<?php endif; ?>
