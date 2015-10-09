<h2><?php echo $object->__name; ?></h2>

<p>
    <?php echo $this->html->link('&#8592; All Actu Cridons', array('controller' => 'actu_cridons')); ?>
</p>
<?php query_posts('p='.$post->ID); ?>
<div class="fake_row">
    <div class="content_left small-12 large-8 columns no-padding" role="main">
        <?php while (have_posts()) : the_post(); ?>
                <article <?php post_class() ?> id="post-<?php the_ID(); ?>">
                        <div class="container_title">
                                <h1 class="entry-title"><?php the_title(); ?></h1>
                                <p class="date"><?php echo get_the_date(); ?></p>
                        </div>
                        <div class="entry-content">
                                <?php if(get_field("chapo_de_page")) { ?>
                                        <p class="chapeau"><?php echo get_field('chapo_de_page'); ?><p>
                                        <hr>
                                <?php } ?>
                                <?php the_content(); ?>
                        </div>
                </article>
        <?php endwhile; ?>
    </div>
    <?php get_sidebar(); ?>
</div>

</section>
