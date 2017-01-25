<?php
?>
<?php get_header(); ?>
    <div id="content" class="page page-calendar">

        <div class="breadcrumbs">
            <div class="wrap cf">
                <?php if (function_exists('CriBreadcrumb')) CriBreadcrumb(); ?>
            </div>
        </div>

        <div id="inner-content" class="wrap cf">

            <div id="main" class="cf" role="main">

                 <h1 class="h1">Calendrier des formations</h1>

                <div id="calendar">
                    <?php $calendar; ?>
                </div>

            </div>

        </div>

    </div>

<?php get_footer(); ?>
