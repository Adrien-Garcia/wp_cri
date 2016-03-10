<?php get_header(); ?>
<div id="content" class="page">

    <div class="breadcrumbs">
        <div id="inner-content" class="wrap cf">
            <?php // if (function_exists('custom_breadcrumbs')) custom_breadcrumbs(); ?>
            <a href="#" title="">Accueil</a> + <span>Mon compte</span>
        </div>
    </div>


    <div id="main" class="cf" role="main">

        <div id="inner-content" class="wrap cf">

            <?php
            foreach ($liste as $key => $member) :
                echo 'NEW MEMBER : ';
                    var_dump($member);
            endforeach;
            ?>

        </div>

        <?php /*get_sidebar();*/ ?>

    </div>

</div>

<?php get_footer(); ?>
